<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('create → get status roundtrip works and fields are consistent', function () {
    $orderId = 'ORD-'.now()->timestamp;

    $create = postJson('/api/v1/payments', [
        'provider' => 'mock',
        'amount' => 123456,
        'currency' => 'IDR',
        'description' => 'Roundtrip',
        'metadata' => ['order_id' => $orderId],
    ], [
        'Idempotency-Key' => 'roundtrip-'.now()->timestamp,
    ]);

    expect($create->status())->toBeIn([201, 200]);

    $provider = (string) $create->json('data.provider');
    $providerRef = (string) ($create->json('data.provider_ref') ?? $create->json('data.reference'));

    expect($provider)->toBe('mock');
    expect($providerRef)->toBeString()->not->toBeEmpty();

    $status = getJson("/api/v1/payments/{$provider}/{$providerRef}/status");

    $status->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['provider', 'provider_ref', 'status'],
        ]);

    expect((string) $status->json('data.provider'))->toBe('mock');
});
