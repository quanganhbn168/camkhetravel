@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => __('site.services'),
        'resourceIndexRoute' => 'services.index',
    ])

    @if ($activeCategory)
        <section class="resource-category-intro">
            <div class="site-shell resource-category-intro__grid">
                <div>
                    <p class="eyebrow">Giới thiệu</p>
                    <h2 class="display-title mt-3 text-3xl leading-tight md:text-4xl">Giải pháp {{ mb_strtolower($activeCategory->name) }} theo đúng nhu cầu thực tế.</h2>
                    <p class="mt-5 max-w-xl text-sm leading-7 text-slate-600 md:text-base md:leading-8">{{ $pageDescription }}</p>
                    <a class="button-dark mt-7" href="{{ LocalizedUrl::route('contact') }}">Nhận tư vấn <span aria-hidden="true">→</span></a>
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
            @include('frontend.partials.resource-filter-bar', [
                'resourceName' => __('site.services'),
                'resourceIndexRoute' => 'services.index',
                'categoryCountAttribute' => 'services_count',
            ])

            <div class="mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($services as $service)
                    @include('frontend.partials.service-card')
                @empty
                    <p class="col-span-full rounded-2xl border border-dashed border-slate-300 p-8 text-sm leading-7 text-slate-500">Chưa có dịch vụ được xuất bản trong danh mục này.</p>
                @endforelse
            </div>

            @if ($services->hasPages())
                <div class="mt-12">{{ $services->onEachSide(1)->links() }}</div>
            @endif
        </div>
    </section>

    <section class="resource-archive-cta">
        @if ($heroImageUrl)<img class="resource-archive-cta__image" src="{{ $heroImageUrl }}" alt="" aria-hidden="true">@endif
        <div class="resource-archive-cta__overlay"></div>
        <div class="site-shell resource-archive-cta__content">
            <div><p class="eyebrow text-primary-soft">THT Media</p><h2 class="mt-3 max-w-2xl font-display text-3xl leading-tight tracking-[-0.045em] text-white md:text-4xl">Cần một giải pháp truyền thông phù hợp với mục tiêu của anh/chị?</h2></div>
            <a class="button-primary shrink-0" href="{{ LocalizedUrl::route('contact') }}">Gửi yêu cầu tư vấn <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
