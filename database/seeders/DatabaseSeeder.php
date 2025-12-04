<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\WebhookEvent;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Payments contoh
        Payment::factory()
            ->count(3)
            ->state(['status' => 'pending'])
            ->create();

        Payment::factory()
            ->count(2)
            ->state(['status' => 'succeeded'])
            ->create();

        // Webhook events contoh (hindari panggil state method yang belum tentu ada)
        WebhookEvent::factory()
            ->state(['status' => 'received'])
            ->create();

        WebhookEvent::factory()
            ->state(['status' => 'processed'])
            ->create();
    }
}
