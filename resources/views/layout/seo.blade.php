{{-- Usage:
  @include('layout.seo', [
    'title'       => 'Custom Page Title',
    'description' => 'Custom description...',
    'image'       => asset('og.png'),
    'image_alt'   => 'Custom OG image alt text',
    'canonical'   => url()->current(),
    'robots'      => 'index,follow', // atau 'noindex,nofollow' per halaman
    // optional: 'jsonld' => [ ...schema.org object... ],
  ])
--}}

@php
  $seoTitle       = $title       ?? null;
  $seoDescription = $description ?? null;
  $seoImage       = $image       ?? null;
  $seoImageAlt    = $image_alt   ?? null;
  $seoCanonical   = $canonical   ?? null;
  $seoRobots      = $robots      ?? null;
@endphp

@if($seoTitle)        @section('title', $seoTitle) @endif
@if($seoDescription)  @section('meta_description', $seoDescription) @endif
@if($seoImage)        @section('meta_image', $seoImage) @endif
@if($seoImageAlt)     @section('meta_image_alt', $seoImageAlt) @endif
@if($seoCanonical)    @section('canonical', $seoCanonical) @endif
@if($seoRobots)       @section('meta_robots', $seoRobots) @endif

@if(!empty($jsonld))
  @push('jsonld')
    <script type="application/ld+json">
      {!! json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
  @endpush
@endif
