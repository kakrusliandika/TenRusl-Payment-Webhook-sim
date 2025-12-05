<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\PaymentsController;
use App\Http\Controllers\Api\V1\WebhooksController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
| Catatan:
| - File routes/api.php umumnya otomatis berada di bawah prefix "/api".
| - Jadi Route::prefix('v1') di bawah menghasilkan endpoint:
|   - /api/v1/payments
|   - /api/v1/webhooks/{provider}
*/

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        /**
         * Provider allowlist untuk constraint route.
         * Tujuan utama:
         * - Provider yang tidak dikenal TIDAK MATCH route sama sekali ⇒ 404 (bukan 405).
         */
        $providers = array_values((array) config('tenrusl.providers_allowlist', ['mock']));

        // =========================
        // PAYMENTS
        // =========================
        Route::post('/payments', [PaymentsController::class, 'store'])
            ->name('payments.store');

        Route::get('/payments/{provider}/{provider_ref}/status', [PaymentsController::class, 'status'])
            ->whereIn('provider', $providers)
            ->where('provider_ref', '[A-Za-z0-9\-\._]+')
            ->name('payments.status');

        // =========================
        // WEBHOOKS
        // =========================
        Route::post('/webhooks/{provider}', [WebhooksController::class, 'receive'])
            ->whereIn('provider', $providers)
            ->middleware('verify.webhook.signature')
            ->name('webhooks.receive');

        // Preflight CORS untuk webhook (harus ikut allowlist agar provider unknown jadi 404, bukan 405)
        Route::options('/webhooks/{provider}', fn () => response()->noContent(204))
            ->whereIn('provider', $providers)
            ->name('webhooks.options');
    });

/*
|--------------------------------------------------------------------------
| Fallback JSON 404 utk endpoint API yang tidak dikenali
|--------------------------------------------------------------------------
| Ditaruh paling bawah agar hanya jalan kalau tidak ada route yang match.
*/
Route::fallback(function () {
    return response()->json([
        'error' => 'not_found',
        'message' => 'Endpoint not found',
    ], 404);
});
