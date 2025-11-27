@php
  $cfg   = trans('main.home.sections.tooling');
  $cards = is_array($cfg['cards'] ?? null) ? $cfg['cards'] : [];

  // Urutan ikon Font Awesome (loop by index)
  $toolingIcons = [
      'fa-book-open-reader', // OpenAPI docs
      'fa-flask-vial',       // Postman / experiments
      'fa-circle-check',     // CI checks
  ];
@endphp

<section id="tooling" class="section" aria-labelledby="tooling-heading">
  <div class="container">
    <header class="tooling-header">
      <h2 id="tooling-heading" class="h2">
        <i
          class="fa-solid fa-toolbox icon"
          aria-hidden="true"
          style="margin-right:.4rem;"
        ></i>
        {{ $cfg['title'] ?? __('main.tooling') }}
      </h2>

      @if (!empty($cfg['lede']))
        <p class="lede tooling-lede">
          {{ $cfg['lede'] }}
        </p>
      @endif
    </header>

    @if (!empty($cards))
      <div class="tooling-grid">
        @foreach ($cards as $idx => $c)
          @php
            $title = (string) ($c['title'] ?? '');
            $desc  = (string) ($c['desc'] ?? '');
            $icon  = $toolingIcons[$idx % count($toolingIcons)] ?? 'fa-circle-check';
            $step  = str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT);
          @endphp

          <article class="tooling-card">
            <div class="tooling-card-head">
              {{-- ICON ala signature (orbit + circle) --}}
              <div class="tooling-card-icon">
                <span class="tooling-icon-orbit" aria-hidden="true"></span>
                <span class="tooling-icon-circle">
                  <i class="fa-solid {{ $icon }} icon" aria-hidden="true"></i>
                </span>
              </div>

              <div class="tooling-meta">
                <p class="tooling-step">
                  <span class="tooling-step-dot" aria-hidden="true"></span>
                  <span class="tooling-step-label">
                    Step {{ $step }}
                  </span>
                </p>

                <h3 class="h3 tooling-title">
                  {{ $title }}
                </h3>
              </div>
            </div>

            @if ($desc !== '')
              <p class="tooling-desc">
                {{ $desc }}
              </p>
            @endif

            <div class="tooling-footer">
              <span class="tooling-chip">
                <i class="fa-solid fa-terminal icon" aria-hidden="true"></i>
                <span>
                  {{ $cfg['work'] }}
                </span>
              </span>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </div>
</section>
