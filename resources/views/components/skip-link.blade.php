{{-- Reusable Skip to Content link (place near top of <body>) --}}
@props([
  'target' => '#main-content',
  'text'   => __('Skip to content'),
])

<a href="{{ $target }}" class="skip-link">{{ $text }}</a>
