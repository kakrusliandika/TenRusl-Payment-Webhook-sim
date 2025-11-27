{{-- Skip link --}}
<a class="skip-link" href="#main-content">
  @lang('main.aria.skip_to_main')
</a>

{{-- Utility bar: language & theme --}}
<nav class="utility-nav" aria-label="@lang('main.aria.utility_nav')">
  <div class="container utility-nav-inner">
    <ul role="list" class="nav-list controls">
      {{-- Language menu (flags) --}}
      @php
        $langs = [
          'en' => 'English',
          'id' => 'Bahasa Indonesia',
          'hi' => 'हिन्दी',
        ];
        $supported   = array_keys($langs);
        $active      = app()->getLocale();
        if (! in_array($active, $supported, true)) {
          $active = 'en';
        }
        $activeLabel = $langs[$active];
      @endphp

      <li class="lang-switch">
        {{-- Button pembuka menu --}}
        <button
          id="lang-menu-button"
          class="icon-btn"
          type="button"
          aria-haspopup="menu"
          aria-expanded="false"
          aria-controls="lang-menu"
        >
          {{-- Tetap pakai bendera --}}
          <img
            src="{{ asset("flags/{$active}.png") }}"
            width="18"
            height="12"
            alt=""
            aria-hidden="true"
            class="flag"
            decoding="async"
            loading="lazy"
          >
          <span class="label">{{ $activeLabel }}</span>
          <i class="fa-solid fa-chevron-down icon" aria-hidden="true"></i>
          <span class="sr-only">@lang('main.aria.language')</span>
        </button>

        {{-- Menu: daftar bahasa --}}
        <ul
          id="lang-menu"
          role="menu"
          class="menu-popper"
          aria-labelledby="lang-menu-button"
          hidden
        >
          @foreach($supported as $hl)
            @php
              $label = $langs[$hl];
              $url   = request()->fullUrlWithQuery(['lang' => $hl]); // konsisten ?lang=
            @endphp

            <li role="none">
              <a
                role="menuitem"
                href="{{ $url }}"
                lang="{{ $hl }}"
                hreflang="{{ $hl }}"
                class="menu-item inline-flex items-center gap-2"
              >
                <img
                  src="{{ asset("flags/{$hl}.png") }}"
                  width="18"
                  height="12"
                  alt=""
                  aria-hidden="true"
                  class="flag"
                  decoding="async"
                  loading="lazy"
                >
                <span>{{ $label }}</span>
              </a>
            </li>
          @endforeach
        </ul>
      </li>

      {{-- Theme toggle: Font Awesome sun/moon --}}
      <li>
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
      </li>
    </ul>
  </div>
</nav>
