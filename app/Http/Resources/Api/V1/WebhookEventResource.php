<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\WebhookEvent */
class WebhookEventResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => (string) $this->id,
            'provider'               => (string) $this->provider,
            'event_id'               => (string) $this->event_id,
            'event_type'             => $this->when(isset($this->event_type), (string) $this->event_type),

            'payment_provider_ref'   => $this->when(isset($this->payment_provider_ref), (string) $this->payment_provider_ref),
            'payment_status'         => $this->when(isset($this->payment_status), (string) $this->payment_status),

            'attempts'               => (int) ($this->attempts ?? 0),

            'received_at'            => optional($this->received_at)->toISOString(),
            'last_attempt_at'        => optional($this->last_attempt_at)->toISOString(),
            'processed_at'           => optional($this->processed_at)->toISOString(),
            'next_retry_at'          => optional($this->next_retry_at)->toISOString(),

            'payload'                => $this->when(isset($this->payload), (array) $this->payload),
        ];
    }
}
