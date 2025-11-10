<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class MidtransSignature
{
    /**
     * Verifikasi signature_key Midtrans.
     *
     * Rumus (Transaction notification):
     *   signature_key = SHA512(order_id + status_code + gross_amount + server_key)
     *
     * Catatan:
     * - Ambil nilai persis dari payload (string), jangan diubah formatnya.
     * - gross_amount pada notifikasi Midtrans biasanya berupa string numerik, contoh "100000.00".
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $serverKey = (string) config('tenrusl.midtrans_server_key');
        if ($serverKey === '' || $serverKey === null) {
            return false;
        }

        $data = json_decode($rawBody, true);
        if (!is_array($data)) {
            return false;
        }

        $orderId     = (string) ($data['order_id']     ?? '');
        $statusCode  = (string) ($data['status_code']  ?? '');
        $grossAmount = (string) ($data['gross_amount'] ?? '');
        $postedSig   = (string) ($data['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $postedSig === '') {
            return false;
        }

        $calc = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        // Compare case-insensitively (hex)
        return hash_equals(strtolower($calc), strtolower($postedSig));
    }
}
