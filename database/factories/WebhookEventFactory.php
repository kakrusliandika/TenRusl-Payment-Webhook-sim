<?php

namespace Database\Factories;

use App\Models\WebhookEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WebhookEventFactory extends Factory
{
    protected $model = WebhookEvent::class;

    public function definition(): array
    {
        $provider = $this->faker->randomElement(['mock', 'xendit', 'midtrans']);
        $eventId = 'evt_'.Str::ulid()->toBase32();
        $payRef = 'sim_'.$provider.'_'.Str::ulid()->toBase32();
        $sentAtIso = now()->toISOString();

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

        // Simpan raw untuk audit/verifikasi signature (yang butuh raw body)
        $raw = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($raw === false) {
            $raw = '{}';
        }

        return [
            'id' => (string) Str::ulid(),
            'provider' => $provider,
            'event_id' => $eventId,

            // Optional: tipe event untuk audit / debugging (selaras dengan migration)
            'event_type' => $eventType,

            // Referensi payment di simulator (untuk tracing)
            'payment_provider_ref' => $payRef,

            // Jejak verifikasi signature (opsional)
            'signature_hash' => $this->faker->sha256(),

            // Raw + parsed payload (selaras dengan migration)
            'payload_raw' => $raw,
            'payload' => $payload,

            // Status pipeline webhook (bukan status pembayaran)
            'status' => $status,

            // Selaras dengan kode: gunakan "attempts" (bukan attempt_count)
            'attempts' => $this->faker->numberBetween(0, 3),

            // Waktu audit (opsional)
            'received_at' => now(),
            'last_attempt_at' => null,
            'processed_at' => $status === 'processed' ? now() : null,

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
        ]);
    }

    public function processed(): static
    {
        return $this->state(fn () => [
            'status' => 'processed',
            'processed_at' => now(),
            'payment_status' => 'succeeded',
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
