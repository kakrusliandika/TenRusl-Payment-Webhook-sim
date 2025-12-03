<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\RetryWebhookCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/*
|--------------------------------------------------------------------------
| Console Kernel
|--------------------------------------------------------------------------
| - Di Laravel 11/12+, scheduling “biasanya” didefinisikan di routes/console.php
|   atau lewat ->withSchedule() di bootstrap/app.php.
|
| - File Kernel ini masih valid untuk:
|   1) registrasi command class secara eksplisit (kalau kamu tidak mengandalkan auto-discovery),
|   2) kebutuhan kompatibilitas project yang masih pakai pola Kernel.
|
| - Karena schedule sudah kita taruh di routes/console.php, method schedule() di sini
|   sengaja dikosongkan untuk menghindari jadwal dobel.
|--------------------------------------------------------------------------
*/
class Kernel extends ConsoleKernel
{
    /**
     * Daftar command aplikasi.
     *
     * Catatan:
     * - Kalau kamu pakai auto-discovery command, list ini bisa dikosongkan.
     * - Tapi menaruhnya di sini itu “aman” dan eksplisit.
     *
     * @var array<class-string>
     */
    protected $commands = [
        RetryWebhookCommand::class,
    ];

    /**
     * Definisikan jadwal tugas.
     *
     * Penting:
     * - Untuk menghindari double schedule, jadwal utama TenRusl ada di routes/console.php.
     * - Kalau kamu *memutuskan* memindahkan scheduling balik ke Kernel, pindahkan block
     *   Schedule::command(...) dari routes/console.php ke sini (dan hapus di routes/console.php).
     */
    protected function schedule(Schedule $schedule): void
    {
        // Intentionally left blank (schedule lives in routes/console.php).
    }

    /**
     * Daftarkan command tambahan (jika diperlukan).
     *
     * Karena bootstrap/app.php sudah memuat routes/console.php, kita gunakan require_once
     * biar tidak kedobel kalau suatu saat ini juga dipanggil.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require_once base_path('routes/console.php');
    }
}
