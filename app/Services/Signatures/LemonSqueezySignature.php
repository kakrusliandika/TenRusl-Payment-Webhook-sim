<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class LemonSqueezySignature
{
    /**
     * Verifies Lemon Squeezy webhook signature.
     *
     * Lemon Squeezy mengirim hash payload di header `X-Signature`.
     * Umumnya: HMAC SHA-256 atas raw body menggunakan signing secret.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = config('tenrusl.ls_webhook_secret');
        if (!is_string($secret) || $secret === '') {
            return false;
        }

        $signature = self::headerString($request, 'X-Signature');
        if ($signature === null) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals(strtolower($expected), strtolower($signature));
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
