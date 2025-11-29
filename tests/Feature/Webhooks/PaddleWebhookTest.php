<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;

use function Pest\Laravel\post;

it('accepts Paddle webhook (form-encoded) with (bypassed) signature verification and returns 202', function () {
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    // Skema Paddle bervariasi (p_signature lama vs signing secret baru).
    // Untuk test alur, kirim form-urlencoded minimal.
    $form = [
        'alert_id' => 'evt_'.now()->timestamp,
        'alert_name' => 'payment_succeeded',
        'p_signature' => 'dummy', // tidak dipakai karena middleware dibypass
    ];

    $resp = post('/api/v1/webhooks/paddle', $form, [
        'Content-Type' => 'application/x-www-form-urlencoded',
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'paddle');
});

it('rejects Paddle webhook with missing/invalid signature and returns 401', function () {
    // Middleware aktif; kirim tanpa p_signature / header yang diperlukan → 401
    $form = [
        'alert_id' => 'evt_invalid',
        'alert_name' => 'payment_succeeded',
    ];

    $resp = post('/api/v1/webhooks/paddle', $form, [
        'Content-Type' => 'application/x-www-form-urlencoded',
    ]);

    $resp->assertStatus(401);
});
