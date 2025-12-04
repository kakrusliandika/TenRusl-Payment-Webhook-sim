<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\WebhookEvent;
use App\ValueObjects\PaymentStatus;
use DateTimeInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

final class WebhookEventRepository
{
    /**
     * Ambil event berdasarkan dedup key (provider,event_id).
     *
     * Catatan:
     * - Jika $forUpdate=true, baris akan di-lock (pessimistic lock).
     * - Lock ini hanya efektif jika dipanggil di dalam DB transaction.
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
     */
    public function storeNewOrGetExisting(
        string $provider,
        string $eventId,
        ?string $eventType,
        string $rawBody,
        array $payload,
        ?DateTimeInterface $receivedAt = null,
        bool $lockExisting = true
    ): array {
        $now = $this->asCarbon($receivedAt) ?? now(); // now() => Illuminate\Support\Carbon :contentReference[oaicite:2]{index=2}

        try {
            $event = new WebhookEvent();

            $event->provider = $provider;
            $event->event_id = $eventId;

            $event->event_type = $eventType;

            $event->payload_raw = $rawBody;
            $event->payload = $payload;

            $event->status = 'received';

            $event->attempts = 1;
            $event->received_at = $now;
            $event->last_attempt_at = $now;

            $event->save();

            return [$event, false];
        } catch (QueryException $e) {
            if (! $this->isDuplicateKey($e)) {
                throw $e;
            }

            $existing = $this->findByProviderEvent($provider, $eventId, $lockExisting);

            if ($existing === null) {
                $existing = $this->findByProviderEvent($provider, $eventId, false);
            }

            if ($existing === null) {
                throw $e;
            }

            return [$existing, true];
        }
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
        ?DateTimeInterface $processedAt = null
    ): bool {
        $event->status = 'processed';
        $event->payment_provider_ref = $paymentProviderRef;

        // ✅ Pastikan property bertipe PaymentStatus|null tidak diisi string
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
     * Konversi DateTimeInterface (CarbonImmutable/DateTimeImmutable/etc) => Illuminate\Support\Carbon (mutable).
     *
     * Ini yang menghilangkan error:
     * - "CarbonImmutable does not accept Carbon" / "Cannot implicitly convert CarbonImmutable..."
     *
     * Eloquent date casting biasanya memakai Carbon (mutable), atau pakai immutable_datetime kalau mau immutability. :contentReference[oaicite:3]{index=3}
     */
    private function asCarbon(?DateTimeInterface $dt): ?Carbon
    {
        if ($dt === null) {
            return null;
        }

        if ($dt instanceof Carbon) {
            return $dt;
        }

        // Carbon bisa dibuat dari DateTimeInterface. :contentReference[oaicite:4]{index=4}
        return new Carbon($dt);
    }

    /**
     * Normalisasi PaymentStatus:
     * - jika sudah PaymentStatus => pakai apa adanya
     * - jika string => coba map ke enum/value-object
     * - jika invalid => null (biar tidak nabrak type PaymentStatus|null)
     *
     * Catatan: ini mengasumsikan PaymentStatus adalah backed-enum string (punya tryFrom()).
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

        // Backed enum: aman tanpa throw.
        return PaymentStatus::tryFrom($v);
    }

    /**
     * Helper: deteksi duplicate key lintas DB.
     *
     * - MySQL/MariaDB: SQLSTATE 23000 + driverCode 1062
     * - PostgreSQL:    SQLSTATE 23505
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
