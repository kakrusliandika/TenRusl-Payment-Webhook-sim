<?php

// app/Services/Signatures/SignatureVerifier.php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;
use Throwable;

/**
 * SignatureVerifier
 * -----------------
 * Source-of-truth untuk routing verifikasi signature berbasis provider.
 *
 * Kontrak utama:
 * - Dipanggil oleh middleware VerifyWebhookSignature sebelum masuk controller/domain.
 * - Provider harus ada di allowlist config('tenrusl.providers_allowlist').
 * - Tiap verifier provider idealnya punya:
 *
 *     public static function verifyWithReason(string $rawBody, Request $request): array{ok:bool, reason:string}
 *
 *   Backward-compat masih didukung:
 *
 *     public static function verify(string $rawBody, Request $request): bool
 *
 * Catatan penting:
 * - Raw body HARUS yang asli dari Request::getContent() (bukan json_encode hasil decode).
 * - Bandingkan signature pakai constant-time compare (hash_equals) di verifier provider.
 */
final class SignatureVerifier
{
    /**
     * Pemetaan provider → verifier class.
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
     * Backward-compatible: verifikasi signature untuk provider tertentu (bool only).
     */
    public static function verify(string $provider, string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($provider, $rawBody, $request)['ok'];
    }

    /**
     * Verifikasi signature dengan output standar: ok + reason singkat (tanpa bocor secret).
     *
     * Fail-safe defaults:
     * - allowlist kosong => FAIL (deny-by-default)
     * - provider tidak dikenal / verifier tidak tersedia => FAIL
     * - exception di verifier => FAIL
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $provider, string $rawBody, Request $request): array
    {
        $provider = strtolower(trim($provider));
        if ($provider === '') {
            return self::result(false, 'missing_provider');
        }

        // Enforce allowlist (deny-by-default).
        $allow = self::normalizeProviders((array) config('tenrusl.providers_allowlist', []));
        if ($allow === []) {
            return self::result(false, 'allowlist_empty');
        }
        if (!in_array($provider, $allow, true)) {
            return self::result(false, 'provider_not_allowed');
        }

        $class = self::MAP[$provider] ?? null;
        if ($class === null) {
            return self::result(false, 'unsupported_provider');
        }

        if (!class_exists($class)) {
            return self::result(false, 'verifier_class_missing');
        }

        try {
            // Preferred: standardized verifier output
            if (is_callable([$class, 'verifyWithReason'])) {
                /** @var mixed $out */
                $out = $class::verifyWithReason($rawBody, $request);

                // Normalize expected shape
                if (is_array($out)) {
                    $ok = $out['ok'] ?? null;
                    $reason = $out['reason'] ?? null;

                    if (is_bool($ok) && is_string($reason) && $reason !== '') {
                        return ['ok' => $ok, 'reason' => $reason];
                    }
                }

                // If provider returns unexpected shape, fail-closed
                return self::result(false, 'invalid_verifier_return');
            }

            // Fallback: legacy bool verifier
            if (is_callable([$class, 'verify'])) {
                /** @var bool $ok */
                $ok = (bool) $class::verify($rawBody, $request);

                return $ok
                    ? self::result(true, 'ok')
                    : self::result(false, 'invalid_signature');
            }

            return self::result(false, 'verifier_contract_missing');
        } catch (Throwable) {
            return self::result(false, 'verifier_exception');
        }
    }

    /**
     * Daftar provider yang *disupport oleh verifier layer ini*.
     * Jika allowlist diset, hasilnya adalah interseksi MAP vs allowlist.
     * Jika allowlist kosong => [] (deny-by-default).
     *
     * @return string[]
     */
    public static function supported(): array
    {
        $providers = array_keys(self::MAP);
        $allow = self::normalizeProviders((array) config('tenrusl.providers_allowlist', []));

        if ($allow === []) {
            return [];
        }

        $filtered = array_values(array_intersect($providers, $allow));
        sort($filtered);

        return $filtered;
    }

    /**
     * Daftar semua provider yang tersedia di MAP, tanpa mempertimbangkan allowlist.
     *
     * @return string[]
     */
    public static function supportedAll(): array
    {
        $providers = array_keys(self::MAP);
        sort($providers);

        return $providers;
    }

    /**
     * @param  array<int, mixed>  $providers
     * @return string[]
     */
    private static function normalizeProviders(array $providers): array
    {
        $out = [];

        foreach ($providers as $p) {
            $v = strtolower(trim((string) $p));
            if ($v !== '') {
                $out[] = $v;
            }
        }

        $out = array_values(array_unique($out));
        sort($out);

        return $out;
    }

    /**
     * @return array{ok: bool, reason: string}
     */
    private static function result(bool $ok, string $reason): array
    {
        return ['ok' => $ok, 'reason' => $reason];
    }
}
