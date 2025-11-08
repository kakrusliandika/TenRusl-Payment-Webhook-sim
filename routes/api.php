<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaymentsController;
use App\Http\Controllers\Api\V1\WebhooksController;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
| Prefix "api" ditambahkan otomatis oleh RouteServiceProvider.
| Di sini kita tambahkan "v1" untuk versioning endpoint.
*/

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {

        // Payments
        Route::post('/payments', [PaymentsController::class, 'store'])
            ->name('payments.store');

        Route::get('/payments/{id}', [PaymentsController::class, 'show'])
            ->where('id', '[A-Za-z0-9\-]+')
            ->name('payments.show');

        // Webhooks (proteksi signature/token per provider)
        Route::post('/webhooks/{provider}', [WebhooksController::class, 'store'])
            ->whereIn('provider', ['mock', 'xendit', 'midtrans'])
            ->middleware(['verify.webhook.signature'])
            ->name('webhooks.store');

        // (Opsional) preflight CORS
        Route::options('/webhooks/{provider}', fn () => response()->noContent(204))
            ->whereIn('provider', ['mock', 'xendit', 'midtrans']);
    });

/*
|--------------------------------------------------------------------------
| Fallback JSON 404
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json([
        'error'   => 'not_found',
        'message' => 'Endpoint not found',
        'code'    => '404',
    ], 404);
});
