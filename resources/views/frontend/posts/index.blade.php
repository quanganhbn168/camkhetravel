@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/posts.scss')
@endpush

@section('content')
    <section class="position-relative overflow-hidden archive-hero">
        @if ($activeCategory ? ($pageBannerUrl ?? null) : ($heroImageUrl ?: $defaultBannerUrl))
            <img class="position-absolute h-100 w-100 object-fit-cover archive-hero__image" data-page-banner-image src="{{ $activeCategory ? $pageBannerUrl : ($heroImageUrl ?: $defaultBannerUrl) }}" alt="" aria-hidden="true">
        @endif
        <div class="position-absolute archive-hero__overlay"></div>
        <div class="container position-relative">
            <p class="small"><a class="link-light" href="{{ route('home') }}">Trang chủ</a><span class="mx-2">›</span><span>Tin tức</span></p>
            <h1 class="display-title text-white">{{ $activeCategory?->name ?? 'Tin tức & kiến thức' }}</h1>
        </div>
    </section>

    <section class="section-space blog-index">
        <div class="container">
            <nav class="blog-categories nav nav-pills gap-2 mb-5" aria-label="Danh mục tin tức">
                <a class="nav-link {{ ! $activeCategory ? 'active' : '' }}" href="{{ route('posts.index') }}" @if(! $activeCategory) aria-current="page" @endif>Tất cả tin tức</a>
                @foreach ($categories as $category)
                    <a class="nav-link {{ $activeCategory?->is($category) ? 'active' : '' }}" href="{{ route('posts.category', ['slug' => $category->slug]) }}" @if($activeCategory?->is($category)) aria-current="page" @endif>{{ $category->tree_label ?? $category->name }}</a>
                @endforeach
            </nav>

            <div>
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                    <h2 class="display-title h2 mb-0">{{ $activeCategory?->name ?? 'Tất cả tin tức' }}</h2>
                    <form action="{{ $listingUrl }}" method="get">
                        <label class="d-flex align-items-center fw-medium gap-2" for="news-sort">
                            <span class="text-nowrap">Sắp xếp</span>
                            <select class="fw-semibold form-select" id="news-sort" name="sort" onchange="this.form.submit()">
                                @foreach ($sortOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button class="visually-hidden" type="submit">Áp dụng sắp xếp</button>
                    </form>
                </div>
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    @forelse ($posts as $post)
                        <div class="col">@include('frontend.partials.post-card', ['showExcerpt' => true])</div>
                    @empty
                        <p class="empty-state w-100">Chưa có bài viết trong chuyên mục này.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @if ($posts->hasPages())
            <div class="container mt-4">{{ $posts->onEachSide(1)->links('frontend.partials.pagination') }}</div>
        @endif
    </section>
    @include('frontend.partials.category-content')
@endsection
