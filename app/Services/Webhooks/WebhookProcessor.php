<?php

declare(strict_types=1);

namespace App\Services\Webhooks;

use App\Repositories\PaymentRepository;
use App\Repositories\WebhookEventRepository;
use App\ValueObjects\PaymentStatus;
use BackedEnum;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        $capMs = (int) config('tenrusl.retry_cap_ms', 30000);

        $mode = $this->normalizeBackoffMode((string) config('tenrusl.scheduler_backoff_mode', 'full'));
        $maxAttempts = (int) config('tenrusl.max_retry_attempts', 5);

        return DB::transaction(function () use (
            $provider, $eventId, $type, $rawBody, $payload, $now,
            $baseMs, $capMs, $mode, $maxAttempts
        ): array {
            [$event, $duplicate] = $this->events->storeNewOrGetExisting(
                provider: $provider,
                eventId: $eventId,
                eventType: $type,
                rawBody: $rawBody,
                payload: $payload,
                receivedAt: $now,
                lockExisting: true
            );

            $isInternalRetry = ($type === 'retry');

            if ($duplicate && ! $isInternalRetry) {
                $this->events->touchAttempt($event, $now);
            } elseif ($isInternalRetry && ! $this->wasClaimedVeryRecently($event, $now)) {
                $this->events->touchAttempt($event, $now);
            }

            $attempts = (int) ($event->attempts ?? 1);

            // Fast-path: event sudah processed & final
            $existingStatus = strtolower($this->statusToString($event->payment_status ?? null));
            if (($event->status ?? null) === 'processed' && $existingStatus !== '' && $existingStatus !== 'pending') {
                return [
                    'duplicate' => true,
                    'persisted' => true,
                    'status' => $existingStatus,
                    'payment_provider_ref' => $event->payment_provider_ref,
                    'next_retry_ms' => null,
                ];
            }

            $inferred = $this->inferStatus($provider, $payload); // pending|succeeded|failed
            $providerRef = $this->extractProviderRef($provider, $payload);

            // Simpan audit field ke event (tipe aman: PaymentStatus|null)
            $ps = $this->statusFromString($inferred);
            if ($ps !== null) {
                $event->payment_status = $ps;
            }
            if ($providerRef !== null) {
                $event->payment_provider_ref = $providerRef;
            }

            $persisted = false;
            $effectiveStatus = $inferred;

            if ($providerRef !== null) {
                try {
                    $this->payments->updateStatusByProviderRef($provider, $providerRef, $inferred);

                    $payment = $this->payments->findByProviderRef($provider, $providerRef);

                    if ($payment !== null) {
                        $persisted = true;

                        $paymentStatus = strtolower($this->statusToString($payment->status));
                        if (in_array($paymentStatus, ['succeeded', 'failed'], true)) {
                            $effectiveStatus = $paymentStatus;

                            $this->events->markProcessed(
                                $event,
                                $providerRef,
                                $this->statusFromString($paymentStatus) ?? $paymentStatus,
                                $now
                            );

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

            if ($providerRef !== null && $persisted && $inferred !== 'pending') {
                $this->events->markProcessed(
                    $event,
                    $providerRef,
                    $this->statusFromString($inferred) ?? $inferred,
                    $now
                );

                return [
                    'duplicate' => $duplicate,
                    'persisted' => true,
                    'status' => $inferred,
                    'payment_provider_ref' => $providerRef,
                    'next_retry_ms' => null,
                ];
            }

            $nextRetryMs = null;

            $shouldRetry =
                $attempts < $maxAttempts
                && (
                    $inferred === 'pending'
                    || $providerRef === null
                    || ! $persisted
                );

            if ($shouldRetry) {
                $nextRetryMs = RetryBackoff::compute(
                    $attempts,
                    $baseMs,
                    $capMs,
                    $mode,
                    $maxAttempts
                );

                $nextAt = $now->addMilliseconds($nextRetryMs);

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

    private function normalizeBackoffMode(string $mode): string
    {
        $m = strtolower(trim($mode));
        return in_array($m, ['full', 'equal', 'decorrelated'], true) ? $m : 'full';
    }

    private function wasClaimedVeryRecently($event, CarbonImmutable $now): bool
    {
        $last = $event->last_attempt_at ?? null;

        if ($last instanceof CarbonInterface) {
            return $last->greaterThan($now->subSeconds(2));
        }

        return false;
    }

    /**
     * Convert status apa pun jadi string aman untuk compare/logika.
     */
    private function statusToString(mixed $status): string
    {
        // Backed enum: ambil property ->value (bukan method value()).
        if ($status instanceof BackedEnum) {
            return (string) $status->value;
        }

        if (is_string($status)) {
            return $status;
        }

        if (is_int($status)) {
            return (string) $status;
        }

        if (is_object($status)) {
            // Pola VO: public $value
            if (property_exists($status, 'value')) {
                /** @var mixed $v */
                $v = $status->{'value'};
                if (is_string($v) || is_int($v)) {
                    return (string) $v;
                }
            }

            // Pola VO: method value() (pakai is_callable + call_user_func supaya Intelephense aman)
            if (is_callable([$status, 'value'])) {
                /** @var mixed $v */
                $v = \call_user_func([$status, 'value']);
                if (is_string($v) || is_int($v)) {
                    return (string) $v;
                }
            }

            if ($status instanceof \Stringable) {
                return (string) $status;
            }
        }

        return '';
    }

    /**
     * Buat PaymentStatus dari string.
     * (Tanpa method_exists() supaya PHPStan tidak warning "always true".)
     */
    private function statusFromString(string $status): ?PaymentStatus
    {
        $status = strtolower(trim($status));
        if ($status === '') {
            return null;
        }

        // Untuk backed enum, tryFrom() tersedia dan aman (mengembalikan null kalau invalid).
        return PaymentStatus::tryFrom($status);
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
