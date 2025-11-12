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
        // Alias middleware kustom
        $middleware->alias([
            'correlation.id'            => \App\Http\Middleware\CorrelationIdMiddleware::class,
            'verify.webhook.signature'  => \App\Http\Middleware\VerifyWebhookSignature::class,
            // Multi-language
            'setlocale'                 => \App\Http\Middleware\SetLocale::class,
        ]);

        // Sematkan CorrelationId ke semua route API (group "api")
        $middleware->appendToGroup('api', \App\Http\Middleware\CorrelationIdMiddleware::class);

        // Sematkan SetLocale ke semua route Web (group "web")
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);

        // (Opsional) jadikan global:
        // $middleware->use(\App\Http\Middleware\SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tambahkan handler khusus jika perlu
    })
    ->create();
