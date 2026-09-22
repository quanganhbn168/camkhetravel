@php
    $archiveBannerUrl = $pageBannerUrl ?? null;
@endphp

<section class="resource-archive-hero" @if ($pageKey ?? null) data-system-page="{{ $pageKey }}" @endif>
    @if ($archiveBannerUrl)
        <img class="resource-archive-hero__image" data-page-banner-image src="{{ $archiveBannerUrl }}" alt="" aria-hidden="true">
    @endif
    <div class="resource-archive-hero__overlay"></div>
    <div class="container resource-archive-hero__content">
        <nav aria-label="Breadcrumb">
            <ol class="d-flex flex-wrap align-items-center list-unstyled gap-2 small mb-4">
                <li><a class="link-light" href="{{ route('home') }}">Trang chủ</a></li>
                <li aria-hidden="true">/</li>
                @if ($activeCategory)
                    <li><a class="link-light" href="{{ route($resourceIndexRoute) }}">{{ $resourceName }}</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-white-50" aria-current="page">{{ $activeCategory->name }}</li>
                @else
                    <li class="text-white-50" aria-current="page">{{ $resourceName }}</li>
                @endif
            </ol>
        </nav>
        <h1 class="display-title text-white">{{ $pageTitle }}</h1>
        <p class="lead mb-0">{{ $pageDescription }}</p>
    </div>
</section>
