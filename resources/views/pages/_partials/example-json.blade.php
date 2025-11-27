{{-- resources/views/pages/_partials/example-json.blade.php --}}
@props(['payload' => []])

<section class="section" aria-labelledby="example-json-heading">
  <h2 id="example-json-heading" class="h2">
    {{ __('pages.example_payload_title') }}
  </h2>

  <pre class="code mt-2"><code class="language-json">
@json($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
  </code></pre>
</section>
