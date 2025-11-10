<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\WebhookEvent;
use App\Services\Webhooks\WebhookProcessor;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class RetryWebhookCommand extends Command
{
    protected $signature = 'tenrusl:webhooks:retry
        {--provider= : Filter provider tertentu}
        {--limit=100 : Maksimal event yang diproses dalam satu run}
        {--max-attempts=5 : Batas percobaan sebelum berhenti retry}
        {--mode=full : Mode backoff (full|equal|decorrelated)}
    ';

    protected $description = 'Proses ulang event webhook yang masih pending (berdasar next_retry_at & attempts).';

    public function handle(WebhookProcessor $processor): int
    {
        $provider     = (string) $this->option('provider');
        $limit        = (int) $this->option('limit');
        $maxAttempts  = (int) $this->option('max-attempts');
        $now          = CarbonImmutable::now();

        $query = WebhookEvent::query()
            ->where(function ($q) {
                $q->whereNull('payment_status')
                  ->orWhere('payment_status', 'pending');
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('next_retry_at')
                  ->orWhere('next_retry_at', '<=', $now);
            })
            ->where('attempts', '<', $maxAttempts)
            ->orderBy('next_retry_at')
            ->limit($limit);

        if ($provider !== '') {
            $query->where('provider', $provider);
        }

        /** @var \Illuminate\Support\Collection<int, WebhookEvent> $events */
        $events = $query->get();

        if ($events->isEmpty()) {
            $this->info('Tidak ada event yang perlu di-retry.');
            return self::SUCCESS;
        }

        $this->info(sprintf('Memproses %d event...', $events->count()));
        $bar = $this->output->createProgressBar($events->count());
        $bar->start();

        foreach ($events as $event) {
            // Kirim ulang ke processor (idempotent & dedup aman)
            $processor->process(
                $event->provider,
                $event->event_id,
                $event->event_type ?? 'retry',
                $event->payload_raw ?? '',
                (array) $event->payload
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Selesai.');

        return self::SUCCESS;
    }
}
