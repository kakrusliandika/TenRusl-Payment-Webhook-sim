<?php

declare(strict_types=1);

use Illuminate\Support\Str;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('create -> get status roundtrip works and fields are consistent', function () {
    $ts = now()->timestamp;
    $idemKey = 'roundtrip-'.$ts.'-'.Str::random(6);

    $create = postJson('/api/v1/payments', [
        'provider' => 'mock',
        'amount' => 123456,
        'currency' => 'IDR',
        'description' => 'Roundtrip',
        'metadata' => ['order_id' => 'ORD-'.$ts],
    ], [
        'Idempotency-Key' => $idemKey,
    ]);

    expect($create->getStatusCode())->toBeIn([200, 201]);
    $create->assertHeader('X-Request-ID');
    $create->assertHeader('Idempotency-Key', $idemKey);

    $provider = (string) $create->json('data.provider');
    $providerRef = (string) $create->json('data.provider_ref');

    expect($provider)->toBe('mock');

    // PHPStan-friendly
    expect($providerRef)->toBeString();
    expect(strlen($providerRef))->toBeGreaterThan(0);

    $status = getJson("/api/v1/payments/{$provider}/{$providerRef}/status");

    $status->assertStatus(200)
        ->assertHeader('X-Request-ID')
        ->assertJsonStructure([
            'data' => [
                'provider',
                'provider_ref',
                'status',
            ],
        ]);

    expect((string) $status->json('data.provider'))->toBe('mock');
    expect((string) $status->json('data.provider_ref'))->toBe($providerRef);
});
