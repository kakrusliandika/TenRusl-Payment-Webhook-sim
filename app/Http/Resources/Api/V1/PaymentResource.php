<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Payment */
class PaymentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => (string) $this->id,
            'provider'     => (string) $this->provider,
            'provider_ref' => (string) $this->provider_ref,
            'amount'       => (string) $this->amount,
            'currency'     => (string) $this->currency,
            'status'       => (string) $this->status, // cast enum → string
            'meta'         => $this->when($this->meta !== null, (array) $this->meta),

            'created_at'   => optional($this->created_at)->toISOString(),
            'updated_at'   => optional($this->updated_at)->toISOString(),
        ];
    }
}
