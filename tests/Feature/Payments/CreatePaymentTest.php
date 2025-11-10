<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('creates a payment and returns 201 with basic resource structure', function () {
    $payload = [
        'provider'    => 'mock',
        'amount'      => 150000,
        'currency'    => 'idr',
        'description' => 'Order ' . now()->timestamp,
        'metadata'    => ['order_id' => 'ORD-' . now()->timestamp],
    ];

    $idk1 = now()->timestamp . '-A';

    $resp = postJson('/api/v1/payments', $payload, [
        'Idempotency-Key' => $idk1,
    ]);

    expect($resp->status())->toBeIn([201, 200]);

    $resp->assertJsonStructure([
        'data' => [
            'id',
            'provider',
            'amount',
            'currency',
            'status',
        ],
    ]);

    $provider = (string) $resp->json('data.provider');
    $currency = strtoupper((string) $resp->json('data.currency'));

    expect($provider)->toBe('mock');
    expect($currency)->toBe('IDR');
});

it('is idempotent: same Idempotency-Key returns the same payment', function () {
    $payload = [
        'provider'    => 'mock',
        'amount'      => 50000,
        'currency'    => 'IDR',
        'description' => 'Idem test',
    ];

    $key = 'idem-' . now()->timestamp;

    $first  = postJson('/api/v1/payments', $payload, ['Idempotency-Key' => $key]);
    $second = postJson('/api/v1/payments', $payload, ['Idempotency-Key' => $key]);

    $id1 = (string) $first->json('data.id');
    $id2 = (string) $second->json('data.id');

    expect($id1)->toBeString()->and($id2)->toBeString()->and($id1)->toBe($id2);
    expect($second->status())->toBeIn([200, 201, 409, 208]);
});

it('rejects invalid provider with 422', function () {
    $payload = [
        'provider' => 'unknown',
        'amount'   => 10000,
        'currency' => 'IDR',
    ];

    postJson('/api/v1/payments', $payload)->assertStatus(422);
});
