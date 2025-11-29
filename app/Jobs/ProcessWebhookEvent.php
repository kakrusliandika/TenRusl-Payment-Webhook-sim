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

    /**
     * Nama queue khusus untuk webhook.
     * Untuk driver database, akan mengisi kolom `queue` pada tabel jobs.
     */
    public string $queue = 'webhooks';

    public function __construct(
        public string $webhookEventId
    ) {}

    public function handle(WebhookProcessor $processor): void
    {
        $event = WebhookEvent::find($this->webhookEventId);

        if (! $event) {
            return;
        }

        // Sama seperti jalur inline di command.
        $processor->process(
            $event->provider,
            $event->event_id,
            $event->event_type ?? 'retry',
            $event->payload_raw ?? '',
            (array) $event->payload
        );
    }
}
