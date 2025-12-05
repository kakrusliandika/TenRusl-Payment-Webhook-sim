<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('accepts Amazon BWP webhook with (bypassed) signature verification and returns 202', function () {
    /** @var \Tests\TestCase $this */
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $payload = [
        'detail-type' => 'BuyWithPrime.OrderCompleted',
        'detail' => ['orderId' => 'amzn_'.now()->timestamp],
        'id' => 'evt_'.now()->timestamp,
    ];

    $resp = postJson('/api/v1/webhooks/amazon_bwp', $payload, [
        'x-amzn-signature' => 'dummy',
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'amazon_bwp');
});

it('rejects Amazon BWP webhook with invalid signature and returns 401', function () {
    $payload = [
        'detail-type' => 'BuyWithPrime.OrderCompleted',
        'detail' => ['orderId' => 'amzn_invalid'],
        'id' => 'evt_invalid',
    ];

    $resp = postJson('/api/v1/webhooks/amazon_bwp', $payload, [
        'x-amzn-signature' => 'invalid',
    ]);

    $resp->assertStatus(401);
});
