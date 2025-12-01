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
     * Insert event baru; kalau kena unique-constraint (provider,event_id),
     * ambil row existing (dedup sukses).
     *
     * WAJIB dipanggil di dalam DB::transaction() kalau forUpdate=true ingin efektif.
     */
    public function storeNewOrGetExisting(
        string $provider,
        string $eventId,
        ?string $eventType,
        string $rawBody,
        array $payload,
        ?CarbonImmutable $receivedAt = null
    ): array {
        $now = $receivedAt ?: CarbonImmutable::now();

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

            return [$event, false]; // duplicate=false
        } catch (QueryException $e) {
            if (! $this->isDuplicateKey($e)) {
                throw $e;
            }

            // Duplicate key => fetch existing row w/ lock (caller should be in transaction)
            $existing = $this->findByProviderEvent($provider, $eventId, true);

            if ($existing === null) {
                // extremely rare race; retry fetch once without lock
                $existing = $this->findByProviderEvent($provider, $eventId, false);
            }

            if ($existing === null) {
                // If still missing, bubble up (something inconsistent)
                throw $e;
            }

            return [$existing, true]; // duplicate=true
        }
    }

    public function touchAttempt(WebhookEvent $event, ?CarbonImmutable $at = null): bool
    {
        $now = $at ?: CarbonImmutable::now();

        $event->attempts = ((int) ($event->attempts ?? 0)) + 1;
        $event->last_attempt_at = $now;

        return $event->save();
    }

    public function markProcessed(
        WebhookEvent $event,
        ?string $paymentProviderRef,
        string|PaymentStatus|null $status,
        ?CarbonImmutable $processedAt = null
    ): bool {
        $event->status = 'processed';
        $event->payment_provider_ref = $paymentProviderRef;

        if ($status !== null) {
            $event->payment_status = $status instanceof PaymentStatus ? $status->value : $status;
        }

        $event->processed_at = $processedAt ?: CarbonImmutable::now();
        $event->next_retry_at = null;
        $event->error_message = null;

        return $event->save();
    }

    public function markFailed(WebhookEvent $event, string $message, ?CarbonImmutable $at = null): bool
    {
        $event->status = 'failed';
        $event->error_message = $message;
        $event->last_attempt_at = $at ?: CarbonImmutable::now();

        return $event->save();
    }

    public function scheduleNextRetry(WebhookEvent $event, CarbonImmutable $nextAt, ?string $message = null): bool
    {
        $event->status = 'received';
        $event->next_retry_at = $nextAt;

        if ($message !== null) {
            $event->error_message = $message;
        }

        return $event->save();
    }

    private function isDuplicateKey(QueryException $e): bool
    {
        // SQLSTATE 23000 = integrity constraint violation (umum). :contentReference[oaicite:1]{index=1}
        $sqlState = $e->errorInfo[0] ?? null;
        if ($sqlState === '23000') {
            return true;
        }

        // MySQL duplicate entry: 1062
        $driverCode = $e->errorInfo[1] ?? null;

        return $driverCode === 1062;
    }
}
