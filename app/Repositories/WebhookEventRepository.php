<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Payment;
use App\Models\WebhookEvent;
use App\ValueObjects\PaymentStatus;
use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

final class WebhookEventRepository
{
    /**
     * Ambil event berdasarkan dedup key (provider,event_id).
     *
     * Catatan:
     * - Jika $forUpdate=true, baris akan di-lock (pessimistic lock).
     * - Lock efektif jika dipanggil di dalam DB transaction.
     */
    public function findByProviderEvent(string $provider, string $eventId, bool $forUpdate = false): ?WebhookEvent
    {
        $q = WebhookEvent::query()
            ->where('provider', $provider)
            ->where('event_id', $eventId);

        if ($forUpdate) {
            $q->lockForUpdate();
        }

        /** @var WebhookEvent|null $e */
        $e = $q->first();

        return $e;
    }

    /**
     * Insert event baru, atau kalau duplicate key (unique provider+event_id) maka ambil event existing.
     *
     * Return: [WebhookEvent $event, bool $duplicate]
     *
     * Definisi attempts (konsisten untuk operasi & UI):
     * - attempts menghitung "handling count" (penerimaan + retry/processing attempt).
     * - Event baru saat diterima: attempts = 1 dan last_attempt_at = received_at.
     *
     * Signature audit:
     * - webhook_events.signature_hash DIISI saat pertama kali disimpan.
     * - Best-effort: hash dari material signature (header/body signature) tanpa menyimpan secret.
     *
     * @return array{0: WebhookEvent, 1: bool}
     */
    public function storeNewOrGetExisting(
        string $provider,
        string $eventId,
        ?string $eventType,
        string $rawBody,
        array $payload,
        ?DateTimeInterface $receivedAt = null,
        bool $lockExisting = true,
        ?string $requestId = null,
        ?string $sourceIp = null,
        array $headers = [],
        ?string $signatureHash = null,
        ?string $signatureSource = null,
    ): array {
        $now = $this->asCarbon($receivedAt) ?? now();

        try {
            $event = new WebhookEvent;

            $event->provider = $provider;
            $event->event_id = $eventId;
            $event->event_type = $eventType;

            $event->payload_raw = $rawBody;
            $event->payload = $payload;

            // -------------------------
            // Audit fields (best-effort)
            // -------------------------
            // request_id: correlation id dari middleware (untuk tracing end-to-end)
            if (is_string($requestId) && trim($requestId) !== '') {
                $event->request_id = trim($requestId);
            }

            // source_ip: ip setelah TrustProxies benar (proxy/LB)
            if (is_string($sourceIp) && trim($sourceIp) !== '') {
                $event->source_ip = trim($sourceIp);
            }

            // headers: simpan subset aman saja (controller wajib sanitize)
            if ($headers !== []) {
                $event->headers = $this->sanitizeHeaders($headers);
            }

            $event->status = 'received';

            // attempts menghitung penerimaan + handling
            $event->attempts = 1;

            $event->received_at = $now;
            $event->last_attempt_at = $now;

            // Audit signature hash (best-effort, tanpa menyimpan secret)
            // Prefer fingerprint dari middleware VerifyWebhookSignature.
            $event->signature_hash = $this->normalizeSignatureHash(
                $signatureHash
                ?? $this->computeSignatureHash($provider, $payload, $rawBody)
            );

            if (is_string($signatureSource) && trim($signatureSource) !== '') {
                $event->signature_source = trim($signatureSource);
            }

            $event->save();

            return [$event, false];
        } catch (QueryException $e) {
            if (! $this->isDuplicateKey($e)) {
                throw $e;
            }

            $existing = $this->findByProviderEvent($provider, $eventId, $lockExisting);

            if ($existing === null) {
                $existing = $this->findByProviderEvent($provider, $eventId);
            }

            if ($existing === null) {
                // Jika benar-benar tidak ketemu tapi error duplicate, biarkan error aslinya naik
                throw $e;
            }

            return [$existing, true];
        }
    }

    /**
     * Kaitkan event ke Payment (untuk monitoring).
     *
     * Catatan:
     * - Skema DB simulator memakai payment_provider_ref.
     * - Relasi Eloquent bisa disesuaikan di Model bila mau eager-load based on provider_ref.
     */
    public function attachToPayment(WebhookEvent $event, Payment $payment): bool
    {
        $event->payment_provider_ref = (string) $payment->provider_ref;

        return $event->save();
    }

