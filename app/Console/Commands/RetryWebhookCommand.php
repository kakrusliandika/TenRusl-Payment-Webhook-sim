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

/**
 * Retry engine:
 * - pilih event webhook yg "due" (next_retry_at <= now atau null)
 * - lakukan claiming atomik (lockForUpdate + update attempts/last_attempt_at/next_retry_at)
 * - proses inline atau dispatch job ke queue
 *
 * Kenapa harus ada claiming?
 * - supaya scheduler multi-run / multi-worker tidak memproses event yg sama bersamaan.
 * - next_retry_at berperan sebagai "lease" sekaligus jadwal retry berikutnya.
 *
 * Catatan:
 * - lockForUpdate wajib di dalam transaction agar benar-benar mengunci row. :contentReference[oaicite:2]{index=2}
 */
class RetryWebhookCommand extends Command
{
    protected $signature = 'tenrusl:webhooks:retry
        {--provider= : Filter provider tertentu}
        {--limit=100 : Maksimal event yang diproses per run}
        {--max-attempts=5 : Batas attempt sebelum berhenti retry}
        {--mode=full : Mode backoff (full|equal|decorrelated)}
        {--queue : Dispatch ke queue (bukan proses inline)}
    ';

    protected $description = 'Retry webhooks yang due (next_retry_at) dengan locking + exponential backoff + jitter.';

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

        // Base/cap dari config (fallback aman kalau config belum ada)
        $baseMs = (int) config('tenrusl.retry_base_ms', 500);
        $capMs = (int) config('tenrusl.retry_cap_ms', 30000);

        // Lease minimum biar tidak kepilih ulang "langsung" (karena full jitter bisa 0ms).
        $minLeaseMs = (int) config('tenrusl.retry_min_lease_ms', 250);

        $now = CarbonImmutable::now();

        /**
         * Ambil batch event "due" + claim atomik:
         * - lock row (FOR UPDATE)
         * - increment attempts
         * - update last_attempt_at
         * - set next_retry_at = now + delay (delay = backoff berdasar attempt)
         *
         * Ini bikin retry periodik tidak “mandek” dan tidak double-process.
         */
        /** @var Collection<int, WebhookEvent> $events */
        $events = DB::transaction(function () use (
            $provider,
            $limit,
            $maxAttempts,
            $mode,
            $now,
            $baseMs,
            $capMs,
            $minLeaseMs
        ): Collection {
            $q = WebhookEvent::query()
                // Jangan sentuh yang sudah final processed
                ->where('status', '!=', 'processed')
                // Yang masih pending / belum punya status inferred
                ->where(function ($qq) {
                    $qq->whereNull('payment_status')
                        ->orWhere('payment_status', 'pending');
                })
                // Due sekarang atau belum pernah dischedule
                ->where(function ($qq) use ($now) {
                    $qq->whereNull('next_retry_at')
                        ->orWhere('next_retry_at', '<=', $now);
                })
                // Batasi retry
                ->where('attempts', '<', $maxAttempts)
                // Prioritas: yang belum pernah dischedule (NULL) dulu, lalu yang paling cepat due
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
                $currentAttempts = (int) ($event->attempts ?? 0);
                $nextAttempt = $currentAttempts + 1;

                // Safety: jangan lewat batas
                if ($nextAttempt > $maxAttempts) {
                    continue;
                }

                // Delay untuk "setelah attempt ini" (attempt number = nextAttempt)
                $delayMs = RetryBackoff::compute(
                    attempt: $nextAttempt,
                    baseMs: $baseMs,
                    capMs: $capMs,
                    mode: $mode,
                    maxAttempts: $maxAttempts
                );

                // Lease minimum untuk menghindari kepilih ulang instan
                $leaseMs = max($minLeaseMs, $delayMs);

                // Claim row: update attempts + last_attempt_at + next_retry_at (lease/jadwal)
                $event->forceFill([
                    'attempts' => $nextAttempt,
                    'last_attempt_at' => $now,
                    'next_retry_at' => $now->addMilliseconds($leaseMs),
                ])->save();
            }

            return $picked;
        }, 3); // attempts=3 -> auto retry transaction jika deadlock :contentReference[oaicite:3]{index=3}

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
                    /**
                     * Jalur queue:
                     * - Pastikan worker jalan: `php artisan queue:work --queue=webhooks`
                     * - Event sudah di-claim (attempts/next_retry_at sudah diupdate)
                     */
                    ProcessWebhookEvent::dispatch($event->id);
                    $ok++;
                } else {
                    /**
                     * Jalur inline (sinkron):
                     * - Kirim type='retry' agar processor tidak menaikkan attempts lagi.
                     * - attempts sudah dinaikkan saat claim.
                     */
                    $processor->process(
                        $event->provider,
                        $event->event_id,
                        'retry',
                        (string) ($event->payload_raw ?? ''),
                        (array) ($event->payload ?? [])
                    );

                    // Kalau sudah final, idealnya processor sudah set status=processed & next_retry_at=null.
                    // Tapi kita defensif: kalau sudah bukan pending, bersihkan next_retry_at.
                    $event->refresh();
                    $final = (string) ($event->payment_status ?? '');
                    if ($final !== '' && $final !== 'pending') {
                        $event->forceFill([
                            'status' => 'processed',
                            'processed_at' => $event->processed_at ?? CarbonImmutable::now(),
                            'next_retry_at' => null,
                            'error_message' => null,
                        ])->save();
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

                // Catat gagal supaya bisa ditrace; next_retry_at sudah diset saat claim.
                try {
                    $event->forceFill([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ])->save();
                } catch (Throwable) {
                    // Jangan bikin command crash hanya karena gagal update error_message
                }
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
