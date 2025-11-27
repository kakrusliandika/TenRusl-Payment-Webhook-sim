{{-- resources/views/pages/_partials/card.blade.php --}}
@props([
  'endpoint' => [
    'method' => 'POST',
    'path'   => '/',
    'desc'   => null,
  ],
])

@php
  $method = strtoupper($endpoint['method'] ?? '');
  $path   = $endpoint['path']   ?? '';
  $desc   = $endpoint['desc']   ?? null;
@endphp

<x-card>
  <x-slot name="title">
    <span class="cluster">
      @if($method)
        <span class="badge">{{ $method }}</span>
      @endif

      @if($path)
        <code>{{ $path }}</code>
      @endif
    </span>
  </x-slot>

  @if($desc)
    <p class="muted m-0">
      {{ $desc }}
    </p>
  @endif
</x-card>
