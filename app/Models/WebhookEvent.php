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
 * Menyimpan event webhook yang masuk:
 * - provider, event_id, event_type
 * - signature_hash, source_ip, request_id, headers
 * - payload_raw (string), payload (json/array)
 * - status (received|processing|processed|failed|dead)
 * - attempts, received_at, last_attempt_at, processed_at, next_retry_at
 * - payment_status, payment_provider_ref, error_message
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
     * Demo-friendly: mass-assign aman tapi tetap eksplisit.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',

        'provider',
        'event_id',
        'event_type',

        'signature_hash',
        'source_ip',
        'request_id',
        'headers',

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
     * Casting attributes (Laravel 11+ mendukung casts() method).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'payload' => 'array',
            'payload_raw' => 'string',

            'signature_hash' => 'string',
            'source_ip' => 'string',
            'request_id' => 'string',

            'status' => 'string',
            'event_type' => 'string',
            'payment_provider_ref' => 'string',

            'attempts' => 'integer',

            'received_at' => 'datetime',
            'last_attempt_at' => 'datetime',
            'processed_at' => 'datetime',
            'next_retry_at' => 'datetime',

            // audit status payment (pending|succeeded|failed) - custom cast/VO
            'payment_status' => PaymentStatus::class,

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relasi balik ke Payment (belongsTo).
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
