<footer class="app-footer">
  {{-- Kiri: copy + status --}}
  <div class="left">
    <span class="muted">
      © {{ date('Y') }} {{ config('app.name', 'TenRusl') }}
    </span>
    <span class="dot">•</span>
    <span class="badge">
      <i class="fa-solid fa-code-branch" aria-hidden="true"></i>
      <span>@lang('main.build'): {{ config('app.env') }}</span>
    </span>
  </div>

  <div class="right">
    <a class="icon-btn" href="{{ url('/terms') }}">
      <i class="fa-solid fa-scale-balanced icon" aria-hidden="true"></i>
      <span>@lang('main.terms')</span>
    </a>
    <a class="icon-btn" href="{{ url('/privacy') }}">
      <i class="fa-solid fa-shield icon" aria-hidden="true"></i>
      <span>@lang('main.privacy')</span>
    </a>
    <a class="icon-btn" href="{{ url('/cookies') }}">
      <i class="fa-solid fa-cookie-bite icon" aria-hidden="true"></i>
      <span>@lang('main.cookies')</span>
    </a>
    <a class="icon-btn"
      href="https://github.com/kakrusliandika/TenRusl-Payment-Webhook-sim"
      target="_blank"
      rel="noopener noreferrer"
      aria-label="@lang('main.github')"
    >
      <i class="fa-brands fa-github icon" aria-hidden="true"></i>
      <span>Github</span>
    </a>
  </div>
</footer>
