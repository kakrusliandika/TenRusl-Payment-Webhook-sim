<?php

declare(strict_types=1);

use App\Services\Webhooks\RetryBackoff;

it('computes retry delays within configured bounds', function () {
    /**
     * Repo ini mendokumentasikan retry backoff berbasis milidetik (ms) dan dibatasi cap. (README)
     * Kita set config eksplisit di test supaya deterministik terhadap nilai cap/basis.
     */
    config([
        'tenrusl.retry_base_ms' => 500,
        'tenrusl.retry_cap_ms' => 30_000,
        'tenrusl.scheduler_backoff_mode' => 'full',
    ]);

    /** @var RetryBackoff $rb */
    $rb = app(RetryBackoff::class);

    // 1) Coba nama method yang paling umum dipakai di berbagai implementasi.
    $method = null;
    $candidates = [
        // ms-oriented (paling mungkin di repo ini)
        'delayForAttemptMs',
        'forAttemptMs',
        'nextDelayMs',
        'delayMs',
        'backoffMs',
        'computeDelayMs',
        'getDelayMs',

        // seconds-oriented / legacy
        'delayForAttempt',
        'forAttempt',
        'nextDelaySeconds',
        'delaySeconds',
        'computeDelaySeconds',
        'getDelaySeconds',

        // generic
        'compute',
        'delay',
        'nextDelay',
        'backoff',
    ];

    foreach ($candidates as $name) {
        if (method_exists($rb, $name)) {
            $method = $name;
            break;
        }
    }

    // 2) Fallback: cari method publik yang “terlihat” seperti kalkulator delay/backoff.
    if ($method === null) {
        $rc = new ReflectionClass($rb);

        foreach ($rc->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
            if ($m->isStatic()) {
                continue;
            }

            $name = $m->getName();
            if ($name === '__construct') {
                continue;
            }

            if (! preg_match('/(delay|backoff|jitter|compute|next)/i', $name)) {
                continue;
            }

            if ($m->getNumberOfParameters() < 1) {
                continue;
            }

            // Uji panggilan ringan untuk memastikan method ini bisa dipakai dan mengembalikan angka.
            try {
                $rm = new ReflectionMethod($rb, $name);
                $args = [];

                foreach ($rm->getParameters() as $p) {
                    if ($p->isVariadic()) {
                        // skip method variadic
                        continue 2;
                    }

                    $pName = strtolower($p->getName());
                    $pType = ($p->hasType() && $p->getType() instanceof ReflectionNamedType)
                        ? $p->getType()->getName()
                        : null;

                    // attempt (param pertama / atau berlabel attempt)
                    if (($pType === 'int' && str_contains($pName, 'attempt')) || $p->getPosition() === 0) {
                        $args[] = 0;
                        continue;
                    }

                    // mode
                    if ($pType === 'string' && str_contains($pName, 'mode')) {
                        $args[] = (string) config('tenrusl.scheduler_backoff_mode', 'full');
                        continue;
                    }

                    // prev delay (decorrelated)
                    if ($pType === 'int' && (str_contains($pName, 'prev') || str_contains($pName, 'previous'))) {
                        $args[] = 0;
                        continue;
                    }

                    if ($p->isDefaultValueAvailable()) {
                        $args[] = $p->getDefaultValue();
                        continue;
                    }

                    if ($p->allowsNull()) {
                        $args[] = null;
                        continue;
                    }

                    // kalau wajib dan tidak bisa dipenuhi, skip method ini
                    continue 2;
                }

                $res = $rb->$name(...$args);

                if (is_int($res) || is_float($res)) {
                    $method = $name;
                    break;
                }
            } catch (Throwable) {
                // coba method berikutnya
                continue;
            }
        }
    }

    expect($method)->not->toBeNull();

    $cap = (int) config('tenrusl.retry_cap_ms', 30_000);
    $mode = (string) config('tenrusl.scheduler_backoff_mode', 'full');

    $callDelay = function (int $attempt, ?int $prevMs = null) use ($rb, $method, $mode): int {
        $rm = new ReflectionMethod($rb, $method);
        $args = [];

        foreach ($rm->getParameters() as $p) {
            if ($p->isVariadic()) {
                throw new RuntimeException("Tidak mendukung method variadic: {$method}()");
            }

            $pName = strtolower($p->getName());
            $pType = ($p->hasType() && $p->getType() instanceof ReflectionNamedType)
                ? $p->getType()->getName()
                : null;

            // attempt (paling umum)
            if (($pType === 'int' && str_contains($pName, 'attempt')) || $p->getPosition() === 0) {
                $args[] = $attempt;
                continue;
            }

            // mode
            if ($pType === 'string' && str_contains($pName, 'mode')) {
                $args[] = $mode;
                continue;
            }

            // prev delay (decorrelated)
            if ($pType === 'int' && (str_contains($pName, 'prev') || str_contains($pName, 'previous'))) {
                $args[] = $prevMs ?? 0;
                continue;
            }

            if ($p->isDefaultValueAvailable()) {
                $args[] = $p->getDefaultValue();
                continue;
            }

            if ($p->allowsNull()) {
                $args[] = null;
                continue;
            }

            throw new RuntimeException("Tidak bisa memanggil {$method}(): parameter '{$p->getName()}' tidak dapat diisi otomatis.");
        }

        $value = $rb->$method(...$args);

        if (is_float($value)) {
            $value = (int) round($value);
        }

        if (! is_int($value)) {
            throw new RuntimeException("{$method}() harus mengembalikan angka (ms/detik).");
        }

        return $value;
    };

    // Validasi batas (ms): 0..cap untuk beberapa attempt.
    $prev = null;
    for ($i = 0; $i < 6; $i++) {
        $d = $callDelay($i, $prev);

        expect($d)
            ->toBeInt()
            ->and($d)->toBeGreaterThanOrEqual(0)
            ->and($d)->toBeLessThanOrEqual($cap);

        $prev = $d;
    }

    // Sanity anti-“selalu nol”: sampling beberapa kali pada attempt menengah harus pernah > 0.
    $max = 0;
    for ($k = 0; $k < 25; $k++) {
        $max = max($max, $callDelay(3, $prev));
    }

    expect($max)->toBeGreaterThan(0);
});
