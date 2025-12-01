<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessWebhookEvent;
use App\Models\WebhookEvent;
use App\Services\Webhooks\RetryBackoff;
use App\Services\Webhooks\WebhookProcessor;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetryWebhookCommand extends Command
{
    protected $signature = 'tenrusl:webhooks:retry
        {--provider= : Filter provider tertentu}
        {--limit=100 : Maksimal event yang diproses per run}
        {--max-attempts=5 : Batas percobaan sebelum berhenti retry}
        {--mode=full : Mode backoff (full|equal|decorrelated)}
        {--queue : Enqueue job ke queue (bukannya proses inline)}
    ';

    protected $description = 'Proses ulang event webhook yang due (berdasar next_retry_at & attempts) dengan locking + backoff.';

    public function handle(WebhookProcessor $processor): int
    {
        $provider = trim((string) $this->option('provider'));
        $limit = (int) $this->option('limit');
        $maxAttempts = (int) $this->option('max-attempts');
        $mode = RetryBackoff::normalizeMode((string) $this->option('mode'));
        $useQueue = (bool) $this->option('queue');

        // Sanitasi input
        $limit = $limit <= 0 ? 100 : min($limit, 2000);
        $maxAttempts = $maxAttempts <= 0 ? 1 : $maxAttempts;

        // Base/cap bisa diambil dari config/env (fallback aman kalau config belum ada)
        $baseMs = (int) config('tenrusl.retry_base_ms', 500);
        $capMs  = (int) config('tenrusl.retry_cap_ms', 30000);

        $now = CarbonImmutable::now();

        /**
         * Ambil batch event "due" + lakukan claim atomik:
         * - lock row (FOR UPDATE) di dalam transaction
         * - increment attempts
         * - set next_retry_at berdasarkan jitter/backoff (lease untuk menghindari double pick)
         */
        /** @var Collection<int, WebhookEvent> $events */
        $events = DB::transaction(function () use (
            $provider,
            $limit,
            $maxAttempts,
            $mode,
            $now,
            $baseMs,
            $capMs
        ): Collection {
            $q = WebhookEvent::query()
                ->where(function ($qq) {
                    $qq->whereNull('payment_status')
                        ->orWhere('payment_status', 'pending');
                })
                ->where(function ($qq) use ($now) {
                    $qq->whereNull('next_retry_at')
                        ->orWhere('next_retry_at', '<=', $now);
                })
                ->where('attempts', '<', $maxAttempts)
                // null next_retry_at diprioritaskan dulu, lalu yang paling cepat due
                ->orderByRaw('CASE WHEN next_retry_at IS NULL THEN 0 ELSE 1 END ASC, next_retry_at ASC')
                ->limit($limit)
                ->lockForUpdate();

            if ($provider !== '') {
                $q->where('provider', $provider);
            }

            /** @var Collection<int, WebhookEvent> $picked */
            $picked = $q->get();

            if ($picked->isEmpty()) {
                return $picked;
            }

            foreach ($picked as $event) {
                $nextAttempt = ((int) $event->attempts) + 1;

                // Jika ternyata sudah melewati batas (safety), skip update.
                if ($nextAttempt > $maxAttempts) {
                    continue;
                }

                $delayMs = RetryBackoff::compute(
                    attempt: $nextAttempt,
                    baseMs: $baseMs,
                    capMs: $capMs,
                    mode: $mode,
                    maxAttempts: $maxAttempts
                );

                $event->forceFill([
                    'attempts' => $nextAttempt,
                    'next_retry_at' => $now->addMilliseconds($delayMs),
                ])->save();
            }

            return $picked;
        }, attempts: 3);

        if ($events->isEmpty()) {
            $this->info('Tidak ada event yang perlu di-retry.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Memproses %d event (mode=%s, max_attempts=%d, limit=%d%s%s)...',
            $events->count(),
            $mode,
            $maxAttempts,
            $limit,
            $provider !== '' ? ", provider={$provider}" : '',
            $useQueue ? ', via queue' : ', inline'
        ));

        $bar = $this->output->createProgressBar($events->count());
        $bar->start();

        $ok = 0;
        $fail = 0;

        foreach ($events as $event) {
            try {
                if ($useQueue) {
                    // Dispatch job; event sudah di-claim + attempts/next_retry_at sudah diupdate.
                    ProcessWebhookEvent::dispatch($event->id);
                    $ok++;
                } else {
                    // Proses inline (sinkron).
                    $processor->process(
                        $event->provider,
                        $event->event_id,
                        $event->event_type ?? 'retry',
                        $event->payload_raw ?? '',
                        (array) $event->payload
                    );

                    // Optional: kalau status sudah bukan pending/null, bersihkan next_retry_at
                    // (biar tidak “nyangkut” kalau sebelumnya sudah diset ke future).
                    $event->refresh();
                    $status = $event->payment_status;
                    if ($status !== null && $status !== '' && $status !== 'pending') {
                        $event->forceFill(['next_retry_at' => null])->save();
                    }

                    $ok++;
                }
            } catch (Throwable $e) {
                $fail++;

                Log::error('RetryWebhookCommand: processing failed', [
                    'webhook_event_id' => $event->id,
                    'provider' => $event->provider,
                    'event_id' => $event->event_id,
                    'attempts' => $event->attempts,
                    'next_retry_at' => $event->next_retry_at,
                    'mode' => $mode,
                    'exception' => $e,
                ]);

                // next_retry_at sudah ter-set dengan backoff pada saat claim,
                // jadi di sini cukup lanjut event berikutnya.
            } finally {
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info(sprintf('Selesai. OK=%d, FAIL=%d', $ok, $fail));

        return $fail > 0 ? self::FAILURE : self::SUCCESS;
    }
}
