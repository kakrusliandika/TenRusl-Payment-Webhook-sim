<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\WebhookEvent;
use App\Services\Webhooks\WebhookProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWebhookEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $queue = 'webhooks';

    public function __construct(
        public string $webhookEventId
    ) {}

    public function handle(WebhookProcessor $processor): void
    {
        /** @var WebhookEvent|null $event */
        $event = WebhookEvent::find($this->webhookEventId);

        if (! $event) {
            return;
        }

        // Guard: kalau sudah processed & tidak pending, skip (idempotent terhadap duplicate job)
        if ($event->status === 'processed' && ($event->payment_status ?? null) !== 'pending') {
            return;
        }

        $processor->process(
            $event->provider,
            $event->event_id,
            $event->event_type ?? 'retry',
            $event->payload_raw ?? '',
            (array) $event->payload
        );
    }
}
