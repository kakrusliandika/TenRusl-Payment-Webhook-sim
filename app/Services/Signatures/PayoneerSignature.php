<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class PayoneerSignature
{
    /**
     * Payoneer Checkout Notifications (simulator-friendly).
     *
     * Rekomendasi resmi: gunakan "shared secret" untuk memverifikasi notifikasi.
     * Di sini kita dukung dua pola umum:
     *  - Authorization: Bearer {PAYONEER_SHARED_SECRET}
     *  - X-Payoneer-Signature: hex(HMAC-SHA256(rawBody, PAYONEER_SHARED_SECRET))
     *
     * Opsional: validasi merchant id jika header "X-Payoneer-Merchant-Id" dikirim.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = (string) config('tenrusl.payoneer_shared_secret');
        if ($secret === '' || $secret === null) {
            return false;
        }

        // Optional merchant id check
        $cfgMerchantId = (string) config('tenrusl.payoneer_merchant_id', '');
        $hdrMerchantId = (string) $request->header('X-Payoneer-Merchant-Id', '');
        if ($cfgMerchantId !== '' && $hdrMerchantId !== '' && !hash_equals($cfgMerchantId, $hdrMerchantId)) {
            return false;
        }

        // A) Authorization: Bearer <secret>
        $auth = $request->header('Authorization');
        if ($auth && preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
            if (hash_equals($secret, trim($m[1]))) {
                return true;
            }
        }

        // B) HMAC-SHA256 signature header
        $sig = $request->header('X-Payoneer-Signature');
        if (is_string($sig) && $sig !== '') {
            $expected = hash_hmac('sha256', $rawBody, $secret);
            if (hash_equals(strtolower($expected), strtolower($sig))) {
                return true;
            }
        }

        return false;
    }
}
