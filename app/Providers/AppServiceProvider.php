<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\PaymentRepository;
use App\Repositories\WebhookEventRepository;
use App\Services\Payments\PaymentsService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Di method ini, lakukan binding ke service container.
     * (Sesuai pedoman Laravel: hanya binding di register; bootstrap di boot)
     */
    public function register(): void
    {
        // PaymentsService sebagai singleton (satu instance untuk seluruh aplikasi)
        $this->app->singleton(PaymentsService::class, function (Application $app) {
            // Allowed providers dari config; adapters default akan diregistrasi di ctor
            $allowed = (array) config('tenrusl.providers_allowlist', []);
            return new PaymentsService(adapters: null, allowedProviders: $allowed);
        });

        // Repository bindings
        $this->app->singleton(PaymentRepository::class, fn () => new PaymentRepository());
        $this->app->singleton(WebhookEventRepository::class, fn () => new WebhookEventRepository());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tambahkan bootstrap logic jika diperlukan (macro, morphMap, dll.)
    }
}
