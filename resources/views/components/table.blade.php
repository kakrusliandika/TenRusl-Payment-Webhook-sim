@props([
  'caption' => null,
  'headers' => null, // array of strings (opsional; bila null, isi thead/tbody via $slot)
  'rows'    => null, // array of arrays (opsional; dipakai jika headers juga ada)
  'zebra'   => false,
])

@php
  $classes = 'table';
  if ($zebra) $classes .= ' table-zebra';
@endphp

<table {{ $attributes->merge(['class' => $classes]) }}>
  @if($caption)<caption class="muted mb-2">{{ $caption }}</caption>@endif

  @if(is_array($headers) && is_array($rows))
    <thead>
      <tr>
        @foreach($headers as $h)
          <th scope="col">{{ $h }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
        <tr>
          @foreach($r as $cell)
            <td>{{ $cell }}</td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  @else
    {{ $slot }}
  @endif
</table>
