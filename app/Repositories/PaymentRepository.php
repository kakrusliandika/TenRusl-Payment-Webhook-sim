<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Payment;
use App\ValueObjects\PaymentStatus;
use Illuminate\Support\Facades\DB;

final class PaymentRepository
{
    /**
     * Buat record Payment baru.
     *
     * @param  array{
     *   provider:string,
     *   provider_ref:string,
     *   amount?:string|int|float,
     *   currency?:string,
     *   status?:string|PaymentStatus,
     *   meta?:array
     * } $attributes
     */
    public function create(array $attributes): Payment
    {
        return Payment::query()->create($attributes);
    }

    public function find(string $id): ?Payment
    {
        /** @var Payment|null $p */
        $p = Payment::query()->find($id);
        return $p;
    }

    public function findByProviderRef(string $provider, string $providerRef): ?Payment
    {
        /** @var Payment|null $p */
        $p = Payment::query()
            ->where('provider', $provider)
            ->where('provider_ref', $providerRef)
            ->first();

        return $p;
    }

    /**
     * Update status berdasarkan (provider, provider_ref).
     * Mengembalikan jumlah baris terpengaruh.
     */
    public function updateStatusByProviderRef(string $provider, string $providerRef, string|PaymentStatus $status): int
    {
        $status = $status instanceof PaymentStatus ? $status->value : $status;

        return Payment::query()
            ->where('provider', $provider)
            ->where('provider_ref', $providerRef)
            ->update([
                'status'     => $status,
                'updated_at' => now(),
            ]);
    }

    /**
     * Simpan perubahan pada model Payment.
     */
    public function save(Payment $payment): bool
    {
        return $payment->save();
    }

    /**
     * Hapus berdasarkan primary key.
     */
    public function delete(string $id): bool
    {
        return (bool) Payment::query()->whereKey($id)->delete();
    }

    /**
     * Operasi mass update via DB builder (bila perlu hemat memori).
     */
    public function massUpdate(array $where, array $payload): int
    {
        return DB::table('payments')->where($where)->update($payload);
    }
}
