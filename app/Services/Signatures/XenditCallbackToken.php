<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class XenditCallbackToken
{
    /**
     * Xendit selalu menyertakan token verifikasi pada header:
     *   x-callback-token (atau X-CALLBACK-TOKEN)
     * Bandingkan header tersebut dengan nilai di konfigurasi.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $expected = (string) config('tenrusl.xendit_callback_token');
        if ($expected === '' || $expected === null) {
            return false;
        }

        // Case-insensitive: beberapa integrasi memakai variasi kapitalisasi
        $token = $request->header('x-callback-token')
              ?? $request->header('X-CALLBACK-TOKEN')
              ?? $request->header('X-Callback-Token');

        if (! is_string($token) || $token === '') {
            return false;
        }

        return hash_equals($expected, trim($token));
    }
}
