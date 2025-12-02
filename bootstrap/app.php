<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Route files:
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',

        // Health endpoint (opsional)
        health: '/up',

        // Catatan:
        // - Prefix "/api" biasanya otomatis untuk routes/api.php.
        // - Kalau mau ubah prefix, bisa set apiPrefix di withRouting(...).
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ============================================================
        // 1) GLOBAL MIDDLEWARE
        // ============================================================
        // Correlation ID idealnya global supaya tracing konsisten untuk:
        // - /api/*
        // - / (web)
        //
        // Kita gunakan prepend agar request id sudah ada sejak paling awal pipeline.
        $middleware->prepend(\App\Http\Middleware\CorrelationIdMiddleware::class);

        // Catatan:
        // - HandleCors umumnya sudah ada di global middleware stack default Laravel.
        //   Jadi biasanya tidak perlu ditambahkan manual di sini, kecuali kamu remove/override stack.

        // ============================================================
        // 2) MIDDLEWARE ALIASES (dipakai di routes)
        // ============================================================
        // Alias ini memungkinkan kita menulis ->middleware('verify.webhook.signature')
        // di routes/api.php.
        $middleware->alias([
            // Boleh dipakai di route/group kalau suatu saat tidak ingin global.
            'correlation.id' => \App\Http\Middleware\CorrelationIdMiddleware::class,

            // Dipakai hanya pada route webhook:
            // POST /api/v1/webhooks/{provider}
            'verify.webhook.signature' => \App\Http\Middleware\VerifyWebhookSignature::class,

            // Optional: localization & security hardening
            'setlocale' => \App\Http\Middleware\SetLocale::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // ============================================================
        // 3) GROUP MIDDLEWARE (web / api)
        // ============================================================
        // Web UI: locale + security headers
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // API: security headers
        // (CorrelationIdMiddleware sudah global, jadi tidak perlu ditambah lagi)
        $middleware->appendToGroup('api', [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Catatan penting:
        // - VerifyWebhookSignature sengaja TIDAK dibuat global / group "api"
        //   karena hanya relevan untuk endpoint webhook.
        // - Pemasangan yang benar ada di routes/api.php:
        //     Route::post('/webhooks/{provider}', ...)
        //       ->middleware('verify.webhook.signature');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tempat untuk custom exception rendering/reporting kalau dibutuhkan.
        // Untuk sekarang biarkan default.
    })
    ->create();
