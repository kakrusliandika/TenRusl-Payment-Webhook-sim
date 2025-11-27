@props([
  // items: [['id'=>'overview','label'=>'Overview','active'=>true,'content'=>'<p>..</p>'], ...]
  'items' => [],
  'id' => null,
])

@php
  $uid = $id ?: 'tabs-'.uniqid();
@endphp

<div class="tabs" data-tabs id="{{ $uid }}">
  <div class="cluster" role="tablist" aria-label="@lang('main.features')">
    @foreach($items as $i => $tab)
      @php
        $tid = $uid.'-tab-'.$tab['id'];
        $pid = $uid.'-panel-'.$tab['id'];
        $selected = !empty($tab['active']);
      @endphp
      <button
        type="button"
        class="tab"
        id="{{ $tid }}"
        role="tab"
        aria-controls="{{ $pid }}"
        aria-selected="{{ $selected ? 'true' : 'false' }}"
        tabindex="{{ $selected ? '0' : '-1' }}"
      >
        {{ $tab['label'] }}
      </button>
    @endforeach
  </div>

  @foreach($items as $i => $tab)
    @php
      $tid = $uid.'-tab-'.$tab['id'];
      $pid = $uid.'-panel-'.$tab['id'];
      $selected = !empty($tab['active']);
    @endphp
    <section
      id="{{ $pid }}"
      role="tabpanel"
      aria-labelledby="{{ $tid }}"
      @unless($selected) hidden @endunless
      class="mt-4"
    >
      {!! $tab['content'] ?? '' !!}
    </section>
  @endforeach
</div>
