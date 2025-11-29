<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Services\Payments\Adapters\AirwallexAdapter;
use App\Services\Payments\Adapters\AmazonBwpAdapter;
use App\Services\Payments\Adapters\DanaAdapter;
use App\Services\Payments\Adapters\DokuAdapter;
use App\Services\Payments\Adapters\LemonSqueezyAdapter;
use App\Services\Payments\Adapters\MidtransAdapter;
use App\Services\Payments\Adapters\MockAdapter;
use App\Services\Payments\Adapters\OyAdapter;
use App\Services\Payments\Adapters\PaddleAdapter;
use App\Services\Payments\Adapters\PayoneerAdapter;
use App\Services\Payments\Adapters\PaypalAdapter;
use App\Services\Payments\Adapters\SkrillAdapter;
use App\Services\Payments\Adapters\StripeAdapter;
use App\Services\Payments\Adapters\TripayAdapter;
use App\Services\Payments\Adapters\XenditAdapter;
use App\Services\Payments\Contracts\PaymentAdapter;
use InvalidArgumentException;

/**
 * Registry sederhana untuk adapter pembayaran + operasi create/status.
 *
 * Catatan:
 * - Secara default akan mendaftarkan semua adapter bawaan.
 * - Jika ingin meng-overwrite, injeksikan array $adapters via container.
 * - Penyaringan berdasarkan allowlist: config('tenrusl.providers_allowlist').
 */
final class PaymentsService
{
    /** @var array<string, PaymentAdapter> */
    private array $adapters = [];

    /**
     * @param  iterable<PaymentAdapter>|null  $adapters
     * @param  array<string>|null  $allowedProviders
     */
    public function __construct(?iterable $adapters = null, ?array $allowedProviders = null)
    {
        // Daftar default bila tidak di-inject
        if ($adapters === null) {
            $adapters = [
                new MockAdapter,
                new XenditAdapter,
                new MidtransAdapter,
                new StripeAdapter,
                new PaypalAdapter,
                new PaddleAdapter,
                new LemonSqueezyAdapter,
                new AirwallexAdapter,
                new TripayAdapter,
                new DokuAdapter,
                new DanaAdapter,
                new OyAdapter,
                new PayoneerAdapter,
                new SkrillAdapter,
                new AmazonBwpAdapter,
            ];
        }

        // Allowlist dari config (opsional)
        if ($allowedProviders === null) {
            /** @var array<string> $cfg */
            $cfg = (array) config('tenrusl.providers_allowlist', []);
            $allowedProviders = $cfg;
        }

        foreach ($adapters as $adapter) {
            $name = $adapter->provider();
            if (! empty($allowedProviders) && ! in_array($name, $allowedProviders, true)) {
                continue; // skip yang tidak di-allow
            }
            $this->adapters[$name] = $adapter;
        }
    }

    /**
     * Tambahkan adapter baru secara dinamis.
     */
    public function addAdapter(PaymentAdapter $adapter): void
    {
        $this->adapters[$adapter->provider()] = $adapter;
    }

    /**
     * Dapatkan daftar provider yang tersedia.
     *
     * @return string[]
     */
    public function providers(): array
    {
        ksort($this->adapters);

        return array_keys($this->adapters);
    }

    /**
     * Ambil adapter untuk provider tertentu.
     */
    public function getAdapter(string $provider): PaymentAdapter
    {
        if (! isset($this->adapters[$provider])) {
            throw new InvalidArgumentException("Unknown or disabled provider: {$provider}");
        }

        return $this->adapters[$provider];
    }

    /**
     * Buat pembayaran simulasi via adapter terkait.
     *
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */
    public function create(string $provider, array $input): array
    {
        return $this->getAdapter($provider)->create($input);
    }

    /**
     * Ambil status simulasi via adapter terkait.
     *
     * @return array{provider:string, provider_ref:string, status:string}
     */
    public function status(string $provider, string $providerRef): array
    {
        return $this->getAdapter($provider)->status($providerRef);
    }
}
