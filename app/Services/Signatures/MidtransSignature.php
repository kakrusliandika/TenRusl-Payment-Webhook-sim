<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class MidtransSignature
{
    /**
     * Verifikasi signature_key Midtrans.
     *
     * Rumus:
     *   signature_key = SHA512(order_id + status_code + gross_amount + serverKey)
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $serverKey = config('tenrusl.midtrans_server_key');
        if (!is_string($serverKey) || $serverKey === '') {
            return false;
        }

        $data = json_decode($rawBody, true);
        if (!is_array($data)) {
            return false;
        }

        $orderId = isset($data['order_id']) ? (string) $data['order_id'] : '';
        $statusCode = isset($data['status_code']) ? (string) $data['status_code'] : '';
        $grossAmount = isset($data['gross_amount']) ? (string) $data['gross_amount'] : '';
        $postedSig = isset($data['signature_key']) ? (string) $data['signature_key'] : '';

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $postedSig === '') {
            return false;
        }

        $calc = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals(strtolower($calc), strtolower($postedSig));
    }
}
