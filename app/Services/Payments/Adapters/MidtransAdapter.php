<?php

declare(strict_types=1);

namespace App\Services\Payments\Adapters;

use App\Services\Payments\Contracts\PaymentAdapter;
use Illuminate\Support\Str;

final class MidtransAdapter implements PaymentAdapter
{
    public function provider(): string
    {
        return 'midtrans';
    }

    /**
     * Simulator "create payment" untuk Midtrans.
     * Tidak memanggil API Midtrans sungguhan—hanya membuat reference & snapshot.
     *
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */
    public function create(array $input): array
    {
        $ref = 'sim_midtrans_'.Str::ulid()->toBase32();

        // amount wajib ada sesuai array-shape PHPDoc, jadi tidak perlu ??.
        $amount = (string) $input['amount'];

        return [
            'provider' => $this->provider(),
            'provider_ref' => $ref,
            'status' => 'pending',
            'snapshot' => [
                'amount' => $amount,
                'currency' => strtoupper((string) ($input['currency'] ?? 'IDR')),
                'description' => (string) ($input['description'] ?? ''),
                'metadata' => (array) ($input['metadata'] ?? []),
                // URL dummy untuk alur demo (redirect/finish)
                'checkout_url' => "/simulate/redirect/{$this->provider()}/{$ref}",
            ],
        ];
    }

    /**
     * Status sederhana (simulasi).
     * Pada implementasi nyata, status ditentukan oleh notifikasi webhook Midtrans.
     *
     * @return array{provider:string, provider_ref:string, status:string}
     */
    public function status(string $providerRef): array
    {
        return [
            'provider' => $this->provider(),
            'provider_ref' => $providerRef,
            'status' => 'pending',
        ];
    }
}
