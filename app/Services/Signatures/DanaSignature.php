<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class DanaSignature
{
    /**
     * Verify DANA signature (Simulator).
     *
     * DANA menggunakan Asymmetric Signature (RSA) & header seperti X-SIGNATURE, X-TIMESTAMP,
     * X-PARTNER-ID pada SNAP API. Skema asli membangun canonical string khusus.
     * Untuk SIMULATOR ini, kita validasi X-SIGNATURE sebagai base64(RSA-SHA256(rawBody)).
     *
     * Jika nanti diperlukan mengikuti skema kanonikal DANA, ganti $message sesuai regulasi.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $publicKey = (string) config('tenrusl.dana_public_key');
        if ($publicKey === '' || $publicKey === null) {
            return false;
        }

        $signatureB64 = $request->header('X-SIGNATURE');
        if (! $signatureB64) {
            return false;
        }

        $signature = base64_decode((string) $signatureB64, true);
        if ($signature === false) {
            return false;
        }

        // SIMULATOR: verifikasi atas raw body; real-world bisa memakai canonical string (header + body)
        $message = $rawBody;

        // Pastikan format PEM benar
        $pem = self::normalizePem($publicKey);

        $ok = openssl_verify($message, $signature, $pem, OPENSSL_ALGO_SHA256);

        return $ok === 1;
    }

    /**
     * Ensure PEM has proper headers/footers.
     */
    private static function normalizePem(string $key): string
    {
        $trim = trim($key);
        if (str_contains($trim, 'BEGIN PUBLIC KEY')) {
            return $trim;
        }

        // If key provided without header/footer, wrap it
        $wrapped = chunk_split(str_replace(["\r", "\n", ' '], '', $trim), 64, "\n");

        return "-----BEGIN PUBLIC KEY-----\n{$wrapped}-----END PUBLIC KEY-----\n";
    }
}
