<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonImmutable;

/**
 * Abstraksi waktu agar mudah di-test/mocking.
 * - Default implementasi memakai now() / Carbon.
 * - Di test, bisa bind ke container: app()->instance(Clock::class, new FakeClock(...))
 */
class Clock
{
    public function now(): CarbonImmutable
    {
        return CarbonImmutable::now();
    }

    public function nowMutable(): Carbon
    {
        return Carbon::now();
    }

    public function today(): CarbonImmutable
    {
        return CarbonImmutable::today();
    }

    public function afterSeconds(int $seconds): CarbonImmutable
    {
        return $this->now()->addSeconds($seconds);
    }

    public function afterMinutes(int $minutes): CarbonImmutable
    {
        return $this->now()->addMinutes($minutes);
    }
}
