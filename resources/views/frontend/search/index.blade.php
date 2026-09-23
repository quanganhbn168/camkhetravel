@extends('layouts.master')

@push('styles')
    @vite('resources/css/pages/search.css')
@endpush

@section('content')
    <section class="py-5 bg-light">
        <div class="container">
            <h1 class="display-title text-uppercase">Tìm kiếm</h1>
            <p class="lead">Tra cứu dịch vụ và bài viết phù hợp với nhu cầu của anh/chị.</p>

            <form class="flex-column d-flex gap-3 mt-4" action="{{ route('search') }}" method="GET" role="search">
                <label class="visually-hidden" for="search-page-query">Từ khóa tìm kiếm</label>
                <div class="position-relative search-field">
                    <svg class="position-absolute search-field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></svg>
                    <input class="form-control" id="search-page-query" name="q" type="search" value="{{ $keyword }}" placeholder="Nhập dịch vụ hoặc chủ đề cần tìm" maxlength="100" autofocus>
                </div>
                <button class="btn btn-primary flex-shrink-0" type="submit">Tìm kiếm <span aria-hidden="true">→</span></button>
            </form>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            @if ($keyword === '')
                <div class="text-center empty-state">
                    <p class="h5">Nhập từ khóa để bắt đầu tìm kiếm.</p>
                    <p class="mx-auto text-body mb-0">Ví dụ: phim doanh nghiệp, quay sự kiện, profile hoặc website.</p>
                </div>
            @else
                <div class="mb-4">
                    <p class="text-body">Kết quả cho <strong class="fw-semibold text-body">“{{ $keyword }}”</strong>: {{ $services->total() + $posts->total() }} nội dung.</p>
                </div>

                <section class="mb-5" aria-labelledby="search-services-title">
                    <div class="flex-column d-flex gap-2 mb-4">
                        <h2 class="display-title text-uppercase h2" id="search-services-title">Dịch vụ phù hợp</h2>
                        <p class="text-body">{{ $services->total() }} kết quả</p>
                    </div>

                    @if ($services->isNotEmpty())
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                            @foreach ($services as $service)
                                <div class="col">@include('frontend.partials.service-card')</div>
                            @endforeach
                        </div>
                        <div class="mt-4">{{ $services->onEachSide(1)->links('frontend.partials.pagination') }}</div>
                    @else
                        <p class="empty-state w-100">Chưa tìm thấy dịch vụ phù hợp với từ khóa này.</p>
                    @endif
                </section>

                <section aria-labelledby="search-posts-title">
                    <div class="flex-column d-flex gap-2 mb-4">
                        <h2 class="display-title text-uppercase h2" id="search-posts-title">Bài viết liên quan</h2>
                        <p class="text-body">{{ $posts->total() }} kết quả</p>
                    </div>

                    @if ($posts->isNotEmpty())
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                            @foreach ($posts as $post)
                                <div class="col">@include('frontend.partials.post-card')</div>
                            @endforeach
                        </div>
                        <div class="mt-4">{{ $posts->onEachSide(1)->links('frontend.partials.pagination') }}</div>
                    @else
                        <p class="empty-state w-100">Chưa tìm thấy bài viết liên quan với từ khóa này.</p>
                    @endif
                </section>
            @endif
        </div>
    </section>
@endsection
