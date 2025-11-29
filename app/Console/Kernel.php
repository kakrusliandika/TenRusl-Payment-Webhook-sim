<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\RetryWebhookCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar command aplikasi.
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
        // Jalankan tiap menit; batasi overlap agar aman di beban tinggi.
        $maxAttempts = (int) config('tenrusl.max_retry_attempts', 5);
        $provider = (string) config('tenrusl.scheduler_provider', '');      // opsional filter
        $mode = (string) config('tenrusl.scheduler_backoff_mode', 'full'); // full|equal|decorrelated
        $limit = (int) config('tenrusl.scheduler_limit', 200);

        $cmd = sprintf(
            'tenrusl:webhooks:retry --limit=%d --max-attempts=%d --mode=%s',
            $limit,
            $maxAttempts,
            $mode
        );

        if ($provider !== '') {
            $cmd .= ' --provider='.$provider;
        }

        $schedule
            ->command($cmd)
            ->everyMinute()
            ->withoutOverlapping(); // gunakan cache lock default
    }

    /**
     * Daftarkan file route konsol (jika diperlukan).
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
