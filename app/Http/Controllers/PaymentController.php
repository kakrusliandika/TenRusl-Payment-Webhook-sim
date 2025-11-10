<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Payments\PaymentsService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Daftar provider aktif (untuk halaman/info sederhana via web route).
     */
    public function providers(PaymentsService $payments): JsonResponse
    {
        return response()->json([
            'providers' => $payments->providers(),
        ], 200);
    }

    /**
     * Ambil status simulasi dari adapter (tanpa menyentuh DB).
     * Endpoint web sederhana; untuk API V1 gunakan controller API.
     */
    public function status(
        string $provider,
        string $providerRef,
        PaymentsService $payments
    ): JsonResponse {
        $status = $payments->status($provider, $providerRef);

        return response()->json($status, 200);
    }
}
