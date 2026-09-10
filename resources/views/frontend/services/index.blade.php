@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => __('site.services'),
        'resourceIndexRoute' => 'services.index',
    ])

    @if ($activeCategory)
        <section class="resource-category-intro">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 resource-category-intro__grid">
                <div>
                    <h2 class="display-title text-3xl leading-tight md:text-4xl">Giải pháp {{ mb_strtolower($activeCategory->name) }} theo đúng nhu cầu thực tế.</h2>
                    <p class="mt-5 max-w-xl text-sm leading-7 text-slate-600 md:text-base md:leading-8">{{ $pageDescription }}</p>
                    <a class="button-dark mt-7" href="{{ LocalizedUrl::route('contact') }}">Nhận tư vấn <span aria-hidden="true">→</span></a>
                </div>
                <div class="resource-category-intro__visual">
                    @if (($heroImageUrl ?: $defaultBannerUrl))
                        <img src="{{ ($heroImageUrl ?: $defaultBannerUrl) }}" alt="{{ $activeCategory->name }}">
                    @else
                        <span class="image-placeholder">DV</span>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if (! $activeCategory)
        <section class="section-space bg-white">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
                <header class="mx-auto max-w-2xl text-center">
                    <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">Danh mục</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600 md:text-base">Chọn một nhóm giải pháp để xem các dịch vụ phù hợp với mục tiêu của doanh nghiệp.</p>
                </header>

                <div class="mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($categories as $category)
                        <a class="service-category-card group" href="{{ LocalizedUrl::route('services.category', ['category' => $category]) }}">
                            <div class="service-category-card__media">
                                @if ($category->image_url)
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" loading="lazy">
                                @else
                                    <span class="image-placeholder">DV</span>
                                @endif
                            </div>
                            <div class="service-category-card__body">
                                <h2 class="text-xl leading-tight font-bold text-ink md:text-2xl">{{ $category->name }}</h2>
                                @if ($category->description)
                                    <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $category->description }}</p>
                                @endif
                                <span class="service-category-card__link">Xem dịch vụ <span aria-hidden="true">→</span></span>
                            </div>
                        </a>
                    @empty
                        <p class="col-span-full rounded-2xl border border-dashed border-slate-300 p-8 text-sm leading-7 text-slate-500">Chưa có danh mục dịch vụ để hiển thị.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @else
        <section class="section-space bg-white">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
                <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">{{ $activeCategory->name }}</h2>
                    </div>
                    <a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">Xem các danh mục <span aria-hidden="true">←</span></a>
                </div>

                <div class="mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($services as $service)
                        @include('frontend.partials.service-card', ['showCategoryBadge' => false])
                    @empty
                        <p class="col-span-full rounded-2xl border border-dashed border-slate-300 p-8 text-sm leading-7 text-slate-500">Chưa có dịch vụ được xuất bản trong danh mục này.</p>
                    @endforelse
                </div>

                @if ($services->hasPages())
                    <div class="mt-12">{{ $services->onEachSide(1)->links() }}</div>
                @endif
            </div>
        </section>
    @endif

    <section class="resource-archive-cta">
        @if (($heroImageUrl ?: $defaultBannerUrl))<img class="resource-archive-cta__image" src="{{ ($heroImageUrl ?: $defaultBannerUrl) }}" alt="" aria-hidden="true">@endif
        <div class="resource-archive-cta__overlay"></div>
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 resource-archive-cta__content">
            <div><h2 class="max-w-2xl font-display text-3xl leading-tight tracking-[-0.045em] text-white md:text-4xl">Cần một giải pháp truyền thông phù hợp với mục tiêu của anh/chị?</h2></div>
            <a class="button-primary shrink-0" href="{{ LocalizedUrl::route('contact') }}">Gửi yêu cầu tư vấn <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
