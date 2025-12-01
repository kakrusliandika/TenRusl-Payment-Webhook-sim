<?php

declare(strict_types=1);

namespace App\Services\Webhooks;

use App\Repositories\PaymentRepository;
use App\Repositories\WebhookEventRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Proses webhook:
 *  - Dedup berdasarkan (provider, event_id) (unik di DB)
 *  - Simpan payload mentah
 *  - Infer status generik → update Payment (jika provider_ref teridentifikasi)
 *  - Tentukan retry schedule (RetryBackoff) bila masih pending / payment belum ada
 *
 * Penting:
 * - Race-safe: storeNewOrGetExisting dipanggil di dalam transaction, dan jika duplicate,
 *   fetch existing row dengan lockForUpdate. :contentReference[oaicite:2]{index=2}
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

        $baseMs = (int) config('tenrusl.retry_base_ms', 500);
        $capMs  = (int) config('tenrusl.retry_cap_ms', 30000);
        $mode   = RetryBackoff::normalizeMode((string) config('tenrusl.scheduler_backoff_mode', 'full'));
        $maxAttempts = (int) config('tenrusl.max_retry_attempts', 5);

        $duplicate = false;
        $persisted = false;
        $nextRetryMs = null;

        // 1) Dedup + claim event secara aman
        [$event, $duplicate] = DB::transaction(function () use ($provider, $eventId, $type, $rawBody, $payload, $now) {
            [$event, $dup] = $this->events->storeNewOrGetExisting(
                provider: $provider,
                eventId: $eventId,
                eventType: $type,
                rawBody: $rawBody,
                payload: $payload,
                receivedAt: $now,
            );

            // Kalau duplicate dari provider (bukan internal retry), naikkan attempts.
            // Heuristik: type === 'retry' dianggap internal retry.
            if ($dup && $type !== 'retry') {
                $this->events->touchAttempt($event, $now);
            }

            return [$event, $dup];
        }, attempts: 3);

        $attempts = (int) ($event->attempts ?? 1);

        // 2) Infer status generik
        $status = $this->inferStatus($provider, $payload); // succeeded|failed|pending

        // Simpan inferred status ke event (untuk audit)
        $event->payment_status = $status;
        $event->save();

        // 3) Extract provider ref
        $providerRef = $this->extractProviderRef($provider, $payload);

        // 4) Update payment + state transition (delegasi ke PaymentRepository)
        if ($providerRef !== null) {
            try {
                $affected = $this->payments->updateStatusByProviderRef($provider, $providerRef, $status);
                $persisted = $affected > 0;
            } catch (\Throwable $e) {
                Log::warning('Payment updateStatusByProviderRef failed', [
                    'provider' => $provider,
                    'provider_ref' => $providerRef,
                    'status' => $status,
                    'exception' => $e,
                ]);
                $persisted = false;
            }

            if ($persisted && $status !== 'pending') {
                // Final state berhasil dipersist
                $this->events->markProcessed($event, $providerRef, $status, $now);
                return [
                    'duplicate' => $duplicate,
                    'persisted' => true,
                    'status' => $status,
                    'payment_provider_ref' => $providerRef,
                    'next_retry_ms' => null,
                ];
            }
        }

        // 5) Kalau belum final / payment belum ketemu, schedule retry (selama attempts masih boleh)
        if ($attempts < $maxAttempts) {
            $nextRetryMs = RetryBackoff::compute(
                attempt: $attempts,
                baseMs: $baseMs,
                capMs: $capMs,
                mode: $mode,
                maxAttempts: $maxAttempts
            );

            $nextAt = $now->addMilliseconds($nextRetryMs);

            $reason = null;
            if ($providerRef === null) {
                $reason = 'provider_ref not found in payload';
            } elseif (! $persisted && $status !== 'pending') {
                $reason = 'payment not found / not updated (will retry)';
            }

            $this->events->scheduleNextRetry($event, $nextAt, $reason);
        } else {
            $this->events->markFailed($event, 'Max retry attempts reached.', $now);
        }

        return [
            'duplicate' => $duplicate,
            'persisted' => $persisted,
            'status' => $status,
            'payment_provider_ref' => $providerRef,
            'next_retry_ms' => $nextRetryMs,
        ];
    }

    private function inferStatus(string $provider, array $p): string
    {
        $v = strtolower((string) ($p['status']
            ?? $p['payment_status']
            ?? $p['transaction_status']
            ?? Arr::get($p, 'data.status')
            ?? Arr::get($p, 'resource.status')
            ?? ''));

        $truthy = [
            'paid', 'succeeded', 'success', 'completed', 'captured',
            'charge.succeeded', 'payment_intent.succeeded', 'paid_out', 'settled',
        ];
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

        if ($provider === 'midtrans') {
            $vt = strtolower((string) ($p['transaction_status'] ?? ''));

            return match ($vt) {
                'capture', 'settlement' => 'succeeded',
                'deny', 'expire', 'cancel' => 'failed',
                default => 'pending',
            };
        }

        if (Arr::get($p, 'paid') === true) {
            return 'succeeded';
        }

        return 'pending';
    }

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
