<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'tenrusl.doku_secret_key' => 'test_doku_secret',
        // harus match dengan yang dipakai DokuSignature untuk Request-Target
        'tenrusl.doku_request_target' => '/api/v1/webhooks/doku',
    ]);
});

it('accepts DOKU webhook with valid signature and returns 202', function () {
    $payload = [
        'order' => ['invoice_number' => 'INV-'.now()->timestamp],
        'amount' => 100000,
    ];

    $rawBody = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($rawBody === false) {
        throw new \RuntimeException('json_encode failed');
    }

    $clientId = 'DOKU-CLIENT-ID';
    $requestId = (string) Str::uuid();
    $timestamp = now()->toIso8601String();
    $target = (string) config('tenrusl.doku_request_target', '/');

    $components =
        "Client-Id:{$clientId}\n".
        "Request-Id:{$requestId}\n".
        "Request-Timestamp:{$timestamp}\n".
        "Request-Target:{$target}";

    // POST => include Digest (sesuai DokuSignature)
    $digestB64 = base64_encode(hash('sha256', $rawBody, true));
    $components .= "\nDigest:{$digestB64}";

    $secretKey = (string) config('tenrusl.doku_secret_key');
    $signature = 'HMACSHA256='.base64_encode(hash_hmac('sha256', $components, $secretKey, true));

    $resp = postJson('/api/v1/webhooks/doku', $payload, [
        'Client-Id' => $clientId,
        'Request-Id' => $requestId,
        'Request-Timestamp' => $timestamp,
        'Request-Target' => $target,
        'Signature' => $signature,
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'doku');
});

it('rejects DOKU webhook with missing signature and returns 401', function () {
    $payload = [
        'order' => ['invoice_number' => 'INV-INVALID'],
        'amount' => 100000,
    ];

    $resp = postJson('/api/v1/webhooks/doku', $payload, [
        'Client-Id' => 'DOKU-CLIENT-ID',
        'Request-Id' => (string) Str::uuid(),
        'Request-Timestamp' => now()->toIso8601String(),
        'Request-Target' => (string) config('tenrusl.doku_request_target', '/'),
        // Signature sengaja tidak dikirim
    ]);

    $resp->assertStatus(401);
});
