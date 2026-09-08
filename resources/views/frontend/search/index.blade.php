@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="border-b border-slate-200 bg-mist py-14 md:py-20">
        <div class="site-container w-full mx-auto px-4 lg:px-8 max-w-4xl">
            <h1 class="display-title text-3xl leading-tight uppercase md:text-5xl">Tìm kiếm</h1>
            <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">Tra cứu dịch vụ và bài viết phù hợp với nhu cầu của anh/chị.</p>

            <form class="mt-8 flex flex-col gap-3 sm:flex-row" action="{{ LocalizedUrl::route('search') }}" method="GET" role="search">
                <label class="sr-only" for="search-page-query">Từ khóa tìm kiếm</label>
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></svg>
                    <input class="min-h-13 w-full rounded-full border border-slate-200 bg-white py-3 pl-12 pr-5 text-base text-ink outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4" style="--tw-ring-color: color-mix(in srgb, var(--site-color-primary) 16%, transparent)" id="search-page-query" name="q" type="search" value="{{ $keyword }}" placeholder="Nhập dịch vụ hoặc chủ đề cần tìm" maxlength="100" autofocus>
                </div>
                <button class="button-primary shrink-0" type="submit">Tìm kiếm <span aria-hidden="true">→</span></button>
            </form>
        </div>
    </section>

    <section class="section-space bg-white">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
            @if ($keyword === '')
                <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-mist px-6 py-12 text-center">
                    <p class="font-display text-2xl text-ink">Nhập từ khóa để bắt đầu tìm kiếm.</p>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-500">Ví dụ: phim doanh nghiệp, quay sự kiện, profile hoặc website.</p>
                </div>
            @else
                <div class="border-b border-slate-200 pb-8">
                    <p class="text-sm leading-7 text-slate-600">Kết quả cho <strong class="font-semibold text-ink">“{{ $keyword }}”</strong>: {{ $services->total() + $posts->total() }} nội dung.</p>
                </div>

                <section class="pt-12" aria-labelledby="search-services-title">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <h2 class="display-title text-2xl leading-tight uppercase md:text-3xl" id="search-services-title">Dịch vụ phù hợp</h2>
                        <p class="text-sm text-slate-500">{{ $services->total() }} kết quả</p>
                    </div>

                    @if ($services->isNotEmpty())
                        <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($services as $service)
                                @include('frontend.partials.service-card')
                            @endforeach
                        </div>
                        <div class="mt-9">{{ $services->onEachSide(1)->links('frontend.partials.pagination') }}</div>
                    @else
                        <p class="mt-7 rounded-2xl bg-mist px-5 py-4 text-sm leading-7 text-slate-600">Chưa tìm thấy dịch vụ phù hợp với từ khóa này.</p>
                    @endif
                </section>

                <section class="mt-16 border-t border-slate-200 pt-12" aria-labelledby="search-posts-title">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <h2 class="display-title text-2xl leading-tight uppercase md:text-3xl" id="search-posts-title">Bài viết liên quan</h2>
                        <p class="text-sm text-slate-500">{{ $posts->total() }} kết quả</p>
                    </div>

                    @if ($posts->isNotEmpty())
                        <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($posts as $post)
                                @include('frontend.partials.post-card')
                            @endforeach
                        </div>
                        <div class="mt-9">{{ $posts->onEachSide(1)->links('frontend.partials.pagination') }}</div>
                    @else
                        <p class="mt-7 rounded-2xl bg-mist px-5 py-4 text-sm leading-7 text-slate-600">Chưa tìm thấy bài viết liên quan với từ khóa này.</p>
                    @endif
                </section>
            @endif
        </div>
    </section>
@endsection
