@props([
  'title' => null,
  'open' => false,
  'id' => null,
])

<details class="accordion" @if($open) open @endif id="{{ $id }}">
  <summary class="summary">
    <strong>{{ $title ?? 'Details' }}</strong>
  </summary>
  <div class="content p-4">
    {{ $slot }}
  </div>
</details>
