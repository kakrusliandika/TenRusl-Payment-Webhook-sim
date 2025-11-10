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
     * Gunakan guarded kosong agar dinamis saat menyimpan payload mentah.
     * (Alternatif: definisikan fillable satu per satu.)
     */
    protected $guarded = [];

    /**
     * Casting atribut untuk kemudahan pemrosesan & serialisasi.
     */
    protected $casts = [
        'payload'          => 'array',
        'attempts'         => 'int',
        'received_at'      => 'datetime',
        'last_attempt_at'  => 'datetime',
        'processed_at'     => 'datetime',
        'next_retry_at'    => 'datetime',
        'payment_status'   => PaymentStatus::class,
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    /**
     * Scope bantu untuk dedup berdasarkan provider & event_id.
     */
    public function scopeByProviderEvent($query, string $provider, string $eventId)
    {
        return $query->where('provider', $provider)
                     ->where('event_id', $eventId);
    }
}
