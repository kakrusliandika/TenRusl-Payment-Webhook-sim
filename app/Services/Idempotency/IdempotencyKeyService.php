<?php

namespace App\Services\Idempotency;

use App\Models\Payment;

/**
 * Layanan idempotensi sederhana untuk endpoint create payment:
 * - Mengecek snapshot Payment berdasarkan Idempotency-Key pada tabel payments.
 * - Untuk general-purpose idempotency table, bisa dibuat tabel terpisah di masa depan.
 */
class IdempotencyKeyService
{
    public function findPaymentByKey(string $key): ?Payment
    {
        return Payment::query()->where('idempotency_key', $key)->first();
    }
}
