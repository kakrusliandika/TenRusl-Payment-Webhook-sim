<?php

declare(strict_types=1);

use function Pest\Laravel\postJson;

it('accepts Midtrans webhook with valid signature_key and returns 202', function () {
    // Rumus resmi: SHA512(order_id + status_code + gross_amount + ServerKey)
    config(['tenrusl.midtrans_server_key' => 'server-key']);

    $orderId = 'INV-'.now()->timestamp;
    $statusCode = '200';
    $grossAmount = '100000.00';
    $raw = $orderId.$statusCode.$grossAmount.'server-key';
    $signature = hash('sha512', $raw);

    $payload = [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'signature_key' => $signature,
    ];

    $resp = postJson('/api/v1/webhooks/midtrans', $payload);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'midtrans');
});

it('rejects Midtrans webhook with invalid signature_key and returns 401', function () {
    config(['tenrusl.midtrans_server_key' => 'server-key']);

    $payload = [
        'order_id' => 'INV-invalid',
        'status_code' => '200',
        'gross_amount' => '100000.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'signature_key' => 'deadbeef',
    ];

    $resp = postJson('/api/v1/webhooks/midtrans', $payload);

    $resp->assertStatus(401);
});
