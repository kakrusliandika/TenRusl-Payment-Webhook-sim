<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\WebhookEvent;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class WebhookEventRepository
{
    public function findByProviderAndEventId(string $provider, string $eventId): ?WebhookEvent
    {
        return WebhookEvent::query()
            ->where('provider', $provider)
            ->where('event_id', $eventId)
            ->first();
    }

    public function createReceived(string $provider, string $eventId, ?string $signatureHash, array $payload): WebhookEvent
    {
        return WebhookEvent::query()->create([
            'provider'       => $provider,
            'event_id'       => $eventId,
            'signature_hash' => $signatureHash,
            'payload'        => $payload,
            'status'         => 'received',
            'attempt_count'  => 0,
            'next_retry_at'  => null,
            'error_message'  => null,
        ]);
    }

    public function markProcessed(WebhookEvent $event): WebhookEvent
    {
        $event->status = 'processed';
        $event->error_message = null;
        $event->save();

        return $event;
    }

    public function markFailedAndScheduleRetry(WebhookEvent $event, int $attempt, int $backoffSeconds, string $errorMessage): WebhookEvent
    {
        $event->status = 'failed';
        $event->attempt_count = $attempt;
        $event->next_retry_at = now()->addSeconds($backoffSeconds);
        $event->error_message = $errorMessage;
        $event->save();

        return $event;
    }

    /** Ambil event yang due untuk retry. @return Collection<int,WebhookEvent> */
    public function dueForRetry(int $limit = 50): Collection
    {
        return WebhookEvent::query()
            ->where('status', 'failed')
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '<=', now())
            ->orderBy('next_retry_at')
            ->limit($limit)
            ->get();
    }

    /** Bersihkan event processed lebih tua dari $days hari. @return int rows deleted */
    public function purgeProcessedOlderThan(int $days = 30): int
    {
        $threshold = CarbonImmutable::now()->subDays($days);
        return WebhookEvent::query()
            ->where('status', 'processed')
            ->where('created_at', '<', $threshold)
            ->delete();
    }
}
