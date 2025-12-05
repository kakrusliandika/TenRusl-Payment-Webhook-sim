<?php

declare(strict_types=1);

use App\Services\Webhooks\RetryBackoff;

it('computes retry delays within configured bounds', function () {
    $baseMs = 500;
    $capMs = 30_000;
    $maxAttempts = 5;

    // Mode "equal" seharusnya deterministik (tanpa jitter besar) dan nondecreasing (hingga cap).
    $prev = -1;

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $delay = RetryBackoff::compute($attempt, $baseMs, $capMs, 'equal', $maxAttempts);

        expect($delay)
            ->toBeInt()
            ->toBeGreaterThanOrEqual(0)
            ->toBeLessThanOrEqual($capMs);

        if ($prev >= 0) {
            expect($delay)->toBeGreaterThanOrEqual($prev);
        }

        $prev = $delay;
    }

    // Mode "full" mengandung jitter; kita cek bounds dan ada nilai > 0 (bukan selalu nol).
    $max = 0;

    for ($i = 0; $i < 25; $i++) {
        $d = RetryBackoff::compute(3, $baseMs, $capMs, 'full', $maxAttempts);

        expect($d)
            ->toBeInt()
            ->toBeGreaterThanOrEqual(0)
            ->toBeLessThanOrEqual($capMs);

        $max = max($max, $d);
    }

    expect($max)->toBeGreaterThan(0);
});