    /**
     * Tambah attempts & catat waktu attempt terakhir.
     */
    public function touchAttempt(WebhookEvent $event, ?DateTimeInterface $at = null): bool
    {
        $now = $this->asCarbon($at) ?? now();

        $event->attempts = ((int) ($event->attempts ?? 0)) + 1;
        $event->last_attempt_at = $now;

        return $event->save();
    }

    /**
     * Tandai event sukses diproses.
     */
    public function markProcessed(
        WebhookEvent $event,
        ?string $paymentProviderRef,
        PaymentStatus|string|null $status,
        ?DateTimeInterface $processedAt = null,
    ): bool {
        $event->status = 'processed';
        $event->payment_provider_ref = $paymentProviderRef;
        $event->payment_status = $this->normalizePaymentStatus($status);

        $event->processed_at = $this->asCarbon($processedAt) ?? now();

        $event->next_retry_at = null;
        $event->error_message = null;

        return $event->save();
    }

    /**
     * Tandai event gagal diproses.
     */
    public function markFailed(WebhookEvent $event, string $message, ?DateTimeInterface $at = null): bool
    {
        $event->status = 'failed';
        $event->error_message = $message;
        $event->last_attempt_at = $this->asCarbon($at) ?? now();

        return $event->save();
    }

    /**
     * Jadwalkan retry berikutnya.
     */
    public function scheduleNextRetry(WebhookEvent $event, DateTimeInterface $nextAt, ?string $message = null): bool
    {
        $event->status = 'received';
        $event->next_retry_at = $this->asCarbon($nextAt) ?? now();

        if ($message !== null) {
            $event->error_message = $message;
        }

        return $event->save();
    }

    /**
     * Helper demo admin: paksa retry segera (next_retry_at=now, status=received).
     */
    public function retryNow(WebhookEvent $event, ?string $message = null): bool
    {
        $event->status = 'received';
        $event->next_retry_at = now();

        if ($message !== null) {
            $event->error_message = $message;
        }

        return $event->save();
    }

