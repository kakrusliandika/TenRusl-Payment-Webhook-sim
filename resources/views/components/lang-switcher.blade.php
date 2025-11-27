@props([
  'locales' => null,       // e.g. ['en','id','hi']; default dari config
  'param' => 'hl',         // query parameter untuk bahasa
  'label' => 'Language',   // teks label aksesibel
])

@php
  $supported = $locales ?: config('app.supported_locales', ['en']);
  $current = request($param, app()->getLocale());
@endphp

<form action="{{ url()->current() }}" method="get" class="cluster" aria-label="{{ $label }}">
  <label class="visually-hidden" for="lang-chooser">{{ $label }}</label>
  <select id="lang-chooser" name="{{ $param }}" class="select" onchange="this.form.submit()">
    @foreach($supported as $lc)
      <option value="{{ $lc }}" @selected($current === $lc)>{{ strtoupper($lc) }}</option>
    @endforeach
  </select>

  {{-- Pertahankan query lain selain param bahasa --}}
  @foreach(request()->except($param) as $k => $v)
    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
  @endforeach
</form>
