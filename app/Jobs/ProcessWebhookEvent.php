<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\WebhookEvent;
use App\Services\Webhooks\WebhookProcessor;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Job queue untuk memproses webhook event (jalur retry / replay / admin-trigger).
 *
 * Prinsip yang dijaga di sini:
 * - Idempotent: job boleh dipanggil ulang / duplicate tanpa merusak state.
 * - Anti double-processing: kalau 2 worker / 2 enqueue terjadi bersamaan, hanya 1 yang jalan.
 * - Safe replay: kalau event sudah final, job hanya melakukan "finalize" ringan lalu stop.
 *
 * Defense-in-depth yang dipakai:
 * 1) Unique Job (ShouldBeUniqueUntilProcessing) => mencegah enqueue duplicate untuk event yg sama.
 * 2) Middleware WithoutOverlapping => mencegah overlap eksekusi (lock berbasis cache).
 * 3) DB claim (transaction + lockForUpdate + status=processing) => mencegah race paling bawah.
 *
 * Catatan:
 * - Field status yang dipakai: received|processing|failed|processed (string bebas).
 * - payment_status: pending|succeeded|failed (final ketika != pending).
 */
class ProcessWebhookEvent implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Unik per event. Cache lock TTL (detik) untuk unique job.
     * Angka dibuat cukup lama untuk menahan burst enqueue duplicate.
     */
    public int $uniqueFor = 600;

    /**
     * Jangan retry otomatis dari queue layer (kita punya retry engine sendiri).
     * Kalau kamu ingin queue layer retry, naikkan tries + tambah backoff,
     * tapi pastikan tidak double-increment attempts.
     */
    public int $tries = 1;

    /**
     * @param string $webhookEventId  Primary key webhook_events (ULID/UUID/string)
     * @param string|null $trigger   'retry'|'admin'|'replay'|null (opsional)
     * @param bool $force            true = abaikan beberapa guard non-final (tetap skip jika sudah final)
     */
    public function __construct(
        public string $webhookEventId,
        public ?string $trigger = 'retry',
        public bool $force = false
    ) {
        /**
         * PENTING:
         * Jangan deklarasikan `public $queue` / `public string $queue` lagi di class ini,
         * karena trait Queueable sudah mendefinisikan property tersebut.
         * Set queue gunakan onQueue() (disediakan Queueable).
         */
        $this->onQueue('webhooks');
    }

    /**
     * Unique identifier untuk job uniqueness.
     * Dengan ini, job duplicate untuk eventId yang sama tidak akan di-enqueue bersamaan.
     */
    public function uniqueId(): string
    {
        return 'webhook-event:'.$this->webhookEventId;
    }

    /**
     * Middleware tambahan mencegah overlap execution untuk event yang sama.
     * - releaseAfter: kalau lock masih dipegang job lain, job dilepas untuk dicoba lagi.
     */
    public function middleware(): array
    {
        // 15 detik cukup untuk "menghindari tabrakan" singkat antar worker.
        // Kalau processing bisa lama, naikkan lock TTL / gunakan dontRelease() sesuai kebutuhan.
        return [
            (new WithoutOverlapping($this->uniqueId()))
                ->releaseAfter(15),
        ];
    }

    public function handle(WebhookProcessor $processor): void
    {
        /** @var WebhookEvent|null $event */
        $event = WebhookEvent::query()->find($this->webhookEventId);

        if (! $event instanceof Model) {
            return;
        }

        // -------------------------
        // Guard 0: bila sudah final (payment_status != pending), finalize ringan dan stop.
        // Ini penting untuk idempotency: duplicate job tidak boleh mengubah final state.
        // -------------------------
        $final = strtolower((string) ($event->payment_status ?? ''));
        if ($final !== '' && $final !== 'pending') {
            // defensif: pastikan status/processed_at konsisten
            if (($event->status ?? null) !== 'processed') {
                $event->forceFill([
                    'status' => 'processed',
                    'processed_at' => $event->processed_at ?? CarbonImmutable::now(),
                    'next_retry_at' => null,
                    'error_message' => null,
                ])->save();
            }

            return;
        }

        // -------------------------
        // Guard 1: kalau status sudah processed tapi payment_status belum kebaca (edge case),
        // tetap stop kecuali force=true (misal admin mau replay untuk recover).
        // -------------------------
        if (($event->status ?? null) === 'processed' && $this->force !== true) {
            return;
        }

        // -------------------------
        // Guard 2: Hindari double-processing jika ada event lain sedang memproses.
        // Kita pakai "lease" berbasis updated_at untuk memulihkan lock yang stuck.
        // -------------------------
        $processingLeaseSeconds = (int) config('tenrusl.processing_lease_seconds', 90);
        $now = CarbonImmutable::now();

        if (($event->status ?? null) === 'processing' && $this->force !== true) {
            $updatedAt = $event->updated_at;

            if ($updatedAt instanceof CarbonInterface) {
                $age = $updatedAt->diffInSeconds($now);
                if ($age < $processingLeaseSeconds) {
                    return; // masih dianggap diproses worker lain
                }
            } else {
                // kalau updated_at tidak valid, main aman: jangan proses paralel
                return;
            }
        }

        // -------------------------
        // Guard 3: attempts max safety (tambahan).
        // Biasanya retry engine yg membatasi, tapi job juga defensif.
        // -------------------------
        $maxAttempts = (int) config('tenrusl.max_retry_attempts', 5);
        $attempts = (int) ($event->attempts ?? 0);

        if ($maxAttempts > 0 && $attempts >= $maxAttempts && $this->force !== true) {
            return;
        }

        // -------------------------
        // Claim row secara atomik dengan lockForUpdate (anti-race paling bawah).
        // - Jika sukses claim => status=processing, error_message dibersihkan
        // - Jika gagal claim => berarti ada worker lain yg menang
        // -------------------------
        /** @var WebhookEvent|null $claimed */
        $claimed = DB::transaction(function () use ($now): ?WebhookEvent {
            /** @var WebhookEvent|null $fresh */
            $fresh = WebhookEvent::query()
                ->whereKey($this->webhookEventId)
                ->lockForUpdate()
                ->first();

            if (! $fresh) {
                return null;
            }

            // Kalau sudah final, stop (idempotent)
            $ps = strtolower((string) ($fresh->payment_status ?? ''));
            if ($ps !== '' && $ps !== 'pending') {
                // finalize defensif
                if (($fresh->status ?? null) !== 'processed') {
                    $fresh->forceFill([
                        'status' => 'processed',
                        'processed_at' => $fresh->processed_at ?? CarbonImmutable::now(),
                        'next_retry_at' => null,
                        'error_message' => null,
                    ])->save();
                }

                return null;
            }

            // Kalau sedang diproses dan bukan force, stop
            if (($fresh->status ?? null) === 'processing' && $this->force !== true) {
                return null;
            }

            $fresh->forceFill([
                'status' => 'processing',
                'error_message' => null,
            ])->save();

            return $fresh;
        }, 3);

        if (! $claimed) {
            return;
        }

        // -------------------------
        // Prepare payload untuk processor
        // -------------------------
        $rawBody = (string) ($claimed->payload_raw ?? '');

        // Kalau payload_raw kosong tapi payload ada, reconstruct sebagai fallback.
        if ($rawBody === '' && ! empty($claimed->payload)) {
            $encoded = json_encode($claimed->payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $rawBody = $encoded === false ? '' : $encoded;
        }

        $payloadArr = (array) ($claimed->payload ?? []);

        // Type untuk processor agar dia bisa membedakan incoming vs retry/admin replay.
        $type = $this->trigger ?: 'retry';

        try {
            $processor->process(
                $claimed->provider,
                $claimed->event_id,
                $type,
                $rawBody,
                $payloadArr
            );

            // Refresh untuk lihat hasil finalisasi di processor
            $claimed->refresh();

            $ps2 = strtolower((string) ($claimed->payment_status ?? ''));
            if ($ps2 !== '' && $ps2 !== 'pending') {
                $claimed->forceFill([
                    'status' => 'processed',
                    'processed_at' => $claimed->processed_at ?? CarbonImmutable::now(),
                    'next_retry_at' => null,
                    'error_message' => null,
                ])->save();
            } else {
                // Masih pending => kembalikan ke state yang aman untuk retry berikutnya.
                // Jangan hapus next_retry_at di sini karena itu adalah "lease/jadwal next attempt".
                $claimed->forceFill([
                    'status' => 'received',
                ])->save();
            }
        } catch (Throwable $e) {
            Log::error('ProcessWebhookEvent failed', [
                'webhook_event_id' => $claimed->id,
                'provider' => $claimed->provider,
                'event_id' => $claimed->event_id,
                'trigger' => $this->trigger,
                'force' => $this->force,
                'attempts' => $claimed->attempts,
                'next_retry_at' => $claimed->next_retry_at,
                'exception' => $e,
            ]);

            // Mark failed tapi tetap biarkan next_retry_at (lease/jadwal) apa adanya
            // supaya retry engine tidak "ngambil" event ini berulang-ulang tanpa backoff.
            try {
                $claimed->forceFill([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ])->save();
            } catch (Throwable) {
                // jangan sampai error logging membuat job crash lebih parah
            }

            // Re-throw agar queue mencatat job failed (opsional).
            // Jika kamu tidak ingin queue failed, comment line ini.
            throw $e;
        }
    }
}
