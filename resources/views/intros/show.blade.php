@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/intros-show.css')
@endpush
@section('title', $seoTitle)
@section('meta_description'){{ $seoDescription }}@endsection
@section('canonical', $intro->url)
@section('og_title', $seoTitle)
@section('og_description'){{ $seoDescription }}@endsection
@section('og_type', 'article')
@section('seo_image'){{ $intro->image_url }}@endsection
@section('content')
<article class="mx-auto site-intros-show__article-1" style="max-width: 56rem">
    <nav aria-label="Đường dẫn" class="site-intros-show__nav-2"><a href="{{ route('home') }}">Trang chủ</a> / <a href="{{ route('about') }}">Giới thiệu</a></nav>
    <h1 class="fw-bold site-intros-show__heading-3">{{ $intro->title }}</h1>
    @if($intro->summary)<p class="site-intros-show__copy-4">{{ $intro->summary }}</p>@endif
    @if($intro->image_url)<img src="{{ $intro->image_url }}" alt="{{ $intro->title }}" class="w-100 site-intros-show__media-5">@endif
    <div class="article-prose site-intros-show__div-6">{!! $content !!}</div>
</article>
@endsection
