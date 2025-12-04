<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class PayoneerSignature
{
    /**
     * Payoneer Checkout Notifications (simulator-friendly).
     *
     * Pola verifikasi yang didukung:
     *  - Authorization: Bearer {PAYONEER_SHARED_SECRET}
     *  - X-Payoneer-Signature: hex(HMAC-SHA256(rawBody, PAYONEER_SHARED_SECRET))
     *
     * Opsional: validasi merchant id jika header "X-Payoneer-Merchant-Id" dikirim.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = config('tenrusl.payoneer_shared_secret');
        if (!is_string($secret) || $secret === '') {
            return false;
        }

        // Optional merchant id check
        $cfgMerchantId = config('tenrusl.payoneer_merchant_id', '');
        $cfgMerchantId = is_string($cfgMerchantId) ? trim($cfgMerchantId) : '';

        $hdrMerchantId = self::headerString($request, 'X-Payoneer-Merchant-Id');

        if ($cfgMerchantId !== '' && $hdrMerchantId !== null && $hdrMerchantId !== '') {
            if (!hash_equals($cfgMerchantId, $hdrMerchantId)) {
                return false;
            }
        }

        // A) Authorization: Bearer <secret>
        $auth = self::headerString($request, 'Authorization');
        if ($auth !== null && preg_match('/^Bearer\s+(.+)$/i', $auth, $m) === 1) {
            if (hash_equals($secret, trim((string) $m[1]))) {
                return true;
            }
        }

        // B) HMAC-SHA256 signature header
        $sig = self::headerString($request, 'X-Payoneer-Signature');
        if ($sig !== null) {
            $expected = hash_hmac('sha256', $rawBody, $secret);
            if (hash_equals(strtolower($expected), strtolower($sig))) {
                return true;
            }
        }

        return false;
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
