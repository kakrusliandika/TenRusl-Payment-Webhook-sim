<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class TripaySignature
{
    /**
     * Verify TriPay callback signature.
     *
     * Docs: header `X-Callback-Signature` berisi HMAC-SHA256(raw JSON body, private_key).
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $privateKey = (string) config('tenrusl.tripay_private_key');
        if ($privateKey === '' || $privateKey === null) {
            return false;
        }

        $headerSig = $request->header('X-Callback-Signature');
        if (! $headerSig) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $privateKey);

        // TriPay contoh di docs menggunakan hexdump; gunakan compare case-insensitive
        return hash_equals(strtolower($expected), strtolower((string) $headerSig));
    }
}
