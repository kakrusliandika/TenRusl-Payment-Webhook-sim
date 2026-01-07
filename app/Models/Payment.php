<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUlid;
use App\ValueObjects\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Payment
 *
 * Catatan:
 * - meta di-cast ke array agar stabil di API response.
 * - status di-cast ke Enum/Cast class (PaymentStatus).
 * - relasi webhookEvents dipakai untuk kebutuhan GET /payments/{id}
 *   (include event-event webhook yang terkait).
 */
class Payment extends Model
{
    use HasFactory;
    use HasUlid;

    protected $table = 'payments';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Kolom mass-assignable (sesuaikan dengan migrasi).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider',
        'provider_ref',

        'amount',
        'currency',
        'description',

        'meta',

        'status',

        'idempotency_key',
        'idempotency_request_hash',
    ];

    /**
     * Casting attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'meta' => 'array',
            'status' => PaymentStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Scope: filter berdasarkan provider + provider_ref.
     */
    public function scopeByProviderRef(Builder $query, string $provider, string $providerRef): Builder
    {
        return $query
            ->where('provider', $provider)
            ->where('provider_ref', $providerRef);
    }

    /**
     * Relasi: Payment punya banyak WebhookEvent.
     *
     * Asumsi default:
     * - tabel webhook_events punya kolom payment_id yang mengarah ke payments.id
     * Jika skema kamu beda, ganti foreign key / local key di sini.
     */
    public function webhookEvents(): HasMany
    {
        return $this->hasMany(\App\Models\WebhookEvent::class, 'payment_id', $this->getKeyName());
    }
}
