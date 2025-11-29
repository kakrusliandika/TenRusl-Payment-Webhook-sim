<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;

use function Pest\Laravel\postJson;

it('accepts Payoneer webhook with (bypassed) signature verification and returns 202', function () {
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $payload = [
        'event' => 'payment.approved',
        'id' => 'py_'.now()->timestamp,
        'data' => ['payoutId' => 'PO-'.now()->timestamp],
    ];

    $resp = postJson('/api/v1/webhooks/payoneer', $payload, [
        // placeholder; middleware dibypass di skenario ini
        'X-Payoneer-Signature' => 'dummy',
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

it('rejects Payoneer webhook with invalid/missing signature and returns 401', function () {
    $payload = [
        'event' => 'payment.approved',
        'id' => 'py_invalid',
    ];

    $resp = postJson('/api/v1/webhooks/payoneer', $payload /* no signature header */);

    $resp->assertStatus(401);
});
