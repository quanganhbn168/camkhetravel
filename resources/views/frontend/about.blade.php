@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/about.scss')
@endpush

@section('content')
<div data-system-page="about" class="about-summary">
    @if ($pageBannerUrl)
        <section class="resource-archive-hero" aria-label="{{ $page['title'] }}">
            <img class="resource-archive-hero__image" data-page-banner-image src="{{ $pageBannerUrl }}" alt="{{ $page['title'] }}">
            <div class="resource-archive-hero__overlay"></div>
        </section>
    @endif
    <section class="about-page-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="{{ $about['image_url'] ? 'col-lg-7' : 'col-lg-10' }}">
                    <h1>{{ $about['title'] }}</h1>
                    @if ($about['intro'])
                        <p class="about-page-hero__intro mb-0">{{ $about['intro'] }}</p>
                    @endif
                </div>
                @if ($about['image_url'])
                    <div class="col-lg-5 about-page-hero__media">
                        <img src="{{ $about['image_url'] }}" alt="{{ $about['title'] }}" fetchpriority="high">
                    </div>
                @endif
            </div>
        </div>
    </section>
    @if (filled(trim(strip_tags($about['story']))))
        <section class="section-space">
            <div class="container">
                <div class="row g-4 g-lg-5">
                    <div class="col-lg-4">
                        <h2 class="display-title">{{ $about['story_title'] }}</h2>
                        @if ($about['services_link_label'])
                            <a class="section-link" href="{{ route('services.index') }}">{{ $about['services_link_label'] }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                        @endif
                    </div>
                    <div class="col-lg-8">
                        <div class="article-prose about-summary__fields">{!! $about['story'] !!}</div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    @if ($about['mission'] || $about['vision'] || $about['core_values'])
        <section class="section-space bg-light">
            <div class="container">
                @if ($about['principles_title'])
                    <h2 class="display-title mb-4">{{ $about['principles_title'] }}</h2>
                @endif
                <div class="row g-4">
                    @if ($about['vision'])
                        <div class="col-md-6"><article class="about-summary__principle h-100">
                            <h3>Tầm nhìn</h3><p class="mb-0">{{ $about['vision'] }}</p>
                        </article></div>
                    @endif
                    @if ($about['mission'])
                        <div class="col-md-6"><article class="about-summary__principle h-100">
                            <h3>Sứ mệnh</h3><p class="mb-0">{{ $about['mission'] }}</p>
                        </article></div>
                    @endif
                </div>
                @if (filled(trim(strip_tags($about['core_values']))))
                    <div class="about-summary__values mt-4">
                        <h3>Giá trị cốt lõi</h3>
                        <div class="article-prose">{!! $about['core_values'] !!}</div>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
@endsection
