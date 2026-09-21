@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/posts-index.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="position-relative overflow-hidden site-posts-index__section-1">
        @if (($heroImageUrl ?: $defaultBannerUrl))
            <img class="position-absolute h-100 w-100 object-fit-cover site-posts-index__media-2" src="{{ ($heroImageUrl ?: $defaultBannerUrl) }}" alt="" aria-hidden="true">
        @endif
        <div class="position-absolute site-posts-index__div-3"></div>
        <div class="site-container w-100 mx-auto site-posts-index__div-4">
            <p class="site-posts-index__copy-5"><a class="site-posts-index__action-6" href="{{ LocalizedUrl::route('home') }}">{{ __('site.home') }}</a><span class="site-posts-index__copy-7">›</span><span>{{ __('site.news') }}</span></p>
            <h1 class="site-posts-index__heading-8">{{ $activeCategory?->name ?? 'Tin tức' }}</h1>
        </div>
    </section>

    <section class="section-space">
        <div class="site-container w-100 mx-auto align-items-start site-posts-index__div-9">
            @include('frontend.partials.news-sidebar')

            <div>
                <div class="flex-column justify-content-between site-posts-index__div-10">
                    <h2 class="display-title site-posts-index__heading-11">{{ $activeCategory?->name ?? 'Tất cả tin tức' }}</h2>
                    <form action="{{ $listingUrl }}" method="get">
                        <label class="d-flex align-items-center fw-medium site-posts-index__element-12" for="news-sort">
                            <span>Sắp xếp</span>
                            <select class="fw-semibold site-posts-index__element-13" id="news-sort" name="sort" onchange="this.form.submit()">
                                @foreach ($sortOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button class="site-posts-index__action-14" type="submit">Áp dụng sắp xếp</button>
                    </form>
                </div>
                <div class="site-posts-index__div-15">
                    @forelse ($posts as $post)
                        @include('frontend.partials.post-card')
                    @empty
                        <p class="site-posts-index__copy-16">Chưa có bài viết trong chuyên mục này.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @if ($posts->hasPages())
            <div class="site-container w-100 mx-auto site-posts-index__div-17">{{ $posts->onEachSide(1)->links('frontend.partials.pagination') }}</div>
        @endif
    </section>
@endsection
