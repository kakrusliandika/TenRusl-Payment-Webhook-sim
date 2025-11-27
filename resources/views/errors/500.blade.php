{{-- 500 — Internal Server Error (graceful, accessible) --}}
@extends('layout.app')

@include('layout.seo', [
  'title'       => '500 — '.__('main.site_name'),
  'description' => __('pages.server_error'),
  'robots'      => 'noindex,follow',
])

@section('content')
<main class="section" aria-labelledby="e500-title" aria-describedby="e500-desc">
  <div class="container">
    <section class="card" role="alert" aria-live="assertive">
      <header class="cluster">
        <x-badge variant="danger">500</x-badge>
        <h1 id="e500-title" class="h1 m-0">{{ __('pages.server_error') }}</h1>
      </header>

      <p id="e500-desc" class="lede">
        {{ __('main.error_generic_message') ?? 'Something went wrong on our side. Please try again in a moment.' }}
      </p>

      <div class="cluster mt-3">
        <x-button as="a" href="{{ url()->previous() ?: url('/') }}" iconLeft="arrow-left">
          {{ __('main.go_back') ?? 'Go back' }}
        </x-button>
        <x-button as="a" href="{{ url('/') }}" variant="ghost" iconLeft="home">
          {{ __('main.nav_home') ?? 'Home' }}
        </x-button>
        @if(config('app.status_url'))
          <x-button as="a" href="{{ config('app.status_url') }}" target="_blank" rel="noopener" variant="ghost" iconRight="arrow-up-right">
            {{ __('main.status_page') ?? 'Status page' }}
          </x-button>
        @endif
      </div>

      <p class="muted mt-2">
        {{ __('main.try_again_hint') ?? 'If the issue persists, please contact support and include the time of the error.' }}
      </p>
    </section>
  </div>
</main>
@endsection
