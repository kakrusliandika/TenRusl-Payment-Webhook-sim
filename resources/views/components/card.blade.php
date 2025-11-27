@props([
  'as' => 'article',  // 'div' | 'article' | 'section'
  'title' => null,
  'subtitle' => null,
])

@php
  $tag = in_array($as, ['div','article','section']) ? $as : 'article';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'card']) }}>
  @if($title || $subtitle)
    <header class="mb-2">
      @if($title)<h3 class="h3 m-0">{{ $title }}</h3>@endif
      @if($subtitle)<p class="muted m-0">{{ $subtitle }}</p>@endif
    </header>
  @endif

  <div class="content">
    {{ $slot }}
  </div>

  @isset($footer)
    <footer class="mt-2">
      {{ $footer }}
    </footer>
  @endisset
</{{ $tag }}>
