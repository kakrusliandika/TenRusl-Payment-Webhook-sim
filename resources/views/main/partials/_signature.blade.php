@php
  $cfg   = trans('main.home.sections.signature');
  $cards = is_array($cfg['cards'] ?? null) ? $cfg['cards'] : [];

  // Urutan ikon Font Awesome (diputar berdasarkan index)
  $signatureIcons = [
      'fa-shield-halved',     // keamanan
      'fa-clock-rotate-left', // replay protection / timestamp
      'fa-key',               // secrets
      'fa-fingerprint',       // identitas unik
  ];

  // Meta badge kecil per kartu (opsional)
  $signatureMeta = [
      ['HMAC-SHA256', 'Signed timestamps'],
      ['Header tokens', 'Rotatable secrets'],
  ];
@endphp

<section id="signature" class="section signature" aria-labelledby="signature-heading">
  <div class="container">
    <header class="signature-header">
      <h2 id="signature-heading" class="h2">
        <i
          class="fa-solid fa-fingerprint icon"
          aria-hidden="true"
          style="margin-right:.4rem;"
        ></i>
        {{ $cfg['title'] ?? __('main.signature') }}
      </h2>

      <p class="lede signature-lede">
        {{-- pakai lede dari lang kalau ada, fallback copy default --}}
        {{ $cfg['lede'] ?? 'Verify raw webhook payloads with HMAC signatures, timestamps, and strict header checks.' }}
      </p>
    </header>

    @if (!empty($cards))
      <div class="signature-grid">
        {{-- Kolom kiri: kartu-kartu fitur signature --}}
        <div class="signature-list">
          @foreach ($cards as $idx => $c)
            @php
              $title = (string) ($c['title'] ?? '');
              $desc  = (string) ($c['desc'] ?? '');
              $icon  = $signatureIcons[$idx % count($signatureIcons)] ?? 'fa-circle-check';
              $meta  = $signatureMeta[$idx] ?? [];
            @endphp

            <article class="signature-card">
              <div class="signature-card-icon">
                <span class="signature-icon-orbit" aria-hidden="true"></span>
                <div class="signature-icon-circle">
                  <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                </div>
              </div>

              <div class="signature-card-body">
                <h3 class="h3 signature-card-title">
                  {{ $title }}
                </h3>

                @if($desc !== '')
                  <p class="signature-card-desc">
                    {{ $desc }}
                  </p>
                @endif

                @if (!empty($meta))
                  <ul class="signature-meta" role="list">
                    @foreach ($meta as $chip)
                      <li class="signature-meta-chip">
                        <i
                          class="fa-solid fa-circle-check icon"
                          aria-hidden="true"
                        ></i>
                        <span>{{ $chip }}</span>
                      </li>
                    @endforeach
                  </ul>
                @endif
              </div>
            </article>
          @endforeach
        </div>

        {{-- Kolom kanan: callout + contoh header signature --}}
        <aside class="signature-aside" aria-label="Signature verification example">
          <div class="signature-aside-head">
            <span class="badge badge-soft">
              <i class="fa-solid fa-lock icon" aria-hidden="true"></i>
              <span>Signed webhooks</span>
            </span>
          </div>

          <p class="signature-aside-text">
            {{ $cfg['compare'] ?? 'Compare the signature your provider sends with what you compute from the raw request body, shared secret, and timestamp — in constant time..' }}
          </p>

          <pre class="signature-aside-code"><code>POST /api/webhooks/midtrans
X-Signature: t=1715151515,v1=abc123...

<body>
  {"event":"payment.completed","amount":250000}
</body></code></pre>

          <p class="signature-aside-foot muted">
            <i class="fa-solid fa-shield-check icon" aria-hidden="true"></i>
            <span>
              {{ $cfg['reject'] ?? 'Reject mismatched signatures and stale timestamps automatically.' }}
            </span>
          </p>
        </aside>
      </div>
    @endif
  </div>
</section>
