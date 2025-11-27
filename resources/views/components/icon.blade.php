{{-- Simple sprite-based icon. If no label is supplied, it’s decorative. --}}
@props([
  'name',
  'label' => null,
])

@php $sprite = asset('icon.svg'); @endphp

<svg
  {{ $attributes->merge(['class' => 'icon']) }}
  @if($label)
    role="img" aria-label="{{ $label }}"
  @else
    aria-hidden="true"
  @endif
  focusable="false"
>
  <use href="{{ $sprite }}#{{ e($name) }}"></use>
</svg>
