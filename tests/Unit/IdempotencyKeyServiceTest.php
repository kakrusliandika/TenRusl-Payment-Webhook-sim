<?php

use App\Models\Payment;
use App\Services\Idempotency\IdempotencyKeyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

// JANGAN uses(TestCase::class) lagi di file ini.
// TestCase sudah di-assign global via tests/Pest.php
uses(RefreshDatabase::class);

it('finds payment by idempotency key', function () {
    $key = (string) Str::uuid();

    Payment::factory()->create([
        'idempotency_key' => $key,
        'amount' => 25000,
        'status' => 'pending',
    ]);

    $svc = app(IdempotencyKeyService::class);

    $found = $svc->findPaymentByKey($key);
    expect($found)->not()->toBeNull()
        ->and($found->idempotency_key)->toBe($key);
});

it('returns null for unknown idempotency key', function () {
    $svc = app(IdempotencyKeyService::class);
    $found = $svc->findPaymentByKey((string) Str::uuid());
    expect($found)->toBeNull();
});
