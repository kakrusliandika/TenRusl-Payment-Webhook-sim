<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\WebhookEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class WebhookEventFactory extends Factory
{
    protected $model = WebhookEvent::class;

    public function definition(): array
    {
        $provider = $this->faker->randomElement(['mock', 'xendit', 'midtrans']);
        $eventId = 'evt_'.Str::ulid()->toBase32();
        $payRef = 'sim_'.$provider.'_'.Str::ulid()->toBase32();

        // FIX Intelephense: gunakan Carbon + method yang terdokumentasi jelas
        $sentAtIso = Carbon::now()->toIso8601String();

        // Status event webhook: received | processed | failed
        $status = $this->faker->randomElement(['received', 'processed', 'failed']);

        // Type event (untuk audit / filter admin)
        $eventType = $this->faker->randomElement([
            'payment.succeeded',
            'payment.failed',
            'charge.succeeded',
            'charge.failed',
            'invoice.paid',
        ]);

        $payload = [
            'event_id' => $eventId,
            'type' => $eventType,
            'data' => [
                'provider' => $provider,
                'ref' => $payRef,
                'amount' => $this->faker->numberBetween(1_000, 250_000),
                'currency' => 'IDR',
            ],
            'sent_at' => $sentAtIso,
        ];

        $raw = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($raw === false) {
            $raw = '{}';
        }

        $processedAt = $status === 'processed' ? Carbon::now() : null;

        return [
            'id' => (string) Str::ulid(),
            'provider' => $provider,
            'event_id' => $eventId,

            // Optional: tipe event untuk audit
            'event_type' => $eventType,

            // Referensi payment (tracing)
            'payment_provider_ref' => $payRef,

            // Jejak verifikasi signature (tanpa secret)
            'signature_hash' => hash('sha256', 'factory:'.$provider.':'.$eventId),

            // Audit tambahan (sesuai migration versi production)
            'source_ip' => $this->faker->ipv4(),
            'request_id' => 'req_'.Str::ulid()->toBase32(),
            'headers' => [
                'content_type' => 'application/json',
                'user_agent' => $this->faker->userAgent(),
            ],

            // Raw + parsed payload
            'payload_raw' => $raw,
            'payload' => $payload,

            // Status pipeline webhook
            'status' => $status,

            // attempts menghitung eksekusi proses (default 0 saat diterima)
            'attempts' => $this->faker->numberBetween(0, 3),

            // Audit timestamps
            'received_at' => Carbon::now(),
            'last_attempt_at' => null,
            'processed_at' => $processedAt,

            // Status pembayaran yang dinormalisasi (pending|succeeded|failed)
            'payment_status' => $this->faker->randomElement(['pending', 'succeeded', 'failed']),

            // Retry window
            'next_retry_at' => null,
            'error_message' => null,
        ];
    }

    public function received(): static
    {
        return $this->state(fn () => [
            'status' => 'received',
            'processed_at' => null,
            'payment_status' => 'pending',
            'attempts' => 0,
        ]);
    }

    public function processed(): static
    {
        return $this->state(fn () => [
            'status' => 'processed',
            'processed_at' => Carbon::now(),
            'payment_status' => 'succeeded',
            'attempts' => 1,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => 'failed',
            'processed_at' => null,
            'payment_status' => 'failed',
        ]);
    }
}
