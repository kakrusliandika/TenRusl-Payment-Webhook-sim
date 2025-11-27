@php
  $cfg  = trans('main.home.sections.providers');
  $lede = $cfg['lede'] ?? '';

  // Map slug => label dari lang
  $map = is_array($cfg['map'] ?? null) ? $cfg['map'] : [];
@endphp

<section id="providers" class="section" aria-labelledby="providers-heading">
  <div class="container providers-section">
    {{-- Header --}}
    <header class="providers-header">
      <h2 id="providers-heading" class="h2">
        <i
          class="fa-solid fa-diagram-project icon"
          aria-hidden="true"
          style="margin-right:.4rem;"
        ></i>
        {{ $cfg['title'] ?? __('main.providers_title') }}
      </h2>

      @if ($lede)
        <p class="lede providers-lede">
          {{ $lede }}
        </p>
      @endif
    </header>

    {{-- Marquee logos + text (slider ringan) --}}
    @if (!empty($map))
      <div class="providers-marquee" aria-hidden="true">
        <div class="providers-marquee-track">
          @foreach ($map as $slug => $label)
            @php
              // logo di public/img/providers/{slug}.png
              $logoPath = public_path('img/providers/'.$slug.'.png');
              $hasLogo  = file_exists($logoPath);
              $initials = mb_strtoupper(mb_substr($label, 0, 2, 'UTF-8'), 'UTF-8');
            @endphp

            <div class="providers-marquee-item">
              <div class="providers-marquee-avatar">
                @if ($hasLogo)
                  <img
                    src="{{ asset('img/providers/'.$slug.'.png') }}"
                    alt="{{ $label }}"
                    loading="lazy"
                    decoding="async"
                    width="32"
                    height="32"
                  >
                @else
                  <span class="providers-marquee-initials">
                    {{ $initials }}
                  </span>
                @endif
              </div>

              <span class="providers-marquee-label">
                {{ $label }}
              </span>
            </div>
          @endforeach

          {{-- duplikasi untuk loop seamless --}}
          @foreach ($map as $slug => $label)
            @php
              $logoPath = public_path('img/providers/'.$slug.'.png');
              $hasLogo  = file_exists($logoPath);
              $initials = mb_strtoupper(mb_substr($label, 0, 2, 'UTF-8'), 'UTF-8');
            @endphp

            <div class="providers-marquee-item">
              <div class="providers-marquee-avatar">
                @if ($hasLogo)
                  <img
                    src="{{ asset('img/providers/'.$slug.'.png') }}"
                    alt="{{ $label }}"
                    loading="lazy"
                    decoding="async"
                    width="32"
                    height="32"
                  >
                @else
                  <span class="providers-marquee-initials">
                    {{ $initials }}
                  </span>
                @endif
              </div>

              <span class="providers-marquee-label">
                {{ $label }}
              </span>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- CTA ke katalog lengkap --}}
    <div class="providers-cta">
      <a
        href="{{ route('providers.index') }}"
        class="btn btn-outline btn-icon-right"
      >
        <span>{{ $cfg['cta_all'] }}</span>
        <i class="fa-solid fa-arrow-right icon" aria-hidden="true"></i>
      </a>
    </div>
  </div>
</section>
