<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

/**
 * Verifikasi Midtrans (stub):
 *  - Umumnya: sha512(order_id + status_code + gross_amount + server_key)
 *  - Di demo ini, cukup cek header tersedia agar arsitektur lengkap;
 *    logika lengkap bisa ditambahkan nanti sesuai kebutuhan.
 */
class MidtransSignature
{
    /**
     * @return array{ok:bool, hash:?string, msg:?string}
     */
    public function verify(Request $request): array
    {
        $hdr = $request->header('Signature-Key');
        if (! $hdr) {
            return ['ok' => false, 'hash' => null, 'msg' => 'Missing Signature-Key'];
        }

        return ['ok' => true, 'hash' => 'midtrans:' . substr(hash('sha256', $hdr), 0, 32), 'msg' => null];
    }
}
