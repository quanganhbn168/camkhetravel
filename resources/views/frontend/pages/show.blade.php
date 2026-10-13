@extends('layouts.master')

@section('body_class', 'custom-page')

@section('content')
<article class="section-space">
    <div class="container" style="max-width: 56rem">
        <nav aria-label="Đường dẫn" class="mb-4 small"><a href="{{ route('home') }}">Trang chủ</a> / <span aria-current="page">{{ $page->title }}</span></nav>
        <h1 class="display-title">{{ $page->title }}</h1>
        @if ($page->excerpt)<p class="lead">{{ $page->excerpt }}</p>@endif
        @if ($page->image_url)<img src="{{ $page->image_url }}" alt="{{ $page->title }}" class="w-100 img-fluid rounded my-4">@endif
        <div class="article-prose mt-4">{!! $content !!}</div>
    </div>
</article>
@endsection
