<?php

declare(strict_types=1);

namespace App\ValueObjects;

/**
 * Value Object untuk status pembayaran.
 * Gunakan ->value saat menyimpan ke DB (string).
 */
enum PaymentStatus: string
{
    case PENDING  = 'pending';
    case PAID     = 'paid';
    case FAILED   = 'failed';
    case REFUNDED = 'refunded';

    public function isTerminal(): bool
    {
        return in_array($this, [self::PAID, self::FAILED, self::REFUNDED], true);
    }

    public static function fromString(string $value): self
    {
        return match (strtolower($value)) {
            'pending'  => self::PENDING,
            'paid'     => self::PAID,
            'failed'   => self::FAILED,
            'refunded' => self::REFUNDED,
            default    => self::PENDING,
        };
    }
}
