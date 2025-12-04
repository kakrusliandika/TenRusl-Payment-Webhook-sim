<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;

use function Pest\Laravel\postJson;

it('accepts Stripe webhook with (bypassed) signature verification and returns 202', function () {
    /** @var \Tests\TestCase $this */
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $payload = [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'charge.succeeded',
        'data' => ['object' => ['id' => 'pi_'.now()->timestamp]],
    ];

    $resp = postJson('/api/v1/webhooks/stripe', $payload, [
        'Stripe-Signature' => 't=0,v1=dummy', // tidak dipakai karena middleware dibypass
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'stripe');
});

it('rejects Stripe webhook with invalid signature and returns 401', function () {
    $resp = postJson('/api/v1/webhooks/stripe', [
        'id' => 'evt_invalid',
        'type' => 'charge.succeeded',
    ], [
        'Stripe-Signature' => 't=123,v1=invalidsig',
    ]);

    $resp->assertStatus(401);
});
