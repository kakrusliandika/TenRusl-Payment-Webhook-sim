<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

/**
 * Router verifikasi signature berbasis nama provider.
 *
 * Kontrak:
 * - Endpoint: /webhooks/{provider}
 * - Provider diverifikasi terhadap allowlist (config('tenrusl.providers_allowlist'))
 * - Masing-masing kelas *Signature* harus mengimplementasikan:
 *
 *   public static function verify(string $rawBody, Request $request): bool
 *
 *   Di dalamnya:
 *     - Baca header signature/timestamp sesuai provider (Stripe-Signature, X-CALLBACK-SIGNATURE, dsb.)
 *     - Gunakan raw body ($rawBody) untuk HMAC, bukan hasil json_decode/json_encode.
 *     - Terapkan timestamp leeway (mis. config('tenrusl.signature.timestamp_leeway_seconds', 300))
 *       bila provider memakai timestamp.
 *     - Bandingkan signature dengan constant-time compare (hash_equals).
 */
final class SignatureVerifier
{
    /**
     * Pemetaan provider → kelas verifier.
     *
     * @var array<string, class-string>
     */
    private const MAP = [
        // existing
        'mock' => MockSignature::class,
        'xendit' => XenditSignature::class,
        'midtrans' => MidtransSignature::class,

        // tambahan
        'stripe' => StripeSignature::class,
        'paypal' => PaypalSignature::class,
        'paddle' => PaddleSignature::class,
        'lemonsqueezy' => LemonSqueezySignature::class,
        'airwallex' => AirwallexSignature::class,
        'tripay' => TripaySignature::class,
        'doku' => DokuSignature::class,
        'dana' => DanaSignature::class,
        'oy' => OySignature::class,
        'payoneer' => PayoneerSignature::class,
        'skrill' => SkrillSignature::class,
        'amazon_bwp' => AmazonBwpSignature::class,
    ];

    /**
     * Verifikasi signature untuk provider tertentu.
     *
     * @param  string  $provider  Nama provider (mock, xendit, midtrans, dll.)
     * @param  string  $rawBody  Raw HTTP body (seperti diterima dari Request::getContent())
     * @param  \Illuminate\Http\Request  $request  Request Laravel (untuk akses header, query, dsb.)
     */
    public static function verify(string $provider, string $rawBody, Request $request): bool
    {
        // Opsional: batasi ke allowlist dari config
        $allow = (array) config('tenrusl.providers_allowlist', []);
        if (! empty($allow) && ! in_array($provider, $allow, true)) {
            return false;
        }

        $class = self::MAP[$provider] ?? null;
        if ($class === null) {
            // Provider tidak dikenal
            return false;
        }

        if (! method_exists($class, 'verify')) {
            // Kelas tidak memenuhi kontrak
            return false;
        }

        /** @var callable(string, \Illuminate\Http\Request): bool $call */
        $call = [$class, 'verify'];

        return (bool) $call($rawBody, $request);
    }

    /**
     * Daftar provider yang didukung oleh verifier ini (interseksi allowlist jika ada).
     *
     * @return string[]
     */
    public static function supported(): array
    {
        $providers = array_keys(self::MAP);
        $allow = (array) config('tenrusl.providers_allowlist', []);

        if (empty($allow)) {
            sort($providers);

            return $providers;
        }

        $filtered = array_values(array_intersect($providers, $allow));
        sort($filtered);

        return $filtered;
    }
}
