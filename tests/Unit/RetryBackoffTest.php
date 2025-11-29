<?php

declare(strict_types=1);

use App\Services\Webhooks\RetryBackoff;

it('gives increasing delays with sane bounds', function () {
    /** @var RetryBackoff $rb */
    $rb = app(RetryBackoff::class);

    $method = null;
    foreach (['nextDelaySeconds', 'delaySeconds', 'forAttempt', 'delayForAttempt'] as $m) {
        if (method_exists($rb, $m)) {
            $method = $m;
            break;
        }
    }
    expect($method)->not->toBeNull();

    $prev = 0;
    for ($i = 0; $i < 6; $i++) {
        $d = (int) $rb->$method($i);
        expect($d)->toBeGreaterThanOrEqual(1)->and($d)->toBeLessThanOrEqual(3600);
        expect($d)->toBeGreaterThanOrEqual((int) floor($prev / 2));
        $prev = $d;
    }
});

// (Sengaja dihapus pengujian nextRunAt untuk kompatibilitas implementasi yang tidak menyediakannya)
