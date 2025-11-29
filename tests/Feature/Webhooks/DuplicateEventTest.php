<?php

use App\Models\Payment;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['tenrusl.mock_secret' => 'testsecret']);
});

it('treats duplicate event_id as idempotent (only one row saved)', function () {
    $payment = Payment::factory()->pending()->create();

    $payload = [
        'event_id' => 'evt_dup_1',
        'type' => 'payment.paid',
        'data' => ['payment_id' => (string) $payment->id],
        'sent_at' => now()->toISOString(),
    ];

    $json = json_encode($payload);
    $sig = hash_hmac('sha256', $json, config('tenrusl.mock_secret'));

    // First delivery
    $r1 = $this->withHeaders(['X-Mock-Signature' => $sig])
        ->postJson('/api/v1/webhooks/mock', $payload);
    $r1->assertOk();

    // Second delivery (duplicate)
    $r2 = $this->withHeaders(['X-Mock-Signature' => $sig])
        ->postJson('/api/v1/webhooks/mock', $payload);
    $r2->assertOk();

    $count = WebhookEvent::query()
        ->where('provider', 'mock')
        ->where('event_id', 'evt_dup_1')
        ->count();

    expect($count)->toBe(1);
});
