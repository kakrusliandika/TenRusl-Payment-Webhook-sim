<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class DanaSignature
{
    /**
     * Verify DANA signature (SIMULATOR).
     *
     * Simulator:
     * - Validasi X-SIGNATURE sebagai base64(RSA-SHA256(rawBody)).
     * - Real-world DANA biasanya pakai canonical string (header + body). Bisa kamu ubah di $message.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $publicKey = config('tenrusl.dana_public_key');
        if (!is_string($publicKey) || $publicKey === '') {
            return false;
        }

        $signatureB64 = self::headerString($request, 'X-SIGNATURE');
        if ($signatureB64 === null) {
            return false;
        }

        $signature = base64_decode($signatureB64, true);
        if ($signature === false) {
            return false;
        }

        // Simulator: sign/verify langsung raw body
        $message = $rawBody;

        $pem = self::normalizePem($publicKey);

        $ok = openssl_verify($message, $signature, $pem, OPENSSL_ALGO_SHA256);

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
