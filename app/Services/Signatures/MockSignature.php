<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class MockSignature
{
    /**
     * Verifikasi "mock" untuk keperluan demo.
     *
     * Skema yang diterima:
     *  A) Authorization: Bearer {MOCK_SECRET}
     *  B) X-Mock-Signature: hex(HMAC-SHA256(rawBody, MOCK_SECRET))
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = config('tenrusl.mock_secret');
        if (!is_string($secret) || $secret === '') {
            return false;
        }

        // A) Authorization bearer
        $auth = self::headerString($request, 'Authorization');
        if ($auth !== null && preg_match('/^Bearer\s+(.+)$/i', $auth, $m) === 1) {
            if (hash_equals($secret, trim((string) $m[1]))) {
                return true;
            }
        }

        // B) HMAC header
        $sig = self::headerString($request, 'X-Mock-Signature');
        if ($sig !== null) {
            $calc = hash_hmac('sha256', $rawBody, $secret);
            if (hash_equals(strtolower($calc), strtolower($sig))) {
                return true;
            }
        }

        return false;
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->header($key);

        if (!is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }
}
