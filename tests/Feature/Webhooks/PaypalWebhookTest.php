<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;

use function Pest\Laravel\postJson;

it('accepts PayPal webhook with (bypassed) signature verification and returns 202', function () {
    /** @var \Tests\TestCase $this */
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $payload = [
        'id' => 'WH-'.now()->timestamp,
        'event_type' => 'PAYMENT.SALE.COMPLETED',
        'resource' => ['id' => 'sale_'.now()->timestamp],
    ];

    $resp = postJson('/api/v1/webhooks/paypal', $payload);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'paypal');
});

it('rejects PayPal webhook with missing signature headers and returns 401', function () {
    $resp = postJson('/api/v1/webhooks/paypal', [
        'id' => 'WH-invalid',
        'event_type' => 'PAYMENT.SALE.COMPLETED',
    ]);

    $resp->assertStatus(401);
});
