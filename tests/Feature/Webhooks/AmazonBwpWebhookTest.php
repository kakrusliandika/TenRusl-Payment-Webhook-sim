<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;
use function Pest\Laravel\postJson;

it('accepts Amazon BWP webhook with (bypassed) signature verification and returns 202', function () {
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    // Contoh event sederhana
    $payload = [
        'detail-type' => 'BuyWithPrime.OrderCompleted',
        'detail'      => ['orderId' => 'amzn_' . now()->timestamp],
        'id'          => 'evt_' . now()->timestamp,
    ];

    $resp = postJson('/api/v1/webhooks/amazon_bwp', $payload, [
        'x-amzn-signature' => 'dummy', // tidak dipakai karena middleware dibypass
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event'  => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'amazon_bwp');
});

it('rejects Amazon BWP webhook with invalid signature and returns 401', function () {
    $payload = [
        'detail-type' => 'BuyWithPrime.OrderCompleted',
        'detail'      => ['orderId' => 'amzn_invalid'],
        'id'          => 'evt_invalid',
    ];

    $resp = postJson('/api/v1/webhooks/amazon_bwp', $payload, [
        'x-amzn-signature' => 'invalid',
    ]);

    $resp->assertStatus(401);
});
