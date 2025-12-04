<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'tenrusl.oy_callback_secret' => 'testsecret',
        // ensure whitelist doesn't block tests
        'tenrusl.oy_ip_whitelist' => '',
    ]);
});

it('accepts OY webhook with valid signature and returns 202', function () {
    $payload = [
        'event' => 'payment_success',
        'id' => 'oy_'.now()->timestamp,
        'data' => ['reference' => 'OY-'.now()->timestamp],
    ];

    $rawBody = json_encode($payload);
    if ($rawBody === false) {
        $rawBody = '';
    }

    $secret = (string) config('tenrusl.oy_callback_secret');
    $sig = hash_hmac('sha256', $rawBody, $secret);

    $resp = postJson('/api/v1/webhooks/oy', $payload, [
        'X-OY-Signature' => $sig,
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'oy');
});

it('rejects OY webhook with missing/invalid signature and returns 401', function () {
    $payload = [
        'event' => 'payment_success',
        'id' => 'oy_invalid',
    ];

    $resp = postJson('/api/v1/webhooks/oy', $payload /* no signature headers */);

    $resp->assertStatus(401);
});
