<?php

declare(strict_types=1);

namespace App\Services\Webhooks;

use App\Repositories\PaymentRepository;
use App\Repositories\WebhookEventRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Proses webhook (domain core):
 *  - Dedup berdasarkan (provider, event_id) -> ENFORCED oleh unique index di DB.
 *  - Race-safe: insert-or-get + handle duplicate-key + lockForUpdate (di dalam transaction).
 *  - Update Payment + update WebhookEvent dilakukan konsisten/atomic (dalam transaction yang sama).
 *  - Retry scheduling: next_retry_at dihitung memakai RetryBackoff, mode diambil dari config yang sama
 *    dengan scheduler/command (tenrusl.scheduler_backoff_mode).
 *
 * Catatan desain:
 * - "attempts" dimaknai sebagai jumlah percobaan proses yang sudah terjadi.
 *   Saat percobaan #1 gagal => delay dihitung pakai attempt=1 (base * 2^(1-1) = base).
 * - Jalur retry (queue/command) idealnya melakukan "claiming" + increment attempts sebelum memanggil processor.
 *   Tapi processor ini juga punya guard agar tidak double-increment jika sudah di-claim barusan.
 */
final class WebhookProcessor
{
    public function __construct(
        private readonly WebhookEventRepository $events,
        private readonly PaymentRepository $payments,
    ) {}

