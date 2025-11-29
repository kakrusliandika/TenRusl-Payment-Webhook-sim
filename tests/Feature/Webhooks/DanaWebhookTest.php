<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;

use function Pest\Laravel\postJson;

it('accepts DANA webhook with (bypassed) signature verification and returns 202', function () {
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $payload = [
        'id' => 'dana_'.now()->timestamp,
        'event' => 'payment_succeeded',
        'data' => ['id' => 'pg_'.now()->timestamp],
    ];

    $resp = postJson('/api/v1/webhooks/dana', $payload, [
        'X-SIGNATURE' => 'dummy', // tidak dipakai karena middleware dibypass
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'dana');
});

it('rejects DANA webhook with invalid signature and returns 401', function () {
    $payload = [
        'id' => 'dana_invalid',
        'event' => 'payment_succeeded',
    ];

    // Tanpa atau dengan signature tidak valid → 401
    $resp = postJson('/api/v1/webhooks/dana', $payload, [
        'X-SIGNATURE' => 'invalid',
    ]);

    $resp->assertStatus(401);
});
