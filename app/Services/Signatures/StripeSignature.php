<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class StripeSignature
{
    /**
     * Verifies Stripe webhook signature.
     *
     * Stripe signs the raw payload with: HMAC-SHA256(secret, "{$t}.{$rawBody}")
     * And sends it in the `Stripe-Signature` header as: t=TIMESTAMP,v1=HEX[,v1=...]
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = (string) config('tenrusl.stripe_webhook_secret');
        if ($secret === '' || $secret === null) {
            return false;
        }

        $sigHeader = $request->header('Stripe-Signature');
        if (! $sigHeader) {
            return false;
        }

        [$timestamp, $signatures] = self::parseStripeHeader($sigHeader);
        if (! $timestamp || empty($signatures)) {
            return false;
        }

        $signedPayload = $timestamp.'.'.$rawBody;
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        foreach ($signatures as $candidate) {
            if (hash_equals($expected, $candidate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse "Stripe-Signature" header to get timestamp and all v1 signatures.
     * Example: "t=1492774577,v1=5257...,v0=6ffb..."
     *
     * @return array{0: string|null, 1: string[]} [timestamp, v1Signatures]
     */
    private static function parseStripeHeader(string $header): array
    {
        $timestamp = null;
        $v1 = [];

        foreach (explode(',', $header) as $part) {
            $kv = explode('=', trim($part), 2);
            if (count($kv) !== 2) {
                continue;
            }
            [$k, $v] = $kv;
            if ($k === 't') {
                $timestamp = $v;
            } elseif ($k === 'v1') {
                $v1[] = strtolower($v);
            }
            // ignore other schemes like v0
        }

        return [$timestamp, $v1];
    }
}
