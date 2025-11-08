<?php

namespace App\Services\Payments\Adapters;

use App\Services\Payments\Contracts\PaymentAdapter;
use Illuminate\Support\Str;

class MockAdapter implements PaymentAdapter
{
    public function createCharge(array $payload): array
    {
        $ref = 'mock_' . Str::ulid();

        return [
            'provider'  => 'mock',
            'reference' => $ref,
            'status'    => 'pending',
            'raw'       => [
                'echo'     => $payload,
                'hint'     => 'This is a mock provider. Use webhook to mark paid/failed.',
                'ref_note' => 'Use POST /webhooks/mock to deliver events for this reference/payment.',
            ],
        ];
    }

    public function fetchStatus(string $reference): array
    {
        // Simulasi: status tidak berubah di provider mock (kendalikan lewat webhook)
        return [
            'provider'  => 'mock',
            'reference' => $reference,
            'status'    => 'unknown',
            'raw'       => [
                'hint' => 'Mock does not track status; update via webhook.',
            ],
        ];
    }
}
