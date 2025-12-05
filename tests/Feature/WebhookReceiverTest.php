<?php

declare(strict_types=1);

use App\Jobs\ProcessWebhookEvent;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Pest\Laravel\call;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

/**
 * Helper: bikin signature mock yang “paling umum” dipakai: HMAC-SHA256(raw body, secret).
 * Dibuat toleran terhadap variasi key config/env sesuai implementasi verifier.
 */
if (! function_exists('mockSignature')) {
    function mockSignature(string $rawBody): string
    {
        $secret =
            config('tenrusl.mock_secret')
            ?? config('tenrusl.signatures.mock.secret')
            ?? env('TENRUSL_MOCK_SECRET')
            ?? 'changeme';

        return hash_hmac('sha256', $rawBody, (string) $secret);
    }
}

it('returns 404 (or 405 when OPTIONS wildcard exists) for unknown webhook provider (blocked by allowlist)', function () {
    $resp = postJson('/api/v1/webhooks/unknown', ['id' => 'evt']);

    // Kalau ada route OPTIONS wildcard/global, Laravel bisa balas 405 untuk POST unknown,
    // karena path-nya "match" method lain tapi POST tidak diizinkan.
    expect($resp->getStatusCode())->toBeIn([404, 405]);
});

it('handles OPTIONS preflight with 204', function () {
    $response = call('OPTIONS', '/api/v1/webhooks/mock', [], [], [], [
        'HTTP_ORIGIN' => 'http://localhost',
        'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
        'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'Content-Type',
    ]);

    expect($response->getStatusCode())->toBe(204);
});

it('rejects invalid signature with 401 (mock provider)', function () {
    $payload = [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.paid',
        'status' => 'paid',
    ];

    $raw = json_encode($payload, JSON_UNESCAPED_SLASHES);

    $bad = 'invalid-signature';

    $resp = call('POST', '/api/v1/webhooks/mock', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_MOCK_SIGNATURE' => $bad,
        'HTTP_X_SIGNATURE' => $bad,
    ], $raw);

    expect($resp->getStatusCode())->toBe(401);
});

it('dedup: same (provider,event_id) only creates 1 row and attempts increases', function () {
    $eventId = 'evt_dedup_'.now()->timestamp.'_'.Str::random(6);

    $payload = [
        'id' => $eventId,
        'type' => 'payment.paid',
        'status' => 'paid',
        'data' => ['object' => ['id' => 'sim_mock_'.Str::random(8)]],
    ];

    $raw = json_encode($payload, JSON_UNESCAPED_SLASHES);
    $sig = mockSignature($raw);

    $resp1 = call('POST', '/api/v1/webhooks/mock', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_MOCK_SIGNATURE' => $sig,
        'HTTP_X_SIGNATURE' => $sig,
    ], $raw);

    expect($resp1->getStatusCode())->toBeIn([202, 200]);

    $resp2 = call('POST', '/api/v1/webhooks/mock', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_MOCK_SIGNATURE' => $sig,
        'HTTP_X_SIGNATURE' => $sig,
    ], $raw);

    expect($resp2->getStatusCode())->toBeIn([202, 200]);

    $count = DB::table('webhook_events')
        ->where('provider', 'mock')
        ->where('event_id', $eventId)
        ->count();

    expect($count)->toBe(1);

    $attempts = (int) DB::table('webhook_events')
        ->where('provider', 'mock')
        ->where('event_id', $eventId)
        ->value('attempts');

    expect($attempts)->toBeGreaterThanOrEqual(2);
});

