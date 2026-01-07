// tests/Feature/Webhooks/PaddleWebhookTest.php
<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'tenrusl.paddle_signing_secret' => 'test_paddle_secret',
        // Longgarin sedikit biar test tidak flakey kalau runner lagi lambat
        'tenrusl.signature.timestamp_leeway_seconds' => 600,
    ]);
});

it('accepts Paddle webhook with valid Paddle-Signature (HMAC) and returns 202', function () {
    $payload = [
        'event_id' => 'evt_'.now()->timestamp,
        'event_type' => 'subscription.created',
        'data' => [
            'id' => 'sub_'.now()->timestamp,
        ],
    ];

    $rawBody = json_encode($payload);
    if ($rawBody === false) {
        $rawBody = '';
    }

    $ts = time();
    $secret = (string) config('tenrusl.paddle_signing_secret');

    // Paddle Billing signature: HMAC_SHA256( "{ts}:{rawBody}", secret )
    $h1 = hash_hmac('sha256', $ts.':'.$rawBody, $secret);
    $signatureHeader = "ts={$ts}; h1={$h1}";

    $resp = postJson('/api/v1/webhooks/paddle', $payload, [
        'Paddle-Signature' => $signatureHeader,
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'paddle')
        ->assertJsonPath('data.event.event_id', $payload['event_id'])
        ->assertJsonPath('data.event.type', $payload['event_type']);
});

it('rejects Paddle webhook with missing/invalid Paddle-Signature and returns 401', function () {
    $payload = [
        'event_id' => 'evt_invalid',
        'event_type' => 'subscription.created',
    ];

    $resp = postJson('/api/v1/webhooks/paddle', $payload, [
        'Paddle-Signature' => 'ts=0; h1=invalid',
    ]);

    $resp->assertStatus(401);
});
