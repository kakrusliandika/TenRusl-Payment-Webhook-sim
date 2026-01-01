<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class PayoneerSignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Payoneer notifications (simulator-friendly).
     *
     * Supported:
     *  - Authorization: Bearer {PAYONEER_SHARED_SECRET}
     *  - X-Payoneer-Signature: hex(HMAC-SHA256(rawBody, PAYONEER_SHARED_SECRET))
     *
     * Optional merchant id check:
     *  - If config has merchant id AND header is present, must match.
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $secret = config('tenrusl.payoneer_shared_secret');
        if (!is_string($secret) || trim($secret) === '') {
            return self::result(false, 'missing_secret');
        }

        // Optional merchant id check
        $cfgMerchantId = config('tenrusl.payoneer_merchant_id', '');
        $cfgMerchantId = is_string($cfgMerchantId) ? trim($cfgMerchantId) : '';

        $hdrMerchantId = self::headerString($request, 'X-Payoneer-Merchant-Id');
        if ($cfgMerchantId !== '' && $hdrMerchantId !== null && $hdrMerchantId !== '') {
            if (!hash_equals($cfgMerchantId, $hdrMerchantId)) {
                return self::result(false, 'merchant_mismatch');
            }
        }

        // A) Authorization: Bearer <secret>
        $auth = self::headerString($request, 'Authorization');
        if ($auth !== null && preg_match('/^Bearer\s+(.+)$/i', $auth, $m) === 1) {
            if (hash_equals($secret, trim((string) $m[1]))) {
                return self::result(true, 'ok_bearer');
            }
        }

        // B) HMAC-SHA256 signature header
        $sig = self::headerString($request, 'X-Payoneer-Signature');
        if ($sig !== null) {
            $sigNorm = strtolower(trim($sig));
            if (str_starts_with($sigNorm, 'sha256=')) {
                $sigNorm = substr($sigNorm, 7);
            }

            $expected = hash_hmac('sha256', $rawBody, $secret);
            if (hash_equals(strtolower($expected), $sigNorm)) {
                return self::result(true, 'ok_hmac');
            }

            return self::result(false, 'invalid_signature');
        }

        return self::result(false, 'missing_signature');
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->headers->get($key);
        if (!is_string($v)) {
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
