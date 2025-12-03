<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\WebhookEvent;
use App\ValueObjects\PaymentStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

final class WebhookEventRepository
{
    /**
     * Ambil event berdasarkan dedup key (provider,event_id).
     *
     * Catatan:
     * - Jika $forUpdate=true, baris akan di-lock (pessimistic lock).
     * - Lock ini *hanya efektif* jika dipanggil di dalam DB::transaction(). :contentReference[oaicite:3]{index=3}
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
     * Insert event baru.
     * Jika terjadi duplicate key (unique provider+event_id), kita anggap dedup sukses:
     * - ambil row existing (opsional: lockForUpdate untuk konsistensi).
     *
     * Return:
     *   [WebhookEvent $event, bool $duplicate]
     *
     * Penting:
     * - Unique index gabungan provider+event_id dibuat di migration. :contentReference[oaicite:4]{index=4}
     * - Jika kamu mau benar-benar aman dari race, panggil method ini di dalam:
     *   DB::transaction(fn() => ... ) lalu set $lockExisting=true.
     */
    public function storeNewOrGetExisting(
        string $provider,
        string $eventId,
        ?string $eventType,
        string $rawBody,
        array $payload,
        ?CarbonImmutable $receivedAt = null,
        bool $lockExisting = true
    ): array {
        $now = $receivedAt ?: CarbonImmutable::now();

        try {
            $event = new WebhookEvent;

            // Dedup identity
            $event->provider = $provider;
            $event->event_id = $eventId;

            // Optional: subject/type event
            $event->event_type = $eventType;

            // Raw body + parsed JSON
            $event->payload_raw = $rawBody;
            $event->payload = $payload;

            // Status event lifecycle
            $event->status = 'received';

            // First attempt dianggap 1 (karena event diterima + kita catat sebagai upaya proses)
            $event->attempts = 1;
            $event->received_at = $now;
            $event->last_attempt_at = $now;

            $event->save();

            return [$event, false];
        } catch (QueryException $e) {
            // Bukan error duplicate? lempar lagi.
            if (! $this->isDuplicateKey($e)) {
                throw $e;
            }

            // Duplicate key => ambil event existing
            $existing = $this->findByProviderEvent($provider, $eventId, $lockExisting);

            // Edge case: kalau transaksi tidak dipakai, lockForUpdate bisa “no-op”,
            // tapi fetch tetap harus dapat row.
            if ($existing === null) {
                $existing = $this->findByProviderEvent($provider, $eventId, false);
            }

            if ($existing === null) {
                // Kondisi sangat jarang; biarkan error naik agar cepat ketahuan inconsistency
                throw $e;
            }

            return [$existing, true];
        }
    }

    /**
     * Tambah attempts & catat waktu attempt terakhir.
     * Ini dipanggil ketika:
     * - event duplicate datang lagi, atau
     * - retry scheduled diproses lagi.
     */
    public function touchAttempt(WebhookEvent $event, ?CarbonImmutable $at = null): bool
    {
        $now = $at ?: CarbonImmutable::now();

        $event->attempts = ((int) ($event->attempts ?? 0)) + 1;
        $event->last_attempt_at = $now;

        return $event->save();
    }

    /**
     * Tandai event sukses diproses:
     * - status = processed
     * - set payment_provider_ref & payment_status untuk audit
     * - bersihkan next_retry_at & error_message
     */
    public function markProcessed(
        WebhookEvent $event,
        ?string $paymentProviderRef,
        string|PaymentStatus|null $status,
        ?CarbonImmutable $processedAt = null
    ): bool {
        $event->status = 'processed';

        $event->payment_provider_ref = $paymentProviderRef;

        if ($status !== null) {
            // Jika PaymentStatus = backed enum => pakai ->value
            // Jika string => langsung
            $event->payment_status = $status instanceof PaymentStatus ? $status->value : $status;
        }

        $event->processed_at = $processedAt ?: CarbonImmutable::now();

        // Clear retry markers
        $event->next_retry_at = null;
        $event->error_message = null;

        return $event->save();
    }

    /**
     * Tandai event gagal diproses (mis. throw di domain):
     * - status = failed
     * - simpan error_message
     *
     * Catatan:
     * - Pada simulator, kamu boleh pilih:
     *   a) failed = berhenti total
     *   b) failed = status sementara + retry (kalau attempts < maxAttempts)
     *   Yang menentukan adalah scheduler/command + WebhookProcessor.
     */
    public function markFailed(WebhookEvent $event, string $message, ?CarbonImmutable $at = null): bool
    {
        $event->status = 'failed';
        $event->error_message = $message;
        $event->last_attempt_at = $at ?: CarbonImmutable::now();

        return $event->save();
    }

    /**
     * Jadwalkan retry berikutnya.
     * - status kembali ke 'received' (masih menunggu diproses)
     * - next_retry_at di-set
     * - error_message opsional dicatat untuk observability
     */
    public function scheduleNextRetry(WebhookEvent $event, CarbonImmutable $nextAt, ?string $message = null): bool
    {
        $event->status = 'received';
        $event->next_retry_at = $nextAt;

        if ($message !== null) {
            $event->error_message = $message;
        }

        return $event->save();
    }

    /**
     * Helper: mendeteksi error duplicate key lintas DB.
     *
     * - MySQL/MariaDB:
     *   SQLSTATE 23000 + driverCode 1062
     * - PostgreSQL:
     *   SQLSTATE 23505 (unique_violation)
     */
    private function isDuplicateKey(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? null;
        if (in_array($sqlState, ['23000', '23505'], true)) {
            return true;
        }

        // MySQL duplicate entry
        $driverCode = $e->errorInfo[1] ?? null;

        return $driverCode === 1062;
    }
}
