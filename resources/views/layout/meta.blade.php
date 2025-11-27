@php
  $locale    = app()->getLocale();

  // SEO defaults (bisa di-override per page via layout/seo.blade.php)
  $title       = trim($__env->yieldContent('title'))
                  ?: (config('app.name', 'TenRusl') . ' — ' . __('main.meta.default_title'));
  $description = trim($__env->yieldContent('meta_description', __('main.meta.default_description')));
  $canonical   = trim($__env->yieldContent('canonical', url()->current()));
  $siteName    = config('app.name', 'TenRusl Payment Webhook Sim');
  $image       = trim($__env->yieldContent('meta_image', asset('og.png')));

  // Alt text untuk image sosial (OG/Twitter)
  $imageAlt    = trim($__env->yieldContent('meta_image_alt', __('main.meta.default_image_alt')));

  // Robots (per page bisa override ke noindex, nofollow, dll.)
  $robots      = trim($__env->yieldContent('meta_robots', 'index,follow'));

  // Supported locales for hreflang (configure in config/app.php if needed)
  $supportedLocales = (array) (config('app.supported_locales') ?? ['en','id','hi']);

  // Normalisasi locale untuk Open Graph (contoh: en-US → en_US)
  $ogLocale = str_replace('-', '_', $locale);
@endphp

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light dark">

{{-- Robots policy --}}
<meta name="robots" content="{{ $robots }}">

{{-- Canonical MUST be absolute and unique per page --}}
<link rel="canonical" href="{{ $canonical }}">

{{-- Theme color for light/dark --}}
<meta name="theme-color" media="(prefers-color-scheme: light)" content="#ffffff">
<meta name="theme-color" media="(prefers-color-scheme: dark)"  content="#0b0f19">

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">

{{-- Open Graph --}}
<meta property="og:type"        content="website">
<meta property="og:site_name"   content="{{ $siteName }}">
<meta property="og:title"       content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url"         content="{{ $canonical }}">
<meta property="og:image"       content="{{ $image }}">
<meta property="og:image:alt"   content="{{ $imageAlt }}">
<meta property="og:locale"      content="{{ $ogLocale }}">
@foreach($supportedLocales as $hl)
  @if($hl !== $locale)
    <meta property="og:locale:alternate" content="{{ str_replace('-', '_', $hl) }}">
  @endif
@endforeach
{{-- Opsional: dimensi OG image (sesuaikan dengan og.png kamu) --}}
<meta property="og:image:width"  content="1200">
<meta property="og:image:height" content="630">

{{-- Twitter Card --}}
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image"       content="{{ $image }}">
<meta name="twitter:image:alt"   content="{{ $imageAlt }}">

{{-- hreflang alternates (absolute URLs, consistent ?lang=) --}}
@foreach($supportedLocales as $hl)
  <link rel="alternate" href="{{ url()->current() }}?lang={{ $hl }}" hreflang="{{ $hl }}">
@endforeach
<link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

<link rel="stylesheet" href="https://tenrusl-diffview.pages.dev/assets/plugin/fontawesome/css/all.min.css"/>

{{-- JSON-LD: Organization (rendered via JSON to keep @context intact) --}}
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@type'    => 'Organization',
  'name'     => $siteName,
  'url'      => url('/'),
  'logo'     => asset('logo.svg'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- JSON-LD: WebSite + SearchAction --}}
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@type'    => 'WebSite',
  'url'      => url('/'),
  'name'     => $siteName,
  'potentialAction' => [
    '@type'       => 'SearchAction',
    'target'      => url('/search').'?q={search_term_string}',
    'query-input' => 'required name=search_term_string',
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- Per-page JSON-LD slot --}}
@stack('jsonld')
