<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\RetryWebhookCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar command aplikasi (opsional; Laravel juga mendeteksi otomatis via artisan).
     *
     * @var array<class-string>
     */
    protected $commands = [
        RetryWebhookCommand::class,
    ];

    /**
     * Definisikan jadwal tugas.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan setiap menit, cegah overlap jika proses sebelumnya belum selesai.
        $schedule->command('tenrusl:webhooks:retry --limit=200 --max-attempts=' . (int) config('tenrusl.max_retry_attempts', 5))
            ->everyMinute()
            ->withoutOverlapping(); // gunakan cache lock default
    }

    /**
     * Daftarkan file route konsol (jika diperlukan).
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
