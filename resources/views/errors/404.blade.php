{{-- 404 — Not Found (custom, helpful, fast) --}}
@extends('layout.app')

{{-- Minimal SEO: biarkan status 404 yang mengontrol indexing; "noindex" tambahan sebagai kehati-hatian --}}
@include('layout.seo', [
  'title'       => '404 — '.__('main.site_name'),
  'description' => __('pages.not_found'),
  'robots'      => 'noindex,follow',
])

@section('content')
<main class="section" aria-labelledby="e404-title" aria-describedby="e404-desc">
  <div class="container">
    <div class="stack">
      <x-badge variant="warning">404</x-badge>

      <h1 id="e404-title" class="h1 m-0">{{ __('pages.not_found') }}</h1>
      <p id="e404-desc" class="lede">
        {{ __('pages.no_results') }} — {{ __('main.providers.description') ?? 'Try browsing providers or go back home.' }}
      </p>

      {{-- Search the providers catalog (client-side filter will still help) --}}
      <form class="mt-2" role="search" action="{{ url('/providers') }}" method="get" aria-label="{{ __('pages.search_providers') }}">
        <label class="sr-only" for="e404-search">{{ __('pages.search_providers') }}</label>
        <input id="e404-search" name="q" type="search" class="input w-full"
               placeholder="{{ __('pages.search_providers') }}" autocomplete="off" />
      </form>

      <div class="cluster mt-3">
        <x-button as="a" href="{{ url('/') }}" iconLeft="home">{{ __('main.nav_home') ?? 'Home' }}</x-button>
        <x-button as="a" href="{{ url('/providers') }}" variant="ghost" iconRight="arrow-right">
          {{ __('main.providers.title') ?? 'Browse providers' }}
        </x-button>
        <x-button as="a"
          href="{{ (config('app.repo_url') ? rtrim(config('app.repo_url'),'/') : 'https://github.com/yourname/yourrepo') .
                  '/issues/new?title=404%20on%20' . urlencode(request()->path()) }}"
          target="_blank" rel="noopener" variant="ghost" iconRight="arrow-up-right">
          {{ __('main.report_issue') ?? 'Report issue' }}
        </x-button>
      </div>

      <p class="muted">
        {{ __('main.tips_check_url') ?? 'Tip: Check the URL for typos or outdated links.' }}
      </p>
    </div>
  </div>
</main>
@endsection
