{{-- C:\laragon\www\TenRusl-Payment-Webhook-sim\resources\views\main\partials\_endpoints.blade.php --}}
@php
  $cfg   = trans('main.home.sections.endpoints');
  $cards = is_array($cfg['cards'] ?? null) ? $cfg['cards'] : [];

  // Map HTTP method → Font Awesome icon
  $methodIcons = [
      'GET'    => 'fa-arrow-down-short-wide',
      'POST'   => 'fa-arrow-up-right-from-square',
      'PUT'    => 'fa-pen-to-square',
      'PATCH'  => 'fa-pen',
      'DELETE' => 'fa-trash',
  ];
@endphp

<section
  id="endpoints"
  class="section endpoints"
  aria-labelledby="endpoints-heading"
>
  <div class="container">
    <header class="endpoints-header">
      <h2 id="endpoints-heading" class="h2">
        <i
          class="fa-solid fa-diagram-project icon"
          aria-hidden="true"
          style="margin-right:.4rem;"
        ></i>
        {{ $cfg['title'] ?? __('main.endpoints') }}
      </h2>

      @php
        $lede = (string) ($cfg['lede'] ?? '');
      @endphp

      <p class="lede endpoints-lede">
        @if($lede !== '')
          {{ $lede }}
        @else
          Define a small, realistic surface area: create payments, poll status,
          and receive provider-style webhooks with idempotent semantics.
        @endif
      </p>
    </header>

    @if (!empty($cards))
      <div class="endpoints-grid">
        @foreach ($cards as $c)
          @php
            $rawTitle = (string) ($c['title'] ?? '');
            $desc     = (string) ($c['desc'] ?? '');
            $code     = (string) ($c['code'] ?? $rawTitle);

            // Derive METHOD + PATH from title, e.g. "POST /api/payments"
            $method = '';
            $path   = $rawTitle;
            if (preg_match('/^(GET|POST|PUT|PATCH|DELETE|HEAD|OPTIONS)\s+(.+)$/i', $rawTitle, $m)) {
                $method = strtoupper($m[1]);
                $path   = $m[2];
            }

            $icon   = $methodIcons[$method] ?? 'fa-code';
            $methodClass = $method !== '' ? strtolower($method) : 'api';
          @endphp

          <article class="endpoint-card">
            <div class="endpoint-card-inner">
              <header class="endpoint-head">
                <span class="endpoint-method endpoint-method--{{ $methodClass }}">
                  <i
                    class="fa-solid {{ $icon }}"
                    aria-hidden="true"
                  ></i>
                  <span>{{ $method !== '' ? $method : 'API' }}</span>
                </span>

                <code class="endpoint-path">
                  {{ $path }}
                </code>
              </header>

              @if ($desc !== '')
                <p class="endpoint-desc">
                  {{ $desc }}
                </p>
              @endif

              <div class="endpoint-meta">
                <span class="endpoint-meta-chip">
                  <i
                    class="fa-solid fa-shield-halved"
                    aria-hidden="true"
                  ></i>
                  <span>Verified signatures</span>
                </span>

                <span class="endpoint-meta-chip">
                  <i
                    class="fa-solid fa-code"
                    aria-hidden="true"
                  ></i>
                  <span>JSON payloads</span>
                </span>
              </div>

              <div class="endpoint-example">
                <span class="endpoint-example-label">
                  <i
                    class="fa-solid fa-terminal"
                    aria-hidden="true"
                  ></i>
                  <span>Example</span>
                </span>

                <code class="endpoint-example-code">
                  {{ $code }}
                </code>
              </div>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </div>
</section>
