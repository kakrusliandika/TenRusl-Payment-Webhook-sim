<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessWebhookEvent;
use App\Models\WebhookEvent;
use App\Services\Webhooks\RetryBackoff;
use App\Services\Webhooks\WebhookProcessor;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Retry engine:
 * - pilih event webhook yg "eligible" (pending + belum processed)
 * - lakukan claiming atomik (lockForUpdate + update attempts/last_attempt_at/next_retry_at)
 * - proses inline atau dispatch job ke queue
 *
 * Tambahan operasional:
 * - concurrency guard (distributed lock) supaya hanya 1 runner jalan pada satu waktu.
 *
 * Catatan:
 * - lockForUpdate wajib di dalam transaction agar benar-benar mengunci row.
 * - Cache lock disarankan memakai store shared (redis/database/memcached) untuk multi-instance.
 */
class RetryWebhookCommand extends Command
{
    protected $signature = 'tenrusl:webhooks:retry
        {--provider= : Filter provider tertentu}
        {--id= : Proses satu webhook_events.id (ULID/UUID) saja}
        {--force : Abaikan constraint next_retry_at (retry now), tetap hormati final status}
        {--limit=100 : Maksimal event yang diproses per run (abaikan jika --id dipakai)}
        {--max-attempts=5 : Batas attempt sebelum berhenti retry}
        {--mode=full : Mode backoff (full|equal|decorrelated)}
        {--queue : Dispatch ke queue (bukan proses inline)}
        {--queue-name=webhooks : Nama queue (default webhooks)}
    ';

    protected $description = 'Retry webhooks yang due (next_retry_at) dengan locking + capped exponential backoff + jitter.';

