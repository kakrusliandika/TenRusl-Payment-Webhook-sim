<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class StripeSignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Standardized output:
     * - ok: true|false
     * - reason: short code for audit/logging (no secrets)
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $secret = config('tenrusl.stripe_webhook_secret');
        if (! is_string($secret) || trim($secret) === '') {
            return self::result(false, 'missing_secret');
        }

        $sigHeader = self::headerString($request, 'Stripe-Signature');
        if ($sigHeader === null) {
            return self::result(false, 'missing_signature_header');
        }

        [$timestamp, $signatures] = self::parseStripeHeader($sigHeader);
        if ($timestamp === null || $signatures === []) {
            return self::result(false, 'malformed_signature_header');
        }

        $tolerance = (int) config('tenrusl.signature.timestamp_leeway_seconds', 300);
        if ($tolerance < 0) {
            $tolerance = 0;
        }

        $now = time();
        if (abs($now - $timestamp) > $tolerance) {
            return self::result(false, 'timestamp_out_of_tolerance');
        }

        // IMPORTANT: must use RAW request body bytes.
        $signedPayload = (string) $timestamp.'.'.$rawBody;
        $expected = strtolower(hash_hmac('sha256', $signedPayload, $secret)); // lowercase hex

        foreach ($signatures as $candidate) {
            if (hash_equals($expected, $candidate)) {
                return self::result(true, 'ok');
            }
        }

        return self::result(false, 'invalid_signature');
    }

    /**
     * @return array{0: int|null, 1: list<string>} [timestamp, v1SignaturesLowercase]
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

            if ($k === 't' && $v !== '' && ctype_digit($v)) {
                $timestamp = (int) $v;

                continue;
            }

            if ($k === 'v1' && $v !== '') {
                $v1[] = strtolower($v);
            }
        }

        return [$timestamp, $v1];
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->headers->get($key);
        if (! is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }

    /**
     * @return array{ok: bool, reason: string}
     */
    private static function result(bool $ok, string $reason): array
    {
        return ['ok' => $ok, 'reason' => $reason];
    }
}
