<?php

use App\Http\Controllers\Api\V1\PaymentsController;
use App\Http\Controllers\Api\V1\WebhooksController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
| Catatan:
| - Semua route didefinisikan di file routes/* dan otomatis di-load via bootstrap/app.php. :contentReference[oaicite:2]{index=2}
| - routes/api.php umumnya berada di bawah middleware group "api" dan prefix "/api"
|   tergantung konfigurasi bootstrap/app.php project kamu.
|
| - Validation FormRequest (CreatePaymentRequest, WebhookRequest) dipakai dengan:
|     type-hint di method controller
|   Laravel akan menjalankan validasi sebelum controller dipanggil. :contentReference[oaicite:3]{index=3}
|
| - Middleware alias seperti 'verify.webhook.signature' harus didaftarkan dulu
|   (Laravel modern: alias di bootstrap/app.php), setelah itu bisa dipakai di route. :contentReference[oaicite:4]{index=4}
*/

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        // Provider allowlist untuk constraint route (biar route param {provider} aman)
        $providers = (array) config('tenrusl.providers_allowlist', ['mock', 'xendit', 'midtrans']);

        // -------------------------
        // Payments
        // -------------------------
        // POST /api/v1/payments
        // PaymentsController@store harus type-hint CreatePaymentRequest
        Route::post('/payments', [PaymentsController::class, 'store'])
            ->name('payments.store');

        // GET /api/v1/payments/{provider}/{provider_ref}/status
        Route::get('/payments/{provider}/{provider_ref}/status', [PaymentsController::class, 'status'])
            ->whereIn('provider', $providers)
            ->where('provider_ref', '[A-Za-z0-9\-\._]+')
            ->name('payments.status');

        // (Opsional) Preflight CORS (OPTIONS) untuk klien yang strict
        Route::options('/payments', fn () => response()->noContent(204))
            ->name('payments.options');

        // -------------------------
        // Webhooks — throttled + signature verify
        // -------------------------
        // Semua webhook dibatasi rate-limit dengan throttle:webhooks,
        // lalu diverifikasi signature lewat middleware verify.webhook.signature
        Route::middleware(['throttle:webhooks'])
            ->group(function () use ($providers) {
                // POST /api/v1/webhooks/{provider}
                // WebhooksController@receive harus type-hint WebhookRequest
                Route::post('/webhooks/{provider}', [WebhooksController::class, 'receive'])
                    ->whereIn('provider', $providers)
                    ->middleware('verify.webhook.signature')
                    ->name('webhooks.receive');

                // Preflight CORS (OPTIONS) untuk klien yang strict
                Route::options('/webhooks/{provider}', fn () => response()->noContent(204))
                    ->whereIn('provider', $providers)
                    ->name('webhooks.options');
            });
    });

/*
|--------------------------------------------------------------------------
| Fallback JSON 404 (untuk endpoint API tak dikenal)
|--------------------------------------------------------------------------
| Fallback route ini menjadi "penjaga terakhir" jika endpoint tidak match.
| Cocok untuk API agar respon 404 tetap JSON. :contentReference[oaicite:5]{index=5}
*/
Route::fallback(function () {
    return response()->json([
        'error' => 'not_found',
        'message' => 'Endpoint not found',
        'code' => '404',
    ], 404);
});