    public function handle(WebhookProcessor $processor): int
    {
        $provider = trim((string) $this->option('provider'));
        $specificId = trim((string) $this->option('id'));
        $force = (bool) $this->option('force');

        $limit = (int) $this->option('limit');
        $maxAttempts = (int) $this->option('max-attempts');
        $mode = RetryBackoff::normalizeMode((string) $this->option('mode'));

        // =========================================================
        // Mode queue default di production (bisa dimatikan via config)
        // =========================================================
        $useQueue = (bool) $this->option('queue');
        $defaultQueueInProd = (bool) config('tenrusl.retry_command_default_queue', app()->environment('production'));
        if (! $useQueue && $defaultQueueInProd) {
            $useQueue = true;
        }

        $queueName = trim((string) $this->option('queue-name'));
        if ($queueName === '') {
            // Satu sumber kebenaran: pakai webhook_queue (dipakai juga oleh controller/job).
            $queueName = (string) config('tenrusl.webhook_queue', 'webhooks');
            $queueName = trim($queueName) !== '' ? trim($queueName) : 'webhooks';
        }

        // Sanitasi input
        $limit = $limit <= 0 ? 100 : min($limit, 2000);
        $maxAttempts = $maxAttempts <= 0 ? 1 : $maxAttempts;

        // Base/cap dari config (fallback aman kalau config belum ada)
        $baseMs = (int) config('tenrusl.retry_base_ms', 500);
        $capMs = (int) config('tenrusl.retry_cap_ms', 30000);

        // Lease minimum biar tidak kepilih ulang "instan" (karena full jitter bisa 0ms).
        $minLeaseMs = (int) config('tenrusl.retry_min_lease_ms', 250);

        // Processing lease (detik): bila status processing/queued terlalu lama, anggap stale dan boleh di-claim ulang.
        $processingLeaseSeconds = (int) config('tenrusl.processing_lease_seconds', 60);
        $processingLeaseSeconds = $processingLeaseSeconds <= 0 ? 60 : $processingLeaseSeconds;

        $now = CarbonImmutable::now();
        $staleCutoff = $now->subSeconds($processingLeaseSeconds);

        // =========================================================
        // Ops counters
        // =========================================================
        $claimedCount = 0;
        $queuedCount = 0;
        $inlineOk = 0;
        $inlineFail = 0;

        // =========================================================
        // Concurrency guard: hanya 1 runner
        // =========================================================
        $runLock = $this->acquireRunLock($provider, $specificId);
        if ($runLock instanceof Lock) {
            if (! $runLock->get()) {
                $this->info('Runner lain sedang memproses batch retry. Keluar (no-op).');

                return self::SUCCESS;
            }
        }

        try {
            /** @var Collection<int, WebhookEvent> $events */
            if ($specificId !== '') {
                $claimed = $this->claimOne(
                    id: $specificId,
                    provider: $provider,
                    maxAttempts: $maxAttempts,
                    mode: $mode,
                    now: $now,
                    staleCutoff: $staleCutoff,
                    baseMs: $baseMs,
                    capMs: $capMs,
                    minLeaseMs: $minLeaseMs,
                    force: $force
                );

                $events = $claimed ? collect([$claimed]) : collect();
            } else {
                $events = $this->claimBatch(
                    provider: $provider,
                    limit: $limit,
                    maxAttempts: $maxAttempts,
                    mode: $mode,
                    now: $now,
                    staleCutoff: $staleCutoff,
                    baseMs: $baseMs,
                    capMs: $capMs,
                    minLeaseMs: $minLeaseMs,
                    force: $force
                );
            }

            $claimedCount = $events->count();

            if ($events->isEmpty()) {
                $this->info('Tidak ada event yang perlu di-retry.');

                Log::info('RetryWebhookCommand: no-op', [
                    'provider' => $provider,
                    'id' => $specificId,
                    'force' => $force,
                    'limit' => $limit,
                    'max_attempts' => $maxAttempts,
                    'mode' => $mode,
                    'use_queue' => $useQueue,
                    'queue' => $queueName,
                ]);

                return self::SUCCESS;
            }

            $this->info(sprintf(
                'Memproses %d event (mode=%s, max_attempts=%d%s%s%s)...',
                $claimedCount,
                $mode,
                $maxAttempts,
                $provider !== '' ? ", provider={$provider}" : '',
                $specificId !== '' ? ", id={$specificId}" : '',
                $useQueue ? ", via queue={$queueName}" : ', inline'
            ));

            $bar = $this->output->createProgressBar($claimedCount);
            $bar->start();

            foreach ($events as $event) {
                try {
                    if ($useQueue) {
                        /**
                         * Jalur queue:
                         * - Event sudah di-claim (attempts/next_retry_at sudah diupdate)
                         * - Job harus idempotent & punya guard (unique + overlap + db claim)
                         */
                        ProcessWebhookEvent::dispatch((string) $event->id, 'retry', $force)
                            ->onQueue($queueName);

                        // Optional: tandai status queued supaya UI admin bisa bedain
                        $markQueued = (bool) config('tenrusl.retry_mark_queued_status', true);
                        if ($markQueued) {
                            $event->forceFill([
                                'status' => 'queued',
                            ])->save();
                        }

                        $queuedCount++;
                    } else {
                        /**
                         * Jalur inline (sinkron):
                         * - type='retry' agar WebhookProcessor menandai ini internal retry.
                         * - attempts sudah dinaikkan saat claim, processor tidak menaikkan lagi untuk retry.
                         */
                        $processor->process(
                            $event->provider,
                            $event->event_id,
                            'retry',
                            (string) ($event->payload_raw ?? ''),
                            (array) ($event->payload ?? [])
                        );

                        // Defensif finalize bila sudah final
                        $event->refresh();
                        $final = strtolower((string) ($event->payment_status ?? ''));
                        if ($final !== '' && $final !== 'pending') {
                            $event->forceFill([
                                'status' => 'processed',
                                'processed_at' => $event->processed_at ?? CarbonImmutable::now(),
                                'next_retry_at' => null,
                                'error_message' => null,
                            ])->save();
                        } else {
                            // masih pending => kembalikan ke received, next_retry_at sudah terset (backoff + jitter)
                            $event->forceFill([
                                'status' => 'received',
                            ])->save();
                        }

                        $inlineOk++;
                    }
                } catch (Throwable $e) {
                    if (! $useQueue) {
                        $inlineFail++;
                    }

                    Log::error('RetryWebhookCommand: processing failed', [
                        'webhook_event_id' => $event->id,
                        'provider' => $event->provider,
                        'event_id' => $event->event_id,
                        'attempts' => $event->attempts,
                        'next_retry_at' => $event->next_retry_at,
                        'mode' => $mode,
                        'useQueue' => $useQueue,
                        'queue' => $queueName,
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

            if ($useQueue) {
                $this->info(sprintf(
                    'Selesai. CLAIMED=%d, QUEUED=%d',
                    $claimedCount,
                    $queuedCount
                ));
            } else {
                $this->info(sprintf(
                    'Selesai. CLAIMED=%d, OK=%d, FAIL=%d',
                    $claimedCount,
                    $inlineOk,
                    $inlineFail
                ));
            }

            Log::info('RetryWebhookCommand: summary', [
                'provider' => $provider,
                'id' => $specificId,
                'force' => $force,
                'limit' => $limit,
                'claimed' => $claimedCount,
                'max_attempts' => $maxAttempts,
                'mode' => $mode,
                'use_queue' => $useQueue,
                'queue' => $queueName,
                'queued' => $queuedCount,
                'inline_ok' => $inlineOk,
                'inline_fail' => $inlineFail,
            ]);

            // Jalur inline: kalau ada fail, return FAILURE.
            // Jalur queue: return SUCCESS (job failure dikelola worker).
            if ($useQueue) {
                return self::SUCCESS;
            }

            return $inlineFail > 0 ? self::FAILURE : self::SUCCESS;
        } finally {
            // release run lock
            try {
                if ($runLock instanceof Lock) {
                    $runLock->release();
                }
            } catch (Throwable) {
                // ignore
            }
        }
    }

    /**
     * Acquire distributed run lock.
     * Jika store tidak mendukung lock, return null (skip guard).
     */
    private function acquireRunLock(string $provider, string $specificId): ?Lock
    {
        $seconds = (int) config('tenrusl.retry_command_lock_seconds', 900);

        $key = $this->runLockKey($provider, $specificId);

        $storeName = (string) config('tenrusl.retry_command_lock_store', '');
        $storeName = trim($storeName);

        try {

                $cache = $storeName !== '' ? Cache::store($storeName) : Cache::store();

                $store = $cache->getStore();
                if ($store instanceof \Illuminate\Contracts\Cache\LockProvider) {
                    return $store->lock($key, $seconds);
                }

                return null;

        } catch (Throwable) {
            // skip lock jika error
        }

        return null;
    }

    private function runLockKey(string $provider, string $specificId): string
    {
        // Global lock by default.
        return 'tenrusl:cmd:webhooks:retry:global';
    }

    /**
     * Claim batch event "eligible" + update attempts/lease/jadwal atomik.
     *
     * @return Collection<int, WebhookEvent>
     */
    protected function claimBatch(
        string $provider,
        int $limit,
        int $maxAttempts,
        string $mode,
        CarbonImmutable $now,
        CarbonImmutable $staleCutoff,
        int $baseMs,
        int $capMs,
        int $minLeaseMs,
        bool $force
    ): Collection {
        /** @var Collection<int, WebhookEvent> $events */
        $events = DB::transaction(function () use (
            $provider,
            $limit,
            $maxAttempts,
            $mode,
            $now,
            $staleCutoff,
            $baseMs,
            $capMs,
            $minLeaseMs,
            $force
        ): Collection {
            $q = WebhookEvent::query()

                // Jangan sentuh yang sudah final processed
                ->where('status', '!=', 'processed')

                // Yang masih pending / belum punya status inferred
                ->where(function ($qq) {
                    $qq->whereNull('payment_status')
                        ->orWhere('payment_status', 'pending');
                })

                // Batasi retry
                ->where('attempts', '<', $maxAttempts);

            // Provider filter
            if ($provider !== '') {
                $q->where('provider', $provider);
            }

            // Eligibility: normal vs stale recovery
            if (! $force) {
                // A) Jangan ganggu processing/queued yang masih fresh.
                // B) Tapi jika stale (last_attempt_at terlalu lama), boleh diambil lagi.
                $q->where(function ($qq) use ($staleCutoff) {
                    $qq->whereNotIn('status', ['processing', 'queued'])
                        ->orWhere(function ($q2) use ($staleCutoff) {
                            $q2->whereIn('status', ['processing', 'queued'])
                                ->whereNotNull('last_attempt_at')
                                ->where('last_attempt_at', '<=', $staleCutoff);
                        });
                });

                // Due constraint untuk normal case, plus stale recovery.
                $q->where(function ($qq) use ($now, $staleCutoff) {
                    $qq->where(function ($q2) use ($now) {
                        $q2->whereNull('next_retry_at')
                            ->orWhere('next_retry_at', '<=', $now);
                    })->orWhere(function ($q2) use ($staleCutoff) {
                        $q2->whereIn('status', ['processing', 'queued'])
                            ->whereNotNull('last_attempt_at')
                            ->where('last_attempt_at', '<=', $staleCutoff);
                    });
                });
            }

            /** @var Collection<int, WebhookEvent> $picked */
            $picked = $q
                // Prioritas: yang belum pernah dischedule (NULL) dulu, lalu yang paling cepat due
                ->orderByRaw('CASE WHEN next_retry_at IS NULL THEN 0 ELSE 1 END ASC, next_retry_at ASC')
                ->limit($limit)
                ->lockForUpdate()
                ->get();

            if ($picked->isEmpty()) {
                return $picked;
            }

            $claimed = collect();

            foreach ($picked as $event) {
                if ($this->claimMutateRow(
                    event: $event,
                    now: $now,
                    maxAttempts: $maxAttempts,
                    mode: $mode,
                    baseMs: $baseMs,
                    capMs: $capMs,
                    minLeaseMs: $minLeaseMs
                )) {
                    $claimed->push($event);
                }
            }

            /** @var Collection<int, WebhookEvent> $claimed */
            return $claimed;
        }, 3);

        return $events;
    }

    /**
     * Claim 1 event by primary key (untuk admin "retry now").
     */
    protected function claimOne(
        string $id,
        string $provider,
        int $maxAttempts,
        string $mode,
        CarbonImmutable $now,
        CarbonImmutable $staleCutoff,
        int $baseMs,
        int $capMs,
        int $minLeaseMs,
        bool $force
    ): ?WebhookEvent {
        /** @var WebhookEvent|null $event */
        $event = DB::transaction(function () use (
            $id,
            $provider,
            $maxAttempts,
            $mode,
            $now,
            $staleCutoff,
            $baseMs,
            $capMs,
            $minLeaseMs,
            $force
        ): ?WebhookEvent {
            $q = WebhookEvent::query()
                ->whereKey($id)
                ->lockForUpdate();

            if ($provider !== '') {
                $q->where('provider', $provider);
            }

            /** @var WebhookEvent|null $row */
            $row = $q->first();
            if (! $row) {
                return null;
            }

            // Jangan retry yang sudah final
            $ps = strtolower((string) ($row->payment_status ?? ''));
            if ($ps !== '' && $ps !== 'pending') {
                return null;
            }

            if (($row->status ?? null) === 'processed') {
                return null;
            }

            // Normal mode: jangan ganggu processing/queued yang masih fresh
            if (! $force && in_array((string) ($row->status ?? ''), ['processing', 'queued'], true)) {
                $last = $row->last_attempt_at;
                if ($last instanceof \Carbon\CarbonInterface && $last->greaterThan($staleCutoff)) {
                    return null;
                }
            }

            if ((int) ($row->attempts ?? 0) >= $maxAttempts && ! $force) {
                return null;
            }

            // Due constraint (kecuali force)
            if (! $force) {
                $next = $row->next_retry_at;
                if ($next instanceof \Carbon\CarbonInterface && $next->greaterThan($now)) {
                    // Jika processing/queued stale, kita tetap izinkan lewat jalur stale guard di atas.
                    if (! in_array((string) ($row->status ?? ''), ['processing', 'queued'], true)) {
                        return null;
                    }
                }
            }

            $ok = $this->claimMutateRow(
                event: $row,
                now: $now,
                maxAttempts: $maxAttempts,
                mode: $mode,
                baseMs: $baseMs,
                capMs: $capMs,
                minLeaseMs: $minLeaseMs
            );

            return $ok ? $row : null;
        }, 3);

        return $event;
    }

    /**
     * Mutasi row saat claim:
     * - attempts++ (dibatasi maxAttempts)
     * - last_attempt_at = now
     * - next_retry_at = now + leaseMs (backoff+jitter, minimal minLeaseMs)
     * - status = processing (menghindari double-pick), clear error_message
     */
    protected function claimMutateRow(
        WebhookEvent $event,
        CarbonImmutable $now,
        int $maxAttempts,
        string $mode,
        int $baseMs,
        int $capMs,
        int $minLeaseMs
    ): bool {
        $currentAttempts = (int) ($event->attempts ?? 0);
        $nextAttempt = $currentAttempts + 1;

        // Safety: jangan lewat batas
        if ($nextAttempt > $maxAttempts) {
            return false;
        }

        // Delay untuk "jadwal berikutnya" (attempt number = nextAttempt)
        $delayMs = RetryBackoff::compute(
            attempt: $nextAttempt,
            baseMs: $baseMs,
            capMs: $capMs,
            mode: $mode,
            maxAttempts: $maxAttempts
        );

        // Lease minimum untuk menghindari kepilih ulang instan
        $leaseMs = max($minLeaseMs, $delayMs);

        $event->forceFill([
            'attempts' => $nextAttempt,
            'last_attempt_at' => $now,
            'next_retry_at' => $now->addMilliseconds($leaseMs),
            'status' => 'processing',
            'error_message' => null,
        ])->save();

        return true;
    }
}
