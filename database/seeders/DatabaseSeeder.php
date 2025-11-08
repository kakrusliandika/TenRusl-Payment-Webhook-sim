<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\WebhookEvent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat beberapa payment contoh
        Payment::factory()->count(3)->pending()->create();
        Payment::factory()->count(2)->paid()->create();

        // Buat beberapa event contoh (tidak duplikat provider+event_id)
        WebhookEvent::factory()->received()->create();
        WebhookEvent::factory()->processed()->create();
    }
}
