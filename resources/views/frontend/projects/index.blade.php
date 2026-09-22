@extends('layouts.master')



@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => 'Dự án',
        'resourceIndexRoute' => 'projects.index',
        'pageKey' => $page['key'] ?? null,
        'pageBannerUrl' => $pageBannerUrl ?? null,
    ])

    @if ($activeCategory)
        <section class="resource-category-intro">
            <div class="container resource-category-intro__grid">
                <div>
                    <h2 class="display-title h2">Các dự án {{ mb_strtolower($activeCategory->name) }} đã triển khai.</h2>
                    <p class="lead">{{ $pageDescription }}</p>
                    <a class="btn btn-dark mt-3" href="{{ route('contact') }}">Trao đổi dự án <span aria-hidden="true">→</span></a>
                </div>
                <div class="resource-category-intro__visual">
                    @if ($categoryImageUrl)
                        <img src="{{ $categoryImageUrl }}" alt="{{ $activeCategory->name }}">
                    @endif
                </div>
            </div>
        </section>
    @endif

    <section class="section-space">
        <div class="container">
            @include('frontend.partials.resource-filter-bar', [
                'resourceName' => 'Dự án',
                'resourceIndexRoute' => 'projects.index',
                'categoryCountAttribute' => 'projects_count',
            ])

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-4">
                @forelse ($projects as $project)
                    <div class="col">@include('frontend.partials.project-card')</div>
                @empty
                    <p class="empty-state w-100">Chưa có dự án được xuất bản trong danh mục này.</p>
                @endforelse
            </div>

            @if ($projects->hasPages())
                <div class="mt-4">{{ $projects->onEachSide(1)->links('frontend.partials.pagination') }}</div>
            @endif
        </div>
    </section>

    <section class="archive-stats">
        <div class="container archive-stats__grid">
            @foreach ($archiveStats as $stat)
                <div class="archive-stat">
                    <strong>{{ $stat['value'] }}</strong>
                    <span>{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    @if ($marqueePartners->isNotEmpty())
        <section class="partner-strip" aria-label="Đối tác và khách hàng">
            <div class="container">
                <header class="partner-strip__heading">
                    <h2>Được tin tưởng trong nhiều loại công trình</h2>
                </header>
                <div class="partner-strip__items">
                    @foreach ($marqueePartners as $partner)
                        <span class="partner-strip__item">
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

    @include('frontend.partials.category-content')
@endsection
