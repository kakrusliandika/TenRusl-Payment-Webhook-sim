{{-- resources/views/pages/_partials/signature-note.blade.php --}}
@props([
  'notes' => [],
  'docs'  => null,
])

@php
  // Normalisasi: pastikan $notes selalu berupa array sebelum di-loop
  if (is_string($notes) && $notes !== '') {
      $notesList = [$notes];
  } elseif (is_array($notes)) {
      $notesList = $notes;
  } else {
      $notesList = [];
  }
@endphp

<section class="section" aria-labelledby="sig-notes-heading">
  <h2 id="sig-notes-heading" class="h2">
    {{ __('pages.signature_notes_title') }}
  </h2>

  @if(!empty($notesList))
    <ul class="mt-2">
      @foreach($notesList as $n)
        <li>{{ $n }}</li>
      @endforeach
    </ul>
  @endif

  @if($docs)
    <p class="mt-2">
      <a
        class="link"
        href="{{ $docs }}"
        target="_blank"
        rel="noopener"
      >
        {{ __('pages.view_docs') }}
      </a>
    </p>
  @endif
</section>
