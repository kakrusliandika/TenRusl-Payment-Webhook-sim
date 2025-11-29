<?php

declare(strict_types=1);

use function Pest\Laravel\postJson;

it('accepts Mock webhook with valid HMAC header and returns 202', function () {
    // Simulator mock: HMAC-SHA256(raw_body, MOCK_SECRET) → header X-Mock-Signature
    config(['tenrusl.mock_secret' => 'secret-demo']);

    $payload = [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.paid',
        'reference' => 'sim_mock_'.now()->timestamp,
        'amount' => 100000,
        'currency' => 'IDR',
    ];

    // hitung signature berdasarkan JSON yang akan dikirim
    $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
    $sig = hash_hmac('sha256', $json, 'secret-demo');

    $resp = postJson('/api/v1/webhooks/mock', $payload, [
        'X-Mock-Signature' => $sig,
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'mock');
});

it('rejects Mock webhook with wrong signature and returns 401', function () {
    config(['tenrusl.mock_secret' => 'secret-demo']);

    $payload = [
        'id' => 'evt_invalid',
        'type' => 'payment.paid',
    ];

    $resp = postJson('/api/v1/webhooks/mock', $payload, [
        'X-Mock-Signature' => 'invalid',
    ]);

    $resp->assertStatus(401);
});
