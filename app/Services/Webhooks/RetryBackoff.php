<?php

declare(strict_types=1);

namespace App\Services\Webhooks;

/**
 * Kalkulasi exponential backoff + jitter.
 *
 * Implementasi menyediakan tiga mode:
 *  - 'full'          : FULL JITTER (delay = rand(0, base*2^n), dicap ke max)
 *  - 'equal'         : EQUAL JITTER (delay = base*2^n/2 + rand(0, base*2^n/2))
 *  - 'decorrelated'  : DECORRELATED JITTER (per AWS Builders Library)
 *
 * Nilai balik dalam milidetik.
 */
final class RetryBackoff
{
    public static function compute(
        int $attempt,              // mulai dari 1
        int $baseMs = 500,
        int $capMs = 30000,
        string $mode = 'full'
    ): int {
        $attempt = max(1, $attempt);

        $exp = min($capMs, (int) ($baseMs * (2 ** ($attempt - 1))));

        return match ($mode) {
            'equal'        => (int) floor(($exp / 2) + random_int(0, (int) floor($exp / 2))),
            'decorrelated' => self::decorrelated($exp, $capMs),
            default        => random_int(0, $exp), // full jitter
        };
    }

    /**
     * Buat daftar jadwal retry (ms) untuk N percobaan.
     *
     * @return array<int,int> indeks mulai 1 → delay ms
     */
    public static function schedule(int $attempts, int $baseMs = 500, int $capMs = 30000, string $mode = 'full'): array
    {
        $out = [];
        for ($i = 1; $i <= max(0, $attempts); $i++) {
            $out[$i] = self::compute($i, $baseMs, $capMs, $mode);
        }
        return $out;
    }

    /**
     * Dekorrelated jitter: next = random(b, min(cap, prev*3))
     * Untuk kesederhanaan, kita estimasi prev≈exp pada percobaan ke-n (tidak stateful).
     */
    private static function decorrelated(int $exp, int $capMs): int
    {
        $min = (int) floor($exp / 2);
        $max = min($capMs, $exp * 3);
        return random_int($min, $max);
    }
}
