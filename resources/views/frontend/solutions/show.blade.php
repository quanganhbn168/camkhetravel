@extends('layouts.master')

@section('content')
    <section class="resource-archive-hero">
        @if ($solution->banner_url)<img class="resource-archive-hero__image" src="{{ $solution->banner_url }}" alt="{{ $solution->title }}">@endif
        <div class="resource-archive-hero__overlay"></div>
        <div class="container resource-archive-hero__content">
            <nav aria-label="Breadcrumb" class="mb-4"><a class="link-light" href="{{ route('home') }}">Trang chủ</a> / <a class="link-light" href="{{ route('solutions.index') }}">Giải pháp</a></nav>
            <h1 class="display-title text-white">{{ $solution->title }}</h1>
            @if ($solution->excerpt)<p class="lead mb-0">{{ $solution->excerpt }}</p>@endif
        </div>
    </section>
    <section class="section-space">
        <div class="container"><div class="row justify-content-center"><div class="col-lg-9">
            @if ($solution->image_url)<img class="img-fluid rounded mb-4 w-100" src="{{ $solution->image_url }}" alt="{{ $solution->title }}" loading="lazy">@endif
            <div class="article-prose">{!! $bodyHtml !!}</div>
            <a class="btn btn-primary mt-4" href="{{ route('contact') }}">Trao đổi về công trình <i class="fa-solid fa-arrow-right ms-2" aria-hidden="true"></i></a>
        </div></div></div>
    </section>
@endsection
