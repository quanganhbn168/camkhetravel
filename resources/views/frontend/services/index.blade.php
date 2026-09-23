@extends('layouts.master')



@push('styles')
    @vite('resources/css/pages/services.css')
@endpush

@section('body_class', 'services-page')

@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => 'Dịch vụ',
        'resourceIndexRoute' => 'services.index',
        'pageKey' => $page['key'] ?? null,
        'pageBannerUrl' => $pageBannerUrl ?? null,
    ])

    @if ($activeCategory)
        <section class="resource-category-intro">
            <div class="container resource-category-intro__grid">
                <div>
                    <h2 class="display-title h2">{{ $activeCategory->name }} theo nhu cầu hành trình.</h2>
                    <p class="lead">{{ $pageDescription }}</p>
                    <a class="btn btn-dark mt-3" href="{{ route('contact') }}">Nhận tư vấn <span aria-hidden="true">→</span></a>
                </div>
                <div class="resource-category-intro__visual">
                    @if ($categoryImageUrl)
                        <img src="{{ $categoryImageUrl }}" alt="{{ $activeCategory->name }}">
                    @endif
                </div>
            </div>
        </section>
    @endif

    <section class="section-space" id="he-sinh-thai-dich-vu">
        <div class="container">
            <header class="resource-list-heading">
                <div>
                    <p class="service-list-eyebrow"><i class="fa-solid fa-car-side" aria-hidden="true"></i> Theo hành trình của bạn</p>
                    <h2 class="display-title text-uppercase h2">{{ $activeCategory?->name ?: 'Dịch vụ xe và du lịch' }}</h2>
                    <p class="text-body">{{ $activeCategory?->description ?: 'Chọn dịch vụ phù hợp với điểm đón, điểm đến và quy mô đoàn của bạn.' }}</p>
                </div>
                @if ($activeCategory)
                    <a class="section-link" href="{{ route('services.index') }}">Xem tất cả dịch vụ <span aria-hidden="true">←</span></a>
                @endif
            </header>

            @include('frontend.partials.resource-filter-bar', [
                'resourceName' => 'Dịch vụ',
                'resourceIndexRoute' => 'services.index',
                'categoryCountAttribute' => 'services_count',
            ])

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-4">
                @forelse ($services as $service)
                    <div class="col">@include('frontend.partials.service-card', ['showCategoryBadge' => true])</div>
                @empty
                    <p class="empty-state w-100">Chưa có dịch vụ được xuất bản trong danh mục này.</p>
                @endforelse
            </div>

            @if ($services->hasPages())
                <div class="mt-4">{{ $services->onEachSide(1)->links('frontend.partials.pagination') }}</div>
            @endif
        </div>
    </section>

    @if (filled($categoryBodyHtml ?? null))
        <section class="section-space">
            <div class="container">
                <div class="resource-content">{!! $categoryBodyHtml !!}</div>
            </div>
        </section>
    @endif

    @if ($processItems->isNotEmpty())
        <section class="archive-process section-space">
            @if ($processBackgroundUrl)
                <img class="archive-process__image" src="{{ $processBackgroundUrl }}" alt="" aria-hidden="true" loading="lazy">
            @endif
            <div class="archive-process__overlay" aria-hidden="true"></div>
            <div class="container">
                <header class="archive-process__heading">
                    <h2>Rõ ràng - Minh bạch - Hiệu quả</h2>
                    <p>{{ $website->site_name }} trao đổi từng bước, từ tiếp nhận lịch trình đến xác nhận phương án xe.</p>
                </header>
                <ol class="archive-process__steps">
                    @foreach ($processItems as $item)
                        <li>
                            <span class="archive-process__number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h3>{{ $item['title'] ?? 'Bước triển khai' }}</h3>
                                @if (filled($item['description'] ?? null))
                                    <p>{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

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

    @if ($featuredProjects->isNotEmpty())
        <section class="section-space">
            <div class="container">
                <header class="resource-list-heading">
                    <div>
                        <h2 class="display-title text-uppercase h2">Những hành trình đã đồng hành</h2>
                        <p class="text-body">Minh chứng rõ ràng cho năng lực, quy trình và sự đồng hành của {{ $website->site_name }}.</p>
                    </div>
                    <a class="section-link" href="{{ route('projects.index') }}">Xem tất cả dự án <span aria-hidden="true">→</span></a>
                </header>
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    @foreach ($featuredProjects as $project)
                        <div class="col">@include('frontend.partials.project-card', ['project' => $project])</div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
