<?php

declare(strict_types=1);

namespace App\Services\Webhooks;

/**
 * Kalkulasi exponential backoff + jitter.
 *
 * Mode:
 *  - 'full'          : FULL JITTER (delay = rand(0, base*2^n), dicap ke max)
 *  - 'equal'         : EQUAL JITTER (delay = base*2^n/2 + rand(0, base*2^n/2))
 *  - 'decorrelated'  : DECORRELATED JITTER (per AWS Builders Library)
 *
 * Nilai balik dalam milidetik.
 */
final class RetryBackoff
{
    /**
     * Hitung delay untuk 1 percobaan.
     *
     * @param  int  $attempt  percobaan ke-berapa (mulai dari 1)
     * @param  int  $baseMs  base delay dalam ms
     * @param  int  $capMs  batas maksimal delay
     * @param  string  $mode  full|equal|decorrelated
     * @param  int|null  $maxAttempts  jika di-set, attempt dikunci ke maxAttempts
     */
    public static function compute(
        int $attempt,
        int $baseMs = 500,
        int $capMs = 30000,
        string $mode = 'full',
        ?int $maxAttempts = null
    ): int {
        // Normalisasi attempt dan hormati maxAttempts jika diberikan.
        $attempt = max(1, $attempt);
        if ($maxAttempts !== null && $maxAttempts > 0) {
            $attempt = min($attempt, $maxAttempts);
        }

        // Exponential base (tanpa jitter) lalu cap.
        $exp = min($capMs, (int) ($baseMs * (2 ** ($attempt - 1))));

        return match ($mode) {
            'equal' => self::equalJitter($exp),
            'decorrelated' => self::decorrelated($exp, $capMs),
            default => self::fullJitter($exp), // full jitter
        };
    }

    /**
     * Buat daftar jadwal retry (ms) untuk N percobaan.
     *
     * @param  int  $maxAttempts  jumlah maksimum percobaan (max_attempts)
     * @return array<int,int> indeks mulai 1 → delay ms
     */
    public static function schedule(
        int $maxAttempts,
        int $baseMs = 500,
        int $capMs = 30000,
        string $mode = 'full'
    ): array {
        $out = [];
        $maxAttempts = max(0, $maxAttempts);

        for ($i = 1; $i <= $maxAttempts; $i++) {
            $out[$i] = self::compute($i, $baseMs, $capMs, $mode, $maxAttempts);
        }

        return $out;
    }

    private static function fullJitter(int $exp): int
    {
        // 0 .. exp
        return random_int(0, max(1, $exp));
    }

    private static function equalJitter(int $exp): int
    {
        // exp/2 .. exp
        $half = (int) floor($exp / 2);

        return $half + random_int(0, max(1, $half));
    }

    /**
     * Dekorrelated jitter: next ≈ random(exp/2, min(cap, exp*3)).
     * Di sini kita pakai exp sebagai aproksimasi prev-delay (stateless).
     */
    private static function decorrelated(int $exp, int $capMs): int
    {
        $min = max(0, (int) floor($exp / 2));
        $max = max($min + 1, min($capMs, $exp * 3));

        return random_int($min, $max);
    }
}
