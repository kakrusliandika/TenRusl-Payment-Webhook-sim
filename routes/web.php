<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProviderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (named + SEO-friendly)
|--------------------------------------------------------------------------
| - '/'                      => main/index
| - '/providers'             => pages/providers (via ProviderController@index)
| - '/providers/{provider}'  => pages/show (via ProviderController@show)
| - Endpoint JSON demo payments:
|     - '/payments/providers'                        => PaymentController@providers
|     - '/payments/{provider}/{providerRef}/status'  => PaymentController@status
| - 'setlocale' is applied to the 'web' group via bootstrap/app.php
*/

// Global pattern untuk parameter "provider"
Route::pattern('provider', '[A-Za-z0-9][A-Za-z0-9_\-]*');

// ================= Home =================

// Home / landing
Route::get('/', fn () => view('main.index'))->name('home');

// ================= Providers =================

// Daftar providers (catalog)
Route::get('/providers', [ProviderController::class, 'index'])
    ->name('providers.index');

// Detail provider
Route::get('/providers/{provider}', [ProviderController::class, 'show'])
    ->name('providers.show');

// Back-compat redirects (opsional)
Route::redirect('/p/providers', '/providers', 301);

Route::get('/p/{provider}', function (string $provider) {
    return redirect()->route('providers.show', ['provider' => $provider], 301);
});

// ================= Payments demo (JSON sederhana) =================

// Daftar provider aktif via JSON (non-API, sekadar demo)
Route::get('/payments/providers', [PaymentController::class, 'providers'])
    ->name('payments.providers');

// Status simulasi payment dari adapter (tanpa DB)
Route::get(
    '/payments/{provider}/{providerRef}/status',
    [PaymentController::class, 'status']
)->name('payments.status');

// ================= Health =================

// Health check (204)
Route::get('/health', fn () => response()->noContent())->name('health');

// ================= Fallback -> proper 404 =================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
