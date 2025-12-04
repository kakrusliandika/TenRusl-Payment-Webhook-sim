<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class AmazonBwpSignature
{
    /**
     * Verify Amazon Buy with Prime webhook signature (SIMULATOR).
     *
     * Header:
     *  - x-amzn-signature : base64 ECDSA signature (ES384)
     *
     * Simulator:
     *  - public key PEM dari config('tenrusl.amzn_bwp_public_key')
     *  - verify ECDSA SHA-384 terhadap rawBody
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $signatureB64 = self::headerString($request, 'x-amzn-signature');
        if ($signatureB64 === null) {
            return false;
        }

        $publicKeyPem = config('tenrusl.amzn_bwp_public_key');
        if (!is_string($publicKeyPem) || $publicKeyPem === '') {
            return false;
        }

        $sig = base64_decode($signatureB64, true);
        if ($sig === false) {
            return false;
        }

        $pem = self::normalizePem($publicKeyPem);

        // ES384 -> ECDSA-SHA384
        $ok = openssl_verify($rawBody, $sig, $pem, OPENSSL_ALGO_SHA384);

        return $ok === 1;
    }

    private static function normalizePem(string $key): string
    {
        $trim = trim($key);

        if (str_contains($trim, 'BEGIN PUBLIC KEY')) {
            return $trim;
        }

        $wrapped = chunk_split(str_replace(["\r", "\n", ' '], '', $trim), 64, "\n");

        return "-----BEGIN PUBLIC KEY-----\n{$wrapped}-----END PUBLIC KEY-----\n";
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
