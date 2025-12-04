<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;

use function Pest\Laravel\postJson;

it('accepts Tripay webhook with (bypassed) signature verification and returns 202', function () {
    /** @var \Tests\TestCase $this */
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $payload = [
        'id' => 'tp_'.now()->timestamp,
        'event' => 'payment_status',
        'reference' => 'TRX-'.now()->timestamp,
        'status' => 'PAID',
    ];

    $resp = postJson('/api/v1/webhooks/tripay', $payload, [
        'X-Callback-Signature' => 'dummy', // tidak dipakai karena dibypass
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'tripay');
});

it('rejects Tripay webhook with invalid signature and returns 401', function () {
    $payload = [
        'id' => 'tp_invalid',
        'event' => 'payment_status',
    ];

    $resp = postJson('/api/v1/webhooks/tripay', $payload, [
        'X-Callback-Signature' => 'invalid',
    ]);

    $resp->assertStatus(401);
});
