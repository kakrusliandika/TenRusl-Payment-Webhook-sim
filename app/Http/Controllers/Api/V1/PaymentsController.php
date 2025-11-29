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

class PaymentsController extends Controller
{
    public function __construct(
        private readonly PaymentsService $payments,
        private readonly PaymentRepository $paymentsRepo,
        private readonly IdempotencyKeyService $idemp
    ) {}

    /**
     * POST /api/v1/payments
     * Buat pembayaran simulasi (idempotent).
     */
    public function store(CreatePaymentRequest $request): JsonResponse
    {
        $key = $this->idemp->resolveKey($request);

        // Jika sudah ada hasil sebelumnya, kembalikan (idempotent replay)
        if ($stored = $this->idemp->getStoredResponse($key)) {
            return response()
                ->json($stored['body'], $stored['status'])
                ->withHeaders(array_merge($stored['headers'] ?? [], [
                    'Idempotency-Key' => $key,
                ]));
        }

        // Cegah eksekusi ganda paralel
        if (! $this->idemp->acquireLock($key)) {
            // Klien melakukan retry paralel; beri sinyal 409 agar retry kemudian
            return response()
                ->json(['message' => 'Idempotency conflict'], Response::HTTP_CONFLICT)
                ->withHeaders(['Idempotency-Key' => $key]);
        }

        try {
            $data = $request->validated();

            // 1) Buat "intent" di adapter simulator
            $created = $this->payments->create($data['provider'], [
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'IDR',
                'description' => $data['description'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ]);

            // 2) Persist ke DB
            $payment = $this->paymentsRepo->create([
                'provider' => $created['provider'],
                'provider_ref' => $created['provider_ref'],
                'amount' => (string) $created['snapshot']['amount'],
                'currency' => (string) $created['snapshot']['currency'],
                'status' => (string) $created['status'],
                'meta' => $created['snapshot'] ?? [],
            ]);

            // 3) Bentuk response API resource
            $resource = new PaymentResource($payment);
            $body = [
                'data' => $resource->toArray($request),
            ];

            // 4) Simpan hasil untuk idempotent replay
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
     * GET /api/v1/payments/{provider}/{provider_ref}/status
     */
    public function status(Request $request, string $provider, string $providerRef): JsonResponse
    {
        // Coba ambil dari DB; jika tidak ada, fallback ke adapter (tetap pending)
        $payment = $this->paymentsRepo->findByProviderRef($provider, $providerRef);

        if ($payment) {
            return response()->json([
                'data' => (new PaymentResource($payment))->toArray($request),
            ]);
        }

        $status = $this->payments->status($provider, $providerRef);

        return response()->json([
            'data' => [
                'provider' => $status['provider'],
                'provider_ref' => $status['provider_ref'],
                'status' => $status['status'],
            ],
        ]);
    }
}
