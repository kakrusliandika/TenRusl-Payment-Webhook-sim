<section class="hero" aria-labelledby="home-hero-heading">
  <div class="container hero-inner">
    @php
      // Map provider dari lang
      $providerMap   = (array) __('main.providers.map');
      $providerTotal = is_array($providerMap) ? count($providerMap) : 0;

      // Susun array avatar (slug + label) dari map
      $providerAvatars = [];
      foreach ($providerMap as $slug => $label) {
          $providerAvatars[] = [
              'slug'  => $slug,
              'label' => $label,
          ];
      }

      // Acak urutan biar kelihatan random
      if (! empty($providerAvatars)) {
          shuffle($providerAvatars);
      }

      // Batasi jumlah avatar di hero, sisanya ditunjukkan sebagai "+N"
      $maxAvatarsToShow = 12;
      $displayAvatars   = array_slice($providerAvatars, 0, $maxAvatarsToShow);
      $providerRemainder = max($providerTotal - count($displayAvatars), 0);
    @endphp

    {{-- Kolom kiri: copy utama --}}
    <div class="hero-copy">
      <h1 id="home-hero-heading" class="h1">
        <span class="muted">
          <i
            class="fa-solid fa-wave-square icon"
            aria-hidden="true"
            style="margin-right:.35rem;"
          ></i>
          {{ __('main.home.hero.heading_prefix') }}
        </span><br>
        {{ __('main.home.title') }}
        — <span class="text-brand">{{ __('main.home.hero.heading_emph') }}</span>
      </h1>

      <p class="lede">
        {{ __('main.home.hero.lede') }}
      </p>

      <p class="muted hero-sublede">
        <i
          class="fa-solid fa-bug-slash icon"
          aria-hidden="true"
          style="margin-right:.35rem;"
        ></i>
        <span>
          Debug webhooks from Xendit, Midtrans, Stripe, PayPal, and more —
          without touching real money in production.
        </span>
      </p>

    <div class="cta cluster mt-4">
        {{-- Absolute URL to OpenAPI docs --}}
        <a
            href="{{ url('/api/documentation') }}"
            class="btn-hero cta-btn-hero-docs"
        >
           <i class="fa-solid fa-book-open"></i>
            <span>{{ __('main.home.hero.cta_docs') }}</span>
        </a>

        {{-- Anchor ke halaman utama / GitHub (nanti sesuaikan URL) --}}
        <a
            href="https://github.com/kakrusliandika/TenRusl-Payment-Webhook-sim"
            class="btn-hero cta-btn-hero-github"
        >
            <i class="fa-brands fa-github"></i>
            <span>{{ __('main.home.hero.cta_github') }}</span>
        </a>
    </div>

      {{-- Trust / tech badges --}}
      <div class="cluster mt-4 hero-badges" aria-label="Tech badges">
        <span class="badge">
          <i
            class="fa-solid fa-code-branch icon"
            aria-hidden="true"
            style="margin-right:.35rem;"
          ></i>
          <span>Open source</span>
        </span>

        <span class="badge">
          <i
            class="fa-brands fa-laravel icon"
            aria-hidden="true"
            style="margin-right:.35rem;"
          ></i>
          <span>Laravel 12</span>
        </span>

        <span class="badge">
          <i
            class="fa-solid fa-plug-circle-bolt icon"
            aria-hidden="true"
            style="margin-right:.35rem;"
          ></i>
          <span>API-first</span>
        </span>
      </div>

      {{-- Stats --}}
      <ul class="stats" role="list">
        <li>
          <span class="big">
            <i
              class="fa-solid fa-diagram-project icon"
              aria-hidden="true"
              style="margin-right:.35rem;"
            ></i>
            {{ $providerTotal }}+
          </span>
          <span class="muted">
            {{ __('main.home.hero.stats.providers.label') }}
          </span>
        </li>

        <li>
          <span class="big">
            <i
              class="fa-solid fa-vial-circle-check icon"
              aria-hidden="true"
              style="margin-right:.35rem;"
            ></i>
            {{ trans_choice('main.plurals.tests', 6, ['count' => 6]) }}
          </span>
          <span class="muted">
            {{ __('main.home.hero.stats.tests.label') }}
          </span>
        </li>

        <li>
          <span class="big">
            <i
              class="fa-solid fa-book-open icon"
              aria-hidden="true"
              style="margin-right:.35rem;"
            ></i>
            OpenAPI
          </span>
          <span class="muted">
            {{ __('main.home.hero.stats.openapi.label') }}
          </span>
        </li>
      </ul>
    </div>

    {{-- Kolom kanan: provider cloud + code card --}}
    <div class="hero-right">
      {{-- Providers avatar cloud ala Astro --}}
      @if (!empty($displayAvatars))
        <div
          class="hero-providers"
          aria-label="{{ $providerTotal }}+ {{ __('main.providers.title') }}"
        >
          {{-- hanya untuk screen reader --}}
          <span class="sr-only">
            {{ $providerTotal }}+ {{ __('main.providers.title') }}
          </span>

          <div class="hero-providers-avatars" aria-hidden="true">
            @foreach ($displayAvatars as $index => $provider)
              <div
                class="hero-provider-avatar"
                title="{{ $provider['label'] }}"
              >
                <img
                  src="{{ asset('img/providers/'.$provider['slug'].'.png') }}"
                  alt="{{ $provider['label'] }}"
                  loading="lazy"
                  decoding="async"
                  width="52"
                  height="52"
                >
              </div>
            @endforeach

            @if ($providerRemainder > 0)
              <div class="hero-provider-avatar hero-provider-more">
                +{{ $providerRemainder }}
              </div>
            @endif
          </div>
        </div>
      @endif

      <figure class="hero-card">
        <div class="">
          <div class="chip hero-chip">
            <span class="pulse" aria-hidden="true"></span>
            <i
              class="fa-solid fa-plug-circle-bolt icon"
              aria-hidden="true"
            ></i>
            <span>{{ __('main.home.hero.chip') }}</span>
          </div>

          <div class="code-hero">
            <pre class="code"><code>curl -X POST "{{ url('/api/webhooks/mock') }}" \
  -H "Idempotency-Key: 7f1c-42b0" \
  -H "Content-Type: application/json" \
  -d '{
    "event": "payment.completed",
    "amount": 250000,
    "currency": "IDR"
  }'</code></pre>

            <p class="muted code-hero-caption">
              <i class="fa-solid fa-terminal icon" aria-hidden="true"></i>
              <span>
                {{ __('main.home.hero.simulate') }}
              </span>
            </p>
          </div>
        </div>

        <figcaption class="sr-only">
          {{ __('main.meta.default_image_alt') }}
        </figcaption>
      </figure>
    </div>
  </div>
</section>
