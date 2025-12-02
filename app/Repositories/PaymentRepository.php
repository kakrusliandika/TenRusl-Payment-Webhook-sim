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
     *   amount:int|string|float,
     *   currency?:string,
     *   description?:string|null,
     *   meta?:array|null,
     *   status?:string|PaymentStatus|null,
     *   idempotency_key?:string|null,
     *   idempotency_request_hash?:string|null
     * } $attributes
     */
    public function create(array $attributes): Payment
    {
        // Jika status diisi enum, Eloquent akan handle casting. :contentReference[oaicite:6]{index=6}
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
     * (Opsional tapi berguna untuk flow idempotency)
     */
    public function findByIdempotencyKey(string $key): ?Payment
    {
        /** @var Payment|null $p */
        $p = Payment::query()
            ->where('idempotency_key', $key)
            ->first();

        return $p;
    }

    /**
     * Update status payment berdasarkan (provider, provider_ref).
     *
     * Aturan “state transition” yang aman:
     * - Payment final (succeeded/failed) TIDAK boleh balik ke pending.
     * - Payment final juga tidak kita "flip" ke final lain (succeeded <-> failed).
     *   (First final wins; repeated same final is OK / idempotent.)
     *
     * Return: jumlah row yang ter-update.
     */
    public function updateStatusByProviderRef(string $provider, string $providerRef, string|PaymentStatus $status): int
    {
        $status = $status instanceof PaymentStatus ? $status->value : $status;
        $status = strtolower($status);

        $finals = ['succeeded', 'failed'];

        // Payload update minimal
        $payload = [
            'status' => $status,
            'updated_at' => now(),
        ];

        // 1) Kalau incoming = pending, jangan pernah “downgrade” final -> pending
        if ($status === 'pending') {
            return Payment::query()
                ->where('provider', $provider)
                ->where('provider_ref', $providerRef)
                ->whereNotIn('status', $finals)
                ->update($payload);
        }

        // 2) Kalau incoming = final, hanya izinkan:
        //    - pending -> final
        //    - final yang sama -> final yang sama (idempotent)
        //    - TOLAK: final lain -> final ini (prevent flip)
        return Payment::query()
            ->where('provider', $provider)
            ->where('provider_ref', $providerRef)
            ->where(function ($q) use ($status) {
                $q->where('status', 'pending')
                  ->orWhere('status', $status);
            })
            ->update($payload);
    }

    public function save(Payment $payment): bool
    {
        return $payment->save();
    }

    public function delete(string $id): bool
    {
        return (bool) Payment::query()->whereKey($id)->delete();
    }

    /**
     * Operasi update via Query Builder (kalau perlu hemat memori).
     */
    public function massUpdate(array $where, array $payload): int
    {
        return DB::table('payments')->where($where)->update($payload);
    }
}
