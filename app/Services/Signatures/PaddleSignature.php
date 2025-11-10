<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class PaddleSignature
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
        // Prefer Paddle Billing HMAC header if present
        $header = $request->header('Paddle-Signature') ?? $request->header('paddle-signature');
        if ($header) {
            return self::verifyBillingHmac($rawBody, $header);
        }

        // Fallback: classic RSA `p_signature` in form-encoded body
        return self::verifyClassicRsa($rawBody, $request);
    }

    /**
     * Paddle Billing: HMAC-SHA256 over "{$ts}:{$rawBody}" compared to h1 (hex).
     */
    private static function verifyBillingHmac(string $rawBody, string $paddleSignatureHeader): bool
    {
        $secret = (string) config('tenrusl.paddle_signing_secret');
        if ($secret === '' || $secret === null) {
            return false;
        }

        $parts = [];
        foreach (explode(';', $paddleSignatureHeader) as $item) {
            $kv = explode('=', trim($item), 2);
            if (count($kv) === 2) {
                $parts[strtolower($kv[0])] = $kv[1];
            }
        }

        $ts = $parts['ts'] ?? null;
        if (!$ts) {
            return false;
        }

        // Collect all h* signatures (h1, h2, ...)
        $provided = [];
        foreach ($parts as $k => $v) {
            if ($k === 'ts') {
                continue;
            }
            if (str_starts_with($k, 'h')) {
                $provided[] = strtolower($v);
            }
        }
        if (empty($provided)) {
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
     * Body is usually application/x-www-form-urlencoded.
     */
    private static function verifyClassicRsa(string $rawBody, Request $request): bool
    {
        $publicKey = (string) config('tenrusl.paddle_public_key');
        if ($publicKey === '' || $publicKey === null) {
            return false;
        }

        // Parse form body from raw payload (do not rely on filtered inputs)
        $fields = [];
        parse_str($rawBody, $fields);

        if (!is_array($fields)) {
            return false;
        }

        $pSignature = $fields['p_signature'] ?? null;
        if (!$pSignature) {
            return false;
        }

        unset($fields['p_signature']);

        // Sort fields by key and serialize to PHP format (Paddle classic expects PHP-serialize)
        ksort($fields);

        // Recursively ensure all values are strings/binary-safe for serialization
        $data = self::phpSerialize($fields);

        $binarySignature = base64_decode($pSignature, true);
        if ($binarySignature === false) {
            return false;
        }

        // Verify using RSA with SHA1 (historical Paddle Classic behavior)
        $ok = openssl_verify($data, $binarySignature, $publicKey, OPENSSL_ALGO_SHA1);

        return $ok === 1;
    }

    /**
     * Build a PHP-serialized string matching Paddle Classic's verification expectations.
     * We use native serialize() over the sorted array.
     */
    private static function phpSerialize(array $sorted): string
    {
        // Important: keep as close as possible to PHP serialization of the raw sorted POST
        return serialize($sorted);
    }
}
