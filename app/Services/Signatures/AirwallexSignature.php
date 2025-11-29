<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class AirwallexSignature
{
    /**
     * Verify Airwallex webhook signature.
     *
     * Docs: x-timestamp + x-signature (HMAC-SHA256).
     * value_to_digest = "{$timestamp}{$rawBody}"
     * signature = hex(HMAC_SHA256(secret, value_to_digest))
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = (string) config('tenrusl.airwallex_webhook_secret');
        if ($secret === '' || $secret === null) {
            return false;
        }

        $timestamp = $request->header('x-timestamp');
        $signature = $request->header('x-signature');

        if ($timestamp === null || $signature === null) {
            return false;
        }

        // Build message per docs: timestamp + raw JSON body
        $message = (string) $timestamp.$rawBody;
        $expected = hash_hmac('sha256', $message, $secret);

        // Airwallex docs show hex digest in header; compare case-insensitively
        return hash_equals(strtolower($expected), strtolower((string) $signature));
    }
}
