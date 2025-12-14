<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\PaymentsController;
use App\Http\Controllers\Api\V1\WebhooksController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Catatan:
| - routes/api.php secara default sudah diprefix "/api" oleh Laravel.
| - Di file ini kita expose 2 surface sekaligus:
|   1) /api/...      (surface demo publik, canonical)
|   2) /api/v1/...   (compat dengan README / versi v1)
|
| Tujuan:
| - Frontend cukup ganti base URL saja (mis. .../api atau .../api/v1),
|   tanpa endpoint pecah.
*/

$providers = array_values((array) config('tenrusl.providers_allowlist', ['mock']));

/**
 * Register route-set untuk 1 prefix.
 *
 * @param string $prefix     contoh: '' atau 'v1'
 * @param string $namePrefix contoh: 'api.' atau 'api.v1.'
 */
$register = function (string $prefix, string $namePrefix) use ($providers): void {
    $group = Route::name($namePrefix);

    if ($prefix !== '') {
        $group = $group->prefix($prefix);
    }

    $group->group(function () use ($providers) {
        // =========================
        // PAYMENTS (PUBLIC)
        // =========================
        Route::post('/payments', [PaymentsController::class, 'store'])
            ->name('payments.store');

        /*
         * Fetch payment by id (refresh by id) — cocok untuk Admin UI & demo publik.
         * Endpoint:
         * - /api/payments/{id}
         * - /api/v1/payments/{id}
         */
        Route::get('/payments/{id}', [PaymentsController::class, 'show'])
            ->where('id', '[A-Za-z0-9\-]+')
            ->name('payments.show');

        /*
         * Legacy: status by provider/provider_ref
         * Endpoint:
         * - /api/payments/{provider}/{provider_ref}/status
         * - /api/v1/payments/{provider}/{provider_ref}/status
         */
        Route::get('/payments/{provider}/{provider_ref}/status', [PaymentsController::class, 'status'])
            ->whereIn('provider', $providers)
            ->where('provider_ref', '[A-Za-z0-9\-\._]+')
            ->name('payments.status');

        // =========================
        // WEBHOOKS (PUBLIC)
        // =========================
        Route::post('/webhooks/{provider}', [WebhooksController::class, 'receive'])
            ->whereIn('provider', $providers)
            ->middleware('verify.webhook.signature')
            ->name('webhooks.receive');

        // Preflight CORS untuk webhook (ikut allowlist agar provider unknown -> 404, bukan 405)
        Route::options('/webhooks/{provider}', fn () => response()->noContent(204))
            ->whereIn('provider', $providers)
            ->name('webhooks.options');

        // =========================
        // ADMIN (PROTECTED)
        // =========================
        /*
         * Proteksi admin key dilakukan di controller (agar tidak tergantung registrasi middleware baru),
         * tapi tetap dipisahkan path-nya agar jelas.
         *
         * Endpoint:
         * - /api/admin/payments
         * - /api/v1/admin/payments
         */
        Route::prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::get('/payments', [PaymentsController::class, 'adminIndex'])
                    ->name('payments.index');
            });
    });
};

// Canonical demo surface: /api/...
$register('', 'api.');

// Compat v1 surface: /api/v1/...
$register('v1', 'api.v1.');

/*
|--------------------------------------------------------------------------
| Fallback JSON 404 utk endpoint API yang tidak dikenali
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json([
        'error' => 'not_found',
        'message' => 'Endpoint not found',
    ], 404);
});