    /**
     * Admin list untuk UI monitoring (pagination + filter).
     *
     * Filters supported:
     * - provider
     * - status
     * - min_attempts
     * - max_attempts
     * - due_only (bool-ish: 1/true)
     * - q (search event_id)
     *
     * Default ordering: terbaru dulu.
     */
    public function paginateAdmin(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = WebhookEvent::query()
            ->with(['payment'])
            ->orderByDesc('created_at');

        $provider = $filters['provider'] ?? null;
        if (is_string($provider) && $provider !== '') {
            $query->where('provider', $provider);
        }

        $status = $filters['status'] ?? null;
        if (is_string($status) && $status !== '') {
            $query->where('status', $status);
        }

        $minAttempts = $filters['min_attempts'] ?? null;
        if (is_numeric($minAttempts)) {
            $query->where('attempts', '>=', (int) $minAttempts);
        }

        $maxAttempts = $filters['max_attempts'] ?? null;
        if (is_numeric($maxAttempts)) {
            $query->where('attempts', '<=', (int) $maxAttempts);
        }

        $q = $filters['q'] ?? null;
        if (is_string($q) && $q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub->where('event_id', 'like', "%{$q}%");
            });
        }

        $dueOnly = $filters['due_only'] ?? null;
        if ($this->truthy($dueOnly)) {
            $query->where(function (Builder $sub) {
                $sub->whereNull('next_retry_at')
                    ->orWhere('next_retry_at', '<=', now());
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Ambil event yang sudah jatuh tempo untuk retry (untuk command/scheduler).
     */
    public function findDueRetries(int $limit = 50, ?DateTimeInterface $now = null): iterable
    {
        $now = $this->asCarbon($now) ?? now();

        return WebhookEvent::query()
            ->where('status', 'received')
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '<=', $now)
            ->orderBy('next_retry_at')
            ->limit($limit)
            ->get();
    }

    private function truthy(mixed $v): bool
    {
        if (is_bool($v)) {
            return $v;
        }
        if (is_int($v)) {
            return $v === 1;
        }
        if (is_string($v)) {
            $t = strtolower(trim($v));

            return in_array($t, ['1', 'true', 'yes', 'y', 'on'], true);
        }

        return false;
    }

    /**
     * Konversi DateTimeInterface => Carbon.
     */
    private function asCarbon(?DateTimeInterface $dt): ?Carbon
    {
        if ($dt === null) {
            return null;
        }
        if ($dt instanceof Carbon) {
            return $dt;
        }

        return new Carbon($dt);
    }

    /**
     * Normalisasi PaymentStatus:
     * - jika sudah PaymentStatus => pakai apa adanya
     * - jika string => coba map ke enum/value-object
     * - jika invalid => null
     *
     * Catatan: mengasumsikan PaymentStatus adalah backed-enum string (punya tryFrom()).
     */
    private function normalizePaymentStatus(PaymentStatus|string|null $status): ?PaymentStatus
    {
        if ($status === null) {
            return null;
        }

        if ($status instanceof PaymentStatus) {
            return $status;
        }

        $v = trim($status);
        if ($v === '') {
            return null;
        }

        return PaymentStatus::tryFrom($v);
    }

    /**
     * Best-effort signature material -> sha256 hash (tanpa menyimpan secret).
     *
     * Prioritas:
     * 1) signature di payload (contoh Midtrans: signature_key)
     * 2) jika signature header sudah difingerprint oleh middleware VerifyWebhookSignature,
     *    gunakan nilai itu (dipassing sebagai parameter $signatureHash saat store)
     * 3) fallback: body fingerprint (tetap berguna untuk audit) dengan prefix "body:"
     */
    private function computeSignatureHash(string $provider, array $payload, string $rawBody): string
    {
        $provider = strtolower(trim($provider));

        // Midtrans: signature_key ada di body
        if ($provider === 'midtrans') {
            $sigKey = $payload['signature_key'] ?? null;
            if (is_string($sigKey) && trim($sigKey) !== '') {
                return hash('sha256', 'midtrans:'.trim($sigKey));
            }
        }

        // Skrill: md5sig/sha2sig ada di form body
        if ($provider === 'skrill') {
            $md5sig = $payload['md5sig'] ?? null;
            $sha2sig = $payload['sha2sig'] ?? null;

            if (is_string($sha2sig) && trim($sha2sig) !== '') {
                return hash('sha256', 'skrill:sha2:'.trim($sha2sig));
            }
            if (is_string($md5sig) && trim($md5sig) !== '') {
                return hash('sha256', 'skrill:md5:'.trim($md5sig));
            }
        }

        // Untuk provider lain, kita tidak menyimpan raw signature header (bisa sensitif).
        // Fingerprint signature header seharusnya diambil dari middleware VerifyWebhookSignature
        // dan dipassing sebagai $signatureHash. Kalau tidak ada, fallback ke body fingerprint.
        return hash('sha256', 'body:'.$rawBody);
    }

    /**
     * Normalisasi signature hash (sha256 hex) agar konsisten.
     */
    private function normalizeSignatureHash(string $hash): string
    {
        $h = strtolower(trim($hash));

        // Jika bukan hex sha256, tetap hash ulang agar stabil.
        if ($h === '' || ! preg_match('/^[a-f0-9]{64}$/', $h)) {
            return hash('sha256', 'sig:'.$hash);
        }

        return $h;
    }

    /**
     * Sanitasi headers untuk audit:
     * - simpan hanya string scalar
     * - buang header yang berpotensi mengandung secret/credential
     * - batasi panjang value
     *
     * @param  array<string, mixed>  $headers
     * @return array<string, string>
     */
    private function sanitizeHeaders(array $headers): array
    {
        $deny = [
            'authorization',
            'cookie',
            'set-cookie',
            'x-api-key',
            'api-key',
            'x-callback-token',
            'x-callback-signature',
            'stripe-signature',
            'paddle-signature',
            'signature',
        ];

        $out = [];

        foreach ($headers as $k => $v) {
            $key = strtolower(trim((string) $k));
            if ($key === '' || in_array($key, $deny, true)) {
                continue;
            }

            // Flatten common header shapes (string|array)
            if (is_array($v)) {
                $v = implode(',', array_map(static fn ($x) => (string) $x, $v));
            }

            if (! is_string($v)) {
                $v = (string) $v;
            }

            $val = trim($v);
            if ($val === '') {
                continue;
            }

            // Prevent giant blobs
            if (strlen($val) > 512) {
                $val = substr($val, 0, 512).'...';
            }

            $out[$key] = $val;
        }

        ksort($out);

        return $out;
    }

    /**
     * Helper: deteksi duplicate key lintas DB.
     *
     * - MySQL/MariaDB: SQLSTATE 23000 + driverCode 1062
     * - PostgreSQL:    SQLSTATE 23505
     * - SQLite:        SQLSTATE 23000 (constraint)
     */
    private function isDuplicateKey(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? null;
        if (in_array($sqlState, ['23000', '23505'], true)) {
            return true;
        }

        $driverCode = $e->errorInfo[1] ?? null;

        return $driverCode === 1062;
    }
}
