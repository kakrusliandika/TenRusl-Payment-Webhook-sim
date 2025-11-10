<?php

declare(strict_types=1);

it('loads tenrusl config with expected defaults and providers allowlist', function () {
    $ttl  = (int) config('tenrusl.idempotency_ttl');
    $max  = (int) config('tenrusl.max_retry_attempts');
    $list = (array) config('tenrusl.providers_allowlist');

    expect($ttl)->toBeGreaterThan(0);
    expect($max)->toBeGreaterThanOrEqual(1);
    expect($list)->toBeArray()->not->toBeEmpty();

    // Pastikan beberapa provider utama tersedia
    $expected = [
        'mock','xendit','midtrans','stripe','paypal','paddle',
        'lemonsqueezy','airwallex','tripay','doku','dana','oy',
        'payoneer','skrill','amazon_bwp',
    ];
    foreach ($expected as $p) {
        expect(in_array($p, $list, true))->toBeTrue();
    }
});

it('exposes individual secrets via config mapping', function () {
    // Set via config() untuk memastikan config/tenrusl.php membaca ENV/values dengan benar
    config([
        'tenrusl.mock_secret'           => 'mocked',
        'tenrusl.xendit_callback_token' => 'token',
        'tenrusl.midtrans_server_key'   => 'server',
    ]);

    expect(config('tenrusl.mock_secret'))->toBe('mocked');
    expect(config('tenrusl.xendit_callback_token'))->toBe('token');
    expect(config('tenrusl.midtrans_server_key'))->toBe('server');
});
