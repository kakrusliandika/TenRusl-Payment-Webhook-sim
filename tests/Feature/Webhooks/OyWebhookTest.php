<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;

use function Pest\Laravel\postJson;

it('accepts OY webhook with (bypassed) signature verification and returns 202', function () {
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $payload = [
        'event' => 'payment_success',
        'id' => 'oy_'.now()->timestamp,
        'data' => ['reference' => 'OY-'.now()->timestamp],
    ];

    $resp = postJson('/api/v1/webhooks/oy', $payload, [
        // placeholder; middleware dibypass di skenario ini
        'X-OY-Signature' => 'dummy',
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

    $resp = postJson('/api/v1/webhooks/oy', $payload /* no signature header */);

    $resp->assertStatus(401);
});
