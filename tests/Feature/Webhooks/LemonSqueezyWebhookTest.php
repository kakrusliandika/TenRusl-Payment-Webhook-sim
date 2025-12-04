<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

use function Pest\Laravel\postJson;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    config(['tenrusl.ls_webhook_secret' => 'test_ls_secret']);
});

it('accepts Lemon Squeezy webhook with valid signature and returns 202', function () {
    $payload = [
        'meta' => ['event_name' => 'order_paid'],
        'data' => ['id' => 'ls_'.now()->timestamp, 'attributes' => ['total' => 1000]],
    ];

    $rawBody = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($rawBody === false) {
        throw new RuntimeException('json_encode failed');
    }

    $secret = (string) config('tenrusl.ls_webhook_secret');
    $signature = hash_hmac('sha256', $rawBody, $secret); // hex

    $resp = postJson('/api/v1/webhooks/lemonsqueezy', $payload, [
        'X-Signature' => $signature,
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'lemonsqueezy');
});

it('rejects Lemon Squeezy webhook with invalid signature and returns 401', function () {
    $payload = [
        'meta' => ['event_name' => 'order_paid'],
        'data' => ['id' => 'ls_invalid'],
    ];

    $resp = postJson('/api/v1/webhooks/lemonsqueezy', $payload, [
        'X-Signature' => 'invalid',
    ]);

    $resp->assertStatus(401);
});
