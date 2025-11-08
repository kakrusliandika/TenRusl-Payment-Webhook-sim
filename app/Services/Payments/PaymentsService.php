<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Layanan domain untuk Payment:
 * - create() idempotent berdasarkan Idempotency-Key
 * - get() / find()
 */
class PaymentsService
{
    /**
     * Membuat Payment baru secara idempotent berdasarkan Idempotency-Key.
     *
     * @param  array  $payload  amount, currency?, description?, metadata?
     * @param  string $idempotencyKey
     * @return \App\Models\Payment  (existing snapshot jika sudah ada)
     */
    public function create(array $payload, string $idempotencyKey): Payment
    {
        // Snapshot jika sudah ada
        $existing = Payment::query()->where('idempotency_key', $idempotencyKey)->first();
        if ($existing) {
            Log::info('PaymentsService.create: snapshot return', ['idem' => $idempotencyKey, 'payment_id' => (string) $existing->id]);
            return $existing;
        }

        // Normalisasi payload
        $amount      = (int) Arr::get($payload, 'amount');
        $currency    = (string) Arr::get($payload, 'currency', 'IDR');
        $description = Arr::get($payload, 'description');
        $metadata    = Arr::get($payload, 'metadata', []);

        $payment = Payment::query()->create([
            'amount'          => $amount,
            'currency'        => $currency,
            'description'     => $description,
            'metadata'        => $metadata,
            'status'          => 'pending',
            'idempotency_key' => $idempotencyKey,
        ]);

        Log::info('PaymentsService.create: created', ['idem' => $idempotencyKey, 'payment_id' => (string) $payment->id]);

        return $payment;
    }

    /**
     * Ambil Payment by id (ULID).
     */
    public function get(string $id): ?Payment
    {
        return Payment::query()->find($id);
    }
}
