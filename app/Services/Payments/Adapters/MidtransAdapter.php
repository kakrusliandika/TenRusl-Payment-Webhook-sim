<?php

namespace App\Services\Payments\Adapters;

use App\Services\Payments\Contracts\PaymentAdapter;

/**
 * Stub adapter. Tidak memanggil API Midtrans sungguhan.
 */
class MidtransAdapter implements PaymentAdapter
{
    public function createCharge(array $payload): array
    {
        return [
            'provider'  => 'midtrans',
            'reference' => 'mdt_' . substr(sha1(json_encode($payload)), 0, 12),
            'status'    => 'pending',
            'raw'       => [
                'note' => 'MidtransAdapter is a stub in this demo.',
            ],
        ];
    }

    public function fetchStatus(string $reference): array
    {
        return [
            'provider'  => 'midtrans',
            'reference' => $reference,
            'status'    => 'unknown',
            'raw'       => [
                'note' => 'MidtransAdapter fetchStatus is a stub.',
            ],
        ];
    }
}
