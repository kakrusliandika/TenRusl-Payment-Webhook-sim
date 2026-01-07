<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\PaymentRepository;
use App\Repositories\WebhookEventRepository;
use App\Services\Payments\PaymentsService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

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

        // Repository bindings (digunakan di berbagai service, termasuk WebhookProcessor)
        $this->app->singleton(PaymentRepository::class, fn () => new PaymentRepository);
        $this->app->singleton(WebhookEventRepository::class, fn () => new WebhookEventRepository);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fail-fast di production: deny-by-default membutuhkan allowlist non-kosong.
        if ($this->app->environment('production')) {
            $allowlist = $this->normalizeAllowlist((array) config('tenrusl.providers_allowlist', []));
            if ($allowlist === []) {
                throw new RuntimeException(
                    'TENRUSL_PROVIDERS_ALLOWLIST is required in production (deny-by-default). '.
                    'Set it via environment variables / secret manager.'
                );
            }
        }
    }

    /**
     * @param  array<int, mixed>  $providers
     * @return array<int, string>
     */
    private function normalizeAllowlist(array $providers): array
    {
        $normalized = array_map(
            static fn ($p) => strtolower(trim((string) $p)),
            $providers
        );

        $normalized = array_filter($normalized, static fn ($p) => $p !== '');
        $normalized = array_values(array_unique($normalized));

        return $normalized;
    }
}
