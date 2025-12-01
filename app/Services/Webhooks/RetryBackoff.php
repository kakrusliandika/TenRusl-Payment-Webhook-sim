<?php

declare(strict_types=1);

namespace App\Services\Webhooks;

/**
 * Kalkulasi exponential backoff + jitter.
 *
 * Mode:
 *  - 'full'          : FULL JITTER   -> random(0, exp)
 *  - 'equal'         : EQUAL JITTER  -> exp/2 + random(0, exp/2)
 *  - 'decorrelated'  : DECORRELATED  -> min(cap, random(base, prev*3))
 *
 * Nilai balik dalam milidetik.
 */
final class RetryBackoff
{
    /**
     * Normalisasi input mode (biar signature command lebih toleran).
     */
    public static function normalizeMode(string $mode): string
    {
        $mode = strtolower(trim($mode));

        return match ($mode) {
            'full', 'equal', 'decorrelated' => $mode,
            default => 'full',
        };
    }

    /**
     * Hitung delay untuk 1 percobaan.
     *
     * @param  int         $attempt     percobaan ke-berapa (mulai dari 1)
     * @param  int         $baseMs      base delay dalam ms
     * @param  int         $capMs       batas maksimal delay
     * @param  string      $mode        full|equal|decorrelated
     * @param  int|null    $maxAttempts jika di-set, attempt dikunci ke maxAttempts
     * @param  int|null    $prevMs      (opsional) delay sebelumnya untuk decorrelated
     */
    public static function compute(
        int $attempt,
        int $baseMs = 500,
        int $capMs = 30000,
        string $mode = 'full',
        ?int $maxAttempts = null,
        ?int $prevMs = null
    ): int {
        $mode = self::normalizeMode($mode);

        $attempt = max(1, $attempt);
        if ($maxAttempts !== null && $maxAttempts > 0) {
            $attempt = min($attempt, $maxAttempts);
        }

        $baseMs = max(0, $baseMs);
        $capMs = max($baseMs, $capMs);

        // Exponential base (tanpa jitter) lalu cap.
        $exp = (int) ($baseMs * (2 ** ($attempt - 1)));
        $exp = min($capMs, max(0, $exp));

        return match ($mode) {
            'equal' => self::equalJitter($exp),
            'decorrelated' => self::decorrelatedJitter($baseMs, $capMs, $prevMs ?? $exp),
            default => self::fullJitter($exp),
        };
    }

    /**
     * Buat daftar jadwal retry (ms) untuk N percobaan.
     *
     * @return array<int,int> indeks mulai 1 → delay ms
     */
    public static function schedule(
        int $maxAttempts,
        int $baseMs = 500,
        int $capMs = 30000,
        string $mode = 'full'
    ): array {
        $mode = self::normalizeMode($mode);
        $maxAttempts = max(0, $maxAttempts);

        $out = [];
        $prev = $baseMs;

        for ($i = 1; $i <= $maxAttempts; $i++) {
            $delay = self::compute(
                attempt: $i,
                baseMs: $baseMs,
                capMs: $capMs,
                mode: $mode,
                maxAttempts: $maxAttempts,
                prevMs: $prev
            );

            $out[$i] = $delay;
            $prev = $delay;
        }

        return $out;
    }

    private static function fullJitter(int $exp): int
    {
        if ($exp <= 0) {
            return 0;
        }

        return random_int(0, $exp);
    }

    private static function equalJitter(int $exp): int
    {
        if ($exp <= 0) {
            return 0;
        }

        $half = intdiv($exp, 2);

        return $half + ($half > 0 ? random_int(0, $half) : 0);
    }

    /**
     * Decorrelated jitter (AWS-style):
     * next = min(cap, random(base, prev*3))
     */
    private static function decorrelatedJitter(int $baseMs, int $capMs, int $prevMs): int
    {
        $baseMs = max(0, $baseMs);
        $capMs = max($baseMs, $capMs);
        $prevMs = max($baseMs, $prevMs);

        $max = min($capMs, $prevMs * 3);
        if ($max <= $baseMs) {
            return $baseMs;
        }

        return random_int($baseMs, $max);
    }
}
