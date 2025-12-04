<?php

declare(strict_types=1);

use App\Models\Payment;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config(['tenrusl.mock_secret' => 'testsecret']);
});

it('treats duplicate event_id as idempotent (only one row saved)', function () {
    $payment = Payment::factory()->pending()->create();
    $paymentId = (string) $payment->getKey(); // aman untuk analyzer

    $payload = [
        'event_id' => 'evt_dup_1',
        'type' => 'payment.paid',
        'data' => ['payment_id' => $paymentId],
        'sent_at' => now()->toIso8601String(),
    ];

    $rawBody = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($rawBody === false) {
        throw new \RuntimeException('json_encode failed');
    }

    $secret = (string) config('tenrusl.mock_secret');
    $sig = hash_hmac('sha256', $rawBody, $secret);

    // First delivery
    postJson('/api/v1/webhooks/mock', $payload, [
        'X-Mock-Signature' => $sig,
    ])->assertStatus(202);

    // Second delivery (duplicate)
    postJson('/api/v1/webhooks/mock', $payload, [
        'X-Mock-Signature' => $sig,
    ])->assertStatus(202);

    $count = WebhookEvent::query()
        ->where('provider', 'mock')
        ->where('event_id', 'evt_dup_1')
        ->count();

    expect($count)->toBe(1);
});
