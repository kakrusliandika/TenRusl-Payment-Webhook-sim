<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="TenRusl Payment Webhook Simulator API",
 *   description="Dokumentasi OpenAPI untuk endpoint Payments & Webhooks (simulator)."
 * )
 *
 * @OA\Server(
 *   url="/",
 *   description="Default App URL"
 * )
 *
 * @OA\Tag(
 *   name="Payments",
 *   description="Buat dan cek status pembayaran simulasi"
 * )
 *
 * @OA\Tag(
 *   name="Webhooks",
 *   description="Terima webhook dari berbagai provider (simulasi verifikasi signature)"
 * )
 *
 * @OA\Schema(
 *   schema="Payment",
 *   type="object",
 *   required={"provider","provider_ref","status"},
 *   @OA\Property(property="id", type="string", example="01JCDZQ2F1G8W3X1R7SZM3KZ2S"),
 *   @OA\Property(property="provider", type="string", example="xendit"),
 *   @OA\Property(property="provider_ref", type="string", example="sim_xendit_01JCDZQ2F1..."),
 *   @OA\Property(property="amount", type="string", example="100000.00"),
 *   @OA\Property(property="currency", type="string", example="IDR"),
 *   @OA\Property(property="status", type="string", enum={"pending","succeeded","failed"}, example="pending"),
 *   @OA\Property(property="meta", type="object"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="WebhookEvent",
 *   type="object",
 *   required={"provider","event_id"},
 *   @OA\Property(property="id", type="string", example="01JCDZQ5M3..."),
 *   @OA\Property(property="provider", type="string", example="midtrans"),
 *   @OA\Property(property="event_id", type="string", example="evt_01JCDZQ5M3..."),
 *   @OA\Property(property="event_type", type="string", example="invoice.paid"),
 *   @OA\Property(property="payment_provider_ref", type="string", example="sim_midtrans_01J..."),
 *   @OA\Property(property="payment_status", type="string", enum={"pending","succeeded","failed"}),
 *   @OA\Property(property="attempts", type="integer", example=2),
 *   @OA\Property(property="received_at", type="string", format="date-time"),
 *   @OA\Property(property="last_attempt_at", type="string", format="date-time"),
 *   @OA\Property(property="processed_at", type="string", format="date-time"),
 *   @OA\Property(property="next_retry_at", type="string", format="date-time"),
 *   @OA\Property(property="payload", type="object")
 * )
 *
 * @OA\Post(
 *   path="/api/v1/payments",
 *   summary="Buat pembayaran simulasi",
 *   tags={"Payments"},
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"provider","amount"},
 *       @OA\Property(property="provider", type="string", example="xendit"),
 *       @OA\Property(property="amount", type="number", format="float", example=100000),
 *       @OA\Property(property="currency", type="string", example="IDR"),
 *       @OA\Property(property="description", type="string", example="Top up"),
 *       @OA\Property(property="metadata", type="object")
 *     )
 *   ),
 *   @OA\Response(
 *     response=201,
 *     description="Created",
 *     @OA\JsonContent(
 *       @OA\Property(property="data", ref="#/components/schemas/Payment")
 *     )
 *   )
 * )
 *
 * @OA\Get(
 *   path="/api/v1/payments/{provider}/{provider_ref}/status",
 *   summary="Cek status pembayaran",
 *   tags={"Payments"},
 *   @OA\Parameter(name="provider", in="path", required=true, @OA\Schema(type="string")),
 *   @OA\Parameter(name="provider_ref", in="path", required=true, @OA\Schema(type="string")),
 *   @OA\Response(
 *     response=200,
 *     description="OK",
 *     @OA\JsonContent(
 *       @OA\Property(property="data", ref="#/components/schemas/Payment")
 *     )
 *   )
 * )
 *
 * @OA\Post(
 *   path="/api/v1/webhooks/{provider}",
 *   summary="Terima webhook dari provider",
 *   tags={"Webhooks"},
 *   @OA\Parameter(name="provider", in="path", required=true, @OA\Schema(type="string")),
 *   @OA\RequestBody(required=true, description="Payload dari provider (JSON atau form)"),
 *   @OA\Response(
 *     response=202,
 *     description="Accepted (diproses asinkron / retry-aware)",
 *     @OA\JsonContent(
 *       @OA\Property(property="data", type="object",
 *         @OA\Property(property="event", type="object",
 *           @OA\Property(property="provider", type="string"),
 *           @OA\Property(property="event_id", type="string"),
 *           @OA\Property(property="type", type="string")
 *         ),
 *         @OA\Property(property="result", type="object",
 *           @OA\Property(property="duplicate", type="boolean"),
 *           @OA\Property(property="persisted", type="boolean"),
 *           @OA\Property(property="status", type="string"),
 *           @OA\Property(property="payment_provider_ref", type="string", nullable=true),
 *           @OA\Property(property="next_retry_ms", type="integer", nullable=true)
 *         )
 *       )
 *     )
 *   )
 * )
 */
final class Docs
{
    // Kelas dummy sebagai host untuk anotasi global OpenAPI.
}
