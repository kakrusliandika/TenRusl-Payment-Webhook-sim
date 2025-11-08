<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreatePaymentRequest;
use App\Http\Resources\Api\V1\PaymentResource;
use App\Services\Idempotency\IdempotencyKeyService;
use App\Services\Payments\PaymentsService;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PaymentsController extends Controller
{
    public function __construct(
        private PaymentsService $payments,
        private IdempotencyKeyService $idem
    ) {}

    public function store(CreatePaymentRequest $request)
    {
        /** @var SymfonyRequest $sym */
        $sym = $request; // hint tipe agar Intelephense paham properti headers
        $idemKey = (string) $sym->headers->get('Idempotency-Key', '');

        if (strlen($idemKey) < 8) {
            return response()->json([
                'error'   => 'invalid_request',
                'message' => 'Idempotency-Key header is required',
                'code'    => '400',
            ], 400);
        }

        if ($existing = $this->idem->findPaymentByKey($idemKey)) {
            return (new PaymentResource($existing))
                ->response()
                ->setStatusCode(HttpResponse::HTTP_OK);
        }

        $payment = $this->payments->create($request->validated(), $idemKey);

        return (new PaymentResource($payment))
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $payment = $this->payments->get($id);

        if (! $payment) {
            return response()->json([
                'error'   => 'not_found',
                'message' => 'Payment not found',
                'code'    => '404',
            ], 404);
        }

        return new PaymentResource($payment);
    }
}
