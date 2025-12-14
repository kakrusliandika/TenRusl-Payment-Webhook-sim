<?php

use App\Console\Commands\RetryWebhookCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|---------------------------------------------------------------------------
| Console Routes
|---------------------------------------------------------------------------
| - File ini di-load otomatis oleh bootstrap/app.php via:
|     ->withRouting(commands: base_path('routes/console.php'), ...)
|
| - Di Laravel 11/12+, scheduled tasks sering didefinisikan di file ini
|   memakai facade Schedule.
|
| - Scheduler akan dieksekusi oleh:
|     - `php artisan schedule:run` (via cron setiap menit), atau
|     - `php artisan schedule:work` (loop worker untuk local/dev).
|
| - Catatan penting:
|   `withoutOverlapping()` memakai cache lock/mutex.
|   Pastikan CACHE_STORE kamu bukan driver `array` jika scheduler dipakai beneran.
|---------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|---------------------------------------------------------------------------
| Wrapper: retry engine sekali (manual trigger)
|---------------------------------------------------------------------------
*/
Artisan::command('tenrusl:webhooks:retry-once {--queue : Dispatch ke queue (default inline)}', function () {
    $this->info('Delegating to: tenrusl:webhooks:retry');

    $args = [];
    if ((bool) $this->option('queue') === true) {
        $args[] = '--queue';
    }

    $this->call('tenrusl:webhooks:retry', $args);
})->purpose('Run TenRusl webhook retry processor once');

/*
|---------------------------------------------------------------------------
| Wrapper: retry 1 event by ID (untuk admin "retry now")
|---------------------------------------------------------------------------
| Contoh:
| - Inline: php artisan tenrusl:webhooks:retry-one 01J... --force
| - Queue : php artisan tenrusl:webhooks:retry-one 01J... --force --queue
*/
Artisan::command('tenrusl:webhooks:retry-one {id} {--force : Retry now} {--queue : Dispatch ke queue}', function (string $id) {
    $this->info('Delegating to: tenrusl:webhooks:retry --id=...');

    $args = [
        '--id='.$id,
    ];

    if ((bool) $this->option('force') === true) {
        $args[] = '--force';
    }

    if ((bool) $this->option('queue') === true) {
        $args[] = '--queue';
    }

    $this->call('tenrusl:webhooks:retry', $args);
})->purpose('Retry a single webhook event by primary key');

/*
|---------------------------------------------------------------------------
| Utility (opsional): list rute API v1
|---------------------------------------------------------------------------
*/
Artisan::command('tenrusl:route:list-v1', function () {
    $this->call('route:list', ['--path' => 'api/v1']);
})->purpose('List API v1 routes');

/*
|---------------------------------------------------------------------------
| Scheduler: TenRusl webhook retry engine
|---------------------------------------------------------------------------
| - withoutOverlapping() memakai cache locks.
| - Bisa aktifkan onOneServer() jika scheduler jalan di banyak server dan
|   cache store shared (redis/db/memcached/dynamodb).
|---------------------------------------------------------------------------
*/
$maxAttempts = (int) config('tenrusl.max_retry_attempts', 5);
$provider = (string) config('tenrusl.scheduler_provider', '');              // opsional filter provider
$mode = (string) config('tenrusl.scheduler_backoff_mode', 'full');          // full|equal|decorrelated
$limit = (int) config('tenrusl.scheduler_limit', 200);
$useQueue = (bool) config('tenrusl.scheduler_queue', false);               // opsional: jalankan via queue

// Sanitasi ringan
$maxAttempts = $maxAttempts <= 0 ? 1 : $maxAttempts;
$limit = $limit <= 0 ? 200 : min($limit, 2000);
$mode = $mode !== '' ? $mode : 'full';

// Format argumen CLI:
// tenrusl:webhooks:retry --limit=200 --max-attempts=5 --mode=full [--provider=xendit] [--queue]
$params = [
    "--limit={$limit}",
    "--max-attempts={$maxAttempts}",
    "--mode={$mode}",
];

if (trim($provider) !== '') {
    $params[] = '--provider='.trim($provider);
}

if ($useQueue) {
    $params[] = '--queue';
    $params[] = '--queue-name=webhooks';
}

$event = Schedule::command(RetryWebhookCommand::class, $params)
    ->everyMinute()

    // Hindari double-run kalau scheduler “ketarik” 2 kali / proses sebelumnya belum kelar.
    // Argumen 10 = menit TTL lock.
    ->withoutOverlapping(10)

    // Nama event scheduler (berguna saat schedule:list)
    ->name('tenrusl:webhooks:retry');

// Kalau scheduler jalan di banyak server & cache store shared,
// aktifkan ini biar hanya 1 server yang jalanin retry engine.
// $event->onOneServer();
