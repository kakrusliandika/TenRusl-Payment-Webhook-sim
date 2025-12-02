<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUlid;
use App\ValueObjects\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    use HasUlid;

    protected $table = 'payments';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Kolom yang boleh diisi mass-assignment.
     * Pastikan ini match dengan migrasi payments kamu.
     */
    protected $fillable = [
        'provider',
        'provider_ref',

        'amount',
        'currency',
        'description',

        // metadata bebas untuk simulator (di OpenAPI pakai "meta")
        'meta',

        // pending | succeeded | failed
        'status',

        // idempotency (kalau dipakai oleh CreatePaymentRequest/controller)
        'idempotency_key',
        'idempotency_request_hash',
    ];

    /**
     * Casting:
     * - JSON meta => array
     * - status => enum/cast PaymentStatus (jika implemented sebagai enum cast) :contentReference[oaicite:5]{index=5}
     */
    protected $casts = [
        'amount' => 'integer',
        'meta' => 'array',
        'status' => PaymentStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope bantu untuk lookup payment berdasarkan provider & provider_ref.
     */
    public function scopeByProviderRef($query, string $provider, string $providerRef)
    {
        return $query
            ->where('provider', $provider)
            ->where('provider_ref', $providerRef);
    }
}
