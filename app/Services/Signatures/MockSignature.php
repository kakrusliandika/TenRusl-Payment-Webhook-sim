<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class MockSignature
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
     * Skema yang diterima:
     *  A) Authorization: Bearer {MOCK_SECRET}
     *  B) X-Mock-Signature: hex(HMAC-SHA256(rawBody, MOCK_SECRET))
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $secret = config('tenrusl.mock_secret');
        if (!is_string($secret) || trim($secret) === '') {
            return self::result(false, 'missing_secret');
        }

        // A) Authorization bearer
        $auth = self::headerString($request, 'Authorization');
        if ($auth !== null && preg_match('/^Bearer\s+(.+)$/i', $auth, $m) === 1) {
            if (hash_equals($secret, trim((string) $m[1]))) {
                return self::result(true, 'ok');
            }

            return self::result(false, 'invalid_bearer_token');
        }

        // B) HMAC header
        $sig = self::headerString($request, 'X-Mock-Signature');
        if ($sig !== null) {
            $calc = hash_hmac('sha256', $rawBody, $secret);
            if (hash_equals(strtolower($calc), strtolower($sig))) {
                return self::result(true, 'ok');
            }

            return self::result(false, 'invalid_signature');
        }

        return self::result(false, 'missing_signature');
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

    /**
     * @return array{ok: bool, reason: string}
     */
    private static function result(bool $ok, string $reason): array
    {
        return ['ok' => $ok, 'reason' => $reason];
    }
}
