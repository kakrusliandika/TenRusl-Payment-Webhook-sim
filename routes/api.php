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

$hasAllowlist = $providers !== [];

// Provider slug: lowercase + angka + underscore/dash (contoh: amazon_bwp)
$providerPattern = '[a-z0-9][a-z0-9\-_]*';

/**
 * Register route-set untuk 1 prefix.
 *
 * @param string $prefix     contoh: '' atau 'v1'
 * @param string $namePrefix contoh: 'api.' atau 'api.v1.'
 */
$register = function (string $prefix, string $namePrefix) use ($providers, $hasAllowlist, $providerPattern): void {
    $group = Route::name($namePrefix);

    if ($prefix !== '') {
        $group = $group->prefix($prefix);
    }

    $group->group(function () use ($providers, $hasAllowlist, $providerPattern) {
        // =========================
        // PAYMENTS (PUBLIC)
        // =========================
        Route::post('/payments', [PaymentsController::class, 'store'])
            ->name('payments.store');

        Route::get('/payments/{id}', [PaymentsController::class, 'show'])
            ->where('id', '[A-Za-z0-9\-]+')
            ->name('payments.show');

        $statusRoute = Route::get('/payments/{provider}/{provider_ref}/status', [PaymentsController::class, 'status'])
            ->where('provider', $providerPattern)
            ->where('provider_ref', '[A-Za-z0-9\-\._]+')
            ->name('payments.status');

        // Jika allowlist terisi, batasi parameter provider (lebih ketat).
        // Jika kosong, biarkan route tetap match agar tidak "diam-diam 404";
        // validasi allowlist ditangani di boot (production) / layer aplikasi.
        if ($hasAllowlist) {
            $statusRoute->whereIn('provider', $providers);
        }

        // =========================
        // WEBHOOKS (PUBLIC)
        // =========================
        $webhookRoute = Route::post('/webhooks/{provider}', [WebhooksController::class, 'receive'])
            ->where('provider', $providerPattern)
            // throttle dulu supaya request burst ditahan sebelum kerja berat (signature parsing, dsb.)
            ->middleware(['throttle:webhooks', 'verify.webhook.signature'])
            ->name('webhooks.receive');

        if ($hasAllowlist) {
            $webhookRoute->whereIn('provider', $providers);
        }

        // Preflight CORS untuk webhook (tanpa signature middleware)
        $optionsRoute = Route::options('/webhooks/{provider}', fn () => response()->noContent(204))
            ->where('provider', $providerPattern)
            ->middleware(['throttle:webhooks'])
            ->name('webhooks.options');

        if ($hasAllowlist) {
            $optionsRoute->whereIn('provider', $providers);
        }

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