    /**
     * @return array{
     *   duplicate:bool,
     *   persisted:bool,
     *   status:string,
     *   payment_provider_ref:string|null,
     *   next_retry_ms:int|null
     * }
     */
    public function process(string $provider, string $eventId, string $type, string $rawBody, array $payload): array
    {
        $provider = strtolower(trim($provider));
        $eventId = trim($eventId);
        $type = trim($type);

        $now = CarbonImmutable::now();

        // Konfigurasi retry/backoff (boleh kamu tambahkan ke config/tenrusl.php; kalau belum ada pakai default)
        $baseMs = (int) config('tenrusl.retry_base_ms', 500);
        $capMs  = (int) config('tenrusl.retry_cap_ms', 30000);

        // WAJIB: sinkron dengan scheduler/command (Kernel + RetryWebhookCommand)
        $mode = $this->normalizeBackoffMode((string) config('tenrusl.scheduler_backoff_mode', 'full'));
        $maxAttempts = (int) config('tenrusl.max_retry_attempts', 5);

        // DB::transaction param ke-2 = retry attempts saat deadlock.
        // Semua update event/payment dilakukan atomic di sini. :contentReference[oaicite:1]{index=1}
        return DB::transaction(function () use (
            $provider, $eventId, $type, $rawBody, $payload, $now,
            $baseMs, $capMs, $mode, $maxAttempts
        ): array {
            // -------------------------
            // 1) Dedup (race-safe) + lock event row
            // -------------------------
            // storeNewOrGetExisting:
            // - coba insert (provider,event_id) -> kalau duplikat, ambil row existing (opsional lockForUpdate)
            // - dedup "keras" tetap dijamin oleh unique index di DB.
            [$event, $duplicate] = $this->events->storeNewOrGetExisting(
                provider: $provider,
                eventId: $eventId,
                eventType: $type,
                rawBody: $rawBody,
                payload: $payload,
                receivedAt: $now,
                lockExisting: true
            );

            // Jalur internal retry (queue/command) wajib memakai type='retry'
            $isInternalRetry = ($type === 'retry');

            // -------------------------
            // 1b) Attempts counting (hindari race & double increment)
            // -------------------------
            // Jika duplicate dari provider: ini “percobaan proses” baru => increment attempts.
            // Jika internal retry:
            //  - Idealnya: command sudah increment attempts ketika "claiming due events".
            //  - Guard di bawah mencegah double increment jika baru saja di-claim.
            if ($duplicate && ! $isInternalRetry) {
                $this->events->touchAttempt($event, $now);
            } elseif ($isInternalRetry && ! $this->wasClaimedVeryRecently($event, $now)) {
                // fallback safety: kalau ternyata belum di-claim, kita increment di sini
                $this->events->touchAttempt($event, $now);
            }

            $attempts = (int) ($event->attempts ?? 1);

            // -------------------------
            // 1c) Fast-path: event sudah processed & final -> skip (idempotent)
            // -------------------------
            // Kalau event sudah processed dan payment_status final, kita tidak schedule retry apa pun.
            $existingStatus = strtolower((string) ($event->payment_status ?? ''));
            if (($event->status ?? null) === 'processed' && $existingStatus !== '' && $existingStatus !== 'pending') {
                return [
                    'duplicate' => true,
                    'persisted' => true,
                    'status' => $existingStatus,
                    'payment_provider_ref' => $event->payment_provider_ref,
                    'next_retry_ms' => null,
                ];
            }

            // -------------------------
            // 2) Infer status generik dari payload
            // -------------------------
            $inferred = $this->inferStatus($provider, $payload); // pending|succeeded|failed

            // -------------------------
            // 3) Extract provider_ref (kalau ada)
            // -------------------------
            $providerRef = $this->extractProviderRef($provider, $payload);

            // Simpan audit field ke event (nanti akan ikut tersave oleh markProcessed/scheduleNextRetry/markFailed)
            $event->payment_status = $inferred;
            if ($providerRef !== null) {
                $event->payment_provider_ref = $providerRef;
            }

            // -------------------------
            // 4) Update Payment (atomic bersama update event)
            // -------------------------
            $persisted = false;
            $effectiveStatus = $inferred;

            if ($providerRef !== null) {
                try {
                    // Update status payment mengikuti aturan state transition (di PaymentRepository)
                    // NOTE: $affected bisa 0 walau payment ada (mis. nilai sama), tergantung driver.
                    $this->payments->updateStatusByProviderRef($provider, $providerRef, $inferred);

                    // Untuk memastikan "persisted" benar (payment ada / tidak), lakukan lookup model.
                    $payment = $this->payments->findByProviderRef($provider, $providerRef);

                    if ($payment !== null) {
                        $persisted = true;

                        // Kalau payment sudah final (succeeded/failed), anggap event selesai.
                        // Ini penting untuk:
                        // - kasus duplicate webhook datang setelah payment final
                        // - kasus retry yang terlambat
                        $paymentStatus = strtolower((string) $payment->status);
                        if (in_array($paymentStatus, ['succeeded', 'failed'], true)) {
                            $effectiveStatus = $paymentStatus;

                            // Tandai processed, bersihkan next_retry_at.
                            $this->events->markProcessed($event, $providerRef, $paymentStatus, $now);

                            return [
                                'duplicate' => $duplicate,
                                'persisted' => true,
                                'status' => $effectiveStatus,
                                'payment_provider_ref' => $providerRef,
                                'next_retry_ms' => null,
                            ];
                        }
                    }
                } catch (Throwable $e) {
                    // Jangan biarkan exception payment update mematikan worker/request.
                    // Kita catat, lalu producer akan masuk jalur retry jika attempts masih tersedia.
                    Log::warning('Payment update failed during webhook processing', [
                        'provider' => $provider,
                        'provider_ref' => $providerRef,
                        'inferred_status' => $inferred,
                        'attempts' => $attempts,
                        'exception' => $e,
                    ]);

                    $persisted = false;
                }
            }

            // Kalau inferred status final dan payment ada/ter-update, kita mark processed juga.
            if ($providerRef !== null && $persisted && $inferred !== 'pending') {
                $this->events->markProcessed($event, $providerRef, $inferred, $now);

                return [
                    'duplicate' => $duplicate,
                    'persisted' => true,
                    'status' => $inferred,
                    'payment_provider_ref' => $providerRef,
                    'next_retry_ms' => null,
                ];
            }

            // -------------------------
            // 5) Retry scheduling / failure (atomic)
            // -------------------------
            $nextRetryMs = null;

            // Butuh retry bila:
            // - inferred masih pending (simulasi)
            // - provider_ref tidak ketemu
            // - payment belum ada/ga bisa dipersist
            $shouldRetry =
                $attempts < $maxAttempts
                && (
                    $inferred === 'pending'
                    || $providerRef === null
                    || ! $persisted
                );

            if ($shouldRetry) {
                // Hitung delay berbasis jumlah attempts saat ini (attempt ke-n).
                $nextRetryMs = RetryBackoff::compute(
                    $attempts,
                    $baseMs,
                    $capMs,
                    $mode,
                    $maxAttempts
                );

                $nextAt = $now->addMilliseconds($nextRetryMs);

                // Simpan alasan untuk audit/debug (opsional)
                $reason = null;
                if ($providerRef === null) {
                    $reason = 'provider_ref not found in payload';
                } elseif (! $persisted && $inferred !== 'pending') {
                    $reason = 'payment not found / not updated (will retry)';
                } elseif ($inferred === 'pending') {
                    $reason = 'inferred status pending (retry simulation)';
                }

                $this->events->scheduleNextRetry($event, $nextAt, $reason);
            } else {
                // Tidak retry lagi: tandai failed (misal max attempts tercapai)
                $this->events->markFailed($event, 'Max retry attempts reached.', $now);
            }

            return [
                'duplicate' => $duplicate,
                'persisted' => $persisted,
                'status' => $effectiveStatus,
                'payment_provider_ref' => $providerRef,
                'next_retry_ms' => $nextRetryMs,
            ];
        }, 3);
    }

