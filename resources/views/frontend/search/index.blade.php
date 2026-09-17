@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/search-index.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="dv-search-index__section-1">
        <div class="site-container w-100 mx-auto dv-search-index__div-2">
            <h1 class="display-title text-uppercase dv-search-index__heading-3">Tìm kiếm</h1>
            <p class="dv-search-index__copy-4">Tra cứu dịch vụ và bài viết phù hợp với nhu cầu của anh/chị.</p>

            <form class="flex-column dv-search-index__element-5" action="{{ LocalizedUrl::route('search') }}" method="GET" role="search">
                <label class="dv-search-index__element-6" for="search-page-query">Từ khóa tìm kiếm</label>
                <div class="position-relative dv-search-index__div-7">
                    <svg class="position-absolute dv-search-index__media-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></svg>
                    <input class="w-100 dv-search-index__element-9" style="--dv-ui-ring-color: color-mix(in srgb, var(--site-color-primary) 16%, transparent)" id="search-page-query" name="q" type="search" value="{{ $keyword }}" placeholder="Nhập dịch vụ hoặc chủ đề cần tìm" maxlength="100" autofocus>
                </div>
                <button class="btn btn-primary button-primary flex-shrink-0" type="submit">Tìm kiếm <span aria-hidden="true">→</span></button>
            </form>
        </div>
    </section>

    <section class="section-space dv-search-index__section-11">
        <div class="site-container w-100 mx-auto dv-search-index__div-12">
            @if ($keyword === '')
                <div class="text-center dv-search-index__div-13">
                    <p class="dv-search-index__copy-14">Nhập từ khóa để bắt đầu tìm kiếm.</p>
                    <p class="mx-auto dv-search-index__copy-15">Ví dụ: phim doanh nghiệp, quay sự kiện, profile hoặc website.</p>
                </div>
            @else
                <div class="dv-search-index__div-16">
                    <p class="dv-search-index__copy-17">Kết quả cho <strong class="fw-semibold dv-search-index__element-18">“{{ $keyword }}”</strong>: {{ $services->total() + $posts->total() }} nội dung.</p>
                </div>

                <section class="dv-search-index__section-19" aria-labelledby="search-services-title">
                    <div class="flex-column dv-search-index__div-20">
                        <h2 class="display-title text-uppercase dv-search-index__heading-21" id="search-services-title">Dịch vụ phù hợp</h2>
                        <p class="dv-search-index__copy-22">{{ $services->total() }} kết quả</p>
                    </div>

                    @if ($services->isNotEmpty())
                        <div class="dv-search-index__div-23">
                            @foreach ($services as $service)
                                @include('frontend.partials.service-card')
                            @endforeach
                        </div>
                        <div class="dv-search-index__div-24">{{ $services->onEachSide(1)->links('frontend.partials.pagination') }}</div>
                    @else
                        <p class="dv-search-index__copy-25">Chưa tìm thấy dịch vụ phù hợp với từ khóa này.</p>
                    @endif
                </section>

                <section class="dv-search-index__section-26" aria-labelledby="search-posts-title">
                    <div class="flex-column dv-search-index__div-20">
                        <h2 class="display-title text-uppercase dv-search-index__heading-21" id="search-posts-title">Bài viết liên quan</h2>
                        <p class="dv-search-index__copy-22">{{ $posts->total() }} kết quả</p>
                    </div>

                    @if ($posts->isNotEmpty())
                        <div class="dv-search-index__div-23">
                            @foreach ($posts as $post)
                                @include('frontend.partials.post-card')
                            @endforeach
                        </div>
                        <div class="dv-search-index__div-24">{{ $posts->onEachSide(1)->links('frontend.partials.pagination') }}</div>
                    @else
                        <p class="dv-search-index__copy-25">Chưa tìm thấy bài viết liên quan với từ khóa này.</p>
                    @endif
                </section>
            @endif
        </div>
    </section>
@endsection
