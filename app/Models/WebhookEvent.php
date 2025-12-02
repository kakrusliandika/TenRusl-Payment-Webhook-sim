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
     * Karena primary key ULID adalah string.
     * (Biasanya HasUlid juga sudah meng-handle ini, tapi aman dinyatakan eksplisit.)
     */
    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Demo-friendly: izinkan mass assignment untuk semua kolom.
     * Kalau mau lebih ketat, ganti menjadi $fillable.
     */
    protected $guarded = [];

    /**
     * Casting atribut untuk kemudahan pemrosesan.
     * Laravel mendukung enum cast langsung via $casts. :contentReference[oaicite:1]{index=1}
     */
    protected $casts = [
        // payload JSON di DB <-> array di PHP
        'payload' => 'array',

        // catatan: payload_raw kita simpan TEXT/LONGTEXT, tetap string
        'payload_raw' => 'string',

        // counter attempts (retry)
        'attempts' => 'integer',

        // timestamps domain event
        'received_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'processed_at' => 'datetime',
        'next_retry_at' => 'datetime',

        // untuk audit status payment (pending|succeeded|failed) - optional cast
        // Jika PaymentStatus adalah backed enum: cast ini valid. :contentReference[oaicite:2]{index=2}
        'payment_status' => PaymentStatus::class,

        // default timestamps
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope bantu untuk dedup lookup berdasarkan provider & event_id.
     */
    public function scopeByProviderEvent($query, string $provider, string $eventId)
    {
        return $query
            ->where('provider', $provider)
            ->where('event_id', $eventId);
    }
}
