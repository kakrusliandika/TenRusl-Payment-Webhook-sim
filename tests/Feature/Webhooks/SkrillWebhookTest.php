<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;
use function Pest\Laravel\post;

it('accepts Skrill webhook (form-encoded) with (bypassed) signature verification and returns 202', function () {
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    // Form-url-encoded sesuai pola IPN/status_url Skrill
    $form = [
        'transaction_id' => 'sk_' . now()->timestamp,
        'merchant_id'    => '123456',
        'mb_amount'      => '100.00',
        'mb_currency'    => 'EUR',
        'status'         => '2',
        'md5sig'         => 'dummy', // tidak dipakai (middleware dibypass)
    ];

    $resp = post('/api/v1/webhooks/skrill', $form, [
        'Content-Type' => 'application/x-www-form-urlencoded',
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event'  => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'skrill');
});

it('rejects Skrill webhook with missing/invalid md5sig and returns 401', function () {
    $form = [
        'transaction_id' => 'sk_invalid',
        'merchant_id'    => '123456',
        'mb_amount'      => '100.00',
        'mb_currency'    => 'EUR',
        'status'         => '2',
        // 'md5sig' omitted
    ];

    $resp = post('/api/v1/webhooks/skrill', $form, [
        'Content-Type' => 'application/x-www-form-urlencoded',
    ]);

    $resp->assertStatus(401);
});
