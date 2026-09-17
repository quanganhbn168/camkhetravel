@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/projects-index.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => __('site.projects'),
        'resourceIndexRoute' => 'projects.index',
    ])

    @if ($activeCategory)
        <section class="resource-category-intro">
            <div class="site-container resource-category-intro__grid w-100 mx-auto dv-projects-index__div-1">
                <div>
                    <h2 class="display-title dv-projects-index__heading-2">Các dự án {{ mb_strtolower($activeCategory->name) }} đã triển khai.</h2>
                    <p class="dv-projects-index__copy-3">{{ $pageDescription }}</p>
                    <a class="btn btn-dark button-dark dv-projects-index__action-4" href="{{ LocalizedUrl::route('contact') }}">Trao đổi dự án <span aria-hidden="true">→</span></a>
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

    <section class="resource-project-listing section-space dv-projects-index__section-5">
        <div class="site-container w-100 mx-auto dv-projects-index__div-1">
            @if ($backstageService)
                <div class="flex-column dv-projects-index__div-6">
                    <div>
                        <p class="fw-bold text-uppercase dv-projects-index__copy-7">Hậu trường dịch vụ</p>
                        <p class="dv-projects-index__copy-8">Đang xem các dự án được gắn với <strong class="fw-semibold dv-projects-index__element-9">{{ $backstageService->title }}</strong>.</p>
                    </div>
                    <a class="btn btn-dark button-dark flex-shrink-0" href="{{ LocalizedUrl::route('projects.index') }}">Xem tất cả dự án <span aria-hidden="true">→</span></a>
                </div>
            @endif

            @include('frontend.partials.resource-filter-bar', [
                'resourceName' => __('site.projects'),
                'resourceIndexRoute' => 'projects.index',
                'categoryCountAttribute' => 'projects_count',
            ])

            <div class="resource-project-grid dv-projects-index__div-11">
                @forelse ($projects as $project)
                    @include('frontend.partials.project-card')
                @empty
                    <p class="dv-projects-index__copy-12">Chưa có dự án được xuất bản trong danh mục này.</p>
                @endforelse
            </div>

            @if ($projects->hasPages())
                <div class="dv-projects-index__div-13">{{ $projects->onEachSide(1)->links('frontend.partials.pagination') }}</div>
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
