<?php

use App\Services\Webhooks\RetryBackoff;

it('returns exponential backoff series', function () {
    $b = new RetryBackoff();
    expect($b->secondsFor(1))->toBe(1)
        ->and($b->secondsFor(2))->toBe(2)
        ->and($b->secondsFor(3))->toBe(4)
        ->and($b->secondsFor(4))->toBe(8)
        ->and($b->secondsFor(5))->toBe(16)
        ->and($b->secondsFor(6))->toBe(16);
});