it('retry command: only picks due events and respects --limit (queue mode)', function () {
    Bus::fake();

    // Penting: pakai timestamp (detik) supaya aman dari mismatch microseconds (Carbon now()) vs
    // datetime storage SQLite/MySQL yang seringnya tanpa microseconds.
    $startTs = Carbon::now()->timestamp;
    $now = Carbon::createFromTimestamp($startTs);

    // Event due #1: next_retry_at NULL
    $id1 = (string) Str::ulid();
    DB::table('webhook_events')->insert([
        'id' => $id1,
        'provider' => 'mock',
        'event_id' => 'evt_due_1_'.Str::random(10),
        'event_type' => 'payment.paid',
        'payload_raw' => '{"id":"x"}',
        'payload' => json_encode(['id' => 'x']),
        'status' => 'received',
        'payment_status' => 'pending',
        'payment_provider_ref' => null,
        'attempts' => 1,
        'received_at' => $now->copy()->subMinutes(2),
        'last_attempt_at' => $now->copy()->subMinutes(2),
        'processed_at' => null,
        'next_retry_at' => null,
        'error_message' => null,
        'created_at' => $now->copy()->subMinutes(2),
        'updated_at' => $now->copy()->subMinutes(2),
    ]);

    // Event due #2: next_retry_at di masa lalu
    $id2 = (string) Str::ulid();
    DB::table('webhook_events')->insert([
        'id' => $id2,
        'provider' => 'mock',
        'event_id' => 'evt_due_2_'.Str::random(10),
        'event_type' => 'payment.paid',
        'payload_raw' => '{"id":"y"}',
        'payload' => json_encode(['id' => 'y']),
        'status' => 'received',
        'payment_status' => 'pending',
        'payment_provider_ref' => null,
        'attempts' => 1,
        'received_at' => $now->copy()->subMinutes(2),
        'last_attempt_at' => $now->copy()->subMinutes(2),
        'processed_at' => null,
        'next_retry_at' => $now->copy()->subMinute(),
        'error_message' => null,
        'created_at' => $now->copy()->subMinutes(2),
        'updated_at' => $now->copy()->subMinutes(2),
    ]);

    // Not due: next_retry_at future
    $id3 = (string) Str::ulid();
    DB::table('webhook_events')->insert([
        'id' => $id3,
        'provider' => 'mock',
        'event_id' => 'evt_not_due_'.Str::random(10),
        'event_type' => 'payment.paid',
        'payload_raw' => '{"id":"z"}',
        'payload' => json_encode(['id' => 'z']),
        'status' => 'received',
        'payment_status' => 'pending',
        'payment_provider_ref' => null,
        'attempts' => 1,
        'received_at' => $now->copy()->subMinutes(2),
        'last_attempt_at' => $now->copy()->subMinutes(2),
        'processed_at' => null,
        'next_retry_at' => $now->copy()->addMinutes(5),
        'error_message' => null,
        'created_at' => $now->copy()->subMinutes(2),
        'updated_at' => $now->copy()->subMinutes(2),
    ]);

    // limit=1 harus cuma claim 1 event yang due
    Artisan::call('tenrusl:webhooks:retry', [
        '--limit' => 1,
        '--max-attempts' => 5,
        '--mode' => 'full',
        '--queue' => true,
    ]);

    Bus::assertDispatched(ProcessWebhookEvent::class, 1);

    $e1 = WebhookEvent::query()->find($id1);
    $e2 = WebhookEvent::query()->find($id2);
    $e3 = WebhookEvent::query()->find($id3);

    expect($e1)->not->toBeNull();
    expect($e2)->not->toBeNull();
    expect($e3)->not->toBeNull();

    // Tepat 1 dari (e1,e2) yang harus berubah (attempts > 1) karena --limit=1
    $changed1 = (int) $e1->attempts > 1;
    $changed2 = (int) $e2->attempts > 1;

    expect(($changed1 ? 1 : 0) + ($changed2 ? 1 : 0))->toBe(1);

    $claimed = $changed1 ? $e1 : $e2;
    $notClaimed = $changed1 ? $e2 : $e1;

    expect((int) $claimed->attempts)->toBeGreaterThan(1);
    expect($claimed->next_retry_at)->not->toBeNull();

    $claimedNext = Carbon::parse((string) $claimed->next_retry_at);
    expect($claimedNext->timestamp)->toBeGreaterThanOrEqual($now->timestamp);

    expect((int) $notClaimed->attempts)->toBe(1);

    // Event not-due tidak boleh disentuh
    expect((int) $e3->attempts)->toBe(1);
    expect(Carbon::parse((string) $e3->next_retry_at)->timestamp)->toBeGreaterThan($now->timestamp);
});
