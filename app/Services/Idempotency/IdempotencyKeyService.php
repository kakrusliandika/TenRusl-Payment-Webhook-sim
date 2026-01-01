<?php

// app/Services/Idempotency/IdempotencyKeyService.php

declare(strict_types=1);

namespace App\Services\Idempotency;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class IdempotencyKeyService
{
    /** @var array<string, Lock> */
    private array $locks = [];

    public function __construct(
        private readonly RequestFingerprint $fingerprint,
    ) {}

    /**
     * Resolve idempotency key.
     * - Prefer header "Idempotency-Key" (fallback: "X-Idempotency-Key").
     * - If missing, generate deterministic key from request fingerprint.
     * - If too long / contains control chars, normalize to sha256-based key.
     */
    public function resolveKey(Request $request): string
    {
        $raw = (string) $request->header('Idempotency-Key', '');
        if ($raw === '') {
            $raw = (string) $request->header('X-Idempotency-Key', '');
        }

        $raw = trim($raw);

        if ($raw === '') {
            return 'fp_' . $this->fingerprint->hash($request);
        }

        // Reject/normalize odd keys defensively (control chars / extremely long)
        if ($this->hasControlChars($raw) || strlen($raw) > 255) {
            return 'h_' . hash('sha256', $raw);
        }

        return $raw;
    }

    /**
     * Hash fingerprint dari request (dipakai untuk mismatch detection di DB / cache).
     */
    public function requestHash(Request $request): string
    {
        return $this->fingerprint->hash($request);
    }

    /**
     * Acquire distributed lock for the idempotency key.
     *
     * NOTE:
     * - Production-grade: wajib pakai store yang mendukung atomic lock (mis. Redis).
     * - Di environment non-production, akan fallback best-effort bila store tidak mendukung lock.
     */
    public function acquireLock(string $key): bool
    {
        $lockSeconds = (int) (config('tenrusl.idempotency.lock_seconds') ?? 30);

        $store = Cache::getStore();

        // ✅ Panggil lock() dari store yang implement LockProvider (bukan Repository)
        if ($store instanceof LockProvider) {
            $lock = $store->lock($this->lockKey($key), $lockSeconds);
            $acquired = (bool) $lock->get();

            if ($acquired) {
                $this->locks[$key] = $lock;
            }

            return $acquired;
        }

        // Fallback kalau store tidak support atomic lock
        return Cache::add($this->lockKey($key), 1, now()->addSeconds($lockSeconds));
    }


    public function releaseLock(string $key): void
    {
        if (isset($this->locks[$key])) {
            try {
                $this->locks[$key]->release();
            } catch (\Throwable $e) {
                // ignore
            } finally {
                unset($this->locks[$key]);
            }

            return;
        }

        $this->cacheRepo()->forget($this->lockKey($key));
    }

    /**
     * Guard mismatch request hash in cache (optional layer).
     * - If first time: store request hash.
     * - If already exists: must match.
     */
    public function rememberOrMatchRequestHash(string $key, string $requestHash): bool
    {
        $cache = $this->cacheRepo();
        $ttl = $this->ttlSeconds();

        $rk = $this->reqHashKey($key);

        // Atomic "set-if-not-exists"
        if ($cache->add($rk, $requestHash, Carbon::now()->addSeconds($ttl))) {
            return true;
        }

        $existing = (string) ($cache->get($rk) ?? '');
        if ($existing === '') {
            // Race or eviction; set again
            $cache->put($rk, $requestHash, Carbon::now()->addSeconds($ttl));
            return true;
        }

        return hash_equals($existing, $requestHash);
    }

