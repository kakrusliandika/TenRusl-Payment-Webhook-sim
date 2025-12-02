<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\WebhookEvent;
use App\Services\Webhooks\WebhookProcessor;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job queue untuk memproses ulang webhook event (jalur retry).
 *
 * Prinsip:
 * - Harus idempotent: kalau job duplicate / dipanggil ulang, tidak boleh bikin state kacau.
 * - Guard di sini mencegah pemrosesan event yang sudah final.
 * - Guard tambahan mencegah job "keduluan" sebelum next_retry_at due (misal delay queue tidak akurat).
 */
class ProcessWebhookEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Queue khusus webhook.
     * Untuk driver database, ini akan mengisi kolom `queue` pada tabel jobs.
     */
    public string $queue = 'webhooks';

    public function __construct(
        public string $webhookEventId
    ) {}

    public function handle(WebhookProcessor $processor): void
    {
        /** @var WebhookEvent|null $event */
        $event = WebhookEvent::query()->find($this->webhookEventId);

        if (! $event) {
            return;
        }

        // -------------------------
        // Guard 1: kalau sudah processed dan final, skip.
        // -------------------------
        $paymentStatus = strtolower((string) ($event->payment_status ?? ''));
        if (($event->status ?? null) === 'processed' && $paymentStatus !== '' && $paymentStatus !== 'pending') {
            return;
        }

        // -------------------------
        // Guard 2: kalau next_retry_at belum due, skip.
        // (Jaga-jaga kalau job dieksekusi lebih cepat dari jadwal.)
        // -------------------------
        $now = CarbonImmutable::now();
        $next = $event->next_retry_at;

        if ($next instanceof CarbonInterface && $next->greaterThan($now)) {
            return;
        }

        // -------------------------
        // Guard 3 (opsional): kalau attempts sudah >= max, skip.
        // (Biasanya command/scheduler yang akan markFailed, tapi ini tambahan safety.)
        // -------------------------
        $maxAttempts = (int) config('tenrusl.max_retry_attempts', 5);
        $attempts = (int) ($event->attempts ?? 0);

        if ($attempts >= $maxAttempts && ($event->status ?? null) !== 'processed') {
            return;
        }

        // Penting: untuk jalur queue/retry, pakai type='retry'
        // agar processor bisa bedain dari incoming webhook asli (avoid touchAttempt ganda).
        $processor->process(
            $event->provider,
            $event->event_id,
            'retry',
            $event->payload_raw ?? '',
            (array) $event->payload
        );
    }
}
