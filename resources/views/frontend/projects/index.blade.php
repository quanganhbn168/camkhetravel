@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => __('site.projects'),
        'resourceIndexRoute' => 'projects.index',
    ])

    @if ($activeCategory)
        <section class="resource-category-intro">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 resource-category-intro__grid">
                <div>
                    <h2 class="display-title text-3xl leading-tight md:text-4xl">Các dự án {{ mb_strtolower($activeCategory->name) }} đã triển khai.</h2>
                    <p class="mt-5 max-w-xl text-sm leading-7 text-slate-600 md:text-base md:leading-8">{{ $pageDescription }}</p>
                    <a class="button-dark mt-7" href="{{ LocalizedUrl::route('contact') }}">Trao đổi dự án <span aria-hidden="true">→</span></a>
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

    <section class="resource-project-listing section-space bg-white">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
            @if ($backstageService)
                <div class="mb-8 flex flex-col gap-4 rounded-[1.5rem] border border-primary/20 bg-sand/50 p-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-accent">Hậu trường dịch vụ</p>
                        <p class="text-sm leading-7 text-slate-600">Đang xem các dự án được gắn với <strong class="font-semibold text-ink">{{ $backstageService->title }}</strong>.</p>
                    </div>
                    <a class="button-dark shrink-0" href="{{ LocalizedUrl::route('projects.index') }}">Xem tất cả dự án <span aria-hidden="true">→</span></a>
                </div>
            @endif

            @include('frontend.partials.resource-filter-bar', [
                'resourceName' => __('site.projects'),
                'resourceIndexRoute' => 'projects.index',
                'categoryCountAttribute' => 'projects_count',
            ])

            <div class="resource-project-grid mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
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

    <section class="pccc-archive-stats">
        <div class="site-container pccc-archive-stats__grid">
            @foreach ($archiveStats as $stat)
                <div class="pccc-archive-stat">
                    <strong>{{ $stat['value'] }}</strong>
                    <span>{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    @if ($marqueePartners->isNotEmpty())
        <section class="pccc-partner-strip" aria-label="Đối tác và khách hàng">
            <div class="site-container">
                <header class="pccc-partner-strip__heading">
                    <p class="pccc-eyebrow">Đối tác - khách hàng tiêu biểu</p>
                    <h2>Được tin tưởng trong nhiều loại công trình</h2>
                </header>
                <div class="pccc-partner-strip__items">
                    @foreach ($marqueePartners as $partner)
                        <span class="pccc-partner-strip__item">
                            @if ($partner->curatorMedia?->url)
                                <img src="{{ $partner->curatorMedia->url }}" alt="{{ $partner->name }}" loading="lazy">
                            @else
                                {{ $partner->name }}
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
