<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;
use function Pest\Laravel\postJson;

it('accepts DOKU webhook with (bypassed) signature verification and returns 202', function () {
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $payload = [
        'order'  => ['invoice_number' => 'INV-' . now()->timestamp],
        'amount' => 100000,
    ];

    // Header DOKU yang umum—nilai dummy karena middleware dibypass
    $resp = postJson('/api/v1/webhooks/doku', $payload, [
        'Client-Id'         => 'DOKU-CLIENT-ID',
        'Request-Id'        => (string) \Illuminate\Support\Str::uuid(),
        'Request-Timestamp' => now()->toISOString(),
        'Request-Target'    => '/webhooks/doku',
        'Digest'            => 'SHA-256=' . base64_encode(hash('sha256', json_encode($payload), true)),
        'Signature'         => 'HMACSHA256=' . base64_encode(random_bytes(16)),
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event'  => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'doku');
});

it('rejects DOKU webhook with missing/invalid signature and returns 401', function () {
    $payload = [
        'order'  => ['invoice_number' => 'INV-INVALID'],
        'amount' => 100000,
    ];

    // Hilangkan Signature → harus 401
    $resp = postJson('/api/v1/webhooks/doku', $payload, [
        'Client-Id'         => 'DOKU-CLIENT-ID',
        'Request-Id'        => (string) \Illuminate\Support\Str::uuid(),
        'Request-Timestamp' => now()->toISOString(),
        'Request-Target'    => '/webhooks/doku',
        'Digest'            => 'SHA-256=' . base64_encode(hash('sha256', json_encode($payload), true)),
        // 'Signature'        => (tidak ada)
    ]);

    $resp->assertStatus(401);
});
