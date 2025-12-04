<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class PaddleSignature
{
    /**
     * Verifies Paddle webhook signature.
     *
     * Supports:
     * - Paddle Billing (modern): "Paddle-Signature" header with "ts=...,h1=..."
     *   signature = HMAC-SHA256(secret, "{$ts}:{$rawBody}")
     * - (Optional) Paddle Classic: form-POST with "p_signature" (RSA, public key)
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $header = self::headerString($request, 'Paddle-Signature')
            ?? self::headerString($request, 'paddle-signature');

        if ($header !== null) {
            return self::verifyBillingHmac($rawBody, $header);
        }

        return self::verifyClassicRsa($rawBody);
    }

    /**
     * Paddle Billing: HMAC-SHA256 over "{$ts}:{$rawBody}" compared to h1/h2/... (hex).
     */
    private static function verifyBillingHmac(string $rawBody, string $paddleSignatureHeader): bool
    {
        $secret = config('tenrusl.paddle_signing_secret');
        if (!is_string($secret) || $secret === '') {
            return false;
        }

        // Parse header like: "ts=1700000000; h1=abcdef...; h2=..."
        $parts = [];
        foreach (explode(';', $paddleSignatureHeader) as $item) {
            $item = trim($item);
            if ($item === '') {
                continue;
            }

            $kv = explode('=', $item, 2);
            if (count($kv) !== 2) {
                continue;
            }

            $k = strtolower(trim($kv[0]));
            $v = trim($kv[1]);

            // Simpan hanya key/value non-empty agar tipe $parts jadi non-empty-string
            if ($k !== '' && $v !== '') {
                $parts[$k] = $v;
            }
        }

        if (!isset($parts['ts'])) {
            return false;
        }
        $ts = $parts['ts']; // non-empty-string

        // Collect all h* signatures (h1, h2, ...)
        $provided = [];
        foreach ($parts as $k => $v) {
            if ($k === 'ts') {
                continue;
            }
            if (str_starts_with($k, 'h')) {
                // $v sudah non-empty-string, jadi tidak perlu is_string / !== '' lagi
                $provided[] = strtolower($v);
            }
        }

        if ($provided === []) {
            return false;
        }

        $signedPayload = $ts . ':' . $rawBody;
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        foreach ($provided as $sig) {
            if (hash_equals($expected, $sig)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Paddle Classic: verify RSA signature in `p_signature` (form POST).
     */
    private static function verifyClassicRsa(string $rawBody): bool
    {
        $publicKey = config('tenrusl.paddle_public_key');
        if (!is_string($publicKey) || $publicKey === '') {
            return false;
        }

        $fields = [];
        parse_str($rawBody, $fields); // $fields pasti array

        $pSignature = $fields['p_signature'] ?? null;
        if (!is_string($pSignature) || $pSignature === '') {
            return false;
        }

        unset($fields['p_signature']);
        ksort($fields);

        $data = serialize($fields);

        $binarySignature = base64_decode($pSignature, true);
        if ($binarySignature === false) {
            return false;
        }

        $ok = openssl_verify($data, $binarySignature, $publicKey, OPENSSL_ALGO_SHA1);

        return $ok === 1;
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
