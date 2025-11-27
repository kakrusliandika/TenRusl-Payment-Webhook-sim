@extends('layout.app')

@include('layout.seo', [
  'title'       => __('main.providers.title') . ' | ' . __('main.site_name'),
  'description' => __('main.providers.description'),
  'canonical'   => url()->current(),
])

@section('content')
<section class="section" aria-labelledby="providers-title">
  <div class="container providers-page">
    {{-- Heading --}}
    <header class="providers-head">
      <div class="providers-head-copy">
        <h1 id="providers-title" class="h1 mb-2">
          {{ __('main.providers.title') }}
        </h1>

        <p class="lede">
          {{ __('main.providers.description') }}
        </p>
      </div>
    </header>

    {{-- Provider catalog from ProviderController@index --}}
    <div id="providers-grid" class="providers-grid">
      @forelse ($providers as $provider)
        @php
          $slug = (string) ($provider['slug'] ?? '');
          $name = (string) ($provider['name'] ?? ucfirst($slug));

          // logo sudah full URL dari controller; fallback ke public/providers/{slug}.png
          $logo = asset("img/providers/{$slug}.png");

          // optional hint dari lang/pages.php: ['providers' => ['slug' => ['hint' => '...']]]
          $hint = data_get(trans('pages.providers'), "{$slug}.hint");

          // URL detail provider
          $url  = $provider['url'] ?? route('providers.show', ['provider' => $slug]);
        @endphp

        <article
          class="card provider-card"
          data-name="{{ strtolower($name) }}"
          data-slug="{{ $slug }}"
        >
          <div class="provider-card-row">
            {{-- Logo kiri --}}
            <div class="provider-logo-wrap">
              <img
                src="{{ $logo }}"
                alt="{{ $name }} logo"
                width="40"
                height="40"
                loading="lazy"
                decoding="async"
                class="provider-logo"
              >
            </div>

            {{-- Info kanan: nama + hint + tombol --}}
            <div class="provider-card-main">
              <h2 class="h3 provider-card-title">
                {{ $name }}
              </h2>

              @if ($hint)
                <p class="muted provider-card-hint">
                  {{ $hint }}
                </p>
              @endif

              <a
                href="{{ $url }}"
                class="btn btn-primary provider-link"
              >
                <span>{{ __('pages.view_details') }}</span>
                <i class="fa-solid fa-arrow-right icon" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </article>
      @empty
        <p class="muted">
          {{ __('pages.no_results') }}
        </p>
      @endforelse
    </div>

    {{-- Empty-state text (kalau nanti mau dipakai JS) --}}
    <p class="muted providers-empty" id="providers-empty" hidden>
      {{ __('pages.no_results') }}
    </p>
  </div>
</section>
@endsection
