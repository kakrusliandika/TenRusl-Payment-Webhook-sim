<?php

// app/Services/Signatures/SignatureVerifier.php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

/**
 * SignatureVerifier
 * -----------------
 * Source-of-truth untuk routing verifikasi signature berbasis provider.
 *
 * Kontrak utama:
 * - Dipanggil oleh middleware VerifyWebhookSignature sebelum masuk controller/domain.
 * - Provider harus ada di allowlist config('tenrusl.providers_allowlist').
 * - Tiap verifier provider WAJIB punya method:
 *
 *     public static function verify(string $rawBody, Request $request): bool
 *
 * Catatan penting:
 * - Raw body HARUS yang asli dari Request::getContent() (bukan json_encode hasil decode).
 * - Timestamp leeway (jika provider pakai timestamp) diterapkan di kelas verifier provider
 *   memakai config('tenrusl.signature.timestamp_leeway_seconds', 300).
 * - Bandingkan signature pakai constant-time compare (hash_equals) di verifier provider.
 */
final class SignatureVerifier
{
    /**
     * Pemetaan provider → verifier class.
     *
     * NOTE:
     * - Menggunakan ::class menghasilkan string class-name.
     * - Kalau beberapa provider verifier belum ada, verify() akan return false
     *   (karena class_exists / is_callable).
     *
     * @var array<string, class-string>
     */
    private const MAP = [
        // existing / core demo
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
     * @param  string  $provider  Nama provider (mock, xendit, midtrans, dst)
     * @param  string  $rawBody  Raw HTTP body dari Request::getContent()
     * @param  Request  $request  Request Laravel (untuk akses header, query, ip, dsb.)
     */
    public static function verify(string $provider, string $rawBody, Request $request): bool
    {
        // Normalisasi provider
        $provider = strtolower(trim($provider));

        if ($provider === '') {
            return false;
        }

        /**
         * Enforce allowlist:
         * - Harus konsisten antara: route constraint, middleware, dan service.
         * - Jika allowlist kosong, berarti "anggap semua bisa" (tapi untuk proyek ini,
         *   biasanya allowlist diisi).
         */
        $allow = (array) config('tenrusl.providers_allowlist', []);
        if ($allow !== [] && ! in_array($provider, $allow, true)) {
            return false;
        }

        // Mapping provider → verifier class
        $class = self::MAP[$provider] ?? null;
        if ($class === null) {
            // Provider tidak dikenal / belum didukung
            return false;
        }

        // Jangan paksa autoload kalau memang class belum ada
        if (! class_exists($class)) {
            return false;
        }

        // Pastikan class memenuhi kontrak: verify(string $rawBody, Request $request): bool
        if (! is_callable([$class, 'verify'])) {
            return false;
        }

        /** @var callable(string, Request): bool $call */
        $call = [$class, 'verify'];

        return (bool) $call($rawBody, $request);
    }

    /**
     * Daftar provider yang *disupport oleh verifier layer ini*.
     * Jika allowlist diset, hasilnya adalah interseksi MAP vs allowlist.
     *
     * @return string[]
     */
    public static function supported(): array
    {
        $providers = array_keys(self::MAP);
        $allow = (array) config('tenrusl.providers_allowlist', []);

        if ($allow === []) {
            sort($providers);

            return $providers;
        }

        $filtered = array_values(array_intersect($providers, $allow));
        sort($filtered);

        return $filtered;
    }
}
