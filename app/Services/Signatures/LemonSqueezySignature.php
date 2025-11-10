<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class LemonSqueezySignature
{
    /**
     * Verifies Lemon Squeezy webhook signature.
     *
     * Computes HMAC-SHA256(rawBody, secret) and compares with "X-Signature" (hex).
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = (string) config('tenrusl.ls_webhook_secret');
        if ($secret === '' || $secret === null) {
            return false;
        }

        $signature = $request->header('X-Signature');
        if (!$signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);
        return hash_equals(strtolower($expected), strtolower($signature));
    }
}
