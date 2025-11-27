{{-- resources/views/pages/_partials/header.blade.php --}}
@props(['provider'])

@php
  $key     = $provider['key']     ?? '';
  $name    = $provider['name']    ?? __('pages.provider_default_name');
  $summary = $provider['summary'] ?? '';
  $docs    = $provider['docs']    ?? null;

  /**
   * LOGO PROVIDER
   *
   * Standar fallback:
   *   public/img/providers/{key}.png
   * contoh:
   *   img/providers/dana.png
   *   img/providers/xendit.png
   */
  if (!empty($provider['logo'])) {
      // Kalau sudah diset (full URL / asset()) dari luar, pakai apa adanya
      $logo = $provider['logo'];
  } elseif ($key !== '') {
      // Fallback standar berdasarkan key/slug
      $logo = asset("img/providers/{$key}.png");
  } else {
      $logo = null;
  }

  // Render summary dengan newline -> <br>
  $summaryHtml = $summary !== ''
      ? nl2br(e($summary))
      : '';
@endphp

<header>
  @if($logo)
    <div class="mt-3 center">
      <img
        src="{{ $logo }}"
        alt="{{ $name }} logo"
        width="260"
        height="260"
        loading="lazy"
        decoding="async"
      >
    </div>
  @endif

  <h1 id="provider-title" class="h1 m-0">
    {{ $name }}
  </h1>

  @if($summaryHtml)
    <p class="lede">
      {!! $summaryHtml !!}
    </p>
  @endif

  <div class="cluster mt-3 items-center">
    @if($key)
      <x-badge>
        <span>{{ __('pages.provider_label') }}: {{ strtoupper($key) }}</span>
      </x-badge>
    @endif

    @if($docs)
      <x-button
        as="a"
        :href="$docs"
        target="_blank"
        rel="noopener"
        variant="ghost"
        iconRight="arrow-up-right"
      >
        {{ __('pages.view_docs') }}
      </x-button>
    @endif
  </div>


</header>
