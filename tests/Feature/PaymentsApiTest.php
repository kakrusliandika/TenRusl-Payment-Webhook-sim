<?php

declare(strict_types=1);

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('create -> get status roundtrip works and fields are consistent', function () {
    $ts = now()->timestamp;
    $idemKey = 'roundtrip-'.$ts.'-'.Str::random(6);

    $create = postJson('/api/v1/payments', [
        'provider' => 'mock',
        'amount' => 123456,
        'currency' => 'IDR',
        'description' => 'Roundtrip',
        'metadata' => ['order_id' => 'ORD-'.$ts],
    ], [
        'Idempotency-Key' => $idemKey,
    ]);

    expect($create->getStatusCode())->toBeIn([200, 201]);
    $create->assertHeader('X-Request-ID');
    $create->assertHeader('Idempotency-Key', $idemKey);

    $provider = (string) $create->json('data.provider');
    $providerRef = (string) $create->json('data.provider_ref');

    expect($provider)->toBe('mock');

    // PHPStan-friendly
    expect($providerRef)->toBeString();
    expect(strlen($providerRef))->toBeGreaterThan(0);

    $status = getJson("/api/v1/payments/{$provider}/{$providerRef}/status");

    $status->assertStatus(200)
        ->assertHeader('X-Request-ID')
        ->assertJsonStructure([
            'data' => [
                'provider',
                'provider_ref',
                'status',
            ],
        ]);

    expect((string) $status->json('data.provider'))->toBe('mock');
    expect((string) $status->json('data.provider_ref'))->toBe($providerRef);
});

/**
 * NEW: admin list harus butuh auth (tanpa key/token harus ditolak).
 * Endpoint yang dicoba:
 * - /api/v1/admin/payments (utama untuk repo ini)
 * - /api/admin/payments    (alias kalau kamu pakai mode tanpa v1)
 * - /admin/payments        (kalau admin route kamu tidak di bawah /api)
 */
it('admin list requires auth: without key/token it must be rejected', function () {
    // Seed beberapa data supaya nanti test "with auth" punya isi.
    Payment::factory()->count(3)->create([
        'provider' => 'mock',
        'status' => 'pending',
        'currency' => 'IDR',
    ]);

    $paths = [
        '/api/v1/admin/payments',
        '/api/admin/payments',
        '/admin/payments',
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

    // Wajib ditolak (umumnya 401/403)
    expect($resp->getStatusCode())->toBeIn([401, 403]);
});

it('admin list returns data when valid admin key/token is provided', function () {
    Payment::factory()->count(5)->create([
        'provider' => 'mock',
        'status' => 'pending',
        'currency' => 'IDR',
    ]);

    // Pastikan nilai admin key tersedia baik via config maupun env (tahan banting).
    $adminKey = 'test-admin-key';

    config([
        'tenrusl.admin_demo_key' => $adminKey,
        'tenrusl.admin.demo_key' => $adminKey,
    ]);

    putenv("ADMIN_DEMO_KEY={$adminKey}");
    $_ENV['ADMIN_DEMO_KEY'] = $adminKey;
    $_SERVER['ADMIN_DEMO_KEY'] = $adminKey;

    $paths = [
        '/api/v1/admin/payments',
        '/api/admin/payments',
        '/admin/payments',
    ];

    // Cari path yang ada (kalau butuh auth, biasanya tanpa header -> 401/403).
    $selectedPath = null;
    foreach ($paths as $p) {
        $probe = getJson($p);
        if ($probe->getStatusCode() !== 404) {
            $selectedPath = $p;
            break;
        }
    }

    expect($selectedPath)->not()->toBeNull();

    // Kirim beberapa variasi header biar kompatibel dengan middleware yang kamu pilih.
    $headers = [
        // apiKey style
        'X-Admin-Key' => $adminKey,
        'X-Admin-Demo-Key' => $adminKey,
        'X-Tenrusl-Admin-Key' => $adminKey,

        // bearer style
        'Authorization' => "Bearer {$adminKey}",
    ];

    $resp = getJson($selectedPath, $headers);

    $resp->assertStatus(200);
    $resp->assertHeader('X-Request-ID');

    $json = $resp->json();

    // Minimal: harus punya root "data"
    expect($json)->toBeArray();
    expect($json)->toHaveKey('data');

    $data = $json['data'];

    // Support beberapa bentuk response yang umum:
    // A) Laravel ResourceCollection paginator: { data: [..], links:.., meta:.. }
    // B) Nested style: { data: { data: [..], ... } }
    $items = null;

    if (is_array($data) && array_is_list($data)) {
        // A: data langsung list
        $items = $data;
    } elseif (is_array($data) && isset($data['data']) && is_array($data['data'])) {
        // B: data.data list
        $items = $data['data'];
    }

    expect($items)->not()->toBeNull();
    expect($items)->toBeArray();
    expect(count($items))->toBeGreaterThan(0);

    // Cek struktur minimal item pertama
    $first = $items[0];
    expect($first)->toBeArray();
    expect($first)->toHaveKeys([
        'id',
        'provider',
        'provider_ref',
        'status',
    ]);
});
