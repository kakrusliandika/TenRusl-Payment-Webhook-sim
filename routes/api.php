<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaymentsController;
use App\Http\Controllers\Api\V1\WebhooksController;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
| - Prefix '/api' diterapkan otomatis oleh bootstrap/app.php.
| - Di bawah ini kita tambahkan prefix 'v1' untuk versioning: '/api/v1/...'
| - Middleware 'api' group juga diterapkan otomatis oleh bootstrap/app.php.
*/

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        // Provider allowlist untuk constraint
        $providers = (array) config('tenrusl.providers_allowlist', ['mock','xendit','midtrans']);

        // -------------------------
        // Payments
        // -------------------------
        Route::post('/payments', [PaymentsController::class, 'store'])
            ->name('payments.store');

        Route::get('/payments/{provider}/{provider_ref}/status', [PaymentsController::class, 'status'])
            ->whereIn('provider', $providers)
            ->where('provider_ref', '[A-Za-z0-9\-\._]+')
            ->name('payments.status');

        // -------------------------
        // Webhooks — throttled + signature verify
        // -------------------------
        Route::post('/webhooks/{provider}', [WebhooksController::class, 'receive'])
            ->whereIn('provider', $providers)
            ->middleware(['throttle:webhooks', 'verify.webhook.signature'])
            ->name('webhooks.receive');

        // Preflight CORS (OPTIONS) untuk klien yang strict
        Route::options('/webhooks/{provider}', fn () => response()->noContent(204))
            ->whereIn('provider', $providers);
    });

/*
|--------------------------------------------------------------------------
| Fallback JSON 404 (untuk endpoint API tak dikenal)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json([
        'error'   => 'not_found',
        'message' => 'Endpoint not found',
        'code'    => '404',
    ], 404);
});
