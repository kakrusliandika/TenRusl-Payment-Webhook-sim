<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => (string) $this->id,
            'status'          => $this->status,
            'amount'          => (int) $this->amount,
            'currency'        => $this->currency,
            'description'     => $this->description,
            'metadata'        => $this->metadata ?? [],
            'idempotency_key' => $this->idempotency_key,
            'created_at'      => optional($this->created_at)->toISOString(),
            'updated_at'      => optional($this->updated_at)->toISOString(),
        ];
    }
}
