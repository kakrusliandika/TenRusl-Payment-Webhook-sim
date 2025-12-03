<?php

declare(strict_types=1);

namespace App\Services\Idempotency;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * Manajemen Idempotency ala Stripe:
 * - Gunakan header "Idempotency-Key" jika ada; jika tidak, buat dari fingerprint request.
 * - Atomic check-then-set untuk mencegah race condition (pakai Cache::lock jika driver mendukung; fallback ke Cache::add).
 * - Simpan response pertama (status, headers terpilih, body) + fingerprint; repeat request balikan hasil identik.
 *
 * Konfigurasi TTL (sumber utama):
 *   config('tenrusl.idempotency.ttl_seconds')   // env: IDEMPOTENCY_TTL_SECONDS
 *
 * Fallback legacy (kompatibilitas ke belakang):
 *   config('tenrusl.idempotency_ttl')           // env: TENRUSL_IDEMPOTENCY_TTL
 *
 * Lock TTL:
 *   config('tenrusl.idempotency.lock_seconds')  // default 30 detik
 */
final class IdempotencyKeyService
{
    /** @var array<string, Lock> */
    private array $locks = [];

    public function __construct(
        private readonly RequestFingerprint $fingerprint,
    ) {}

    /**
     * Ambil/generate Idempotency-Key untuk request.
     */
    public function resolveKey(Request $request): string
    {
        $hdr = \trim((string) $request->header('Idempotency-Key', ''));

        return $hdr !== '' ? $hdr : $this->fingerprint->hash($request);
    }

    /**
     * Coba akuisisi "lock" untuk key.
     * - Menggunakan atomic lock (Cache::lock) bila store mendukung.
     * - Fallback ke Cache::add (set-if-not-exists) jika tidak mendukung lock.
     *
     * @return bool true jika lock berhasil diakuisisi (request pertama), false jika duplikat/sudah terkunci
     */
    public function acquireLock(string $key): bool
    {
        $lockSeconds = (int) (config('tenrusl.idempotency.lock_seconds') ?? 30);

        // Prefer atomic distributed lock jika store mendukung
        $store = Cache::getStore();
        if ($store instanceof LockProvider) {
            $lock = Cache::lock($this->lockKey($key), $lockSeconds);
            $acquired = (bool) $lock->get(); // non-blocking
            if ($acquired) {
                $this->locks[$key] = $lock;

                return true;
            }

            return false;
        }

        // Fallback: set-if-not-exists via Cache::add
        // NB: ini tidak sekuat distributed lock, namun kompatibel untuk file/array driver.
        return Cache::add($this->lockKey($key), 1, now()->addSeconds($lockSeconds));
    }

    /**
     * Lepas lock bila ada.
     */
    public function releaseLock(string $key): void
    {
        if (isset($this->locks[$key])) {
            // Atomic lock case
            try {
                $this->locks[$key]->release();
            } finally {
                unset($this->locks[$key]);
            }

            return;
        }

        // Fallback (Cache::add) case
        Cache::forget($this->lockKey($key));
    }

    /**
     * Simpan response pertama untuk key ini (sesuai pola Stripe: simpan status+body+headers terpilih).
     *
     * @param array{
     *   status:int,
     *   headers:array<string,string|string[]>,
     *   body:mixed
     * } $response
     */
    public function storeResponse(string $key, array $response): void
    {
        $ttl = $this->ttlSeconds();

        $normalized = [
            'status' => (int) $response['status'],
            // Simpan subset headers yang relevan untuk determinisme (hindari Set-Cookie/Date yang fluktuatif)
            'headers' => $this->normalizeHeaders($response['headers'] ?? []),
            'body' => $response['body'] ?? null,
        ];

        $fingerprint = $this->responseFingerprint($normalized);

        $payload = [
            'stored_at' => now()->toIso8601String(),
            'fingerprint' => $fingerprint,
            'response' => $normalized,
        ];

        Cache::put($this->respKey($key), $payload, now()->addSeconds($ttl));
    }

    /**
     * Ambil response yang pernah disimpan (jika ada & fingerprint masih cocok).
     *
     * @return array{
     *   status:int,
     *   headers:array<string,string|string[]>,
     *   body:mixed
     * }|null
     */
    public function getStoredResponse(string $key): ?array
    {
        /** @var array|null $cached */
        $cached = Cache::get($this->respKey($key));
        if (! $cached || ! isset($cached['response'], $cached['fingerprint'])) {
            return null;
        }

        $response = $cached['response'];

        // Verifikasi ulang fingerprint untuk memastikan payload di cache
        // masih konsisten dengan fingerprint yang disimpan.
        $currentFingerprint = $this->responseFingerprint($response);
        $storedFingerprint = (string) $cached['fingerprint'];

        if (! \hash_equals($storedFingerprint, $currentFingerprint)) {
            // Fingerprint berbeda: anggap tidak valid (misuse / korup), jangan pakai cache.
            return null;
        }

        return $response;
    }

    /**
     * TTL utama idempotensi:
     * - Prioritas: tenrusl.idempotency.ttl_seconds (IDEMPOTENCY_TTL_SECONDS).
     * - Fallback:  tenrusl.idempotency_ttl (TENRUSL_IDEMPOTENCY_TTL) untuk kompatibilitas.
     */
    private function ttlSeconds(): int
    {
        $ttl = config('tenrusl.idempotency.ttl_seconds');

        if ($ttl === null) {
            $ttl = config('tenrusl.idempotency_ttl', 3600);
        }

        return (int) $ttl;
    }

    private function lockKey(string $key): string
    {
        return "idemp:lock:{$key}";
    }

    private function respKey(string $key): string
    {
        return "idemp:resp:{$key}";
    }

    /**
     * Normalisasi headers: lower-case key, ambil subset yang stabil.
     *
     * @param  array<string,string|string[]>  $headers
     * @return array<string,string|string[]>
     */
    private function normalizeHeaders(array $headers): array
    {
        // Header yang disimpan (boleh disesuaikan)
        $whitelist = [
            'content-type',
            'content-language',
            'cache-control',
            'etag',
        ];

        $norm = [];
        foreach ($headers as $k => $v) {
            $lk = \strtolower((string) $k);
            if (\in_array($lk, $whitelist, true)) {
                $norm[$lk] = $v;
            }
        }

        \ksort($norm);

        return $norm;
    }

    /**
     * Hitung fingerprint stabil untuk response (status + headers norm + body canonical JSON).
     *
     * @param array{
     *   status:int,
     *   headers:array<string,string|string[]>,
     *   body:mixed
     * } $response
     */
    private function responseFingerprint(array $response): string
    {
        $headers = $response['headers'] ?? [];
        // Urutkan nilai header array agar stabil
        $headers = \array_map(
            static fn ($v) => \is_array($v) ? Arr::sort($v) : $v,
            $headers
        );
        \ksort($headers);

        $payload = [
            'status' => (int) $response['status'],
            'headers' => $headers,
            // JSON canonical (tanpa escaping ekstra) agar stabil
            'body' => $response['body'],
        ];

        $json = \json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
        );

        return \hash('sha256', $json ?: '');
    }
}
