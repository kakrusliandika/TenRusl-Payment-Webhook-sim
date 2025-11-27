@php
  $cfg   = trans('main.home.sections.features');
  $items = is_array($cfg['items'] ?? null) ? $cfg['items'] : [];

  // Urutan ikon Font Awesome (akan di-loop pakai index)
  $featureIcons = [
      'fa-rotate',            // Idempotency
      'fa-shield-halved',     // Signature Verification
      'fa-clock-rotate-left', // Dedup & Retry
      'fa-book-open',         // OpenAPI
      'fa-flask',             // Postman
      'fa-code-compare',      // CI / pipelines
  ];

  // Badge teks kecil di bawah judul (opsional, purely visual)
  $featureBadges = [
      'Idempotency-Key',
      'HMAC / timestamp',
      'Retry & dedup',
      'Swagger / OpenAPI',
      'Postman collection',
      'CI pipelines',
  ];
@endphp

<section id="features" class="section features" aria-labelledby="features-heading">
  <div class="container">
    <header class="features-header">
      <h2 id="features-heading" class="h2">
        <i
          class="fa-solid fa-screwdriver-wrench icon"
          aria-hidden="true"
          style="margin-right:.4rem;"
        ></i>
        {{ $cfg['title'] ?? __('main.features') }}
      </h2>

      @if (!empty($cfg['lede']))
        <p class="lede features-lede">
          {{ $cfg['lede'] }}
        </p>
      @endif
    </header>

    @if (!empty($items))
      <div class="features-grid">
        @foreach ($items as $idx => $it)
          @php
            $title = (string) ($it['title'] ?? '');
            $desc  = (string) ($it['desc'] ?? '');
            $icon  = $featureIcons[$idx % count($featureIcons)] ?? 'fa-circle-check';
            $badge = $featureBadges[$idx % count($featureBadges)] ?? null;
          @endphp

          <article class="feature-card">
            <div class="feature-card-inner">
              {{-- ICON ala signature (orbit + circle) --}}
              <div class="feature-card-icon">
                <span class="feature-icon-orbit" aria-hidden="true"></span>
                <span class="feature-icon-circle">
                  <i class="fa-solid {{ $icon }} icon" aria-hidden="true"></i>
                </span>
              </div>

              <header class="feature-head">
                <h3 class="h3 feature-title">
                  {{ $title }}
                </h3>

                @if ($badge)
                  <span class="feature-badge">
                    <i class="fa-solid fa-circle-small" aria-hidden="true"></i>
                    <span>{{ $badge }}</span>
                  </span>
                @endif
              </header>

              <p class="feature-desc">
                {{ $desc }}
              </p>

              {{-- Tiny “hint” line, biar beda dari endpoints/tooling --}}
              <p class="feature-hint muted">
                <i
                  class="fa-solid fa-bolt icon"
                  aria-hidden="true"
                  style="margin-right:.35rem;"
                ></i>
                <span>
                 {{ $cfg['ship'] }}
                </span>
              </p>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </div>
</section>
