<?php

// routes/api.php

use App\Http\Controllers\Api\V1\PaymentsController;
use App\Http\Controllers\Api\V1\WebhooksController;
use Illuminate\Support\Facades\Route;

/*
|---------------------------------------------------------------------------
| API Routes (v1)
|---------------------------------------------------------------------------
| Catatan penting:
| - File routes/api.php umumnya sudah berada di bawah prefix "/api" + middleware group "api"
|   (tergantung bootstrap/app.php project kamu).
| - Validasi FormRequest dijalankan otomatis saat FormRequest di type-hint di controller method
|   (jadi validasi terjadi sebelum method controller terpanggil).
| - Middleware alias (contoh: 'verify.webhook.signature') harus didaftarkan dulu
|   (Laravel modern: alias di bootstrap/app.php), baru bisa dipakai di route.
*/

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        /*
        |-----------------------------------------------------------------------
        | Provider allowlist untuk constraint route
        |-----------------------------------------------------------------------
        | Ini supaya {provider} hanya menerima nilai yang kita izinkan.
        | Pastikan list ini sinkron dengan config('tenrusl.providers_allowlist').
        */
        $providers = (array) config('tenrusl.providers_allowlist', ['mock', 'xendit', 'midtrans']);

        // ================================================================
        // PAYMENTS
        // ================================================================

        /*
        | POST /api/v1/payments
        | - Controller method harus type-hint CreatePaymentRequest
        | - Idempotency via header Idempotency-Key ditangani di controller/service
        */
        Route::post('/payments', [PaymentsController::class, 'store'])
            ->name('payments.store');

        /*
        | GET /api/v1/payments/{provider}/{provider_ref}/status
        | - Optional (tetap dipertahankan karena biasanya ada di README / OpenAPI)
        */
        Route::get('/payments/{provider}/{provider_ref}/status', [PaymentsController::class, 'status'])
            ->whereIn('provider', $providers)
            ->where('provider_ref', '[A-Za-z0-9\-\._]+')
            ->name('payments.status');

        // (Opsional) Preflight CORS untuk client yang strict
        Route::options('/payments', fn () => response()->noContent(204))
            ->name('payments.options');

        // ================================================================
        // WEBHOOKS
        // ================================================================

        /*
        | POST /api/v1/webhooks/{provider}
        | Wajib:
        | - path fix sesuai README: /api/v1/webhooks/{provider}
        | - middleware signature dipasang TEPAT di route webhook
        |
        | Order middleware (praktik umum):
        | - throttle dulu (hemat CPU), lalu signature verify (security gate)
        */
        Route::middleware(['throttle:webhooks'])
            ->group(function () use ($providers) {
                Route::post('/webhooks/{provider}', [WebhooksController::class, 'receive'])
                    ->whereIn('provider', $providers)
                    ->middleware('verify.webhook.signature')
                    ->name('webhooks.receive');

                // Preflight CORS (OPTIONS) untuk webhook
                Route::options('/webhooks/{provider}', fn () => response()->noContent(204))
                    ->whereIn('provider', $providers)
                    ->name('webhooks.options');
            });
    });

/*
|---------------------------------------------------------------------------
| Fallback JSON 404 (untuk endpoint API tak dikenal)
|---------------------------------------------------------------------------
| Penjaga terakhir kalau tidak ada route yang match.
| Cocok untuk API agar 404 tetap JSON.
*/
Route::fallback(function () {
    return response()->json([
        'error' => 'not_found',
        'message' => 'Endpoint not found',
        'code' => '404',
    ], 404);
});
