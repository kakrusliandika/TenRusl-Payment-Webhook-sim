<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonImmutable;
use DateTimeInterface;

/**
 * Abstraksi waktu sederhana agar mudah di-test.
 * Default memakai CarbonImmutable::now(); bisa di-freeze / travel via setTestNow().
 */
final class Clock
{
    private static ?CarbonImmutable $testNow = null;

    /**
     * Waktu saat ini (ikut testNow jika di-set).
     */
    public static function now(?string $tz = null): CarbonImmutable
    {
        if (self::$testNow instanceof CarbonImmutable) {
            return $tz ? self::$testNow->setTimezone($tz) : self::$testNow;
        }

        return $tz ? CarbonImmutable::now($tz) : CarbonImmutable::now();
    }

    /**
     * Waktu sekarang dalam UTC.
     */
    public static function utc(): CarbonImmutable
    {
        return self::now('UTC');
    }

    /**
     * Set testNow (membekukan waktu) atau kosongkan untuk kembali realtime.
     */
    public static function setTestNow(?CarbonImmutable $now): void
    {
        self::$testNow = $now;
    }

    /**
     * Bekukan waktu ke saat ini.
     */
    public static function freeze(): void
    {
        self::$testNow = CarbonImmutable::now();
    }

    /**
     * Geser waktu uji (positif = ke depan; negatif = ke belakang).
     */
    public static function travel(int $seconds): void
    {
        self::$testNow = (self::$testNow ?? CarbonImmutable::now())->addSeconds($seconds);
    }

    /**
     * Hapus testNow.
     */
    public static function clear(): void
    {
        self::$testNow = null;
    }

    /**
     * Parse string/DateTime menjadi CarbonImmutable.
     */
    public static function parse(string|DateTimeInterface $time, ?string $tz = null): CarbonImmutable
    {
        if ($time instanceof DateTimeInterface) {
            return CarbonImmutable::instance($time);
        }

        return $tz ? CarbonImmutable::parse($time, $tz) : CarbonImmutable::parse($time);
    }
}
