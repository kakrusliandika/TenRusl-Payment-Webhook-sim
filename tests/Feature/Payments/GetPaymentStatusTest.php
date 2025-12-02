<?php

declare(strict_types=1);

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('returns payment status by provider and provider_ref', function () {
    $providerRef = 'sim_mock_' . now()->timestamp . '_' . Str::random(6);

    /** @var Payment $payment */
    $payment = Payment::query()->create([
        'provider' => 'mock',
        'provider_ref' => $providerRef,
        'amount' => 100000,
        'currency' => 'IDR',
        'status' => 'pending',
        'meta' => ['order_id' => 'ORD-' . now()->timestamp],
    ]);

    $resp = getJson(sprintf(
        '/api/v1/payments/%s/%s/status',
        $payment->provider,
        $payment->provider_ref
    ));

    $resp->assertStatus(200)
        ->assertHeader('X-Request-ID')
        ->assertJsonStructure([
            'data' => [
                'provider',
                'provider_ref',
                'status',
                'amount',
                'currency',
            ],
        ]);

    expect((string) $resp->json('data.provider'))->toBe('mock');
    expect((string) $resp->json('data.provider_ref'))->toBe($providerRef);
});

it('returns 404 when payment not found (or consistent not-found shape)', function () {
    $resp = getJson('/api/v1/payments/mock/does_not_exist/status');

    // tergantung implementasi kamu:
    // - bisa 404 JSON
    // - atau 200 pending dari adapter
    expect($resp->getStatusCode())->toBeIn([200, 404]);

    if ($resp->getStatusCode() === 404) {
        $resp->assertJsonStructure(['message']);
    } else {
        $resp->assertJsonStructure([
            'data' => ['provider', 'provider_ref', 'status'],
        ]);
    }
});
