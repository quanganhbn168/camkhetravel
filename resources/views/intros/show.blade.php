@extends('layouts.master')


@section('title', $seoTitle)
@section('meta_description'){{ $seoDescription }}@endsection
@section('canonical', $intro->url)
@section('og_title', $seoTitle)
@section('og_description'){{ $seoDescription }}@endsection
@section('og_type', 'article')
@section('seo_image'){{ $intro->image_url }}@endsection
@section('content')
<article class="container py-5" style="max-width: 56rem">
    <nav aria-label="Đường dẫn" class="mb-4 small"><a href="{{ route('home') }}">Trang chủ</a> / <a href="{{ route('about') }}">Giới thiệu</a></nav>
    <h1 class="fw-bold display-title">{{ $intro->title }}</h1>
    @if($intro->summary)<p class="lead">{{ $intro->summary }}</p>@endif
    @if($intro->image_url)<img src="{{ $intro->image_url }}" alt="{{ $intro->title }}" class="w-100 img-fluid rounded my-4">@endif
    <div class="article-prose mt-4">{!! $content !!}</div>
</article>
@endsection
