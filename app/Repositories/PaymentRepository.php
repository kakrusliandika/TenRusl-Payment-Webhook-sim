<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Payment;
use App\ValueObjects\PaymentStatus;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository
{
    /**
     * Buat payment baru (status default pending).
     * $data: amount(int), currency(string), description(?string), metadata(array), idempotency_key(string)
     */
    public function create(array $data): Payment
    {
        return Payment::query()->create([
            'amount'          => (int) ($data['amount'] ?? 0),
            'currency'        => (string) ($data['currency'] ?? 'IDR'),
            'description'     => $data['description'] ?? null,
            'metadata'        => $data['metadata'] ?? [],
            'status'          => (string) ($data['status'] ?? PaymentStatus::PENDING->value),
            'idempotency_key' => (string) $data['idempotency_key'],
        ]);
    }

    public function find(string $id): ?Payment
    {
        return Payment::query()->find($id);
    }

    public function findByIdempotencyKey(string $key): ?Payment
    {
        return Payment::query()->where('idempotency_key', $key)->first();
    }

    /** @return Collection<int,Payment> */
    public function listByStatus(PaymentStatus|string $status, int $limit = 50): Collection
    {
        $value = $status instanceof PaymentStatus ? $status->value : (string) $status;
        return Payment::query()->where('status', $value)->limit($limit)->get();
    }

    public function updateStatus(Payment $payment, PaymentStatus|string $status): Payment
    {
        $value = $status instanceof PaymentStatus ? $status->value : (string) $status;
        $payment->status = $value;
        $payment->save();

        return $payment;
    }

    public function markPaid(Payment $payment): Payment
    {
        return $this->updateStatus($payment, PaymentStatus::PAID);
    }

    public function markFailed(Payment $payment): Payment
    {
        return $this->updateStatus($payment, PaymentStatus::FAILED);
    }

    public function markRefunded(Payment $payment): Payment
    {
        return $this->updateStatus($payment, PaymentStatus::REFUNDED);
    }
}
