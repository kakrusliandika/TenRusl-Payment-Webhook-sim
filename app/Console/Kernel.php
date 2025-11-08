<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftarkan command artisan kustom (opsional — auto-discover juga jalan).
     * @var array<class-string>
     */
    protected $commands = [
        \App\Console\Commands\RetryWebhookCommand::class,
    ];

    /**
     * Definisikan jadwal eksekusi artisan commands.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan setiap menit, tanpa overlap, di background
        $schedule->command('tenrusl:webhooks:retry --limit=100 --max=5')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->onOneServer(); // jika pakai multiple worker/server

        // (opsional) Tambahkan pembersihan event processed lama
        // $schedule->call(fn () => app(\App\Repositories\WebhookEventRepository::class)->purgeProcessedOlderThan(30))
        //     ->dailyAt('02:00')
        //     ->runInBackground();
    }

    /**
     * Daftarkan closures command dari routes/console.php
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
