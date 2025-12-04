<?php

declare(strict_types=1);

namespace App\Services\Idempotency;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

final class IdempotencyKeyService
{
    /** @var array<string, Lock> */
    private array $locks = [];

    public function __construct(
        private readonly RequestFingerprint $fingerprint,
    ) {}

    public function resolveKey(Request $request): string
    {
        $hdr = trim((string) $request->header('Idempotency-Key', ''));

        return $hdr !== '' ? $hdr : $this->fingerprint->hash($request);
    }

    public function acquireLock(string $key): bool
    {
        $lockSeconds = (int) (config('tenrusl.idempotency.lock_seconds') ?? 30);

        $store = Cache::getStore();
        if ($store instanceof LockProvider) {
            $lock = Cache::lock($this->lockKey($key), $lockSeconds);
            $acquired = (bool) $lock->get();

            if ($acquired) {
                $this->locks[$key] = $lock;
                return true;
            }

            return false;
        }

        return Cache::add($this->lockKey($key), 1, now()->addSeconds($lockSeconds));
    }

    public function releaseLock(string $key): void
    {
        if (isset($this->locks[$key])) {
            try {
                $this->locks[$key]->release();
            } finally {
                unset($this->locks[$key]);
            }
            return;
        }

        Cache::forget($this->lockKey($key));
    }

    /**
     * Simpan response pertama untuk key (untuk replay idempotent).
     *
     * Dibuat fleksibel: headers/body boleh tidak ada (sesuai pemakaian `??`),
     * ini yang menghilangkan error PHPStan "offset headers ... ?? always exists".
     *
     * @param array{
     *   status:int,
     *   headers?:array<string,string|string[]>,
     *   body?:mixed
     * } $response
     */
    public function storeResponse(string $key, array $response): void
    {
        $ttl = $this->ttlSeconds();

        $headers = $response['headers'] ?? [];
        $body = $response['body'] ?? null;

        $normalized = [
            'status' => (int) $response['status'],
            'headers' => $this->normalizeHeaders($headers),
            'body' => $body,
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
     * @return array{
     *   status:int,
     *   headers:array<string,string|string[]>,
     *   body:mixed
     * }|null
     */
    public function getStoredResponse(string $key): ?array
    {
        /** @var array<string,mixed>|null $cached */
        $cached = Cache::get($this->respKey($key));

        if (! $cached || ! isset($cached['response'], $cached['fingerprint'])) {
            return null;
        }

        /** @var array{status:int, headers:array<string,string|string[]>, body:mixed} $response */
        $response = $cached['response'];

        $currentFingerprint = $this->responseFingerprint($response);
        $storedFingerprint = (string) $cached['fingerprint'];

        if (! hash_equals($storedFingerprint, $currentFingerprint)) {
            return null;
        }

        return $response;
    }

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
     * @param  array<string,string|string[]>  $headers
     * @return array<string,string|string[]>
     */
    private function normalizeHeaders(array $headers): array
    {
        $whitelist = [
            'content-type',
            'content-language',
            'cache-control',
            'etag',
        ];

        $norm = [];

        foreach ($headers as $k => $v) {
            $lk = strtolower((string) $k);
            if (in_array($lk, $whitelist, true)) {
                $norm[$lk] = $v;
            }
        }

        ksort($norm);

        return $norm;
    }

    /**
     * @param array{
     *   status:int,
     *   headers?:array<string,string|string[]>,
     *   body?:mixed
     * } $response
     */
    private function responseFingerprint(array $response): string
    {
        $headers = $response['headers'] ?? [];

        // Urutkan nilai header array agar stabil
        $headers = array_map(
            static fn ($v) => is_array($v) ? Arr::sort($v) : $v,
            $headers
        );

        ksort($headers);

        $payload = [
            'status' => (int) $response['status'],
            'headers' => $headers,
            'body' => $response['body'] ?? null,
        ];

        $json = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
        );

        return hash('sha256', $json ?: '');
    }
}
