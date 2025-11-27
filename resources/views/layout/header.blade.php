{{-- Full fixed header: brand + nav + language (flags + text) + theme --}}
<header class="app-header" data-scope="pages">
  {{-- Skip link --}}
  <a class="skip-link" href="#main-content">
    @lang('main.aria.skip_to_main')
  </a>

  <div class="app-header-main">
    <div class="container app-header-inner">
      {{-- Brand --}}
      <a class="brand" href="{{ url('/') }}">
        <img
          src="{{ asset('icon.svg') }}"
          alt="{{ config('app.name', 'TenRusl') }}"
          width="28"
          height="28"
        >

        <strong>
          <span class="brand-full">
            {{ config('app.name', 'TenRusl Payment Webhook Sim') }}
          </span>
          <span class="brand-abbr">
            TRPW
          </span>
        </strong>

        <span class="badge">
          @lang('main.home_title')
        </span>
      </a>

      {{-- Right: nav + language + theme on the same row --}}
      <div class="app-header-right">
        {{-- Primary nav --}}
        <nav
          id="primary-nav"
          class="nav"
          aria-label="@lang('main.aria.primary_nav')"
        >
          {{-- Providers page --}}
          <a href="{{ url('/') }}#providers">
            @lang('main.providers_title')
          </a>

          {{-- Production (map ke section features di home) --}}
          <a href="{{ url('/') }}#features">
            @lang('main.features')
          </a>

          {{-- Endpoints section --}}
          <a href="{{ url('/') }}#endpoints">
            @lang('main.endpoints')
          </a>

          {{-- Signature section --}}
          <a href="{{ url('/') }}#signature">
            @lang('main.signature')
          </a>

          {{-- Tooling section --}}
          <a href="{{ url('/') }}#tooling">
            @lang('main.tooling')
          </a>
        </nav>

        @php
          // Ambil daftar locale dari config (dipakai UI)
          $availableLocales = (array) config('app.available_locales', []);

          // Bahasa yang didukung UI + punya flag
          $supportedMap = [
              'en' => 'English',
              'id' => 'Indonesia',
              'ar' => 'العربية',
              'de' => 'Deutsch',
              'hi' => 'हिन्दी',
              'ja' => '日本語',
              'ko' => '한국어',
              'pt' => 'Português',
              'ru' => 'Русский',
              'th' => 'ภาษาไทย',
              'zh' => '中文',
              'es' => 'Español',
          ];

          // Filter: hanya locale yang ada di available_locales dan di supportedMap
          $supported = [];
          foreach ($availableLocales as $lc) {
              if (isset($supportedMap[$lc])) {
                  $supported[$lc] = $supportedMap[$lc];
              }
          }

          // Fallback kalau config kosong / tidak sinkron
          if ($supported === []) {
              $supported = $supportedMap;
          }

          // Locale aktif dari Laravel
          $active = app()->getLocale();
          if (! array_key_exists($active, $supported)) {
              $active = 'en';
          }
        @endphp

        {{-- Controls: language (flag + text) + theme + mobile nav --}}
        <div class="header-controls">
          {{-- Language dropdown --}}
          <div class="lang-switch">
            <button
              id="lang-menu-button"
              class="icon-btn"
              type="button"
              aria-haspopup="menu"
              aria-expanded="false"
              aria-controls="lang-menu"
              aria-label="@lang('main.aria.language')"
            >
              <img
                src="{{ asset("flags/{$active}.png") }}"
                width="20"
                height="14"
                alt=""
                aria-hidden="true"
                class="flag"
                decoding="async"
                loading="lazy"
              >
              <span class="label">{{ $supported[$active] }}</span>
              <i class="fa-solid fa-chevron-down icon" aria-hidden="true"></i>
              <span class="sr-only">@lang('main.aria.language')</span>
            </button>

            <ul
              id="lang-menu"
              role="menu"
              class="menu-popper"
              aria-labelledby="lang-menu-button"
              hidden
            >
              @foreach($supported as $hl => $label)
                @php
                  $url = request()->fullUrlWithQuery(['lang' => $hl]);
                @endphp

                <li role="none">
                  <a
                    role="menuitem"
                    href="{{ $url }}"
                    lang="{{ $hl }}"
                    hreflang="{{ $hl }}"
                    class="menu-item"
                  >
                    <img
                      src="{{ asset("flags/{$hl}.png") }}"
                      width="20"
                      height="14"
                      alt=""
                      aria-hidden="true"
                      class="flag"
                      decoding="async"
                      loading="lazy"
                    >
                    <span class="lang-name">{{ $label }}</span>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>

          {{-- Theme toggle --}}
          <button
            id="theme-toggle"
            class="icon-btn"
            type="button"
            aria-pressed="false"
            aria-label="@lang('main.aria.toggle_theme')"
            style="position: relative"
          >
            <i class="fa-solid fa-sun icon icon-sun" aria-hidden="true"></i>
            <i class="fa-solid fa-moon icon icon-moon" aria-hidden="true"></i>
            <span class="sr-only">@lang('main.aria.toggle_theme')</span>
          </button>

          <a
            class="icon-btn"
            href="https://github.com/kakrusliandika/TenRusl-Payment-Webhook-sim"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="@lang('main.github')"
          >
            <i class="fa-brands fa-github icon" aria-hidden="true"></i>
          </a>

          {{-- Mobile nav toggle (hamburger) --}}
          <button
            class="nav-toggle"
            type="button"
            aria-expanded="false"
            aria-controls="primary-nav"
            aria-haspopup="menu"
            aria-label="@lang('main.aria.toggle_menu')"
          >
            <i class="fa-solid fa-bars icon" aria-hidden="true"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</header>
