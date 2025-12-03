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
 * Nilai balik dalam milidetik (ms).
 *
 * Referensi konsep jitter ini umum dipakai di sistem retry agar:
 * - menghindari thundering herd (semua retry di waktu yg sama),
 * - lebih stabil saat provider webhook burst / timeout.
 */
final class RetryBackoff
{
    /**
     * Normalisasi input mode agar tolerant terhadap input user/env.
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
     * Hitung delay (ms) untuk sebuah attempt.
     *
     * Konvensi yang dipakai:
     * - attempt dimulai dari 1
     * - exp tanpa jitter: base * 2^(attempt-1) lalu di-cap ke capMs
     *
     * @param  int  $attempt  attempt ke- (mulai 1)
     * @param  int  $baseMs  base delay dalam ms
     * @param  int  $capMs  batas maksimal delay
     * @param  string  $mode  full|equal|decorrelated
     * @param  int|null  $maxAttempts  bila diisi, clamp attempt ke maxAttempts
     * @param  int|null  $prevMs  (opsional) delay sebelumnya utk decorrelated
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

        // Guard input
        $attempt = max(1, $attempt);
        if ($maxAttempts !== null && $maxAttempts > 0) {
            $attempt = min($attempt, $maxAttempts);
        }

        $baseMs = max(0, $baseMs);
        $capMs = max($baseMs, $capMs);

        // Exponential base (tanpa jitter) -> cap.
        // base=500: attempt1=500, attempt2=1000, attempt3=2000, dst.
        $exp = (int) ($baseMs * (2 ** ($attempt - 1)));
        $exp = min($capMs, max(0, $exp));

        return match ($mode) {
            'equal' => self::equalJitter($exp),
            'decorrelated' => self::decorrelatedJitter($baseMs, $capMs, $prevMs ?? $exp),
            default => self::fullJitter($exp),
        };
    }

    /**
     * Buat daftar jadwal retry (ms) untuk N attempt.
     * Berguna untuk debugging/README: lihat pattern delay.
     *
     * @return array<int,int> key=attempt (mulai 1) => delay(ms)
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
            $prev = $delay; // stateful utk decorrelated
        }

        return $out;
    }

    /**
     * FULL JITTER: random antara 0..exp
     */
    private static function fullJitter(int $exp): int
    {
        if ($exp <= 0) {
            return 0;
        }

        return random_int(0, $exp);
    }

    /**
     * EQUAL JITTER: exp/2 + random(0..exp/2)
     */
    private static function equalJitter(int $exp): int
    {
        if ($exp <= 0) {
            return 0;
        }

        $half = intdiv($exp, 2);

        return $half + ($half > 0 ? random_int(0, $half) : 0);
    }

    /**
     * Decorrelated jitter (gaya AWS):
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
