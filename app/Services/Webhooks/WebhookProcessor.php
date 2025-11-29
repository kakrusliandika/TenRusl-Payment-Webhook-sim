<?php

declare(strict_types=1);

namespace App\Services\Webhooks;

use App\Repositories\PaymentRepository;
use App\Repositories\WebhookEventRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;

/**
 * Proses webhook:
 *  - Dedup berdasarkan (provider, event_id)
 *  - Simpan payload mentah
 *  - Pemetaan status generik → update Payment (jika provider_ref teridentifikasi)
 *  - Hitung jadwal retry simulasi (berdasar RetryBackoff)
 *
 * Catatan: Banyak provider melakukan RETRY otomatis dg exponential backoff. Pastikan
 * handler aman dieksekusi berulang (idempotent) & dedup di DB.
 */
final class WebhookProcessor
{
    public function __construct(
        private readonly WebhookEventRepository $events,
        private readonly PaymentRepository $payments,
    ) {}

    /**
     * Proses sebuah event webhook.
     *
     * @param  string  $provider  contoh: 'stripe','xendit','midtrans',dst.
     * @param  string  $eventId   ID unik event dari provider (atau buatkan hash jika tidak ada).
     * @param  string  $type      tipe/subject event (optional, untuk log)
     * @param  string  $rawBody   payload mentah (string JSON/form) untuk arsip/signature audit
     * @param  array   $payload   payload terurai (array)
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
        $now = CarbonImmutable::now();

        // 1) Dedup: cek apakah event sudah pernah disimpan
        $duplicate = false;

        $event = $this->events->findByProviderEvent($provider, $eventId);

        if ($event !== null) {
            $duplicate = true;
            $this->events->touchAttempt($event, $now);
        } else {
            $event = $this->events->storeNew(
                provider: $provider,
                eventId: $eventId,
                eventType: $type,
                rawBody: $rawBody,
                payload: $payload,
                receivedAt: $now,
            );
        }

        // Refresh nilai attempts setelah update/store
        $attempts = (int) ($event->attempts ?? 1);

        // 2) Tentukan status generik dari payload
        $status = $this->inferStatus($provider, $payload);

        // 3) Coba update Payment jika bisa menemukan provider_ref
        $providerRef = $this->extractProviderRef($provider, $payload);
        $persisted = false;

        if ($providerRef !== null) {
            $affected = $this->payments->updateStatusByProviderRef($provider, $providerRef, $status);
            $persisted = $affected > 0;

            // Tandai event sudah diproses & simpan relasi ke payment (jika ada)
            $this->events->markProcessed(
                event: $event,
                paymentProviderRef: $providerRef,
                status: $status,
                processedAt: $now,
            );
        }

        // 4) Jadwal retry simulasi (hanya jika butuh retry)
        $nextRetryMs = null;

        if ($status === 'pending') {
            // attempts di event sudah naik setiap kali diproses, gunakan +1 sebagai basis next backoff
            $nextRetryMs = RetryBackoff::compute($attempts + 1);
            $nextAt = $now->addMilliseconds($nextRetryMs);

            $this->events->scheduleNextRetry($event, $nextAt);
        }

        return [
            'duplicate' => $duplicate,
            'persisted' => $persisted,
            'status' => $status,
            'payment_provider_ref' => $providerRef,
            'next_retry_ms' => $nextRetryMs,
        ];
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

        // fallback lain per provider
        if ($provider === 'midtrans') {
            // midtrans: capture/settlement = success, pending, deny/expire/cancel = failed
            $vt = strtolower((string) ($p['transaction_status'] ?? ''));

            return match ($vt) {
                'capture', 'settlement' => 'succeeded',
                'deny', 'expire', 'cancel' => 'failed',
                default => 'pending',
            };
        }

        // xendit: paid:true sering muncul
        if (Arr::get($p, 'paid') === true) {
            return 'succeeded';
        }

        return 'pending';
    }

    /**
     * Ekstrak provider_ref dari payload (beragam kunci umum).
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

        // Provider-spesifik tambahan
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
