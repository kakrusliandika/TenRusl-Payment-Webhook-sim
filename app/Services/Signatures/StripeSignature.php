<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class StripeSignature
{
    /**
     * Verifies Stripe webhook signature.
     *
     * Stripe signs the raw payload with:
     *   expected = HMAC-SHA256(secret, "{$t}.{$rawBody}")
     *
     * Header "Stripe-Signature" format:
     *   t=TIMESTAMP,v1=HEX[,v1=HEX...]
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = config('tenrusl.stripe_webhook_secret');
        if (!is_string($secret) || trim($secret) === '') {
            return false;
        }

        $sigHeader = self::headerString($request, 'Stripe-Signature');
        if ($sigHeader === null) {
            return false;
        }

        [$timestamp, $signatures] = self::parseStripeHeader($sigHeader);
        if ($timestamp === null || $signatures === []) {
            return false;
        }

        $signedPayload = $timestamp . '.' . $rawBody;
        $expected = strtolower(hash_hmac('sha256', $signedPayload, $secret)); // hex lowercase

        foreach ($signatures as $candidate) {
            if (hash_equals($expected, $candidate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{0: string|null, 1: list<string>} [timestamp, v1SignaturesLowercase]
     */
    private static function parseStripeHeader(string $header): array
    {
        $timestamp = null;
        $v1 = [];

        foreach (explode(',', $header) as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            $kv = explode('=', $part, 2);
            if (count($kv) !== 2) {
                continue;
            }

            $k = trim($kv[0]);
            $v = trim($kv[1]);

            if ($k === 't' && $v !== '') {
                $timestamp = $v;
                continue;
            }

            if ($k === 'v1' && $v !== '') {
                $v1[] = strtolower($v);
            }
        }

        // $v1 sudah list<string>, jadi array_values() tidak diperlukan.
        return [$timestamp, $v1];
    }

    /**
     * Ambil header jadi ?string yang bersih.
     */
    private static function headerString(Request $request, string $key): ?string
    {
        // HeaderBag::get($key) -> ?string (aman untuk analyzer & tidak pakai argumen tambahan)
        $v = $request->headers->get($key);
        if ($v === null) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }
}
