<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreatePaymentRequest;
use App\Http\Resources\Api\V1\PaymentResource;
use App\Repositories\PaymentRepository;
use App\Services\Idempotency\IdempotencyKeyService;
use App\Services\Payments\PaymentsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PaymentsController (API v1)
 * --------------------------
 * Endpoint:
 * - POST   /api/v1/payments
 * - GET    /api/v1/payments/{provider}/{provider_ref}/status.
 *
 * Penting:
 * - store() WAJIB type-hint CreatePaymentRequest supaya validasi benar-benar dipakai.
 */
class PaymentsController extends Controller
{
    public function __construct(
        private readonly PaymentsService $payments,
        private readonly PaymentRepository $paymentsRepo,
        private readonly IdempotencyKeyService $idemp
    ) {
    }

    /**
     * POST /api/v1/payments
     * Buat pembayaran simulasi (idempotent).
     */
    public function store(CreatePaymentRequest $request): JsonResponse
    {
        $key = $this->idemp->resolveKey($request);

        // 1) Idempotent replay jika response sudah tersimpan
        if ($stored = $this->idemp->getStoredResponse($key)) {
            // PHPStan: offset 'headers' sudah pasti ada -> jangan pakai ?? di sini.
            $headers = $stored['headers'];
            $headers['Idempotency-Key'] = $key;

            return response()
                ->json($stored['body'], (int) $stored['status'])
                ->withHeaders($headers);
        }

        // 2) Lock untuk mencegah concurrent execution
        if (!$this->idemp->acquireLock($key)) {
            return response()
                ->json([
                    'message' => 'Idempotency conflict',
                    'code' => 'idempotency_conflict',
                ], Response::HTTP_CONFLICT)
                ->withHeaders(['Idempotency-Key' => $key]);
        }

        try {
            $data = $request->validated();

            // Normalisasi metadata:
            // - spec menerima 'meta' dan 'metadata' (alias)
            $meta = $data['meta'] ?? $data['metadata'] ?? [];

            // 3) Buat intent/simulasi via PaymentsService
            $created = $this->payments->create((string) $data['provider'], [
                'amount' => (int) $data['amount'],
                'currency' => (string) ($data['currency'] ?? 'IDR'),
                'description' => $data['description'] ?? null,
                'meta' => $meta,
            ]);

            // PHPStan: offset 'status' pada $created sudah pasti ada -> jangan pakai ??.
            $status = trim((string) $created['status']);
            if ($status === '') {
                $status = 'pending';
            }

            // 4) Persist ke DB
            $payment = $this->paymentsRepo->create([
                'provider' => (string) $created['provider'],
                'provider_ref' => (string) $created['provider_ref'],
                'amount' => (int) ($created['snapshot']['amount'] ?? $data['amount']),
                'currency' => (string) ($created['snapshot']['currency'] ?? ($data['currency'] ?? 'IDR')),
                'status' => $status,
                'meta' => $meta,
                // 'idempotency_key' => $key, // kalau kolomnya ada di DB
            ]);

            // 5) Response resource
            $resource = new PaymentResource($payment);
            $body = ['data' => $resource->toArray($request)];

            // 6) Simpan untuk idempotent replay
            $resp = [
                'status' => Response::HTTP_CREATED,
                'headers' => ['Idempotency-Key' => $key],
                'body' => $body,
            ];
            $this->idemp->storeResponse($key, $resp);

            return response()
                ->json($body, Response::HTTP_CREATED)
                ->withHeaders($resp['headers']);
        } finally {
            $this->idemp->releaseLock($key);
        }
    }

    /**
     * GET /api/v1/payments/{provider}/{provider_ref}/status.
     */
    public function status(Request $request, string $provider, string $providerRef): JsonResponse
    {
        $payment = $this->paymentsRepo->findByProviderRef($provider, $providerRef);

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found',
                'code' => 'not_found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => (new PaymentResource($payment))->toArray($request),
        ]);
    }
}
