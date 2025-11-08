<?php

namespace App\Services\Signatures;

class XenditCallbackToken
{
    /**
     * Verifikasi token callback Xendit.
     *
     * @return array{ok:bool, hash:?string, msg:?string}
     */
    public function verify(string $expectedToken, ?string $header): array
    {
        if (! $expectedToken || ! $header) {
            return ['ok' => false, 'hash' => null, 'msg' => 'Missing xendit token'];
        }

        if (! hash_equals($expectedToken, $header)) {
            return ['ok' => false, 'hash' => null, 'msg' => 'Invalid xendit token'];
        }

        // Hash ringkas untuk jejak audit
        return ['ok' => true, 'hash' => 'xendit:' . substr(hash('sha256', $header), 0, 32), 'msg' => null];
    }
}
