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
| - routes/api.php otomatis diprefix "/api" oleh Laravel.
| - Di file ini kita expose 2 surface sekaligus:
|   1) /api/...      (canonical)
|   2) /api/v1/...   (compat)
*/

$providers = (array) config('tenrusl.providers_allowlist', []);
$providers = array_values(array_unique(array_filter(array_map(
    static fn ($p) => strtolower(trim((string) $p)),
    $providers
), static fn ($p) => $p !== '')));

// Fail-closed: jika allowlist kosong, jangan match provider apa pun.
if ($providers === []) {
    $providers = ['__disabled__'];
}

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

        Route::get('/payments/{id}', [PaymentsController::class, 'show'])
            ->where('id', '[A-Za-z0-9\-]+')
            ->name('payments.show');

        Route::get('/payments/{provider}/{provider_ref}/status', [PaymentsController::class, 'status'])
            ->whereIn('provider', $providers)
            ->where('provider_ref', '[A-Za-z0-9\-\._]+')
            ->name('payments.status');

        // =========================
        // WEBHOOKS (PUBLIC)
        // =========================
        Route::post('/webhooks/{provider}', [WebhooksController::class, 'receive'])
            ->whereIn('provider', $providers)
            // throttle dulu supaya request burst ditahan sebelum kerja berat (signature parsing, dsb.)
            ->middleware(['throttle:webhooks', 'verify.webhook.signature'])
            ->name('webhooks.receive');

        // Preflight CORS untuk webhook (tanpa signature middleware)
        Route::options('/webhooks/{provider}', fn () => response()->noContent(204))
            ->whereIn('provider', $providers)
            ->middleware(['throttle:webhooks'])
            ->name('webhooks.options');

        // =========================
        // ADMIN (PROTECTED)
        // =========================
        Route::prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::get('/payments', [PaymentsController::class, 'adminIndex'])
                    ->name('payments.index');
            });
    });
};

// Canonical: /api/...
$register('', 'api.');

// Compat: /api/v1/...
$register('v1', 'api.v1.');

Route::fallback(function () {
    return response()->json([
        'error' => 'not_found',
        'message' => 'Endpoint not found',
    ], 404);
});
