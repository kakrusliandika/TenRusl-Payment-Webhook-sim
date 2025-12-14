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
 * PaymentsController (API)
 * -----------------------
 * Exposed via routes:
 * - POST   /api/payments
 * - GET    /api/payments/{id}
 * - GET    /api/payments/{provider}/{provider_ref}/status (legacy)
 * - GET    /api/admin/payments (protected)
 *
 * Plus compat surface:
 * - /api/v1/... (same handlers)
 */
class PaymentsController extends Controller
{
    public function __construct(
        private readonly PaymentsService $payments,
        private readonly PaymentRepository $paymentsRepo,
        private readonly IdempotencyKeyService $idemp,
    ) {}

    /**
     * POST /api/payments  (alias: /api/v1/payments)
     * Buat pembayaran simulasi (idempotent).
     */
    public function store(CreatePaymentRequest $request): JsonResponse
    {
        $key = $this->idemp->resolveKey($request);

        // 1) Idempotent replay jika response sudah tersimpan
        if ($stored = $this->idemp->getStoredResponse($key)) {
            $headers = $stored['headers'];
            $headers['Idempotency-Key'] = $key;

            return response()
                ->json($stored['body'], (int) $stored['status'])
                ->withHeaders($headers);
        }

        // 2) Lock untuk mencegah concurrent execution
        if (! $this->idemp->acquireLock($key)) {
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
                'status' => strtolower($status),
                'meta' => $meta,
                // 'idempotency_key' => $key, // aktifkan jika kolomnya ada di DB
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
     * GET /api/payments/{id}  (alias: /api/v1/payments/{id})
     * Ambil payment by id dengan relasi yang relevan (untuk state terbaru).
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $payment = $this->paymentsRepo->findByIdWithRelations($id);

        if (! $payment) {
            return response()->json([
                'message' => 'Payment not found',
                'code' => 'not_found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => (new PaymentResource($payment))->toArray($request),
        ]);
    }

    /**
     * GET /api/payments/{provider}/{provider_ref}/status (alias: /api/v1/...)
     * Endpoint legacy untuk kompatibilitas.
     */
    public function status(Request $request, string $provider, string $providerRef): JsonResponse
    {
        $payment = $this->paymentsRepo->findByProviderRef($provider, $providerRef);

        if (! $payment) {
            return response()->json([
                'message' => 'Payment not found',
                'code' => 'not_found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => (new PaymentResource($payment))->toArray($request),
        ]);
    }

    /**
     * GET /api/admin/payments (alias: /api/v1/admin/payments)
     * List payments untuk Admin UI:
     * - pagination + filter + search + date range
     *
     * Proteksi:
     * - wajib header X-Admin-Key cocok dengan env/config yang diset.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        if ($denied = $this->denyIfNotAdmin($request)) {
            return $denied;
        }

        $filters = [
            'provider' => $this->cleanString($request->query('provider')),
            'status' => $this->cleanString($request->query('status')),
            'q' => $this->cleanString($request->query('q')),
            'created_from' => $this->cleanString($request->query('created_from')),
            'created_to' => $this->cleanString($request->query('created_to')),
        ];

        // Pagination (Laravel 12: pakai integer() untuk numeric query param) :contentReference[oaicite:2]{index=2}
        $perPage = $request->integer('per_page', 20);
        if ($perPage <= 0) {
            $perPage = 20;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $paginator = $this->paymentsRepo->paginateAdmin($filters, $perPage);

        // Jangan pakai getCollection() karena type analyzer melihat interface contract.
        $items = collect($paginator->items());

        $data = $items
            ->map(fn ($p) => (new PaymentResource($p))->toArray($request))
            ->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /**
     * Admin protection (demo-friendly):
     * - Header: X-Admin-Key: <key>
     *
     * Sumber key (ambil yang tersedia pertama):
     * - config('tenrusl.admin_demo_key')
     * - config('tenrusl.admin_key')
     * - env('TENRUSL_ADMIN_DEMO_KEY')
     * - env('ADMIN_DEMO_KEY')
     *
     * Default: jika key tidak diset sama sekali, endpoint admin ditolak (fail-closed).
     */
    private function denyIfNotAdmin(Request $request): ?JsonResponse
    {
        $expected = (string) (
            config('tenrusl.admin_demo_key')
            ?? config('tenrusl.admin_key')
            ?? env('TENRUSL_ADMIN_DEMO_KEY')
            ?? env('ADMIN_DEMO_KEY')
            ?? ''
        );

        if ($expected === '') {
            return response()->json([
                'message' => 'Admin demo is not configured',
                'code' => 'admin_not_configured',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $provided = (string) $request->header('X-Admin-Key', '');

        // Optional: support Authorization: Bearer <key>
        if ($provided === '') {
            $auth = (string) $request->header('Authorization', '');
            if (stripos($auth, 'bearer ') === 0) {
                $provided = trim(substr($auth, 7));
            }
        }

        if ($provided === '' || ! hash_equals($expected, $provided)) {
            return response()->json([
                'message' => 'Unauthorized',
                'code' => 'unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return null;
    }

    private function cleanString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $v = trim($value);

        return $v === '' ? null : $v;
    }
}
