@props([
  'variant' => 'neutral', // 'neutral' | 'success' | 'warning' | 'danger' | 'info'
  'icon' => null,
])

@php
  $classes = 'badge';
  // (opsional) tambahkan kelas tema khusus jika kamu menambah di CSS
  // $classes .= ' badge-'.$variant;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
  @if($icon)<x-icon :name="$icon" class="mr-1" aria-hidden="true" />@endif
  <span>{{ $slot }}</span>
</span>
