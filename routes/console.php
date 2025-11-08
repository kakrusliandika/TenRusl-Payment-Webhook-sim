<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
| Di sini kita definisikan command berbasis closure.
| Command retry utama akan dibuat di langkah 12 dengan signature:
|   tenrusl:webhooks:retry
| File ini menyediakan "wrapper" agar mudah mengeksekusi sekali jalan.
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Wrapper untuk menjalankan processor retry sekali (manual trigger).
 * Setelah langkah 12, command asli "tenrusl:webhooks:retry" akan tersedia.
 */
Artisan::command('tenrusl:webhooks:retry-once', function () {
    $this->info('Delegating to: tenrusl:webhooks:retry');
    $this->call('tenrusl:webhooks:retry');
})->purpose('Run TenRusl webhook retry processor once');

/*
 * (Opsional) Command utilitas untuk melihat daftar rute API v1.
 */
Artisan::command('tenrusl:route:list-v1', function () {
    $this->call('route:list', ['--path' => 'api/v1']);
})->purpose('List API v1 routes');
