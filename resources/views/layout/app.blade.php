<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    @include('layout.meta')
    {{-- Load all CSS/JS only through Vite in head --}}
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body>
    {{-- Full sticky header (brand + nav + language + theme) --}}
    @include('layout.header')

    <main id="main-content" tabindex="-1">
      @yield('content')
    </main>

    @includeIf('layout.footer')
  </body>
</html>
