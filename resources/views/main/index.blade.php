@extends('layout.app')

@include('layout.seo', [
  'title'       => __('main.home.title').' | '.__('main.site_name'),
  'description' => __('main.home.description'),
  'image'       => asset('og.png'),
  'canonical'   => url()->current(),
])

@section('content')
  @include('main.partials._hero')
  @include('main.partials._providers')
  @include('main.partials._features')
  @include('main.partials._endpoints')
  @include('main.partials._signature')
  @include('main.partials._tooling')
@endsection