    /**
     * Normalisasi mode backoff agar tidak ada nilai liar.
     */
    private function normalizeBackoffMode(string $mode): string
    {
        $m = strtolower(trim($mode));

        return in_array($m, ['full', 'equal', 'decorrelated'], true) ? $m : 'full';
    }

    /**
     * Guard anti double-increment: jika command sudah "claim" barusan,
     * biasanya last_attempt_at akan sangat dekat dengan $now.
     */
    private function wasClaimedVeryRecently($event, CarbonImmutable $now): bool
    {
        $last = $event->last_attempt_at ?? null;

        if ($last instanceof CarbonInterface) {
            // Jika <= 2 detik terakhir, anggap sudah di-claim oleh scheduler/command
            return $last->greaterThan($now->subSeconds(2));
        }

        return false;
    }

    /**
     * Mapping status generik dari berbagai provider → succeeded|failed|pending.
     */
    private function inferStatus(string $provider, array $p): string
    {
        $v = strtolower((string) ($p['status']
            ?? $p['payment_status']
            ?? $p['transaction_status']
            ?? Arr::get($p, 'data.status')
            ?? Arr::get($p, 'resource.status')
            ?? ''));

        // Umum: status sukses
        $truthy = [
            'paid', 'succeeded', 'success', 'completed', 'captured',
            'charge.succeeded', 'payment_intent.succeeded', 'paid_out', 'settled',
        ];

        // Umum: status gagal
        $falsy = [
            'failed', 'canceled', 'cancelled', 'void', 'expired', 'denied', 'rejected',
            'charge.failed', 'payment_intent.canceled',
        ];

        if (in_array($v, $truthy, true)) {
            return 'succeeded';
        }

        if (in_array($v, $falsy, true)) {
            return 'failed';
        }

        // Provider-spesifik
        if ($provider === 'midtrans') {
            $vt = strtolower((string) ($p['transaction_status'] ?? ''));

            return match ($vt) {
                'capture', 'settlement' => 'succeeded',
                'deny', 'expire', 'cancel' => 'failed',
                default => 'pending',
            };
        }

        // Xendit sering punya paid:true
        if (Arr::get($p, 'paid') === true) {
            return 'succeeded';
        }

        return 'pending';
    }

    /**
     * Ekstrak provider_ref dari payload (beragam kunci umum).
     * Ini dipakai untuk lookup payment di simulator.
     */
    private function extractProviderRef(string $provider, array $p): ?string
    {
        $candidates = [
            Arr::get($p, 'id'),
            Arr::get($p, 'data.object.id'),
            Arr::get($p, 'data.id'),
            Arr::get($p, 'resource.id'),
            Arr::get($p, 'payment_id'),
            Arr::get($p, 'order_id'),
            Arr::get($p, 'external_id'),
            Arr::get($p, 'invoice_id'),
            Arr::get($p, 'reference'),
            Arr::get($p, 'reference_id'),
            Arr::get($p, 'merchant_reference'),
        ];

        // Midtrans: order_id lazim dipakai sebagai ref.
        if ($provider === 'midtrans' && ! empty($p['order_id'])) {
            $candidates[] = (string) $p['order_id'];
        }

        foreach ($candidates as $val) {
            if (is_string($val) && $val !== '') {
                return $val;
            }
        }

        return null;
    }
}
