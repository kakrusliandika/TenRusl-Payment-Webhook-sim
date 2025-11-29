<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class XenditSignature
{
    /**
     * Verifikasi webhook Xendit menggunakan x-callback-token.
     *
     * Setiap webhook Xendit menyertakan header:
     *   x-callback-token: <token rahasia akun>
     * Bandingkan nilai header tersebut dengan token di konfigurasi.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $expected = (string) config('tenrusl.xendit_callback_token');
        if ($expected === '' || $expected === null) {
            return false;
        }

        // Header bisa muncul dengan variasi kapitalisasi; Laravel header() case-insensitive.
        $token = $request->header('x-callback-token')
              ?? $request->header('X-CALLBACK-TOKEN');

        if (! is_string($token) || $token === '') {
            return false;
        }

        return hash_equals($expected, trim($token));
    }
}
