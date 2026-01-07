<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class DanaSignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Verify DANA signature (SIMULATOR).
     *
     * NOTE:
     * - DANA docs mention RSA-SHA256 signature verification with a composed "string to sign".
     * - Simulator implementation verifies RSA-SHA256 directly over raw body.
     *   If you implement canonical string later, replace $message.
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $publicKey = config('tenrusl.dana_public_key');
        if (! is_string($publicKey) || trim($publicKey) === '') {
            return self::result(false, 'missing_public_key');
        }

        $signatureB64 = self::headerString($request, 'X-SIGNATURE');
        if ($signatureB64 === null) {
            return self::result(false, 'missing_signature_header');
        }

        $signature = base64_decode($signatureB64, true);
        if ($signature === false) {
            return self::result(false, 'invalid_signature_base64');
        }

        $pem = self::normalizePem($publicKey);

        // Simulator: sign/verify directly raw body
        $message = $rawBody;

        $ok = @openssl_verify($message, $signature, $pem, OPENSSL_ALGO_SHA256);

        if ($ok === 1) {
            return self::result(true, 'ok');
        }

        if ($ok === 0) {
            return self::result(false, 'invalid_signature');
        }

        return self::result(false, 'openssl_verify_error');
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
