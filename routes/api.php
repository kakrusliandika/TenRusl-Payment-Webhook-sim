<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaymentsController;
use App\Http\Controllers\Api\V1\WebhooksController;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
| Prefix "api" ditambahkan otomatis oleh bootstrap/app.php (Laravel 11+).
| Kita tambahkan "v1" untuk versioning endpoint.
*/

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        // Daftar provider diambil dari config (allowlist global)
        $providers = (array) config('tenrusl.providers_allowlist', ['mock','xendit','midtrans']);

        // -------------------------
        // Payments
        // -------------------------
        Route::post('/payments', [PaymentsController::class, 'store'])
            ->name('payments.store');

        // Status pembayaran berdasarkan {provider}/{provider_ref}
        Route::get('/payments/{provider}/{provider_ref}/status', [PaymentsController::class, 'status'])
            ->whereIn('provider', $providers)
            ->where('provider_ref', '[A-Za-z0-9\-\._]+')
            ->name('payments.status');

        // -------------------------
        // Webhooks — proteksi signature/token per provider
        // -------------------------
        Route::post('/webhooks/{provider}', [WebhooksController::class, 'receive'])
            ->whereIn('provider', $providers)
            ->middleware(['verify.webhook.signature'])
            ->name('webhooks.receive');

        // Preflight CORS (OPTIONS)
        Route::options('/webhooks/{provider}', fn () => response()->noContent(204))
            ->whereIn('provider', $providers);
    });

/*
|--------------------------------------------------------------------------
| Fallback JSON 404 (API endpoint tak dikenal)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json([
        'error'   => 'not_found',
        'message' => 'Endpoint not found',
        'code'    => '404',
    ], 404);
});
