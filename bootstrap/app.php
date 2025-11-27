<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // === Aliases (dipertahankan + ditambah security.headers) ===
        $middleware->alias([
            'correlation.id'           => \App\Http\Middleware\CorrelationIdMiddleware::class,
            'verify.webhook.signature' => \App\Http\Middleware\VerifyWebhookSignature::class,
            'setlocale'                => \App\Http\Middleware\SetLocale::class,
            'security.headers'         => \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // === Sematkan ke grup bawaan ===
        // Web: atur locale + header keamanan untuk semua respons web
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeaders::class);

        // API: correlation id + header keamanan untuk semua respons API
        $middleware->appendToGroup('api', \App\Http\Middleware\CorrelationIdMiddleware::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\SecurityHeaders::class);

        // Catatan: 'verify.webhook.signature' dipakai per-route di routes/api.php sesuai kebutuhan.
        // Jika butuh di awal chain grup, gunakan ->prependToGroup(...). :contentReference[oaicite:1]{index=1}
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
