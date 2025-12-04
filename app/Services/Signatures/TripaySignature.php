<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class TripaySignature
{
    /**
     * Verify TriPay callback signature.
     *
     * Header `X-Callback-Signature` berisi:
     *   hex(HMAC-SHA256(rawBody, private_key))
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $privateKey = config('tenrusl.tripay_private_key');
        if (!is_string($privateKey) || trim($privateKey) === '') {
            return false;
        }

        $headerSig = self::headerString($request, 'X-Callback-Signature');
        if ($headerSig === null) {
            return false;
        }

        $expected = strtolower(hash_hmac('sha256', $rawBody, $privateKey));
        $provided = strtolower($headerSig);

        return hash_equals($expected, $provided);
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->headers->get($key);
        if ($v === null) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }
}
