<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;
use function Pest\Laravel\postJson;

it('accepts Airwallex webhook with (bypassed) signature verification and returns 202', function () {
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $payload = [
        'id'   => 'aw_' . now()->timestamp,
        'type' => 'payment_succeeded',
        'data' => ['id' => 'pay_' . now()->timestamp],
    ];

    // gunakan milidetik untuk x-timestamp agar mirip contoh resminya
    $ts = (string) now()->getPreciseTimestamp(3);

    $resp = postJson('/api/v1/webhooks/airwallex', $payload, [
        'x-timestamp' => $ts,
        'x-signature' => 'dummy', // tidak dipakai karena middleware dibypass
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event'  => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'airwallex');
});

it('rejects Airwallex webhook with invalid signature and returns 401', function () {
    $payload = [
        'id'   => 'aw_invalid',
        'type' => 'payment_succeeded',
    ];

    $resp = postJson('/api/v1/webhooks/airwallex', $payload, [
        'x-timestamp' => (string) now()->getPreciseTimestamp(3),
        'x-signature' => 'invalid',
    ]);

    $resp->assertStatus(401);
});
