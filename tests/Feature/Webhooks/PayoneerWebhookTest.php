// tests/Feature/Webhooks/PayoneerWebhookTest.php
<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'tenrusl.payoneer_shared_secret' => 'testsecret',
        // biarkan kosong kalau tidak mau enforce merchant-id check
        'tenrusl.payoneer_merchant_id' => '',
    ]);
});

it('accepts Payoneer webhook with Authorization Bearer secret and returns 202', function () {
    $payload = [
        'event' => 'payment.approved',
        'id' => 'py_'.now()->timestamp,
        'data' => ['payoutId' => 'PO-'.now()->timestamp],
    ];

    $resp = postJson('/api/v1/webhooks/payoneer', $payload, [
        'Authorization' => 'Bearer '.(string) config('tenrusl.payoneer_shared_secret'),
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'payoneer')
        ->assertJsonPath('data.event.event_id', $payload['id'])
        ->assertJsonPath('data.event.type', $payload['event']);
});

it('accepts Payoneer webhook with X-Payoneer-Signature (HMAC) and returns 202', function () {
    $payload = [
        'event' => 'payment.approved',
        'id' => 'py_'.now()->timestamp,
        'data' => ['payoutId' => 'PO-'.now()->timestamp],
    ];

    $rawBody = json_encode($payload);
    if ($rawBody === false) {
        $rawBody = '';
    }

    $secret = (string) config('tenrusl.payoneer_shared_secret');
    $sig = hash_hmac('sha256', $rawBody, $secret);

    $resp = postJson('/api/v1/webhooks/payoneer', $payload, [
        'X-Payoneer-Signature' => $sig,
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'payoneer');
});

it('rejects Payoneer webhook when merchant id is required but missing/mismatched and returns 401', function () {
    config([
        'tenrusl.payoneer_merchant_id' => 'merchant_123',
        'tenrusl.payoneer_shared_secret' => 'testsecret',
    ]);

    $payload = [
        'event' => 'payment.approved',
        'id' => 'py_'.now()->timestamp,
    ];

    $rawBody = json_encode($payload);
    if ($rawBody === false) {
        $rawBody = '';
    }

    $sig = hash_hmac('sha256', $rawBody, (string) config('tenrusl.payoneer_shared_secret'));

    // Signature valid, tapi merchant id tidak sesuai (atau tidak dikirim) => harus 401
    $resp = postJson('/api/v1/webhooks/payoneer', $payload, [
        'X-Payoneer-Signature' => $sig,
        'X-Payoneer-Merchant-Id' => 'wrong_merchant',
    ]);

    $resp->assertStatus(401);
});
