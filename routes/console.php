<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
| Command berbasis Closure di sini. Command class khusus diregistrasi via
| App\Console\Kernel (mis. tenrusl:webhooks:retry).
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Wrapper untuk menjalankan processor retry sekali (manual trigger).
 * Command utama: tenrusl:webhooks:retry (sudah ada di Console\Commands).
 */
Artisan::command('tenrusl:webhooks:retry-once', function () {
    $this->info('Delegating to: tenrusl:webhooks:retry');
    $this->call('tenrusl:webhooks:retry');
})->purpose('Run TenRusl webhook retry processor once');

/*
 * (Opsional) Utility: daftar rute API v1.
 */
Artisan::command('tenrusl:route:list-v1', function () {
    $this->call('route:list', ['--path' => 'api/v1']);
})->purpose('List API v1 routes');
