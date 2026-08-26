@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => __('site.projects'),
        'resourceIndexRoute' => 'projects.index',
    ])

    @if ($activeCategory)
        <section class="resource-category-intro">
            <div class="site-shell resource-category-intro__grid">
                <div>
                    <h2 class="display-title text-3xl leading-tight md:text-4xl">Các dự án {{ mb_strtolower($activeCategory->name) }} đã triển khai.</h2>
                    <p class="mt-5 max-w-xl text-sm leading-7 text-slate-600 md:text-base md:leading-8">{{ $pageDescription }}</p>
                    <a class="button-dark mt-7" href="{{ LocalizedUrl::route('contact') }}">Trao đổi dự án <span aria-hidden="true">→</span></a>
                </div>
                <div class="resource-category-intro__visual">
                    @if ($heroImageUrl)
                        <img src="{{ $heroImageUrl }}" alt="{{ $activeCategory->name }}">
                    @else
                        <span class="image-placeholder">THT</span>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <section class="section-space bg-white">
        <div class="site-shell">
            @if ($backstageLanding)
                <div class="mb-8 flex flex-col gap-4 rounded-[1.5rem] border border-primary/20 bg-sand/50 p-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm leading-7 text-slate-600">Đang xem các dự án được gắn với <strong class="font-semibold text-ink">{{ $backstageLanding->title }}</strong>.</p>
                    </div>
                    <a class="button-dark shrink-0" href="{{ LocalizedUrl::route('projects.index') }}">Xem tất cả dự án <span aria-hidden="true">→</span></a>
                </div>
            @endif

            @include('frontend.partials.resource-filter-bar', [
                'resourceName' => __('site.projects'),
                'resourceIndexRoute' => 'projects.index',
                'categoryCountAttribute' => 'projects_count',
            ])

            <div class="mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($projects as $project)
                    @include('frontend.partials.project-card')
                @empty
                    <p class="col-span-full rounded-2xl border border-dashed border-slate-300 p-8 text-sm leading-7 text-slate-500">Chưa có dự án được xuất bản trong danh mục này.</p>
                @endforelse
            </div>

            @if ($projects->hasPages())
                <div class="mt-12">{{ $projects->onEachSide(1)->links() }}</div>
            @endif
        </div>
    </section>

    <section class="resource-archive-cta">
        @if ($heroImageUrl)<img class="resource-archive-cta__image" src="{{ $heroImageUrl }}" alt="" aria-hidden="true">@endif
        <div class="resource-archive-cta__overlay"></div>
        <div class="site-shell resource-archive-cta__content">
            <div><h2 class="max-w-2xl font-display text-3xl leading-tight tracking-[-0.045em] text-white md:text-4xl">Anh/chị có dự án cần triển khai?</h2><p class="mt-3 max-w-xl text-sm leading-7 text-white/75">Cùng trao đổi để làm rõ mục tiêu và hướng thực hiện phù hợp.</p></div>
            <a class="button-primary shrink-0" href="{{ LocalizedUrl::route('contact') }}">Gửi yêu cầu tư vấn <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
