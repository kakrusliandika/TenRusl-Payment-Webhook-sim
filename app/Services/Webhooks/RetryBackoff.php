<?php

namespace App\Services\Webhooks;

/**
 * Exponential backoff sederhana: 1, 2, 4, 8, 16 (maks 5 percobaan)
 */
class RetryBackoff
{
    /** @var int[] */
    protected array $series = [1, 2, 4, 8, 16];

    public function secondsFor(int $attempt): int
    {
        $idx = max(1, $attempt);
        $idx = min($idx, count($this->series));
        return $this->series[$idx - 1];
    }
}
