<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class MockSignature
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
        $secret = (string) config('tenrusl.mock_secret');
        if ($secret === '' || $secret === null) {
            return false;
        }

        // A) Authorization bearer
        $auth = $request->header('Authorization');
        if ($auth && preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
            if (hash_equals($secret, trim($m[1]))) {
                return true;
            }
        }

        // B) HMAC header
        $sig = $request->header('X-Mock-Signature');
        if (is_string($sig) && $sig !== '') {
            $calc = hash_hmac('sha256', $rawBody, $secret);
            if (hash_equals(strtolower($calc), strtolower($sig))) {
                return true;
            }
        }

        return false;
    }
}
