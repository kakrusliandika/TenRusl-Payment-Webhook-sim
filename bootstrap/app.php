<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ============================================================
        // Alias middleware
        // ============================================================
        $middleware->alias([
            // Menyematkan / membuat / mewariskan X-Request-ID ke tiap request.
            'correlation.id' => \App\Http\Middleware\CorrelationIdMiddleware::class,

            // Verifikasi signature webhook per provider (/webhooks/{provider}).
            'verify.webhook.signature' => \App\Http\Middleware\VerifyWebhookSignature::class,

            // Set locale (mis. berdasarkan header, user, atau query).
            'setlocale' => \App\Http\Middleware\SetLocale::class,

            // Header keamanan umum (X-Frame-Options, X-Content-Type-Options, dsb).
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // ============================================================
        // Grup "web"
        // ============================================================

        // Untuk semua route web: set locale + header security.
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeaders::class);

        // ============================================================
        // Grup "api"
        // ============================================================

        // CORS untuk API.
        // Secara default Laravel sudah punya HandleCors di global stack,
        // tapi baris ini memastikan untuk grup 'api' tetap ter-apply
        // meskipun suatu saat kamu override stack.
        $middleware->appendToGroup('api', HandleCors::class);

        // Correlation ID untuk semua request API (logging, tracing).
        $middleware->appendToGroup('api', \App\Http\Middleware\CorrelationIdMiddleware::class);

        // Header keamanan tambahan untuk API.
        $middleware->appendToGroup('api', \App\Http\Middleware\SecurityHeaders::class);

        // Catatan:
        // - 'verify.webhook.signature' dipakai per-route di routes/api.php
        //   pada endpoint webhook saja, bukan seluruh API.
        //   Contoh:
        //   Route::post('/webhooks/{provider}', ...)
        //       ->middleware('verify.webhook.signature');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
