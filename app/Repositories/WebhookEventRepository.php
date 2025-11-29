<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\WebhookEvent;
use App\ValueObjects\PaymentStatus;
use Carbon\CarbonImmutable;

final class WebhookEventRepository
{
    public function findByProviderEvent(string $provider, string $eventId): ?WebhookEvent
    {
        /** @var WebhookEvent|null $e */
        $e = WebhookEvent::query()
            ->where('provider', $provider)
            ->where('event_id', $eventId)
            ->first();

        return $e;
    }

    /**
     * Simpan event baru (dedup berada di layer pemanggil).
     *
     * @param  array  $payload  Parsed payload (array)
     */
    public function storeNew(
        string $provider,
        string $eventId,
        ?string $eventType,
        string $rawBody,
        array $payload,
        ?CarbonImmutable $receivedAt = null
    ): WebhookEvent {
        $now = $receivedAt ?: CarbonImmutable::now();

        $event = new WebhookEvent;
        $event->provider = $provider;
        $event->event_id = $eventId;
        $event->event_type = $eventType;
        $event->payload_raw = $rawBody;
        $event->payload = $payload;
        $event->attempts = 1;
        $event->received_at = $now;
        $event->last_attempt_at = $now;
        $event->save();

        return $event;
    }

    /**
     * Tambah attempt & update cap waktu.
     */
    public function touchAttempt(WebhookEvent $event, ?CarbonImmutable $at = null): bool
    {
        $event->attempts = ($event->attempts ?? 0) + 1;
        $event->last_attempt_at = $at ?: CarbonImmutable::now();

        return $event->save();
    }

    /**
     * Tandai event sudah diproses, isi payment_ref & status jika ada.
     */
    public function markProcessed(
        WebhookEvent $event,
        ?string $paymentProviderRef,
        string|PaymentStatus|null $status,
        ?CarbonImmutable $processedAt = null
    ): bool {
        $event->payment_provider_ref = $paymentProviderRef;

        if ($status !== null) {
            $event->payment_status = $status instanceof PaymentStatus ? $status->value : $status;
        }

        $event->processed_at = $processedAt ?: CarbonImmutable::now();

        return $event->save();
    }

    /**
     * Jadwalkan retry berikutnya.
     */
    public function scheduleNextRetry(WebhookEvent $event, CarbonImmutable $nextAt): bool
    {
        $event->next_retry_at = $nextAt;

        return $event->save();
    }
}
