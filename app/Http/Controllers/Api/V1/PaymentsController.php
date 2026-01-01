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
     * Buat pembayaran simulasi (idempotent, production-grade semantics):
     * - Key sama + request sama => replay
     * - Key sama + request beda => 409 konflik (mismatch)
     */
    public function store(CreatePaymentRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Normalisasi metadata:
        // - spec menerima 'meta' dan 'metadata' (alias)
        $meta = $data['meta'] ?? $data['metadata'] ?? [];
        if (! is_array($meta)) {
            $meta = [];
        }

        $key = $this->idemp->resolveKey($request);

        // Fingerprint request untuk mismatch detection (stabil, berbasis payload ter-normalisasi)
        $requestHash = $this->computeIdempotencyRequestHash([
            'provider' => (string) $data['provider'],
            'amount' => (int) $data['amount'],
            'currency' => (string) ($data['currency'] ?? 'IDR'),
            'description' => $data['description'] ?? null,
            'meta' => $meta,
        ]);

        // 0) Jika DB sudah punya payment dengan key ini -> replay / mismatch
        $existing = $this->paymentsRepo->findByIdempotencyKey($key);
        if ($existing) {
            $storedHash = (string) ($existing->idempotency_request_hash ?? '');

            if ($storedHash !== '' && ! hash_equals($storedHash, $requestHash)) {
                logger()->warning('Idempotency key mismatch (db)', [
                    'idempotency_key' => $key,
                    'payment_id' => (string) $existing->getKey(),
                    'stored_hash' => $storedHash,
                    'incoming_hash' => $requestHash,
                ]);

                return response()
                    ->json([
                        'message' => 'Idempotency-Key already used with different request parameters',
                        'code' => 'idempotency_mismatch',
                    ], Response::HTTP_CONFLICT)
                    ->withHeaders(['Idempotency-Key' => $key]);
            }

            // Backfill hash jika belum tersimpan (best-effort)
            if ($storedHash === '') {
                $existing->idempotency_request_hash = $requestHash;
                $existing->save();
            }

            return response()
                ->json([
                    'data' => (new PaymentResource($existing))->toArray($request),
                ], Response::HTTP_CREATED)
                ->withHeaders([
                    'Idempotency-Key' => $key,
                    'Idempotency-Replayed' => 'true',
                ]);
        }

        // 1) Idempotent replay dari cache jika ada (opsional, untuk kecepatan)
        if ($stored = $this->idemp->getStoredResponse($key)) {
            $headers = $stored['headers'];
            $headers['Idempotency-Key'] = $key;
            $headers['Idempotency-Replayed'] = 'true';

            return response()
                ->json($stored['body'], (int) $stored['status'])
                ->withHeaders($headers);
        }

        // 2) Lock untuk mencegah concurrent execution (idealnya Redis atomic lock)
        if (! $this->idemp->acquireLock($key)) {
            return response()
                ->json([
                    'message' => 'Idempotency conflict',
                    'code' => 'idempotency_conflict',
                ], Response::HTTP_CONFLICT)
                ->withHeaders(['Idempotency-Key' => $key]);
        }

        try {
            // Re-check setelah lock (race guard)
            $existing = $this->paymentsRepo->findByIdempotencyKey($key);
            if ($existing) {
                $storedHash = (string) ($existing->idempotency_request_hash ?? '');

                if ($storedHash !== '' && ! hash_equals($storedHash, $requestHash)) {
                    logger()->warning('Idempotency key mismatch (db-after-lock)', [
                        'idempotency_key' => $key,
                        'payment_id' => (string) $existing->getKey(),
                        'stored_hash' => $storedHash,
                        'incoming_hash' => $requestHash,
                    ]);

                    return response()
                        ->json([
                            'message' => 'Idempotency-Key already used with different request parameters',
                            'code' => 'idempotency_mismatch',
                        ], Response::HTTP_CONFLICT)
                        ->withHeaders(['Idempotency-Key' => $key]);
                }

                if ($storedHash === '') {
                    $existing->idempotency_request_hash = $requestHash;
                    $existing->save();
                }

                return response()
                    ->json([
                        'data' => (new PaymentResource($existing))->toArray($request),
                    ], Response::HTTP_CREATED)
                    ->withHeaders([
                        'Idempotency-Key' => $key,
                        'Idempotency-Replayed' => 'true',
                    ]);
            }

            // 3) Buat intent/simulasi via PaymentsService
            $created = $this->payments->create((string) $data['provider'], [
                'amount' => (int) $data['amount'],
                'currency' => (string) ($data['currency'] ?? 'IDR'),
                'description' => $data['description'] ?? null,
                'meta' => $meta,
            ]);

            $status = trim((string) ($created['status'] ?? ''));
            if ($status === '') {
                $status = 'pending';
            }

            // 4) Persist ke DB + sambungkan idempotency fields
            $payment = $this->paymentsRepo->create([
                'provider' => (string) $created['provider'],
                'provider_ref' => (string) $created['provider_ref'],
                'amount' => (int) ($created['snapshot']['amount'] ?? $data['amount']),
                'currency' => (string) ($created['snapshot']['currency'] ?? ($data['currency'] ?? 'IDR')),
                'status' => strtolower($status),
                'meta' => $meta,

                // production-grade: simpan idempotency fields
                'idempotency_key' => $key,
                'idempotency_request_hash' => $requestHash,
            ]);

            // 5) Response resource
            $body = [
                'data' => (new PaymentResource($payment))->toArray($request),
            ];

            // 6) Simpan untuk idempotent replay (cache)
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

        $perPage = $request->integer('per_page', 20);
        if ($perPage <= 0) {
            $perPage = 20;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $paginator = $this->paymentsRepo->paginateAdmin($filters, $perPage);

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

    /**
     * Hash stabil untuk mismatch idempotency:
     * - sort keys rekursif (biar order JSON tidak bikin mismatch palsu)
     */
    private function computeIdempotencyRequestHash(array $normalized): string
    {
        $stable = $this->stableSortRecursive($normalized);

        $json = json_encode($stable, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            // Fallback defensif
            $json = serialize($stable);
        }

        return hash('sha256', $json);
    }

    private function stableSortRecursive(mixed $value): mixed
    {
        if (is_array($value)) {
            $isAssoc = array_keys($value) !== range(0, count($value) - 1);

            foreach ($value as $k => $v) {
                $value[$k] = $this->stableSortRecursive($v);
            }

            if ($isAssoc) {
                ksort($value);
            }

            return $value;
        }

        return $value;
    }
}
