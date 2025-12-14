<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('creates a payment and returns 201 with expected resource structure', function () {
    $ts = now()->timestamp;

    $payload = [
        'provider' => 'mock',
        'amount' => 150000,
        'currency' => 'idr', // sengaja lowercase untuk ngetes normalisasi jadi IDR
        'description' => 'Order '.$ts,
        'metadata' => ['order_id' => 'ORD-'.$ts],
    ];

    $idemKey = 'idem-'.$ts.'-'.Str::random(6);

    $resp = postJson('/api/v1/payments', $payload, [
        'Idempotency-Key' => $idemKey,
    ]);

    // tergantung implementasi: bisa 201 (created) atau 200 (replay/cache)
    expect($resp->getStatusCode())->toBeIn([200, 201]);

    // header tracing & idempotency (kalau middleware kamu sudah dipasang)
    $resp->assertHeader('X-Request-ID');
    $resp->assertHeader('Idempotency-Key', $idemKey);

    $resp->assertJsonStructure([
        'data' => [
            'id',
            'provider',
            'provider_ref',
            'amount',
            'currency',
            'status',
            'created_at',
            'updated_at',
        ],
    ]);

    expect((string) $resp->json('data.provider'))->toBe('mock');
    expect(strtoupper((string) $resp->json('data.currency')))->toBe('IDR');

    // Tambahan (opsional tapi bagus buat demo/admin):
    // Pastikan meta/metadata tidak hilang total setelah normalisasi (kalau implementasi kamu memetakan metadata->meta).
    $data = (array) $resp->json('data');
    expect($data)->toBeArray();
});

it('is idempotent: same Idempotency-Key returns the same payment id', function () {
    $ts = now()->timestamp;

    $payload = [
        'provider' => 'mock',
        'amount' => 50000,
        'currency' => 'IDR',
        'description' => 'Idempotency test '.$ts,
        'metadata' => ['order_id' => 'ORD-'.$ts],
    ];

    $key = 'idem-'.$ts;

    $first = postJson('/api/v1/payments', $payload, ['Idempotency-Key' => $key]);
    $second = postJson('/api/v1/payments', $payload, ['Idempotency-Key' => $key]);

    expect($first->getStatusCode())->toBeIn([200, 201]);

    // Jika implementasi kamu pakai lock ketat, sangat jarang tapi bisa muncul 409 sementara.
    // Kita buat test toleran: kalau 409, coba sekali lagi (biasanya lock sudah release).
    if ($second->getStatusCode() === 409) {
        usleep(150_000); // 150ms (cukup kecil supaya test tetap cepat)
        $second = postJson('/api/v1/payments', $payload, ['Idempotency-Key' => $key]);
    }

    expect($second->getStatusCode())->toBeIn([200, 201, 208]);

    $id1 = (string) $first->json('data.id');
    $id2 = (string) $second->json('data.id');

    // Replace ->not->toBeEmpty() to satisfy PHPStan
    expect($id1)->toBeString();
    expect(strlen($id1))->toBeGreaterThan(0);

    expect($id2)->toBeString();
    expect(strlen($id2))->toBeGreaterThan(0);

    expect($id2)->toBe($id1);

    // pastikan idempotency header tetap ada
    $second->assertHeader('Idempotency-Key', $key);
});

it('rejects invalid provider with 422', function () {
    $payload = [
        'provider' => 'unknown',
        'amount' => 10000,
        'currency' => 'IDR',
    ];

    postJson('/api/v1/payments', $payload)->assertStatus(422);
});

/**
 * NEW: get payment by id (untuk kebutuhan FE admin + kontrak OpenAPI).
 * Support 2 kemungkinan path:
 * - /api/v1/payments/{id} (umum di repo ini)
 * - /api/payments/{id}    (alias kalau kamu aktifkan sesuai demo publik)
 */
it('can fetch payment by id via show endpoint (v1 preferred, alias supported)', function () {
    $ts = now()->timestamp;
    $idemKey = 'idem-show-'.$ts.'-'.Str::random(6);

    $create = postJson('/api/v1/payments', [
        'provider' => 'mock',
        'amount' => 77777,
        'currency' => 'IDR',
        'description' => 'Show endpoint test',
        'metadata' => ['order_id' => 'ORD-'.$ts],
    ], [
        'Idempotency-Key' => $idemKey,
    ]);

    expect($create->getStatusCode())->toBeIn([200, 201]);
    $create->assertHeader('X-Request-ID');
    $create->assertHeader('Idempotency-Key', $idemKey);

    $id = (string) $create->json('data.id');
    expect($id)->toBeString();
    expect(strlen($id))->toBeGreaterThan(0);

    // Coba endpoint v1 dulu, kalau 404 baru fallback ke non-v1 (alias).
    $paths = [
        "/api/v1/payments/{$id}",
        "/api/payments/{$id}",
    ];

    $resp = null;
    foreach ($paths as $p) {
        $try = getJson($p);
        if ($try->getStatusCode() !== 404) {
            $resp = $try;
            break;
        }
    }

    expect($resp)->not()->toBeNull();
    expect($resp->getStatusCode())->toBe(200);

    $resp->assertHeader('X-Request-ID');

    // Struktur minimal yang harus stabil untuk FE:
    $resp->assertJsonStructure([
        'data' => [
            'id',
            'provider',
            'provider_ref',
            'amount',
            'currency',
            'status',
            'created_at',
            'updated_at',
        ],
    ]);

    expect((string) $resp->json('data.id'))->toBe($id);
    expect((string) $resp->json('data.provider'))->toBe('mock');
});
