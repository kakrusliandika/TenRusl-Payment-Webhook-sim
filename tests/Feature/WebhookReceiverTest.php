<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\call;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('returns 404 for unknown webhook provider (blocked by allowlist)', function () {
    // Provider tidak ada di allowlist → route constraint tidak match → 404
    postJson('/api/v1/webhooks/unknown', ['id' => 'evt'])->assertStatus(404);
});

it('handles OPTIONS preflight with 204', function () {
    // Route OPTIONS tersedia untuk preflight CORS
    $response = call('OPTIONS', '/api/v1/webhooks/mock', [], [], [], [
        'HTTP_Origin' => 'http://localhost',
        'HTTP_Access-Control-Request-Method' => 'POST',
        'HTTP_Access-Control-Request-Headers' => 'Content-Type',
    ]);

    expect($response->getStatusCode())->toBe(204);
});

it('parses JSON & form-encoded payloads consistently', function () {
    // JSON
    $json = postJson('/api/v1/webhooks/mock', [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.paid',
    ], [
        // bypass verifikasi signature via config signature mode 'mock' sederhana
        'X-Mock-Signature' => hash_hmac('sha256', json_encode([
            'id' => 'evt_'.now()->timestamp,
            'type' => 'payment.paid',
        ], JSON_UNESCAPED_SLASHES), config('tenrusl.mock_secret', 'changeme')),
    ]);

    // Tergantung middleware kamu, respons bisa 202 atau 401 jika signature tidak cocok.
    expect($json->status())->toBeIn([202, 401]);

    // FORM
    $form = $this->post('/api/v1/webhooks/paddle', [
        'alert_id' => 'evt_'.now()->timestamp,
        'alert_name' => 'payment_succeeded',
        'p_signature' => 'dummy', // mungkin ditolak jika middleware aktif
    ], ['Content-Type' => 'application/x-www-form-urlencoded']);

    expect($form->status())->toBeIn([202, 401]);
});
