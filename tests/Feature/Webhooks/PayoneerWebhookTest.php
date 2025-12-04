<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'tenrusl.payoneer_shared_secret' => 'testsecret',
        // optional merchant id checks can remain empty to avoid blocking
        'tenrusl.payoneer_merchant_id' => '',
    ]);
});

it('accepts Payoneer webhook with valid signature and returns 202', function () {
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

it('rejects Payoneer webhook with missing/invalid signature and returns 401', function () {
    $payload = [
        'event' => 'payment.approved',
        'id' => 'py_invalid',
    ];

    $resp = postJson('/api/v1/webhooks/payoneer', $payload /* no signature header */);

    $resp->assertStatus(401);
});
