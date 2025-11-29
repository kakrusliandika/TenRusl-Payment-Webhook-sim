<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * List providers (catalog page).
     */
    public function index(Request $request)
    {
        $providers = $this->buildProvidersList();

        return view('pages.providers', [
            'providers' => $providers,
        ]);
    }

    /**
     * Detail provider page.
     *
     * Sekarang:
     * - Langsung pakai view generik: resources/views/pages/show.blade.php
     * - Data utama (summary, docs, endpoints, signature_notes, example_payload)
     *   diambil dari resources/lang/{locale}/pages/{slug}.php
     */
    public function show(Request $request, string $provider)
    {
        // Normalisasi ke lowercase (slug)
        $slug = strtolower($provider);

        // Allowlist dari config/tenrusl.php
        $allow = array_map('strtolower', (array) config('tenrusl.providers_allowlist', []));

        if (! in_array($slug, $allow, true)) {
            abort(404);
        }

        // Meta tambahan (nama, logo, tipe signature, dll)
        $meta = $this->providerMeta($slug);

        // Langsung render pages.show
        // pages/show.blade.php:
        // - resolve nama dari main.providers.map.{slug} atau ucfirst(slug)
        // - baca konten dari lang: pages/{slug}.php
        // - build $providerData dan kirim ke partials
        return view('pages.show', [
            'slug' => $slug,
            'back_url' => route('providers.index'),
            'provider_meta' => $meta,
            // 'provider' boleh null; pages.show pakai slug + lang files
        ]);
    }

    /**
     * Build daftar providers untuk halaman index.
     */
    protected function buildProvidersList(): array
    {
        $allow = (array) config('tenrusl.providers_allowlist', []);
        $metaMap = (array) config('tenrusl.providers_meta', []);

        $list = [];

        foreach ($allow as $p) {
            $slug = strtolower((string) $p);
            $meta = (array) ($metaMap[$slug] ?? []);

            // LOGO: standar -> public/img/providers/{slug}.png
            // Kalau config isi logo dengan value custom:
            //   - kalau sudah absolute URL (http/https) / diawali "/" / "img/",
            //     kita pakai apa adanya.
            //   - kalau tidak, kita fallback ke img/providers/{slug}.png
            $logo = $meta['logo'] ?? null;

            if (! $logo || ! preg_match('~^https?://|^/|^img/~i', $logo)) {
                $logo = "img/providers/{$slug}.png";
            }

            $list[] = [
                'slug' => $slug,
                'name' => $meta['display_name'] ?? ucfirst($slug),
                'logo' => asset($logo),
                'signature' => $meta['signature_type'] ?? null,
                // PARAM HARUS "provider" (sesuai nama di route)
                'url' => route('providers.show', ['provider' => $slug]),
            ];
        }

        return $list;
    }

    /**
     * Meta untuk satu provider (nama, logo, dll).
     */
    protected function providerMeta(string $slug): array
    {
        $metaMap = (array) config('tenrusl.providers_meta', []);
        $meta = (array) ($metaMap[$slug] ?? []);

        // LOGO: sama logika dengan buildProvidersList
        $logo = $meta['logo'] ?? null;

        if (! $logo || ! preg_match('~^https?://|^/|^img/~i', $logo)) {
            $logo = "img/providers/{$slug}.png";
        }

        return [
            'slug' => $slug,
            'name' => $meta['display_name'] ?? ucfirst($slug),
            'logo' => asset($logo),
            'signature' => $meta['signature_type'] ?? null,
        ];
    }
}
