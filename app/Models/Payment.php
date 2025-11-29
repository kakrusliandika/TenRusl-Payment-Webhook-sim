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

    /**
     * Kolom yang boleh diisi mass-assignment.
     */
    protected $fillable = [
        'provider',
        'provider_ref',
        'amount',
        'currency',
        'status',
        'meta',
    ];

    /**
     * Casting atribut.
     * - enum casting ke PaymentStatus (PHP 8.1 backed enum)
     * - meta JSON → array
     * - amount: integer (selaras dengan migrasi awal unsignedInteger)
     * - timestamps → datetime Carbon
     */
    protected $casts = [
        'status' => PaymentStatus::class,
        'meta' => 'array',
        'amount' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope bantu untuk mencari berdasarkan provider & ref.
     */
    public function scopeByProviderRef($query, string $provider, string $providerRef)
    {
        return $query->where('provider', $provider)
            ->where('provider_ref', $providerRef);
    }
}
