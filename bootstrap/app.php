<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',      // pastikan file api terdaftar
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias middleware kustom TenRusl
        $middleware->alias([
            'correlation.id'           => \App\Http\Middleware\CorrelationIdMiddleware::class,
            'verify.webhook.signature' => \App\Http\Middleware\VerifyWebhookSignature::class,
        ]);

        // Sematkan CorrelationId ke semua route API (group "api")
        $middleware->appendToGroup('api', \App\Http\Middleware\CorrelationIdMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // bisa ditambahkan handler khusus jika perlu
    })
    ->create();
