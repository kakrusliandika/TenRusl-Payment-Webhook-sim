<?php

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates payment with idempotency and returns snapshot on replay', function () {
    $idem = (string) Str::uuid();

    // 1st request → 201
    $res1 = $this->withHeaders([
        'Idempotency-Key' => $idem,
    ])->postJson('/api/v1/payments', [
        'amount' => 25000,
        'currency' => 'IDR',
        'description' => 'Topup',
    ]);

    $res1->assertCreated();
    $id = $res1->json('id') ?? $res1->json('data.id'); // if wrapped by resource

    expect($id)->not()->toBeNull();
    $first = Payment::query()->find($id);
    expect($first)->not()->toBeNull()
        ->and($first->status)->toBe('pending');

    // 2nd request with same idem → 200 and same id (snapshot)
    $res2 = $this->withHeaders([
        'Idempotency-Key' => $idem,
    ])->postJson('/api/v1/payments', [
        'amount' => 25000,
        'currency' => 'IDR',
        'description' => 'Topup',
    ]);

    $res2->assertOk();
    $id2 = $res2->json('id') ?? $res2->json('data.id');
    expect($id2)->toBe($id);
});
