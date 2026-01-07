<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;
use Throwable;

/**
 * SignatureVerifier
 * -----------------
 * Source-of-truth untuk routing verifikasi signature berbasis provider.
 *
 * Kontrak utama:
 * - Dipanggil oleh middleware VerifyWebhookSignature sebelum masuk controller/domain.
 * - Provider harus ada di allowlist config('tenrusl.providers_allowlist').
 * - Tiap verifier provider idealnya punya:
 *
 *     public static function verifyWithReason(string $rawBody, Request $request): array{ok:bool, reason:string}
 *
 *   Backward-compat masih didukung:
 *
 *     public static function verify(string $rawBody, Request $request): bool
 *
 * Catatan penting:
 * - Raw body HARUS yang asli dari Request::getContent() (bukan json_encode hasil decode).
 * - Provider verifier harus memakai perbandingan signature timing-safe (hash_equals).
 * - Optional: batasi event types yang diterima per provider (config-driven) agar tidak mubazir.
 */
final class SignatureVerifier
{
    /**
     * Pemetaan provider → verifier class.
     *
     * @var array<string, class-string>
     */
    private const MAP = [
        // existing / core demo
        'mock' => MockSignature::class,
        'xendit' => XenditSignature::class,
        'midtrans' => MidtransSignature::class,

        // tambahan
        'stripe' => StripeSignature::class,
        'paypal' => PaypalSignature::class,
        'paddle' => PaddleSignature::class,
        'lemonsqueezy' => LemonSqueezySignature::class,
        'airwallex' => AirwallexSignature::class,
        'tripay' => TripaySignature::class,
        'doku' => DokuSignature::class,
        'dana' => DanaSignature::class,
        'oy' => OySignature::class,
        'payoneer' => PayoneerSignature::class,
        'skrill' => SkrillSignature::class,
        'amazon_bwp' => AmazonBwpSignature::class,
    ];

    /**
     * Backward-compatible: verifikasi signature untuk provider tertentu (bool only).
     */
    public static function verify(string $provider, string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($provider, $rawBody, $request)['ok'];
    }

    /**
     * Verifikasi signature dengan output standar: ok + reason singkat (tanpa bocor secret).
     *
     * Fail-safe defaults:
     * - allowlist kosong => FAIL (deny-by-default)
     * - provider tidak dikenal / verifier tidak tersedia => FAIL
     * - exception di verifier => FAIL
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $provider, string $rawBody, Request $request): array
    {
        $provider = strtolower(trim($provider));
        if ($provider === '') {
            return self::result(false, 'missing_provider');
        }

        // Enforce allowlist (deny-by-default).
        $allow = self::normalizeProviders((array) config('tenrusl.providers_allowlist', []));
        if ($allow === []) {
            return self::result(false, 'allowlist_empty');
        }
        if (! in_array($provider, $allow, true)) {
            return self::result(false, 'provider_not_allowed');
        }

        $class = self::MAP[$provider] ?? null;
        if ($class === null) {
            return self::result(false, 'unsupported_provider');
        }

        if (! class_exists($class)) {
            return self::result(false, 'verifier_class_missing');
        }

        $strict = self::strictMode();

        try {
            // Preferred: standardized verifier output
            if (is_callable([$class, 'verifyWithReason'])) {
                /** @var mixed $out */
                $out = $class::verifyWithReason($rawBody, $request);

                // Normalize expected shape
                if (is_array($out)) {
                    $ok = $out['ok'] ?? null;
                    $reason = $out['reason'] ?? null;

                    if (is_bool($ok) && is_string($reason) && $reason !== '') {
                        if (! $ok) {
                            return ['ok' => false, 'reason' => $reason];
                        }

                        // Optional: event type allowlist (config-driven)
                        $evt = self::extractEventType($provider, $rawBody, $request);
                        $allowed = self::allowedEventTypes($provider);

                        if ($allowed !== []) {
                            if ($evt === null || $evt === '') {
                                return $strict
                                    ? self::result(false, 'missing_event_type')
                                    : self::result(true, 'ok');
                            }

                            if (! in_array($evt, $allowed, true)) {
                                return self::result(false, 'event_type_not_allowed');
                            }
                        }

                        return ['ok' => true, 'reason' => 'ok'];
                    }
                }

