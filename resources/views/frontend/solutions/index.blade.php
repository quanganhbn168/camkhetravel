@extends('layouts.master')

@section('content')
    <section class="resource-archive-hero" data-system-page="solutions">
        @if ($pageBannerUrl)
            <img class="resource-archive-hero__image" data-page-banner-image src="{{ $pageBannerUrl }}" alt="{{ $page['title'] }}">
        @endif
        <div class="resource-archive-hero__overlay"></div>
        <div class="container resource-archive-hero__content">
            <nav aria-label="Breadcrumb">
                <ol class="d-flex flex-wrap align-items-center list-unstyled gap-2 small mb-4">
                    <li><a class="link-light" href="{{ route('home') }}">Trang chủ</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-white-50" aria-current="page">{{ $page['title'] }}</li>
                </ol>
            </nav>
            <h1 class="display-title text-white">{{ $page['title'] }}</h1>
            <p class="lead mb-0">{{ $intro }}</p>
        </div>
    </section>
@endsection
