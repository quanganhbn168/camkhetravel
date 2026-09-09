@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="relative isolate overflow-hidden bg-ink py-16 text-white md:py-20">
        @if (($heroImageUrl ?: $defaultBannerUrl))
            <img class="absolute inset-0 -z-20 h-full w-full object-cover opacity-30" src="{{ ($heroImageUrl ?: $defaultBannerUrl) }}" alt="" aria-hidden="true">
        @endif
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,color-mix(in_srgb,var(--site-color-ink)_96%,transparent),color-mix(in_srgb,var(--site-color-ink)_68%,transparent))]"></div>
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
            <p class="text-sm text-slate-300"><a class="hover:text-white" href="{{ LocalizedUrl::route('home') }}">{{ __('site.home') }}</a><span class="mx-2 text-slate-500">›</span><span>{{ __('site.news') }}</span></p>
            <h1 class="font-display mt-7 text-4xl leading-tight tracking-[-0.045em] md:text-5xl">{{ $activeCategory?->name ?? 'Tin tức' }}</h1>
        </div>
    </section>

    <section class="section-space">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 grid items-start gap-10 lg:grid-cols-[17.75rem_minmax(0,1fr)] lg:gap-9">
            @include('frontend.partials.news-sidebar')

            <div>
                <div class="flex flex-col justify-between gap-3 border-b border-slate-200 pb-5 sm:flex-row sm:items-end">
                    <h2 class="display-title text-3xl leading-tight">{{ $activeCategory?->name ?? 'Tất cả tin tức' }}</h2>
                    <form action="{{ $listingUrl }}" method="get">
                        <label class="flex items-center gap-2 text-sm font-medium text-slate-500" for="news-sort">
                            <span>Sắp xếp</span>
                            <select class="min-h-10 rounded-xl border border-slate-200 bg-white px-3 pr-9 text-sm font-semibold text-ink outline-none transition focus:border-primary focus:ring-3 focus:ring-primary/15" id="news-sort" name="sort" onchange="this.form.submit()">
                                @foreach ($sortOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button class="sr-only" type="submit">Áp dụng sắp xếp</button>
                    </form>
                </div>
                <div class="mt-7 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($posts as $post)
                        @include('frontend.partials.post-card')
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-300 p-8 text-sm text-slate-500 md:col-span-2 xl:col-span-3">Chưa có bài viết trong chuyên mục này.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @if ($posts->hasPages())
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 mt-12">{{ $posts->onEachSide(1)->links('frontend.partials.pagination') }}</div>
        @endif
    </section>
@endsection
