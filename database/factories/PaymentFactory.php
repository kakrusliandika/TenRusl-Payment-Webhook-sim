<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $provider = $this->faker->randomElement(['mock', 'xendit', 'midtrans']);

        return [
            // Set id ULID agar konsisten dengan HasUlid
            'id' => (string) Str::ulid(),

            // Kolom yang dipakai kode/model
            'provider' => $provider,
            'provider_ref' => 'sim_'.$provider.'_'.Str::ulid()->toBase32(),

            'amount' => $this->faker->numberBetween(1_000, 250_000),
            'currency' => 'IDR',
            'description' => $this->faker->sentence(3),

            // Gunakan "meta" (bukan "metadata") agar konsisten dengan model & resource
            'meta' => [
                'customer_id' => 'cus_'.$this->faker->numberBetween(1000, 9999),
            ],

            // Konsisten dengan enum PaymentStatus
            'status' => $this->faker->randomElement(['pending', 'succeeded', 'failed']),

            // Tabel memiliki kolom unik idempotency_key → isi untuk kemudahan test/seed
            'idempotency_key' => (string) Str::uuid(),
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
