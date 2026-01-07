<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class AirwallexSignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Airwallex webhook signature:
     * - x-timestamp: unix timestamp in milliseconds
     * - x-signature: HMAC-SHA256 hex digest of (x-timestamp as string + raw JSON payload)
     * - After signature match, check timestamp tolerance to mitigate replay.
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $secret = config('tenrusl.airwallex_webhook_secret');
        if (! is_string($secret) || trim($secret) === '') {
            return self::result(false, 'missing_secret');
        }

        $timestampStr = self::headerString($request, 'x-timestamp');
        $signature = self::headerString($request, 'x-signature');

        if ($timestampStr === null || $signature === null) {
            return self::result(false, 'missing_signature_headers');
        }

        if (! ctype_digit($timestampStr)) {
            return self::result(false, 'invalid_timestamp_format');
        }

        // IMPORTANT: use x-timestamp EXACTLY as received (string) for digest.
        $valueToDigest = $timestampStr.$rawBody;
        $expected = hash_hmac('sha256', $valueToDigest, $secret); // hex lowercase

        $sigNorm = strtolower(trim($signature));
        if (str_starts_with($sigNorm, 'sha256=')) {
            $sigNorm = substr($sigNorm, 7);
        }

        if (! hash_equals(strtolower($expected), $sigNorm)) {
            return self::result(false, 'invalid_signature');
        }

        // Timestamp tolerance (ms)
        $leewaySeconds = (int) config('tenrusl.signature.timestamp_leeway_seconds', 300);
        if ($leewaySeconds < 0) {
            $leewaySeconds = 0;
        }

        $tsMs = (int) $timestampStr;
        $nowMs = (int) floor(microtime(true) * 1000);

        // Fail-closed if timestamp is absurd (e.g., negative)
        if ($tsMs <= 0) {
            return self::result(false, 'invalid_timestamp_value');
        }

        $diffMs = abs($nowMs - $tsMs);
        if ($diffMs > ($leewaySeconds * 1000)) {
            return self::result(false, 'timestamp_out_of_tolerance');
        }

        return self::result(true, 'ok');
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->headers->get($key);
        if (! is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }

    /**
     * @return array{ok: bool, reason: string}
     */
    private static function result(bool $ok, string $reason): array
    {
        return ['ok' => $ok, 'reason' => $reason];
    }
}
