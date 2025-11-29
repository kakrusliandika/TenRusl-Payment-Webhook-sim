<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class AmazonBwpSignature
{
    /**
     * Verify Amazon Buy with Prime webhook signature.
     *
     * Header:
     *  - x-amzn-signature : base64-encoded ECDSA signature (ES384)
     *  - x-amzn-kid       : key id (opsional di simulator; prod: cocokkan dengan JWKS)
     *
     * PRODUKSI (sesuai docs):
     *  - Ambil public key dari JWKS https://api.buywithprime.amazon.com/jwks.json
     *  - Gunakan kunci EC P-384 (ES384) sesuai kid lalu verifikasi ECDSA SHA-384 atas rawBody
     *
     * SIMULATOR:
     *  - Sediakan public key PEM via env('AMZN_BWP_PUBLIC_KEY') lalu verifikasi dengan OPENSSL_ALGO_SHA384.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $signatureB64 = $request->header('x-amzn-signature');
        if (! $signatureB64) {
            return false;
        }

        $publicKeyPem = (string) config('tenrusl.amzn_bwp_public_key');
        if ($publicKeyPem === '' || $publicKeyPem === null) {
            // Di prod seharusnya fetch dari JWKS berdasarkan x-amzn-kid; untuk simulator kita butuh PEM di env
            return false;
        }

        $sig = base64_decode((string) $signatureB64, true);
        if ($sig === false) {
            return false;
        }

        $pem = self::normalizePem($publicKeyPem);

        // ES384 -> verify ECDSA-SHA384
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
}