                // If provider returns unexpected shape, fail-closed
                return self::result(false, 'invalid_verifier_return');
            }

            // Fallback: legacy bool verifier
            if (is_callable([$class, 'verify'])) {
                /** @var bool $ok */
                $ok = (bool) $class::verify($rawBody, $request);

                if (! $ok) {
                    return self::result(false, 'invalid_signature');
                }

                $evt = self::extractEventType($provider, $rawBody, $request);
                $allowed = self::allowedEventTypes($provider);

                if ($allowed !== []) {
                    if ($evt === null || $evt === '') {
                        return $strict
                            ? self::result(false, 'missing_event_type')
                            : self::result(true, 'ok');
                    }

                    if (! in_array($evt, $allowed, true)) {
                        return self::result(false, 'event_type_not_allowed');
                    }
                }

                return self::result(true, 'ok');
            }

            return self::result(false, 'verifier_contract_missing');
        } catch (Throwable) {
            return self::result(false, 'verifier_exception');
        }
    }

    /**
     * Daftar provider yang *disupport oleh verifier layer ini*.
     * Jika allowlist diset, hasilnya adalah interseksi MAP vs allowlist.
     * Jika allowlist kosong => [] (deny-by-default).
     *
     * @return string[]
     */
    public static function supported(): array
    {
        $providers = array_keys(self::MAP);
        $allow = self::normalizeProviders((array) config('tenrusl.providers_allowlist', []));

        if ($allow === []) {
            return [];
        }

        $filtered = array_values(array_intersect($providers, $allow));
        sort($filtered);

        return $filtered;
    }

    /**
     * Daftar semua provider yang tersedia di MAP, tanpa mempertimbangkan allowlist.
     *
     * @return string[]
     */
    public static function supportedAll(): array
    {
        $providers = array_keys(self::MAP);
        sort($providers);

        return $providers;
    }

    /**
     * Timing-safe compare helper untuk provider verifiers.
     * (Provider verifiers sebaiknya gunakan hash_equals() langsung.)
     */
    public static function constantTimeEquals(string $a, string $b): bool
    {
        // hash_equals tetap aman untuk panjang berbeda.
        return hash_equals($a, $b);
    }

    private static function strictMode(): bool
    {
        $cfg = config('tenrusl.signature.strict');
        if (is_bool($cfg)) {
            return $cfg;
        }

        $env = strtolower((string) config('app.env', 'production'));

        return $env === 'production';
    }

    /**
     * Optional allowlist event types (config-driven).
     *
     * Supported shapes:
     * 1) tenrusl.webhooks.allowed_event_types = ['stripe' => ['payment_intent.succeeded', ...], 'paypal' => [...]]
     * 2) tenrusl.webhooks.allowed_event_types.<provider> = ['...']
     * 3) tenrusl.webhooks.allowed_event_types = ['...']  (global list)
     *
     * @return string[]
     */
    private static function allowedEventTypes(string $provider): array
    {
        $provider = strtolower(trim($provider));
        if ($provider === '') {
            return [];
        }

        $direct = config("tenrusl.webhooks.allowed_event_types.{$provider}");
        if (is_array($direct)) {
            return self::normalizeEventTypes($direct);
        }

        $cfg = config('tenrusl.webhooks.allowed_event_types');
        if (is_array($cfg)) {
            // associative provider map?
            if (array_key_exists($provider, $cfg) && is_array($cfg[$provider])) {
                return self::normalizeEventTypes($cfg[$provider]);
            }

            // global list?
            if (array_is_list($cfg)) {
                return self::normalizeEventTypes($cfg);
            }
        }

        return [];
    }

    /**
     * Extract event type dari raw payload (best-effort).
     * Jika tidak bisa diparse, return null.
     */
    private static function extractEventType(string $provider, string $rawBody, Request $request): ?string
    {
        $provider = strtolower(trim($provider));

        // detect content type
        $ct = $request->headers->get('Content-Type');
        $ct = is_string($ct) ? strtolower(trim(explode(';', $ct, 2)[0])) : '';

        $looksJson = self::looksLikeJson($rawBody);
        $isJson = $looksJson || $ct === 'application/json' || str_ends_with($ct, '+json');

        if ($isJson) {
            $decoded = json_decode($rawBody, true);
            if (! is_array($decoded)) {
                return null;
            }

            $candidates = match ($provider) {
                'stripe' => [
                    $decoded['type'] ?? null,
                ],
                'paypal' => [
                    $decoded['event_type'] ?? null,
                    $decoded['type'] ?? null,
                ],
                'paddle' => [
                    $decoded['event_type'] ?? null,
                    $decoded['alert_name'] ?? null,
                    $decoded['type'] ?? null,
                ],
                'midtrans' => [
                    $decoded['transaction_status'] ?? null,
                    $decoded['status'] ?? null,
                    $decoded['type'] ?? null,
                ],
                default => [
                    $decoded['type'] ?? null,
                    $decoded['event_type'] ?? null,
                    $decoded['event'] ?? null,
                ],
            };

            foreach ($candidates as $v) {
                if (is_string($v)) {
                    $t = trim($v);
                    if ($t !== '') {
                        return $t;
                    }
                }
            }

            return null;
        }

        // form-encoded
        if ($ct === 'application/x-www-form-urlencoded') {
            $params = [];
            parse_str($rawBody, $params);
            if (! is_array($params)) {
                return null;
            }

            $candidates = match ($provider) {
                'paddle' => [
                    $params['event_type'] ?? null,
                    $params['alert_name'] ?? null,
                ],
                'skrill' => [
                    $params['status'] ?? null,
                    $params['transaction_type'] ?? null,
                ],
                default => [
                    $params['type'] ?? null,
                    $params['event_type'] ?? null,
                ],
            };

            foreach ($candidates as $v) {
                if (is_string($v)) {
                    $t = trim($v);
                    if ($t !== '') {
                        return $t;
                    }
                }
            }

            return null;
        }

        return null;
    }

    private static function looksLikeJson(string $rawBody): bool
    {
        $t = ltrim($rawBody);
        return $t !== '' && ($t[0] === '{' || $t[0] === '[');
    }

    /**
     * @param  array<int, mixed>  $types
     * @return string[]
     */
    private static function normalizeEventTypes(array $types): array
    {
        $out = [];

        foreach ($types as $t) {
            $v = trim((string) $t);
            if ($v !== '') {
                $out[] = $v;
            }
        }

        $out = array_values(array_unique($out));
        sort($out);

        return $out;
    }

    /**
     * @param  array<int, mixed>  $providers
     * @return string[]
     */
    private static function normalizeProviders(array $providers): array
    {
        $out = [];

        foreach ($providers as $p) {
            $v = strtolower(trim((string) $p));
            if ($v !== '') {
                $out[] = $v;
            }
        }

        $out = array_values(array_unique($out));
        sort($out);

        return $out;
    }

    /**
     * @return array{ok: bool, reason: string}
     */
    private static function result(bool $ok, string $reason): array
    {
        return ['ok' => $ok, 'reason' => $reason];
    }
}
