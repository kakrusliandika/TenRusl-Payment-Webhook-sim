<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUlid;
use App\ValueObjects\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory;
    use HasUlid;

    protected $table = 'webhook_events';

    /**
     * Dinamis untuk demo. Kalau mau lebih ketat, ganti ke $fillable.
     */
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'payload_raw' => 'string',

        'attempts' => 'int',

        'received_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'processed_at' => 'datetime',
        'next_retry_at' => 'datetime',

        // Jika PaymentStatus kamu adalah cast ValueObject:
        'payment_status' => PaymentStatus::class,

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeByProviderEvent($query, string $provider, string $eventId)
    {
        return $query->where('provider', $provider)
            ->where('event_id', $eventId);
    }
}
