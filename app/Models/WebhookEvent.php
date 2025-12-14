<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUlid;
use App\ValueObjects\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model WebhookEvent
 *
 * Umumnya menyimpan:
 * - provider, event_id, event_type
 * - payload_raw (string), payload (json)
 * - status (received|processed|failed)
 * - attempts, received_at, last_attempt_at, processed_at, next_retry_at
 * - error_message
 * - relasi ke Payment (payment_id)
 */
class WebhookEvent extends Model
{
    use HasFactory;
    use HasUlid;

    protected $table = 'webhook_events';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Lebih aman pakai $fillable (demo-friendly tapi tidak "terlalu longgar").
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',

        'provider',
        'event_id',
        'event_type',

        'payload_raw',
        'payload',

        'status',
        'attempts',

        'received_at',
        'last_attempt_at',
        'processed_at',
        'next_retry_at',

        'payment_provider_ref',
        'payment_status',

        'error_message',
    ];

    /**
     * Casting attributes (Laravel 12 mendukung method casts()). :contentReference[oaicite:1]{index=1}
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'payload_raw' => 'string',

            'attempts' => 'integer',

            'received_at' => 'datetime',
            'last_attempt_at' => 'datetime',
            'processed_at' => 'datetime',
            'next_retry_at' => 'datetime',

            // optional: audit status payment (pending|succeeded|failed)
            'payment_status' => PaymentStatus::class,

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relasi balik ke Payment (belongsTo).
     *
     * Asumsi: webhook_events.payment_id -> payments.id
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', (new Payment())->getKeyName());
    }

    /**
     * Scope bantu untuk lookup dedup berdasarkan provider & event_id.
     */
    public function scopeByProviderEvent(Builder $query, string $provider, string $eventId): Builder
    {
        return $query
            ->where('provider', $provider)
            ->where('event_id', $eventId);
    }
}
