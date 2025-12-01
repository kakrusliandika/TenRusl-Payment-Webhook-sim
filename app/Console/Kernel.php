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
        // Nilai-nilai di bawah ini dibaca dari config/tenrusl.php (yang biasanya baca dari env).
        $maxAttempts = (int) config('tenrusl.max_retry_attempts', 5);
        $provider    = (string) config('tenrusl.scheduler_provider', '');      // opsional filter
        $mode        = (string) config('tenrusl.scheduler_backoff_mode', 'full'); // full|equal|decorrelated
        $limit       = (int) config('tenrusl.scheduler_limit', 200);

        // Kirim sebagai parameter array (Laravel scheduler mendukung command via class + arg list). :contentReference[oaicite:2]{index=2}
        $params = [
            "--limit={$limit}",
            "--max-attempts={$maxAttempts}",
            "--mode={$mode}",
        ];

        if ($provider !== '') {
            $params[] = "--provider={$provider}";
        }

        $event = $schedule
            ->command(RetryWebhookCommand::class, $params)
            ->everyMinute()
            // Cegah overlap. Set expiry lock (menit) supaya tidak “nyangkut” lama bila proses crash. :contentReference[oaicite:3]{index=3}
            ->withoutOverlapping(10)
            ->name('tenrusl:webhooks:retry');

        // Kalau scheduler jalan di MULTI server dan cache driver kamu shared (redis/db/memcached/dynamodb),
        // kamu bisa aktifkan ini supaya hanya jalan di satu server. :contentReference[oaicite:4]{index=4}
        // $event->onOneServer();
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
