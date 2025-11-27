@props([
  'type' => 'status', // 'status' (polite) | 'alert' (assertive)
  'message' => null,
])

@php
  $role = $type === 'alert' ? 'alert' : 'status';
@endphp

<div {{ $attributes->merge(['class' => 'toast']) }} role="{{ $role }}" aria-live="{{ $role === 'alert' ? 'assertive' : 'polite' }}">
  <div class="cluster">
    @isset($icon)<span class="mr-2">{{ $icon }}</span>@endisset
    <span>{{ $message ?? $slot }}</span>
  </div>
</div>
