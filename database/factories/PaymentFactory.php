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
        return [
            // Set id ULID agar konsisten dengan HasUlids
            'id'              => (string) Str::ulid(),
            'amount'          => $this->faker->numberBetween(1000, 250000),
            'currency'        => 'IDR',
            'description'     => $this->faker->sentence(3),
            'metadata'        => [
                'customer_id' => 'cus_' . $this->faker->numberBetween(1000, 9999),
            ],
            'status'          => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'idempotency_key' => (string) Str::uuid(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn() => ['status' => 'pending']);
    }

    public function paid(): static
    {
        return $this->state(fn() => ['status' => 'paid']);
    }

    public function failed(): static
    {
        return $this->state(fn() => ['status' => 'failed']);
    }
}
