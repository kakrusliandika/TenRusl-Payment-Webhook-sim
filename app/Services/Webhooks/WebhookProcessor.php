<?php

namespace App\Services\Webhooks;

use App\Models\Payment;
use App\Models\WebhookEvent;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Orkestrasi pemrosesan webhook:
 * - Dedup (provider + event_id)
 * - Update Payment status
 * - Kelola status event (received|processed|failed) & retry simulasi
 */
class WebhookProcessor
{
    public function __construct(
        protected RetryBackoff $backoff = new RetryBackoff()
    ) {}

    /**
     * Proses satu event webhook.
     *
     * @param string $provider
     * @param array  $payload      (harus memiliki event_id, type, data{payment_id})
     * @param string|null $signatureHash
     * @return array{status:string, duplicated:bool}
     */
    public function process(string $provider, array $payload, ?string $signatureHash = null): array
    {
        $eventId   = (string) Arr::get($payload, 'event_id');
        $type      = (string) Arr::get($payload, 'type');
        $paymentId = (string) Arr::get($payload, 'data.payment_id');

        // Dedup
        $exists = WebhookEvent::query()
            ->where('provider', $provider)
            ->where('event_id', $eventId)
            ->first();

        if ($exists) {
            return ['status' => 'processed', 'duplicated' => true];
        }

        // Simpan event dan proses update payment
        return DB::transaction(function () use ($provider, $eventId, $payload, $signatureHash, $type, $paymentId) {
            $event = WebhookEvent::query()->create([
                'provider'       => $provider,
                'event_id'       => $eventId,
                'signature_hash' => $signatureHash,
                'payload'        => $payload,
                'status'         => 'received',
                'attempt_count'  => 0,
                'next_retry_at'  => null,
                'error_message'  => null,
            ]);

            try {
                if ($paymentId) {
                    $payment = Payment::query()->find($paymentId);
                    if ($payment) {
                        if ($type === 'payment.paid') {
                            $payment->status = 'paid';
                        } elseif ($type === 'payment.failed') {
                            $payment->status = 'failed';
                        }
                        $payment->save();
                    }
                }

                $event->status = 'processed';
                $event->save();

                return ['status' => 'processed', 'duplicated' => false];
            } catch (\Throwable $e) {
                $attempt = 1;
                $event->status = 'failed';
                $event->attempt_count = $attempt;
                $event->next_retry_at = Carbon::now()->addSeconds($this->backoff->secondsFor($attempt));
                $event->error_message = $e->getMessage();
                $event->save();

                return ['status' => 'failed', 'duplicated' => false];
            }
        });
    }
}
