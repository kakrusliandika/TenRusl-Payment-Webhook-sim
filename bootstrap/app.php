<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Route files:
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',

        // Single health endpoint (standard)
        health: '/health',

        // Catatan:
        // - Prefix "/api" biasanya otomatis untuk routes/api.php.
        // - Kalau mau ubah prefix, bisa set apiPrefix di withRouting(...).
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ============================================================
        // 1) GLOBAL MIDDLEWARE
        // ============================================================
        $middleware->prepend(\App\Http\Middleware\CorrelationIdMiddleware::class);

        // ============================================================
        // 2) MIDDLEWARE ALIASES (dipakai di routes)
        // ============================================================
        $middleware->alias([
            'correlation.id' => \App\Http\Middleware\CorrelationIdMiddleware::class,
            'verify.webhook.signature' => \App\Http\Middleware\VerifyWebhookSignature::class,
            'setlocale' => \App\Http\Middleware\SetLocale::class,

            // Package-based hardening (menghilangkan duplikasi custom)
            'secure.headers' => \Bepsvpt\SecureHeaders\SecureHeadersMiddleware::class,
            'csp' => \Spatie\Csp\AddCspHeaders::class,

            // Backward compatible alias (kalau ada route lama pakai ini)
            'security.headers' => \Bepsvpt\SecureHeaders\SecureHeadersMiddleware::class,
        ]);

        // ============================================================
        // 3) GROUP MIDDLEWARE (web / api)
        // ============================================================
        // Web UI: locale + secure headers + CSP
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SetLocale::class,
            \Bepsvpt\SecureHeaders\SecureHeadersMiddleware::class,
            \Spatie\Csp\AddCspHeaders::class,
        ]);

        // API: secure headers (tanpa CSP agar tidak “rame” di response JSON)
        $middleware->appendToGroup('api', [
            \Bepsvpt\SecureHeaders\SecureHeadersMiddleware::class,
        ]);

        // Catatan:
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
