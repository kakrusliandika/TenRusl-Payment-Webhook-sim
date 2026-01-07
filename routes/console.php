<?php

use App\Console\Commands\RetryWebhookCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

/*
|---------------------------------------------------------------------------
| Console Routes
|---------------------------------------------------------------------------
| - Scheduler Laravel cukup dipicu oleh 1 cron entry:
|     * * * * * php /path-to-your-project/artisan schedule:run --no-interaction
|
| - Task schedule didefinisikan di source control (umumnya routes/console.php).
|---------------------------------------------------------------------------
*/

/*
|---------------------------------------------------------------------------
| Scheduler lock store (optional)
|---------------------------------------------------------------------------
| Kamu bisa memaksa scheduler memakai cache store tertentu untuk locks
| (berguna untuk multi-instance: Redis / database / memcached / dynamodb).
| Set via config/env (opsional): tenrusl.scheduler_cache_store
| Contoh: TENRUSL_SCHEDULER_CACHE_STORE=redis
|---------------------------------------------------------------------------
*/
$schedulerCacheStore = (string) config('tenrusl.scheduler_cache_store', '');
if ($schedulerCacheStore !== '') {
    Schedule::useCache($schedulerCacheStore);
}

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
| - onOneServer() menambah “single scheduler active” lintas instance
|   (wajib pakai cache store shared seperti Redis).
|---------------------------------------------------------------------------
*/
$maxAttempts = (int) config('tenrusl.max_retry_attempts', 5);
$provider = (string) config('tenrusl.scheduler_provider', '');              // opsional filter provider
$mode = (string) config('tenrusl.scheduler_backoff_mode', 'full');          // full|equal|decorrelated
$limit = (int) config('tenrusl.scheduler_limit', 200);
$useQueue = (bool) config('tenrusl.scheduler_queue', false);               // opsional: jalankan via queue

// Single scheduler active (lintas instance).
// Default: production = true, non-production = false.
$isProduction = app()->environment('production');
$singleScheduler = (bool) config('tenrusl.scheduler_singleton', $isProduction);

// Cache store yang dipakai scheduler untuk locks (default: cache.default).
$defaultCacheStore = (string) config('cache.default', '');
$lockCacheStore = $schedulerCacheStore !== '' ? $schedulerCacheStore : $defaultCacheStore;

// `onOneServer()` butuh cache driver yang shared lintas instance.
// Kalau store tidak cocok, disable otomatis biar tidak “terlihat singleton” padahal tidak.
$singleServerCapableStores = ['database', 'memcached', 'dynamodb', 'redis'];

if ($singleScheduler && ! in_array($lockCacheStore, $singleServerCapableStores, true)) {
    Log::warning('TenRusl scheduler: disabling onOneServer() because cache store is not single-server capable.', [
        'cache_store' => $lockCacheStore,
        'expected_one_of' => $singleServerCapableStores,
    ]);
    $singleScheduler = false;
}

// `withoutOverlapping()` memakai cache locks; driver `array` tidak cocok untuk production.
if ($lockCacheStore === 'array') {
    Log::warning('TenRusl scheduler: cache store "array" is not suitable for scheduler locks. Use redis/database for production.', [
        'cache_store' => $lockCacheStore,
    ]);
}

// TTL lock untuk withoutOverlapping (menit)
$overlapMinutes = (int) config('tenrusl.scheduler_lock_minutes', 10);

// Sanitasi ringan
$maxAttempts = $maxAttempts <= 0 ? 1 : $maxAttempts;
$limit = $limit <= 0 ? 200 : min($limit, 2000);
$mode = $mode !== '' ? $mode : 'full';
$overlapMinutes = $overlapMinutes <= 0 ? 10 : min($overlapMinutes, 60);

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
    ->withoutOverlapping($overlapMinutes)
    ->name('tenrusl:webhooks:retry');

if ($singleScheduler) {
    $event->onOneServer();
}
