<?php

declare(strict_types=1);

namespace App\ValueObjects;

/**
 * Status pembayaran generik untuk simulator.
 *
 * Gunakan PaymentStatus::fromString($anyProviderStatus) untuk memetakan
 * berbagai status provider ke 3 status inti: pending|succeeded|failed.
 */
enum PaymentStatus: string
{
    case Pending = 'pending';
    case Succeeded = 'succeeded';
    case Failed = 'failed';

    /**
     * Normalisasi dari status provider apa pun ke 3 status inti.
     */
    public static function fromString(string $value): self
    {
        $v = strtolower(trim($value));

        return match ($v) {
            // truthy
            'paid', 'paid_out', 'succeeded', 'success', 'completed', 'captured', 'settled',
            'charge.succeeded', 'payment_intent.succeeded' => self::Succeeded,

            // falsy
            'failed', 'fail', 'canceled', 'cancelled', 'void', 'expired', 'denied', 'rejected',
            'charge.failed', 'payment_intent.canceled' => self::Failed,

            default => self::Pending,
        };
    }

    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    public function isSucceeded(): bool
    {
        return $this === self::Succeeded;
    }

    public function isFailed(): bool
    {
        return $this === self::Failed;
    }

    public function isFinal(): bool
    {
        return $this !== self::Pending;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
