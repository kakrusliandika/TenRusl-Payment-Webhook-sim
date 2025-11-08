<?php

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['tenrusl.mock_secret' => 'testsecret']);
});

it('processes mock webhook with valid signature and marks payment paid', function () {
    $payment = Payment::factory()->pending()->create([
        'amount' => 25000,
        'currency' => 'IDR',
    ]);

    $payload = [
        'event_id' => 'evt_1001',
        'type' => 'payment.paid',
        'data' => [
            'payment_id' => (string) $payment->id,
            'amount' => 25000,
            'currency' => 'IDR',
        ],
        'sent_at' => now()->toISOString(),
    ];

    // Gunakan encoder default (sinkron dengan postJson())
    $json = json_encode($payload);
    $sig  = hash_hmac('sha256', $json, config('tenrusl.mock_secret'));

    $res = $this->withHeaders(['X-Mock-Signature' => $sig])
        ->postJson('/api/v1/webhooks/mock', $payload);

    $res->assertOk()
        ->assertJsonFragment(['status' => 'processed']);

    $payment->refresh();
    expect($payment->status)->toBe('paid');
});
