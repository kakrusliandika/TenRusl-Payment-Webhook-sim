<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $provider = $this->faker->randomElement(['mock', 'xendit', 'midtrans']);

        $amount = $this->faker->numberBetween(1_000, 250_000);
        $currency = 'IDR';
        $description = $this->faker->sentence(3);

        // Kolom unik idempotency_key → isi untuk kemudahan test/seed
        $idemKey = (string) Str::uuid();

        // Fingerprint “stabil” untuk mendeteksi konflik idempotency
        $requestHash = hash('sha256', implode('|', [
            'provider='.$provider,
            'amount='.$amount,
            'currency='.$currency,
            'description='.$description,
            'idempotency_key='.$idemKey,
        ]));

        return [
            // Set id ULID agar konsisten dengan HasUlid
            'id' => (string) Str::ulid(),

            // Identitas provider
            'provider' => $provider,
            'provider_ref' => 'sim_'.$provider.'_'.Str::ulid()->toBase32(),

            // Payment fields
            'amount' => $amount,
            'currency' => $currency,
            'description' => $description,

            // Konsisten: gunakan "meta"
            'meta' => [
                'customer_id' => 'cus_'.$this->faker->numberBetween(1000, 9999),
            ],

            // pending | succeeded | failed
            'status' => $this->faker->randomElement(['pending', 'succeeded', 'failed']),

            // Idempotency fields
            'idempotency_key' => $idemKey,
            'idempotency_request_hash' => $requestHash,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function succeeded(): static
    {
        return $this->state(fn () => ['status' => 'succeeded']);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => 'failed']);
    }
}
