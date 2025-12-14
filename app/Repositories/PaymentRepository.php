<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Payment;
use App\ValueObjects\PaymentStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
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
        return Payment::query()->create($attributes);
    }

    public function find(string $id): ?Payment
    {
        /** @var Payment|null $p */
        $p = Payment::query()->find($id);

        return $p;
    }

    /**
     * Find by id + eager load relasi yang relevan (jika relasinya ada).
     * Ini dipakai oleh GET /payments/{id}.
     */
    public function findByIdWithRelations(string $id): ?Payment
    {
        $query = Payment::query();

        // Eager-load hanya jika relationship method memang ada di model (safe).
        $relations = [];
        foreach (['webhookEvents', 'statusTransitions'] as $rel) {
            if (method_exists(Payment::class, $rel)) {
                $relations[] = $rel;
            }
        }

        if ($relations !== []) {
            $query->with($relations);
        }

        /** @var Payment|null $p */
        $p = $query->find($id);

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
     * List payments untuk Admin UI (pagination + filter).
     *
     * Filters supported:
     * - provider
     * - status
     * - q (search: id atau provider_ref)
     * - created_from (YYYY-MM-DD atau ISO string)
     * - created_to   (YYYY-MM-DD atau ISO string)
     */
    public function paginateAdmin(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Payment::query()->orderByDesc('created_at');

        $provider = $filters['provider'] ?? null;
        if (is_string($provider) && $provider !== '') {
            $query->where('provider', $provider);
        }

        $status = $filters['status'] ?? null;
        if (is_string($status) && $status !== '') {
            $query->where('status', strtolower($status));
        }

        $q = $filters['q'] ?? null;
        if (is_string($q) && $q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                // search by id OR provider_ref (yang paling umum dipakai di admin)
                $sub->where('id', 'like', "%{$q}%")
                    ->orWhere('provider_ref', 'like', "%{$q}%");
            });
        }

        $from = $filters['created_from'] ?? null;
        if (is_string($from) && $from !== '') {
            // gunakan whereDate agar aman untuk input YYYY-MM-DD
            $query->whereDate('created_at', '>=', $from);
        }

        $to = $filters['created_to'] ?? null;
        if (is_string($to) && $to !== '') {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query->paginate($perPage);
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
