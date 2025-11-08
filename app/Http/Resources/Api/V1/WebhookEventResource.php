<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhookEventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => (string) $this->id,
            'provider'      => $this->provider,
            'event_id'      => $this->event_id,
            'status'        => $this->status,
            'attempt_count' => (int) $this->attempt_count,
            'next_retry_at' => optional($this->next_retry_at)->toISOString(),
            'created_at'    => optional($this->created_at)->toISOString(),
        ];
    }
}
