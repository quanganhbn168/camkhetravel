@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/services-index.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    @include('frontend.partials.resource-archive-hero', [
        'resourceName' => __('site.services'),
        'resourceIndexRoute' => 'services.index',
    ])

    @if ($activeCategory)
        <section class="resource-category-intro">
            <div class="site-container resource-category-intro__grid w-100 mx-auto dv-services-index__div-1">
                <div>
                    <h2 class="display-title dv-services-index__heading-2">Giải pháp {{ mb_strtolower($activeCategory->name) }} theo đúng nhu cầu thực tế.</h2>
                    <p class="dv-services-index__copy-3">{{ $pageDescription }}</p>
                    <a class="btn btn-dark button-dark dv-services-index__action-4" href="{{ LocalizedUrl::route('contact') }}">Nhận tư vấn <span aria-hidden="true">→</span></a>
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

    <section class="section-space dv-services-index__section-5" id="he-sinh-thai-dich-vu">
        <div class="site-container w-100 mx-auto dv-services-index__div-1">
            <header class="resource-list-heading">
                <div>
                    <p class="resource-list-heading__eyebrow">Danh mục dịch vụ PCCC</p>
                    <h2 class="display-title text-uppercase dv-services-index__heading-6">{{ $activeCategory?->name ?: 'Hệ sinh thái dịch vụ PCCC' }}</h2>
                    <p class="dv-services-index__copy-7">{{ $activeCategory?->description ?: 'Giải pháp đồng bộ từ khảo sát, thiết kế, thi công đến bảo trì cho từng loại công trình.' }}</p>
                </div>
                @if ($activeCategory)
                    <a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">Xem tất cả dịch vụ <span aria-hidden="true">←</span></a>
                @endif
            </header>

            @include('frontend.partials.resource-filter-bar', [
                'resourceName' => __('site.services'),
                'resourceIndexRoute' => 'services.index',
                'categoryCountAttribute' => 'services_count',
            ])

            <div class="dv-services-index__div-8">
                @forelse ($services as $service)
                    @include('frontend.partials.service-card', ['showCategoryBadge' => true])
                @empty
                    <p class="dv-services-index__copy-9">Chưa có dịch vụ được xuất bản trong danh mục này.</p>
                @endforelse
            </div>

            @if ($services->hasPages())
                <div class="dv-services-index__div-10">{{ $services->onEachSide(1)->links('frontend.partials.pagination') }}</div>
            @endif
        </div>
    </section>

    @if ($processItems->isNotEmpty())
        <section class="pccc-archive-process section-space">
            @if ($processBackgroundUrl)
                <img class="pccc-archive-process__image" src="{{ $processBackgroundUrl }}" alt="" aria-hidden="true" loading="lazy">
            @endif
            <div class="pccc-archive-process__overlay" aria-hidden="true"></div>
            <div class="site-container">
                <header class="pccc-archive-process__heading">
                    <p class="pccc-eyebrow">Quy trình triển khai dịch vụ</p>
                    <h2>Rõ ràng - Minh bạch - Hiệu quả</h2>
                    <p>DVTEC đồng hành theo từng bước, từ tiếp nhận nhu cầu đến bàn giao và hỗ trợ vận hành.</p>
                </header>
                <ol class="pccc-archive-process__steps">
                    @foreach ($processItems as $item)
                        <li>
                            <span class="pccc-archive-process__number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
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

    @if ($featuredProjects->isNotEmpty())
        <section class="section-space pccc-archive-projects dv-services-index__section-5">
            <div class="site-container">
                <header class="resource-list-heading">
                    <div>
                        <p class="resource-list-heading__eyebrow">Dự án tiêu biểu</p>
                        <h2 class="display-title text-uppercase dv-services-index__heading-6">Những công trình chúng tôi đã triển khai</h2>
                        <p class="dv-services-index__copy-7">Minh chứng rõ ràng cho năng lực, quy trình và sự đồng hành của DVTEC.</p>
                    </div>
                    <a class="section-link" href="{{ LocalizedUrl::route('projects.index') }}">Xem tất cả dự án <span aria-hidden="true">→</span></a>
                </header>
                <div class="dv-services-index__div-11">
                    @foreach ($featuredProjects as $project)
                        @include('frontend.partials.project-card', ['project' => $project])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
