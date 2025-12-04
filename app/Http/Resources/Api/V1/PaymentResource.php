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
        // Jangan cast $this->status (bisa jadi ValueObject/Enum) ke string.
        // Ambil raw value dari DB (bypass casts/mutators).
        $statusRaw = $this->resource->getRawOriginal('status');

        $status = match (true) {
            is_string($statusRaw) => $statusRaw,
            is_int($statusRaw) => (string) $statusRaw,
            default => '',
        };

        return [
            'id' => (string) $this->id,
            'provider' => (string) $this->provider,
            'provider_ref' => (string) $this->provider_ref,
            'amount' => (int) $this->amount,
            'currency' => (string) $this->currency,
            'status' => $status,

            'meta' => $this->when($this->meta !== null, (array) $this->meta),

            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
