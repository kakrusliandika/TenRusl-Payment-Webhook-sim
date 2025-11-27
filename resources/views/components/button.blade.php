{{-- Accessible button/link with variants and optional icons (no nested components) --}}
@props([
  'as'        => 'button',     // 'button' | 'a'
  'href'      => null,         // URL when as='a'
  'variant'   => 'primary',    // 'primary' | 'ghost' | 'outline'
  'type'      => 'button',     // button type
  'busy'      => false,        // sets aria-busy
  'disabled'  => false,        // disables or aria-disables
  'icon'      => null,         // left icon id from public/icons.svg (e.g. "book-open")
  'iconRight' => null,         // right icon id
])

@php
  // Base + variants (merge with incoming $attributes later)
  $base = 'inline-flex items-center justify-center gap-2 px-4 py-2 rounded-[var(--btn-radius)] font-semibold
           transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--ring)]
           disabled:opacity-50 disabled:pointer-events-none select-none';

  $variantClasses = match ($variant) {
    'ghost'   => 'bg-transparent text-[var(--text-1)] hover:bg-[var(--surface)] border border-transparent',
    'outline' => 'bg-transparent text-[var(--text-1)] border border-[var(--border-1)] hover:bg-[var(--surface-2)]',
    default   => 'bg-[var(--accent)] text-[var(--accent-contrast)] shadow-[var(--btn-shadow)] hover:brightness-110',
  };

  $classes  = trim("$base $variantClasses");
  $ariaBusy = $busy ? 'true' : 'false';

  $sprite = asset('icons.svg'); // single request sprite
@endphp

@if($as === 'a' || $href)
  <a
    href="{{ $href ?? '#' }}"
    {{ $attributes->merge(['class' => $classes])->merge([
        'aria-busy'     => $ariaBusy,
        // For links, reflect disabled state via aria-disabled and keyboard trap
        'aria-disabled' => $disabled ? 'true' : null,
        'tabindex'      => $disabled ? '-1' : null,
    ]) }}
  >
    @if($icon)
      <svg class="icon shrink-0" aria-hidden="true" focusable="false">
        <use href="{{ $sprite }}#{{ $icon }}"></use>
      </svg>
    @endif

    <span class="truncate">{{ $slot }}</span>

    @if($iconRight)
      <svg class="icon shrink-0" aria-hidden="true" focusable="false">
        <use href="{{ $sprite }}#{{ $iconRight }}"></use>
      </svg>
    @endif
  </a>
@else
  <button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes])->merge([
        'aria-busy' => $ariaBusy,
        'disabled'  => $disabled ? true : null,
    ]) }}
  >
    @if($icon)
      <svg class="icon shrink-0" aria-hidden="true" focusable="false">
        <use href="{{ $sprite }}#{{ $icon }}"></use>
      </svg>
    @endif

    <span class="truncate">{{ $slot }}</span>

    @if($iconRight)
      <svg class="icon shrink-0" aria-hidden="true" focusable="false">
        <use href="{{ $sprite }}#{{ $iconRight }}"></use>
      </svg>
    @endif
  </button>
@endif
