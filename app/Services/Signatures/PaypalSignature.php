<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class PaypalSignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Standardized output:
     * - ok: true|false
     * - reason: short code for audit/logging (no secrets)
     *
     * PayPal verifies webhook signatures via Verify Webhook Signature API.
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $clientId = (string) config('tenrusl.paypal_client_id');
        $clientSecret = (string) config('tenrusl.paypal_client_secret');
        $webhookId = (string) config('tenrusl.paypal_webhook_id');
        $env = (string) config('tenrusl.paypal_env', 'sandbox');

        if ($clientId === '' || $clientSecret === '' || $webhookId === '') {
            self::logWarn($request, 'paypal_missing_config', [
                'env' => $env,
            ]);

            return self::result(false, 'missing_config');
        }

        $base = strtolower($env) === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        // Required headers for verification
        $transmissionId = self::headerString($request, 'PAYPAL-TRANSMISSION-ID');
        $transmissionTime = self::headerString($request, 'PAYPAL-TRANSMISSION-TIME');
        $transmissionSig = self::headerString($request, 'PAYPAL-TRANSMISSION-SIG');
        $certUrl = self::headerString($request, 'PAYPAL-CERT-URL');
        $authAlgo = self::headerString($request, 'PAYPAL-AUTH-ALGO');

        if ($transmissionId === null || $transmissionTime === null || $transmissionSig === null || $certUrl === null || $authAlgo === null) {
            self::logWarn($request, 'paypal_missing_headers', [
                'env' => $env,
                'missing' => [
                    'PAYPAL-TRANSMISSION-ID' => $transmissionId === null,
                    'PAYPAL-TRANSMISSION-TIME' => $transmissionTime === null,
                    'PAYPAL-TRANSMISSION-SIG' => $transmissionSig === null,
                    'PAYPAL-CERT-URL' => $certUrl === null,
                    'PAYPAL-AUTH-ALGO' => $authAlgo === null,
                ],
            ]);

            return self::result(false, 'missing_headers');
        }

        $event = json_decode($rawBody, true);
        if (!is_array($event)) {
            self::logWarn($request, 'paypal_invalid_json', [
                'env' => $env,
            ]);

            return self::result(false, 'invalid_json');
        }

        $eventId = isset($event['id']) ? (string) $event['id'] : '';

        // Fail-closed + resource bounded: short timeout and limited retries.
        $token = self::getOAuthToken($base, $clientId, $clientSecret, $request, $env);
        if ($token === null) {
            self::logWarn($request, 'paypal_oauth_failed', [
                'env' => $env,
                'event_id' => $eventId,
            ]);

            return self::result(false, 'oauth_failed');
        }

        $payload = [
            'auth_algo' => $authAlgo,
            'cert_url' => $certUrl,
            'transmission_id' => $transmissionId,
            'transmission_sig' => $transmissionSig,
            'transmission_time' => $transmissionTime,
            'webhook_id' => $webhookId,
            'webhook_event' => $event,
        ];

        // Verify signature (bounded timeout + bounded retry)
        try {
            $verifyResp = Http::withToken($token)
                ->acceptJson()
                ->timeout(self::verifyTimeoutSeconds())
                ->retry(self::verifyRetries(), self::verifyRetryDelayMs(), throw: false)
                ->post($base . '/v1/notifications/verify-webhook-signature', $payload);
        } catch (\Throwable $e) {
            // Fail-closed on any exception (including timeout connection exception)
            self::logWarn($request, 'paypal_verify_exception', [
                'env' => $env,
                'event_id' => $eventId,
                'exception' => $e::class,
            ]);

            return self::result(false, 'verify_exception');
        }

        if (!$verifyResp->ok()) {
            // If token got revoked/expired unexpectedly, purge cache and stop (fail-closed).
            if (in_array($verifyResp->status(), [401, 403], true)) {
                self::forgetOAuthTokenCache($clientId, $env);
            }

            self::logWarn($request, 'paypal_verify_http_failed', [
                'env' => $env,
                'event_id' => $eventId,
                'status' => $verifyResp->status(),
            ]);

            return self::result(false, 'verify_http_failed');
        }

        $status = strtoupper((string) $verifyResp->json('verification_status'));
        if ($status === 'SUCCESS') {
            return self::result(true, 'ok');
        }

        self::logWarn($request, 'paypal_invalid_signature', [
            'env' => $env,
            'event_id' => $eventId,
            'verification_status' => $status,
        ]);

        return self::result(false, 'invalid_signature');
    }

    /**
     * OAuth token caching to avoid requesting token for every webhook.
     * Uses expires_in with a safety buffer.
     */
    private static function getOAuthToken(string $base, string $clientId, string $clientSecret, Request $request, string $env): ?string
    {
        $cacheKey = self::oauthCacheKey($clientId, $env);
        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        try {
            $resp = Http::asForm()
                ->acceptJson()
                ->withBasicAuth($clientId, $clientSecret)
                ->timeout(self::tokenTimeoutSeconds())
                ->retry(self::tokenRetries(), self::tokenRetryDelayMs(), throw: false)
                ->post($base . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);
        } catch (\Throwable $e) {
            self::logWarn($request, 'paypal_oauth_exception', [
                'env' => $env,
                'exception' => $e::class,
            ]);

            return null;
        }

        if (!$resp->ok()) {
            self::logWarn($request, 'paypal_oauth_http_failed', [
                'env' => $env,
                'status' => $resp->status(),
            ]);

            return null;
        }

        $token = (string) ($resp->json('access_token') ?? '');
        $expiresIn = (int) ($resp->json('expires_in') ?? 0);

        if ($token === '' || $expiresIn <= 0) {
            self::logWarn($request, 'paypal_oauth_invalid_response', [
                'env' => $env,
            ]);

            return null;
        }

        // Safety buffer to avoid edge-expiry (min 60s, max 10min buffer)
        $buffer = min(600, max(60, (int) floor($expiresIn * 0.1)));
        $ttl = max(60, $expiresIn - $buffer);

        Cache::put($cacheKey, $token, $ttl);

        return $token;
    }

    private static function forgetOAuthTokenCache(string $clientId, string $env): void
    {
        Cache::forget(self::oauthCacheKey($clientId, $env));
    }

    private static function oauthCacheKey(string $clientId, string $env): string
    {
        // Do not store clientId in plaintext as cache key
        return 'tenrusl:paypal:oauth_token:' . strtolower($env) . ':' . hash('sha256', $clientId);
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->headers->get($key);
        if (!is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }

    private static function requestId(Request $request): string
    {
        $attr = $request->attributes->get('correlation_id');
        if (is_string($attr) && $attr !== '') {
            return $attr;
        }

        $hdr = $request->headers->get('X-Request-ID');
        if (is_string($hdr) && $hdr !== '') {
            return (string) $hdr;
        }

        return '';
    }

    private static function tokenTimeoutSeconds(): int
    {
        $v = config('tenrusl.paypal.http.token_timeout_seconds');
        if (is_numeric($v) && (int) $v > 0) {
            return (int) $v;
        }

        return 3;
    }

    private static function verifyTimeoutSeconds(): int
    {
        $v = config('tenrusl.paypal.http.verify_timeout_seconds');
        if (is_numeric($v) && (int) $v > 0) {
            return (int) $v;
        }

        return 5;
    }

    private static function tokenRetries(): int
    {
        $v = config('tenrusl.paypal.http.token_retries');
        if (is_numeric($v) && (int) $v >= 0) {
            return (int) $v;
        }

        return 1;
    }

    private static function verifyRetries(): int
    {
        $v = config('tenrusl.paypal.http.verify_retries');
        if (is_numeric($v) && (int) $v >= 0) {
            return (int) $v;
        }

        return 1;
    }

    private static function tokenRetryDelayMs(): int
    {
        $v = config('tenrusl.paypal.http.token_retry_delay_ms');
        if (is_numeric($v) && (int) $v >= 0) {
            return (int) $v;
        }

        return 200;
    }

    private static function verifyRetryDelayMs(): int
    {
        $v = config('tenrusl.paypal.http.verify_retry_delay_ms');
        if (is_numeric($v) && (int) $v >= 0) {
            return (int) $v;
        }

        return 200;
    }

    private static function logWarn(Request $request, string $message, array $context = []): void
    {
        $base = [
            'provider' => 'paypal',
            'request_id' => self::requestId($request),
        ];

        Log::warning($message, array_merge($base, $context));
    }

    /**
     * @return array{ok: bool, reason: string}
     */
    private static function result(bool $ok, string $reason): array
    {
        return ['ok' => $ok, 'reason' => $reason];
    }
}
