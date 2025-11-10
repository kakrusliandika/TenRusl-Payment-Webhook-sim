<?php

declare(strict_types=1);

namespace App\Services\Idempotency;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Manajemen Idempotency-Key ala Stripe:
 * - Pakai header "Idempotency-Key" jika ada; kalau tidak, buat dari fingerprint request.
 * - Simpan lock & optional cached response agar permintaan duplikat mengembalikan hasil yang sama.
 *
 * Rujukan pola: idempotent request menyimpan status code & body pertama untuk key tsb. (Stripe).
 * TTL dikontrol via env TENRUSL_IDEMPOTENCY_TTL.
 */
final class IdempotencyKeyService
{
    public function __construct(
        private readonly RequestFingerprint $fingerprint
    ) {
    }

    /**
     * Ambil/generate Idempotency-Key untuk request.
     */
    public function resolveKey(Request $request): string
    {
        $hdr = (string) ($request->header('Idempotency-Key') ?? '');
        if ($hdr !== '') {
            return trim($hdr);
        }

        return $this->fingerprint->hash($request);
    }

    /**
     * Coba akuisisi "lock" untuk key (true jika lock baru, false jika sudah ada -> duplikat).
     * Implementasi generik via Cache::add agar kompatibel dengan driver file/array.
     */
    public function acquireLock(string $key): bool
    {
        $ttl = (int) config('tenrusl.idempotency_ttl', 3600);
        return Cache::add($this->lockKey($key), 1, $ttl);
    }

    public function releaseLock(string $key): void
    {
        Cache::forget($this->lockKey($key));
    }

    /**
     * Simpan response pertama untuk key ini.
     *
     * @param array{status:int, headers:array<string,string>, body:mixed} $response
     */
    public function storeResponse(string $key, array $response): void
    {
        $ttl = (int) config('tenrusl.idempotency_ttl', 3600);
        Cache::put($this->respKey($key), $response, $ttl);
    }

    /**
     * Ambil response yang pernah disimpan (jika ada).
     *
     * @return array{status:int, headers:array<string,string>, body:mixed}|null
     */
    public function getStoredResponse(string $key): ?array
    {
        /** @var array|null $val */
        $val = Cache::get($this->respKey($key));
        return $val ?: null;
    }

    private function lockKey(string $key): string
    {
        return "idemp:lock:{$key}";
    }

    private function respKey(string $key): string
    {
        return "idemp:resp:{$key}";
    }
}
