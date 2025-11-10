<?php

declare(strict_types=1);

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('returns payment status by provider and provider_ref', function () {
    /** @var \App\Models\Payment $payment */
    $payment = Payment::factory()->create([
        'provider'     => 'mock',
        'provider_ref' => 'sim_mock_' . now()->timestamp,
        'amount'       => 100000,
        'currency'     => 'IDR',
        'status'       => 'pending',
    ]);

    $resp = getJson(sprintf(
        '/api/v1/payments/%s/%s/status',
        $payment->provider,
        $payment->provider_ref
    ));

    $resp->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'provider',
                'provider_ref',
                'status',
                'amount',
                'currency',
            ],
        ]);

    $data = (array) $resp->json('data');

    expect($data['provider'])->toBe('mock');
    expect($data['provider_ref'])->toBe($payment->provider_ref);
});
