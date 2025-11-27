@extends('layout.app')

@php
  /**
   * NORMALISASI INPUT
   *
   * Bisa dipanggil dengan:
   * - view('pages.show', ['slug' => 'stripe'])
   * - view('pages.show', ['provider' => ['key' => 'stripe', ...]])
   * - route: /providers/{provider}
   */

  // Input mentah dari view/controller
  $rawProvider  = $provider       ?? null;
  $metaProvider = $provider_meta  ?? null;

  // 1) Resolve slug
  $slug = $slug
       ?? (is_array($rawProvider)  ? ($rawProvider['key'] ?? $rawProvider['slug'] ?? null) : null)
       ?? (is_string($rawProvider) ? $rawProvider : null)
       ?? (is_array($metaProvider) ? ($metaProvider['slug'] ?? null) : null)
       ?? request()->route('provider');

  $slug = (string) $slug;

  // 2) Back URL (bisa di-override dari controller)
  $backUrl = $back_url ?? route('providers.index');

  // 3) Nama provider (prioritas: array → meta → lang map → ucfirst(slug))
  if (is_array($rawProvider) && !empty($rawProvider['name'])) {
      $name = $rawProvider['name'];
  } elseif (is_array($metaProvider) && !empty($metaProvider['name'])) {
      $name = $metaProvider['name'];
  } else {
      $name = trans("main.providers.map.$slug");
      if ($name === "main.providers.map.$slug") {
          $name = ucfirst($slug);
      }
  }

  /**
   * 4) Ambil data dari lang per provider:
   *    resources/lang/en/pages/{slug}.php
   *
   * Format:
   *   'summary'         => '...',
   *   'docs'            => 'https://...',
   *   'signature_notes' => [...],
   *   'example_payload' => [...],
   *   'endpoints'       => [...],
   */

  // SUMMARY
  $summaryLang = trans("pages/{$slug}.summary");

  if ($summaryLang === "pages/{$slug}.summary") {
      // fallback ke pola lama: pages.summaries.{slug}
      $tmp = trans("pages.summaries.$slug");
      $summaryLang = $tmp !== "pages.summaries.$slug" ? $tmp : null;
  }

  $summary = $rawProvider['summary']
          ?? $summaryLang
          ?? __('pages.default_summary');

  // DOCS
  $docsLang = trans("pages/{$slug}.docs");
  if ($docsLang === "pages/{$slug}.docs") {
      $docsLang = null;
  }

  $docs = $rawProvider['docs'] ?? ($docsLang ?: null);

  // SIGNATURE NOTES
  $sigNotesLang = trans("pages/{$slug}.signature_notes");

  if (! is_array($sigNotesLang)) {
      // fallback pola lama: pages.signature_notes.{slug}
      $tmpNotes = trans("pages.signature_notes.$slug");
      $sigNotesLang = is_array($tmpNotes) ? $tmpNotes : [];
  }

  $sigNotes = $rawProvider['signature_notes'] ?? $sigNotesLang;

  // EXAMPLE PAYLOAD
  $exampleLang = trans("pages/{$slug}.example_payload");
  if (! is_array($exampleLang)) {
      $exampleLang = null;
  }

  $example = $rawProvider['example_payload']
          ?? $exampleLang
          ?? [
              'id'   => 'evt_' . now()->timestamp,
              'type' => 'payment.succeeded',
              'data' => [
                  'object' => [
                      'provider' => $slug,
                      'status'   => 'succeeded',
                  ],
              ],
          ];

  // ENDPOINTS
  $endpointsLang = trans("pages/{$slug}.endpoints");
  if (! is_array($endpointsLang)) {
      $endpointsLang = [];
  }

  $endpoints = $rawProvider['endpoints']
           ?? (!empty($endpointsLang) ? $endpointsLang : [
                [
                    'method' => 'POST',
                    'path'   => '/api/v1/payments',
                    'desc'   => __('pages.create_payment'),
                ],
                [
                    'method' => 'GET',
                    'path'   => '/api/v1/payments/{id}',
                    'desc'   => __('pages.get_payment'),
                ],
                [
                    'method' => 'POST',
                    'path'   => "/api/v1/webhooks/{$slug}",
                    'desc'   => __('pages.receive_webhook'),
                ],
            ]);

  // LOGO: provider → meta → default (img/providers/{slug}.png)
  // Standar: public/img/providers/{slug}.png
  $logoPath = null;

  // Boleh override (kalau suatu hari perlu)
  if (is_array($rawProvider) && !empty($rawProvider['logo'])) {
      $logoPath = $rawProvider['logo'];     // boleh full URL atau path lain
  } elseif (is_array($metaProvider) && !empty($metaProvider['logo'])) {
      $logoPath = $metaProvider['logo'];   // misal "img/providers/custom.png" atau asset()
  }

  // Kalau tidak ada override → pakai standar img/providers/{slug}.png
  if (! $logoPath && $slug !== '') {
      $logoPath = asset("img/providers/{$slug}.png");
  }

  // 5) Payload final untuk partials
  $providerData = [
      'key'             => $slug,
      'name'            => $name,
      'summary'         => $summary,
      'docs'            => $docs,
      'endpoints'       => $endpoints,
      'signature_notes' => $sigNotes,
      'example_payload' => $example,
      'logo'            => $logoPath,
  ];
@endphp

@include('layout.seo', [
  'title'       => $name . ' | ' . __('main.site_name'),
  'description' => $summary,
  'canonical'   => url()->current(),
])

@section('content')
<section class="section provider-page" aria-labelledby="provider-title">
  <div class="container provider-page-container">
    {{-- Back link --}}
    <nav aria-label="{{ __('pages.breadcrumb') }}" class="provider-breadcrumb mb-3">
      <a
        href="{{ $backUrl }}"
        class="btn btn-ghost btn-icon-left"
      >
        <i class="fa-solid fa-arrow-left icon" aria-hidden="true"></i>
        <span>{{ __('pages.back_to_providers') }}</span>
      </a>
    </nav>

    <div class="provider-page-body">
      {{-- Header block (logo, name, summary, docs link) --}}
      @include('pages._partials.header', ['provider' => $providerData])

      {{-- Endpoint cards --}}
      <div class="provider-endpoints grid grid-3 mt-4">
        @foreach($providerData['endpoints'] as $ep)
          @include('pages._partials.card', ['endpoint' => $ep])
        @endforeach
      </div>

      <div class="provider-detail-sections">
        {{-- Signature notes (bulleted list, a11y-friendly) --}}
        @include('pages._partials.signature-note', [
          'notes' => $providerData['signature_notes'],
          'docs'  => $providerData['docs'],
        ])

        {{-- Example JSON payload --}}
        @include('pages._partials.example-json', [
          'payload' => $providerData['example_payload'],
        ])
      </div>
    </div>
  </div>
</section>
@endsection
