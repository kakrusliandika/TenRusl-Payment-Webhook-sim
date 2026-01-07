<?php

declare(strict_types=1);

namespace App\Services\Payments\Adapters;

use App\Services\Payments\Contracts\PaymentAdapter;
use Illuminate\Support\Str;

final class DokuAdapter implements PaymentAdapter
{
    public function provider(): string
    {
        return 'doku';
    }

    /**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */
    public function create(array $input): array
    {
        $ref = 'sim_doku_'.Str::ulid()->toBase32();

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
                'checkout_url' => "/simulate/redirect/{$this->provider()}/{$ref}",
            ],
        ];
    }

    /**
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
