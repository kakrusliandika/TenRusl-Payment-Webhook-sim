<?php

declare(strict_types=1);

use function Pest\Laravel\postJson;

it('accepts Xendit webhook with correct x-callback-token and returns 202', function () {
    // dok: header x-callback-token harus sama dengan token dashboard.
    // (set via config agar middleware membaca dari sini)
    config(['tenrusl.xendit_callback_token' => 'test-token']);

    $payload = [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'invoice.paid',
        'data' => ['id' => 'inv_'.now()->timestamp],
    ];

    $resp = postJson('/api/v1/webhooks/xendit', $payload, [
        'x-callback-token' => 'test-token',
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'xendit');
});

it('rejects Xendit webhook with invalid token and returns 401', function () {
    config(['tenrusl.xendit_callback_token' => 'test-token']);

    $payload = [
        'id' => 'evt_invalid',
        'type' => 'invoice.paid',
    ];

    $resp = postJson('/api/v1/webhooks/xendit', $payload, [
        'x-callback-token' => 'wrong-token',
    ]);

    $resp->assertStatus(401);
});
