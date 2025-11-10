<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;
use function Pest\Laravel\postJson;

it('accepts Lemon Squeezy webhook with (bypassed) signature verification and returns 202', function () {
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $payload = [
        'meta' => ['event_name' => 'order_paid'],
        'data' => ['id' => 'ls_' . now()->timestamp, 'attributes' => ['total' => 1000]],
    ];

    $resp = postJson('/api/v1/webhooks/lemonsqueezy', $payload, [
        'X-Signature' => 'dummy', // tidak dipakai karena middleware dibypass
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event'  => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'lemonsqueezy');
});

it('rejects Lemon Squeezy webhook with invalid signature and returns 401', function () {
    // Middleware aktif; signature salah
    $payload = [
        'meta' => ['event_name' => 'order_paid'],
        'data' => ['id' => 'ls_invalid'],
    ];

    $resp = postJson('/api/v1/webhooks/lemonsqueezy', $payload, [
        'X-Signature' => 'invalid',
    ]);

    $resp->assertStatus(401);
});
