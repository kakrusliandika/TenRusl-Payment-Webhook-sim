<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *   title="TenRusl Payment Webhook Simulator",
 *   version="0.1.0",
 *   description="Demo Laravel: idempotency, webhook dedup, signature verification, dan exponential backoff retry (simulasi)."
 * )
 *
 * @OA\Server(
 *   url="/api/v1",
 *   description="Local API base (Laravel 12)"
 * )
 *
 * @OA\Tag(name="Payments", description="Endpoints untuk membuat & membaca Payment")
 * @OA\Tag(name="Webhooks", description="Receiver webhook dari provider (mock|xendit|midtrans)")
 *
 * @OA\Schema(
 *   schema="Payment",
 *   type="object",
 *   @OA\Property(property="id", type="string", example="01JF3XQ9H0N7E7Q0ZR3R0B3K9R"),
 *   @OA\Property(property="status", type="string", enum={"pending","paid","failed","refunded"}, example="pending"),
 *   @OA\Property(property="amount", type="integer", minimum=1000, example=25000),
 *   @OA\Property(property="currency", type="string", example="IDR"),
 *   @OA\Property(property="description", type="string", example="Topup"),
 *   @OA\Property(property="metadata", type="object", additionalProperties=true, example={"customer_id":"cus_1"}),
 *   @OA\Property(property="idempotency_key", type="string", example="9f6b8a74-1b2c-4d3e-9f00-1234567890ab"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 *
 * @OA\Schema(
 *   schema="CreatePaymentRequest",
 *   type="object",
 *   required={"amount"},
 *   @OA\Property(property="amount", type="integer", minimum=1000, example=25000),
 *   @OA\Property(property="currency", type="string", example="IDR"),
 *   @OA\Property(property="description", type="string", maxLength=140, example="Topup"),
 *   @OA\Property(property="metadata", type="object", additionalProperties=true, example={"customer_id":"cus_1"})
 * )
 *
 * @OA\Schema(
 *   schema="WebhookEvent",
 *   type="object",
 *   required={"event_id","type","data"},
 *   @OA\Property(property="event_id", type="string", example="evt_123"),
 *   @OA\Property(property="type", type="string", example="payment.paid"),
 *   @OA\Property(property="data", type="object", additionalProperties=true, example={"payment_id":"01JF3XQ9H0N7E7Q0ZR3R0B3K9R","amount":25000,"currency":"IDR"}),
 *   @OA\Property(property="sent_at", type="string", format="date-time", example="2025-11-08T12:01:00Z")
 * )
 *
 * @OA\Schema(
 *   schema="Error",
 *   type="object",
 *   @OA\Property(property="error", type="string", example="invalid_request"),
 *   @OA\Property(property="message", type="string", example="Idempotency-Key header is required"),
 *   @OA\Property(property="code", type="string", example="400")
 * )
 *
 * @OA\Parameter(
 *   parameter="IdempotencyKey",
 *   name="Idempotency-Key",
 *   in="header",
 *   required=true,
 *   @OA\Schema(type="string", minLength=8),
 *   description="Unique key per intent; server menyimpan snapshot response."
 * )
 *
 * @OA\Parameter(
 *   parameter="MockSignature",
 *   name="X-Mock-Signature",
 *   in="header",
 *   required=false,
 *   @OA\Schema(type="string"),
 *   description="HMAC-SHA256 dari raw body menggunakan MOCK_SECRET (untuk provider=mock)."
 * )
 *
 * @OA\Parameter(
 *   parameter="XenditCallbackToken",
 *   name="x-callback-token",
 *   in="header",
 *   required=false,
 *   @OA\Schema(type="string"),
 *   description="Token callback yang cocok dengan XENDIT_CALLBACK_TOKEN (untuk provider=xendit)."
 * )
 *
 * @OA\Parameter(
 *   parameter="MidtransSignatureKey",
 *   name="Signature-Key",
 *   in="header",
 *   required=false,
 *   @OA\Schema(type="string"),
 *   description="SHA512(order_id+status_code+gross_amount+server_key) (untuk provider=midtrans)."
 * )
 *
 * @OA\Post(
 *   path="/payments",
 *   tags={"Payments"},
 *   summary="Create payment (idempotent)",
 *   @OA\Parameter(ref="#/components/parameters/IdempotencyKey"),
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CreatePaymentRequest")),
 *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Payment")),
 *   @OA\Response(response=400, description="Bad Request", @OA\JsonContent(ref="#/components/schemas/Error")),
 *   @OA\Response(response=409, description="Conflict", @OA\JsonContent(ref="#/components/schemas/Error"))
 * )
 *
 * @OA\Get(
 *   path="/payments/{id}",
 *   tags={"Payments"},
 *   summary="Get payment status",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Payment")),
 *   @OA\Response(response=404, description="Not Found", @OA\JsonContent(ref="#/components/schemas/Error"))
 * )
 *
 * @OA\Post(
 *   path="/webhooks/{provider}",
 *   tags={"Webhooks"},
 *   summary="Receive webhook event",
 *   description="Provider: mock | xendit | midtrans.",
 *   @OA\Parameter(name="provider", in="path", required=true, @OA\Schema(type="string", enum={"mock","xendit","midtrans"})),
 *   @OA\Parameter(ref="#/components/parameters/MockSignature"),
 *   @OA\Parameter(ref="#/components/parameters/XenditCallbackToken"),
 *   @OA\Parameter(ref="#/components/parameters/MidtransSignatureKey"),
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/WebhookEvent")),
 *   @OA\Response(
 *     response=200,
 *     description="Event processed (idempotent)",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(property="received", type="boolean", example=true),
 *       @OA\Property(property="provider", type="string", example="mock"),
 *       @OA\Property(property="event_id", type="string", example="evt_123"),
 *       @OA\Property(property="status", type="string", example="processed"),
 *       @OA\Property(property="duplicated", type="boolean", example=false),
 *     )
 *   ),
 *   @OA\Response(response=400, description="Bad Request", @OA\JsonContent(ref="#/components/schemas/Error")),
 *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
 *   @OA\Response(response=409, description="Conflict", @OA\JsonContent(ref="#/components/schemas/Error"))
 * )
 */
class Docs
{
    // hanya tempat anotasi OpenAPI — tidak ada kode eksekusi
}
