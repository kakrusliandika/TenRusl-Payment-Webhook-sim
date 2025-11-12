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
        $provider   = $this->faker->randomElement(['mock', 'xendit', 'midtrans']);
        $eventId    = 'evt_' . Str::ulid()->toBase32();
        $payRef     = 'sim_' . $provider . '_' . Str::ulid()->toBase32();
        $sentAtIso  = now()->toISOString();

        // Status event webhook: received | processed | failed
        $status     = $this->faker->randomElement(['received', 'processed', 'failed']);

        return [
            'id'                   => (string) Str::ulid(),
            'provider'             => $provider,
            'event_id'             => $eventId,
            'payment_provider_ref' => $payRef,

            'signature_hash'       => $this->faker->sha256(),

            'payload'              => [
                'event_id'   => $eventId,
                'type'       => $this->faker->randomElement([
                    'payment.succeeded',
                    'payment.failed',
                    'charge.succeeded',
                    'charge.failed',
                ]),
                'data'       => [
                    'provider'   => $provider,
                    'ref'        => $payRef,
                    'amount'     => $this->faker->numberBetween(1_000, 250_000),
                    'currency'   => 'IDR',
                ],
                'sent_at'    => $sentAtIso,
            ],

            // Status pipeline webhook (bukan status pembayaran)
            'status'               => $status,

            // Selaras dengan kode: gunakan "attempts" (bukan attempt_count)
            'attempts'             => $this->faker->numberBetween(0, 3),

            // Waktu audit (opsional)
            'received_at'          => now(),
            'last_attempt_at'      => null,
            'processed_at'         => $status === 'processed' ? now() : null,

            // Status pembayaran yang dinormalisasi (pending|succeeded|failed)
            'payment_status'       => $this->faker->randomElement(['pending', 'succeeded', 'failed']),

            // Retry window
            'next_retry_at'        => null,
            'error_message'        => null,
        ];
    }

    public function received(): static
    {
        return $this->state(fn () => [
            'status'          => 'received',
            'processed_at'    => null,
            'payment_status'  => 'pending',
        ]);
    }

    public function processed(): static
    {
        return $this->state(fn () => [
            'status'          => 'processed',
            'processed_at'    => now(),
            'payment_status'  => 'succeeded',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status'          => 'failed',
            'processed_at'    => null,
            'payment_status'  => 'failed',
        ]);
    }
}
