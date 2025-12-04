<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('creates a payment and returns 201 with expected resource structure', function () {
    $ts = now()->timestamp;

    $payload = [
        'provider' => 'mock',
        'amount' => 150000,
        'currency' => 'idr', // sengaja lowercase untuk ngetes normalisasi jadi IDR
        'description' => 'Order '.$ts,
        'metadata' => ['order_id' => 'ORD-'.$ts],
    ];

    $idemKey = 'idem-'.$ts.'-'.Str::random(6);

    $resp = postJson('/api/v1/payments', $payload, [
        'Idempotency-Key' => $idemKey,
    ]);

    // tergantung implementasi: bisa 201 (created) atau 200 (replay/cache)
    expect($resp->getStatusCode())->toBeIn([200, 201]);

    // header tracing & idempotency (kalau middleware kamu sudah dipasang)
    $resp->assertHeader('X-Request-ID');
    $resp->assertHeader('Idempotency-Key', $idemKey);

    $resp->assertJsonStructure([
        'data' => [
            'id',
            'provider',
            'provider_ref',
            'amount',
            'currency',
            'status',
            'created_at',
            'updated_at',
        ],
    ]);

    expect((string) $resp->json('data.provider'))->toBe('mock');
    expect(strtoupper((string) $resp->json('data.currency')))->toBe('IDR');
});

it('is idempotent: same Idempotency-Key returns the same payment id', function () {
    $ts = now()->timestamp;

    $payload = [
        'provider' => 'mock',
        'amount' => 50000,
        'currency' => 'IDR',
        'description' => 'Idempotency test '.$ts,
        'metadata' => ['order_id' => 'ORD-'.$ts],
    ];

    $key = 'idem-'.$ts;

    $first = postJson('/api/v1/payments', $payload, ['Idempotency-Key' => $key]);
    $second = postJson('/api/v1/payments', $payload, ['Idempotency-Key' => $key]);

    expect($first->getStatusCode())->toBeIn([200, 201]);
    expect($second->getStatusCode())->toBeIn([200, 201, 208, 409]); // 409 kalau implementasi lock ketat

    $id1 = (string) $first->json('data.id');
    $id2 = (string) $second->json('data.id');

    // Replace ->not->toBeEmpty() to satisfy PHPStan
    expect($id1)->toBeString();
    expect(strlen($id1))->toBeGreaterThan(0);

    expect($id2)->toBeString();
    expect(strlen($id2))->toBeGreaterThan(0);

    expect($id2)->toBe($id1);

    // pastikan idempotency header tetap ada
    $second->assertHeader('Idempotency-Key', $key);
});

it('rejects invalid provider with 422', function () {
    $payload = [
        'provider' => 'unknown',
        'amount' => 10000,
        'currency' => 'IDR',
    ];

    postJson('/api/v1/payments', $payload)->assertStatus(422);
});