    /**
     * Simpan response pertama untuk key (untuk replay idempotent).
     *
     * @param array{
     *   status:int,
     *   headers?:array<string,string|string[]>,
     *   body?:mixed
     * } $response
     */
    public function storeResponse(string $key, array $response, ?string $requestHash = null): void
    {
        $ttl = $this->ttlSeconds();

        $headers = $response['headers'] ?? [];
        $body = $response['body'] ?? null;

        $normalized = [
            'status' => (int) $response['status'],
            'headers' => $this->normalizeHeaders(is_array($headers) ? $headers : []),
            'body' => $body,
        ];

        $payload = [
            'stored_at' => Carbon::now()->toIso8601String(),
            'response_fingerprint' => $this->responseFingerprint($normalized),
            'request_hash' => $requestHash,
            'response' => $normalized,
        ];

        $this->cacheRepo()->put(
            $this->respKey($key),
            $payload,
            Carbon::now()->addSeconds($ttl)
        );

        if (is_string($requestHash) && $requestHash !== '') {
            // also store standalone request hash for quick mismatch check
            $this->cacheRepo()->put(
                $this->reqHashKey($key),
                $requestHash,
                Carbon::now()->addSeconds($ttl)
            );
        }
    }

    /**
     * @return array{
     *   status:int,
     *   headers:array<string,string|string[]>,
     *   body:mixed
     * }|null
     */
    public function getStoredResponse(string $key, ?string $requestHash = null): ?array
    {
        /** @var array<string,mixed>|null $cached */
        $cached = $this->cacheRepo()->get($this->respKey($key));

        if (! is_array($cached) || ! isset($cached['response'], $cached['response_fingerprint'])) {
            return null;
        }

        /** @var array{status:int, headers:array<string,string|string[]>, body:mixed} $response */
        $response = $cached['response'];

        $currentFingerprint = $this->responseFingerprint($response);
        $storedFingerprint = (string) $cached['response_fingerprint'];

        if ($storedFingerprint === '' || ! hash_equals($storedFingerprint, $currentFingerprint)) {
            return null;
        }

        if (is_string($requestHash) && $requestHash !== '') {
            $storedReq = (string) ($cached['request_hash'] ?? '');
            if ($storedReq !== '' && ! hash_equals($storedReq, $requestHash)) {
                return null; // mismatch -> let caller treat as conflict
            }
        }

        return $response;
    }

    private function ttlSeconds(): int
    {
        $ttl = config('tenrusl.idempotency.ttl_seconds');
        if ($ttl === null) {
            $ttl = config('tenrusl.idempotency_ttl', 3600);
        }

        $ttl = (int) $ttl;

        return $ttl > 0 ? $ttl : 3600;
    }

    private function cacheRepo(): CacheRepository
    {
        $storeName = config('tenrusl.idempotency.cache_store');

        if (is_string($storeName) && $storeName !== '') {
            return Cache::store($storeName);
        }

        return Cache::store();
    }

    private function lockKey(string $key): string
    {
        return 'tenrusl:idemp:lock:' . $this->keySlug($key);
    }

    private function respKey(string $key): string
    {
        return 'tenrusl:idemp:resp:' . $this->keySlug($key);
    }

    private function reqHashKey(string $key): string
    {
        return 'tenrusl:idemp:reqhash:' . $this->keySlug($key);
    }

    private function keySlug(string $key): string
    {
        $k = trim($key);
        if ($k === '') {
            return 'empty';
        }

        // Keep keys reasonably short and cache-safe
        if (strlen($k) > 160) {
            return 'sha_' . hash('sha256', $k);
        }

        return $k;
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

        $headers = array_map(
            static fn ($v) => is_array($v) ? Arr::sort($v) : $v,
            is_array($headers) ? $headers : []
        );

        ksort($headers);

        $payload = [
            'status' => (int) ($response['status'] ?? 200),
            'headers' => $headers,
            'body' => $response['body'] ?? null,
        ];

        $json = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
        );

        return hash('sha256', $json ?: '');
    }

    private function isProduction(): bool
    {
        $env = (string) config('app.env', 'production');

        return strtolower($env) === 'production';
    }

    private function hasControlChars(string $s): bool
    {
        return (bool) preg_match('/[\x00-\x1F\x7F]/', $s);
    }

    private function safeKeyForLog(string $key): string
    {
        if ($key === '') {
            return 'empty';
        }

        return strlen($key) <= 32 ? $key : substr($key, 0, 12) . '…' . substr($key, -8);
    }
}
