<?php

namespace App\Services\Signatures;

class MockSignature
{
    /**
     * Verifikasi HMAC-SHA256 raw body menggunakan secret.
     *
     * @return array{ok:bool, hash:?string, msg:?string}
     */
    public function verify(string $rawBody, string $secret, ?string $header): array
    {
        if (! $secret || ! $header) {
            return ['ok' => false, 'hash' => null, 'msg' => 'Missing mock header/secret'];
        }

        $calc = hash_hmac('sha256', $rawBody, $secret);
        if (! hash_equals($calc, $header)) {
            return ['ok' => false, 'hash' => null, 'msg' => 'Invalid mock signature'];
        }

        return ['ok' => true, 'hash' => $calc, 'msg' => null];
    }
}
