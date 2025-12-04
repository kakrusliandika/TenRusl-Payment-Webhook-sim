<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class AirwallexSignature
{
    /**
     * Verify Airwallex webhook signature.
     *
     * Docs (umum): x-timestamp + x-signature (HMAC-SHA256)
     * value_to_digest = "{$timestamp}{$rawBody}"
     * signature = hex(HMAC_SHA256(secret, value_to_digest))
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = config('tenrusl.airwallex_webhook_secret');
        if (!is_string($secret) || $secret === '') {
            return false;
        }

        $timestamp = self::headerString($request, 'x-timestamp');
        $signature = self::headerString($request, 'x-signature');

        if ($timestamp === null || $signature === null) {
            return false;
        }

        $message = $timestamp . $rawBody;

        // hash_hmac default output = hex lowercase
        $expected = hash_hmac('sha256', $message, $secret);

        // header bisa beda casing
        $sigNorm = strtolower($signature);

        return hash_equals($expected, $sigNorm);
    }

    /**
     * Laravel Request::header($key) untuk $key string -> string|null.
     * Jadi kita cukup normalize trim + empty-check.
     */
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
