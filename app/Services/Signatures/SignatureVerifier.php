<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Fasad verifikasi signature/token untuk berbagai provider.
 * Middleware boleh menggunakan kelas ini untuk memusatkan aturan verifikasi.
 */
class SignatureVerifier
{
    public function verify(string $provider, Request $request): array
    {
        $raw = $request->getContent();

        if ($provider === 'mock') {
            $impl = new MockSignature();
            return $impl->verify($raw, (string) config('tenrusl.mock_secret'), $request->header('X-Mock-Signature'));
        }

        if ($provider === 'xendit') {
            $impl = new XenditCallbackToken();
            return $impl->verify((string) config('tenrusl.xendit_callback_token'), $request->header('x-callback-token'));
        }

        if ($provider === 'midtrans') {
            $impl = new MidtransSignature();
            return $impl->verify($request);
        }

        return [
            'ok'   => false,
            'hash' => null,
            'msg'  => 'Unknown provider',
        ];
    }
}
