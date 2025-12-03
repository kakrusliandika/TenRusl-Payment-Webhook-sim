<?php

use App\Console\Commands\RetryWebhookCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
| - File ini di-load otomatis oleh bootstrap/app.php via:
|     ->withRouting(commands: base_path('routes/console.php'), ...)
|
| - Di Laravel 11/12+, scheduled tasks "biasanya" didefinisikan di file ini
|   memakai facade Schedule. Ini menghindari kasus scheduler “mandek” karena
|   schedule ditulis di tempat yang tidak dieksekusi framework-nya.
|
| - Scheduler akan dieksekusi oleh:
|     - `php artisan schedule:run` (via cron setiap menit), atau
|     - `php artisan schedule:work` (loop worker untuk local/dev).
|
| - Catatan penting:
|   `withoutOverlapping()` memakai cache lock/mutex. Jadi pastikan CACHE_STORE
|   kamu bukan driver `array` kalau scheduler beneran dipakai. (file/redis/db OK)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Contoh command bawaan
|--------------------------------------------------------------------------
*/
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Wrapper untuk menjalankan retry sekali (manual trigger)
|--------------------------------------------------------------------------
| Command utama tetap: tenrusl:webhooks:retry (class command).
| Wrapper ini cuma memanggil command tersebut, enak buat testing manual.
*/
Artisan::command('tenrusl:webhooks:retry-once', function () {
    $this->info('Delegating to: tenrusl:webhooks:retry');

    // Memanggil command utama yang kamu punya di app/Console/Commands
    $this->call('tenrusl:webhooks:retry');
})->purpose('Run TenRusl webhook retry processor once');

/*
|--------------------------------------------------------------------------
| Utility (opsional): list rute API v1
|--------------------------------------------------------------------------
*/
Artisan::command('tenrusl:route:list-v1', function () {
    $this->call('route:list', ['--path' => 'api/v1']);
})->purpose('List API v1 routes');

/*
|--------------------------------------------------------------------------
| Scheduler: TenRusl webhook retry engine
|--------------------------------------------------------------------------
| Kita jadwalkan command class RetryWebhookCommand tiap menit + withoutOverlapping.
| Param disuntik dari config('tenrusl.*') supaya bisa diatur lewat env/config.
|
| Referensi Laravel:
| - Schedule::command(CommandClass::class, [...args]) untuk scheduled Artisan command
| - withoutOverlapping() memakai cache lock/mutex
|--------------------------------------------------------------------------
*/
$maxAttempts = (int) config('tenrusl.max_retry_attempts', 5);
$provider = (string) config('tenrusl.scheduler_provider', '');          // opsional filter provider
$mode = (string) config('tenrusl.scheduler_backoff_mode', 'full');  // full|equal|decorrelated
$limit = (int) config('tenrusl.scheduler_limit', 200);

// Sanitasi ringan (biar tidak kebablasan)
$maxAttempts = $maxAttempts <= 0 ? 1 : $maxAttempts;
$limit = $limit <= 0 ? 200 : min($limit, 2000);
$mode = $mode !== '' ? $mode : 'full';

// Format argumen seperti CLI:
// tenrusl:webhooks:retry --limit=200 --max-attempts=5 --mode=full [--provider=xendit]
$params = [
    "--limit={$limit}",
    "--max-attempts={$maxAttempts}",
    "--mode={$mode}",
];

if (trim($provider) !== '') {
    $params[] = '--provider='.trim($provider);
}

Schedule::command(RetryWebhookCommand::class, $params)
    ->everyMinute()

    // Hindari double-run kalau scheduler “ketarik” 2 kali / proses sebelumnya belum kelar.
    // Argumen 10 = menit TTL lock.
    ->withoutOverlapping(10)

    // Nama event scheduler (berguna saat schedule:list)
    ->name('tenrusl:webhooks:retry');

// Kalau scheduler jalan di banyak server dan cache store shared (redis/db/memcached),
// kamu bisa aktifkan ini supaya hanya 1 server yang jalanin job.
// ->onOneServer();
