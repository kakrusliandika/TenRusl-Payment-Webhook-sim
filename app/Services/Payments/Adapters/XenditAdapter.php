<?php

namespace App\Services\Payments\Adapters;

use App\Services\Payments\Contracts\PaymentAdapter;

/**
 * Stub adapter. Tidak memanggil API Xendit sungguhan.
 * Disediakan agar arsitektur siap integrasi nyata di masa depan.
 */
class XenditAdapter implements PaymentAdapter
{
    public function createCharge(array $payload): array
    {
        return [
            'provider'  => 'xendit',
            'reference' => 'xnd_' . substr(md5(json_encode($payload)), 0, 12),
            'status'    => 'pending',
            'raw'       => [
                'note' => 'XenditAdapter is a stub in this demo.',
            ],
        ];
    }

    public function fetchStatus(string $reference): array
    {
        return [
            'provider'  => 'xendit',
            'reference' => $reference,
            'status'    => 'unknown',
            'raw'       => [
                'note' => 'XenditAdapter fetchStatus is a stub.',
            ],
        ];
    }
}
