<?php

// use Spatie\Csp\Directive;
// use Spatie\Csp\Keyword;

$env = (string) env('APP_ENV', 'production');
$isLocal = in_array($env, ['local', 'development', 'testing'], true);

// Host tambahan yang memang dipakai oleh view bawaan / tooling (sesuaikan kebutuhan)
$extraStyle = [
    'https://fonts.bunny.net',
    'https://tenrusl-diffview.pages.dev',
];

$extraFont = [
    'https://fonts.bunny.net',
    'https://tenrusl-diffview.pages.dev',
];

// Vite dev server (local/dev saja)
$viteDev = [
    'http://localhost:*',
    'http://127.0.0.1:*',
    'http://[::1]:*',
];

$viteWs = [
    'ws://localhost:*',
    'ws://127.0.0.1:*',
    'ws://[::1]:*',
];

// connect-src production: biasanya cukup 'self' + https:
$connectProd = [
    'https:',
];

// Aktifkan directive tanpa value (upgrade-insecure-requests, block-all-mixed-content) via env
$upgradeInsecure = (bool) env('CSP_UPGRADE_INSECURE_REQUESTS', false);
$blockMixed = (bool) env('CSP_BLOCK_ALL_MIXED_CONTENT', false);

/**
 * Dedupe token CSP dengan aman untuk campuran string + enum/object (Keyword).
 * Jangan pakai array_unique() karena akan mencoba cast object ke string (fatal).
 */
$dedupeTokens = static function (array $tokens): array {
    $seen = [];
    $out = [];

    foreach ($tokens as $t) {
        if ($t === null) {
            continue;
        }

        if (is_object($t)) {
            if ($t instanceof \BackedEnum) {
                $key = 'enum:' . get_class($t) . ':' . $t->value;
            } elseif ($t instanceof \UnitEnum) {
                $key = 'enum:' . get_class($t) . ':' . $t->name;
            } elseif (method_exists($t, '__toString')) {
                $key = 'obj:' . get_class($t) . ':' . (string) $t;
            } else {
                $key = 'obj:' . get_class($t) . ':' . spl_object_id($t);
            }
        } else {
            $key = 'scalar:' . (string) $t;
        }

        if (! isset($seen[$key])) {
            $seen[$key] = true;
            $out[] = $t;
        }
    }

    return $out;
};

$fontSrc = $dedupeTokens(array_merge(
    [Spatie\Csp\Keyword::SELF, 'data:'],
    $extraFont
));

$styleSrc = $dedupeTokens(array_merge(
    [Spatie\Csp\Keyword::SELF, Spatie\Csp\Keyword::UNSAFE_INLINE],
    $extraStyle
));

$scriptSrc = $dedupeTokens(array_merge(
    [Spatie\Csp\Keyword::SELF],
    $isLocal ? [Spatie\Csp\Keyword::UNSAFE_EVAL, Spatie\Csp\Keyword::UNSAFE_INLINE] : [],
    $isLocal ? $viteDev : []
));

$connectSrc = $dedupeTokens(array_merge(
    [Spatie\Csp\Keyword::SELF],
    $isLocal ? array_merge($viteDev, $viteWs) : $connectProd
));

$directives = [
    // [Directive::SCRIPT, [Keyword::UNSAFE_EVAL, Keyword::UNSAFE_INLINE]],

    // Frame embedding: pakai CSP (lebih modern daripada X-Frame-Options)
    [Spatie\Csp\Directive::FRAME_ANCESTORS, [Spatie\Csp\Keyword::SELF]],

    // Images: izinkan https + data (mis. inline svg/base64)
    [Spatie\Csp\Directive::IMG, [Spatie\Csp\Keyword::SELF, 'data:', 'https:']],

    // Fonts: izinkan CDN font yang dipakai (bunny/diffview)
    [Spatie\Csp\Directive::FONT, $fontSrc],

    // Style: aman untuk view bawaan + optional inline (jika masih ada inline style)
    [Spatie\Csp\Directive::STYLE, $styleSrc],

    // Script: production ketat, local boleh untuk Vite/hot reload
    [Spatie\Csp\Directive::SCRIPT, $scriptSrc],

    // Connect: local butuh WS + dev server, prod cukup https: (opsional)
    [Spatie\Csp\Directive::CONNECT, $connectSrc],
];

// Optional: kalau kamu memang full-https dan ingin harden mixed content, aktifkan via env
if ($upgradeInsecure) {
    $directives[] = [Spatie\Csp\Directive::UPGRADE_INSECURE_REQUESTS, Spatie\Csp\Value::NO_VALUE];
}

if ($blockMixed) {
    $directives[] = [Spatie\Csp\Directive::BLOCK_ALL_MIXED_CONTENT, Spatie\Csp\Value::NO_VALUE];
}

return [

    /*
     * Presets will determine which CSP headers will be set. A valid CSP preset is
     * any class that implements `Spatie\Csp\Preset`
     */
    'presets' => [
        Spatie\Csp\Presets\Basic::class,
    ],

    /**
     * Register additional global CSP directives here.
     */
    'directives' => $directives,

    /*
     * These presets which will be put in a report-only policy. This is great for testing out
     * a new policy or changes to existing CSP policy without breaking anything.
     */
    'report_only_presets' => [
        //
    ],

    /**
     * Register additional global report-only CSP directives here.
     */
    'report_only_directives' => [
        // [Directive::SCRIPT, [Keyword::UNSAFE_EVAL, Keyword::UNSAFE_INLINE]],
    ],

    /*
     * All violations against a policy will be reported to this url.
     * A great service you could use for this is https://report-uri.com/
     */
    'report_uri' => env('CSP_REPORT_URI', ''),

    /*
     * Headers will only be added if this setting is set to true.
     */
    'enabled' => env('CSP_ENABLED', true),

    /**
     * Headers will be added when Vite is hot reloading.
     */
    'enabled_while_hot_reloading' => env('CSP_ENABLED_WHILE_HOT_RELOADING', false),

    /*
     * The class responsible for generating the nonces used in inline tags and headers.
     */
    'nonce_generator' => Spatie\Csp\Nonce\RandomString::class,

    /*
     * Set false to disable automatic nonce generation and handling.
     * This is useful when you want to use 'unsafe-inline' for scripts/styles
     * and cannot add inline nonces.
     * Note that this will make your CSP policy less secure.
     */
    'nonce_enabled' => env('CSP_NONCE_ENABLED', true),
];
