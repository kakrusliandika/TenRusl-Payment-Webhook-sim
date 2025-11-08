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
        $paymentId = (string) Str::ulid();

        return [
            'id'             => (string) Str::ulid(),
            'provider'       => $this->faker->randomElement(['mock', 'xendit', 'midtrans']),
            'event_id'       => 'evt_' . $this->faker->unique()->numerify('#####'),
            'signature_hash' => $this->faker->sha256(),
            'payload'        => [
                'event_id' => 'evt_' . $this->faker->numerify('#####'),
                'type'     => $this->faker->randomElement(['payment.paid', 'payment.failed']),
                'data'     => [
                    'payment_id' => $paymentId,
                    'amount'     => $this->faker->numberBetween(1000, 250000),
                    'currency'   => 'IDR',
                ],
                'sent_at'  => now()->toISOString(),
            ],
            'status'         => $this->faker->randomElement(['received', 'processed', 'failed']),
            'attempt_count'  => $this->faker->numberBetween(0, 3),
            'next_retry_at'  => null,
            'error_message'  => null,
        ];
    }

    public function received(): static
    {
        return $this->state(fn() => ['status' => 'received']);
    }

    public function processed(): static
    {
        return $this->state(fn() => ['status' => 'processed']);
    }

    public function failed(): static
    {
        return $this->state(fn() => ['status' => 'failed']);
    }
}
