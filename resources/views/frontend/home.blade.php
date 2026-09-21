@extends('layouts.master')

@php
    $contactPhones = collect($website->phones ?? [])
        ->filter(fn ($phone) => is_array($phone) && filled($phone['number'] ?? null))
        ->values();

    if ($contactPhones->isEmpty()) {
        $contactPhones = collect([
            ['number' => $website->hotline],
            ['number' => $website->contact_phone],
        ])->filter(fn ($phone) => filled($phone['number'] ?? null))->values();
    }

    $primaryPhone = data_get($contactPhones->first(), 'number');
    $firstProjectTab = $projectTabs->first();
    $featuredProjects = collect();

    if (is_array($firstProjectTab)) {
        if (! empty($firstProjectTab['primary'])) {
            $featuredProjects->push($firstProjectTab['primary']);
        }

        $featuredProjects = $featuredProjects
            ->concat($firstProjectTab['secondary'] ?? [])
            ->filter()
            ->take(4)
            ->values();
    }

    $featuredPost = $posts->first();
    $sidePosts = $posts->slice(1, 3)->values();
    $solutionImageUrl = $featuredServiceCategories->first()?->home_image_url
        ?: $services->first()?->image_url
        ?: $aboutImageUrl
        ?: ($defaultBannerUrl ?? null);
    $whyImageUrl = $featuredProjects->first()?->image_url
        ?: $aboutImageUrl
        ?: ($defaultBannerUrl ?? null);

    $uspItems = [
        ['title' => 'Khảo sát thực tế', 'description' => 'Đánh giá chính xác yêu cầu'],
        ['title' => 'Thi công đồng bộ', 'description' => 'Đảm bảo chất lượng toàn diện'],
        ['title' => 'Hỗ trợ hồ sơ pháp lý', 'description' => 'Tư vấn đúng quy định'],
        ['title' => 'Bảo trì dài hạn', 'description' => 'Đồng hành sau bàn giao'],
    ];

    $solutions = [
        [
            'key' => 'factory',
            'name' => 'Nhà xưởng',
            'title' => 'Giải pháp PCCC nhà xưởng',
            'description' => 'Thiết kế đồng bộ theo đặc thù sản xuất, quy mô và mức độ rủi ro của từng nhà máy.',
            'items' => ['Báo cháy tự động', 'Chữa cháy Sprinkler', 'Cấp nước chữa cháy', 'Bơm và van', 'Thoát hiểm và chỉ dẫn an toàn'],
        ],
        [
            'key' => 'warehouse',
            'name' => 'Kho bãi',
            'title' => 'Giải pháp PCCC kho bãi',
            'description' => 'Tập trung phát hiện sớm, kiểm soát cháy lan và bảo vệ hàng hóa, tài sản.',
            'items' => ['Báo cháy tự động', 'Sprinkler chữa cháy', 'Họng nước chữa cháy', 'Bơm chữa cháy', 'Chiếu sáng và chỉ dẫn thoát nạn'],
        ],
        [
            'key' => 'office',
            'name' => 'Văn phòng',
            'title' => 'Giải pháp PCCC văn phòng',
            'description' => 'Đảm bảo an toàn, thẩm mỹ và phù hợp đặc thù vận hành của khối văn phòng.',
            'items' => ['Hệ thống báo cháy', 'Bình chữa cháy', 'Đèn exit và chiếu sáng sự cố', 'Họng nước vách tường', 'Phương án thoát nạn'],
        ],
        [
            'key' => 'hotel',
            'name' => 'Khách sạn',
            'title' => 'Giải pháp PCCC khách sạn',
            'description' => 'Tăng khả năng phát hiện sớm và đảm bảo an toàn cho khu vực lưu trú đông người.',
            'items' => ['Báo cháy địa chỉ', 'Sprinkler', 'Tăng áp và hút khói', 'Họng nước chữa cháy', 'Hệ thống thoát nạn'],
        ],
        [
            'key' => 'apartment',
            'name' => 'Chung cư',
            'title' => 'Giải pháp PCCC chung cư',
            'description' => 'Đồng bộ từ phát hiện cháy, chữa cháy đến thoát hiểm, chống khói và cứu nạn.',
            'items' => ['Báo cháy tự động', 'Sprinkler', 'Tăng áp cầu thang', 'Hút khói hành lang', 'Họng nước chữa cháy'],
        ],
    ];

    $whyChooseUs = [
        ['number' => '01', 'title' => 'Khảo sát kỹ hiện trạng', 'description' => 'Đánh giá chi tiết để đưa ra phương án phù hợp thực tế.'],
        ['number' => '02', 'title' => 'Phương án tối ưu', 'description' => 'Phù hợp công năng, ngân sách và yêu cầu công trình.'],
        ['number' => '03', 'title' => 'Thi công đồng bộ', 'description' => 'Đảm bảo chất lượng, tiến độ và tính thống nhất.'],
        ['number' => '04', 'title' => 'Hỗ trợ sau bàn giao', 'description' => 'Bảo trì định kỳ và đồng hành khi vận hành.'],
    ];

    $processSteps = [
        ['number' => '01', 'title' => 'Khảo sát', 'description' => 'Nắm hiện trạng'],
        ['number' => '02', 'title' => 'Phân tích', 'description' => 'Đề xuất giải pháp'],
        ['number' => '03', 'title' => 'Thiết kế', 'description' => 'Hoàn thiện hồ sơ'],
        ['number' => '04', 'title' => 'Thi công', 'description' => 'Lắp đặt đồng bộ'],
        ['number' => '05', 'title' => 'Kiểm tra', 'description' => 'Nghiệm thu bàn giao'],
        ['number' => '06', 'title' => 'Bảo trì', 'description' => 'Hỗ trợ lâu dài'],
    ];

    $productGroups = [
        ['code' => 'BC', 'name' => 'Bình chữa cháy'],
        ['code' => 'TB', 'name' => 'Trung tâm báo cháy'],
        ['code' => 'DB', 'name' => 'Đầu báo khói'],
        ['code' => 'BM', 'name' => 'Máy bơm chữa cháy'],
        ['code' => 'VN', 'name' => 'Van tín hiệu'],
        ['code' => 'TP', 'name' => 'Tủ điều khiển PCCC'],
        ['code' => 'SP', 'name' => 'Sprinkler'],
    ];
@endphp

@section('body_class', 'home-page bg-white')
@section('main_class', '')

@section('content')
<div class="home-bootstrap">
    <h1 class="visually-hidden">{{ $companyName }} — Giải pháp PCCC cho công trình</h1>

    {{-- HERO --}}
    <section class="site-hero" data-hero-section>
        @forelse ($heroSlides as $slide)
            @if ($loop->first)
                <div class="swiper hero-swiper" data-hero-swiper>
                    <div class="swiper-wrapper">
            @endif

            <article class="swiper-slide">
                @if ($slide['image_url'])
                    <img class="site-hero__media" src="{{ $slide['image_url'] }}" alt="" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                @endif
                <div class="site-hero__overlay"></div>

                @if ($slide['video_url'])
                    <a class="hero-video-play" href="{{ $slide['video_url'] }}" target="_blank" rel="noopener" aria-label="Phát video {{ $slide['title'] ?: $website->site_name }}">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.1v13.8L19 12 8 5.1Z"/></svg>
                    </a>
                @endif

                <div class="container position-relative z-2">
                    <div class="site-hero__content">
                        @if ($slide['title'])
                            <h2 class="site-hero__title">{{ $slide['title'] }}</h2>
                        @endif
                        @if ($slide['description'])
                            <p class="site-hero__copy">{{ $slide['description'] }}</p>
                        @endif
                        @if ($slide['has_primary_cta'] || $slide['has_secondary_cta'])
                            <div class="d-flex flex-wrap gap-3 mt-4">
                                @if ($slide['has_primary_cta'])
                                    <a class="btn btn-primary" href="{{ $slide['primary_url'] }}">{{ $slide['primary_label'] }} <span aria-hidden="true">↗</span></a>
                                @endif
                                @if ($slide['has_secondary_cta'])
                                    <a class="btn btn-outline-light" href="{{ $slide['secondary_url'] }}">{{ $slide['secondary_label'] }}</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </article>

            @if ($loop->last)
                    </div>
                </div>
            @endif
        @empty
            <article class="swiper-slide">
                <div class="site-hero__overlay"></div>
                <div class="container position-relative z-2">
                    <div class="site-hero__content">
                        <h2 class="site-hero__title">Kiến tạo hệ thống PCCC an toàn, đồng bộ và bền vững.</h2>
                    </div>
                </div>
            </article>
        @endforelse
    </section>

    {{-- USP --}}
    <section class="site-usp">
        <div class="container">
            <div class="row g-0">
                @foreach ($uspItems as $item)
                    <div class="col-6 col-lg-3">
                        <article class="site-usp__item h-100">
                            <span class="site-icon-circle">✓</span>
                            <div>
                                <h3 class="h6 fw-bold text-uppercase mb-1">{{ $item['title'] }}</h3>
                                <p class="small text-secondary mb-0">{{ $item['description'] }}</p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- SERVICES --}}
    <section class="section-space" id="dich-vu">
        <div class="container">
            <div class="text-center mx-auto" style="max-width: 760px">
                <p class="section-kicker">Dịch vụ PCCC</p>
                <h2 class="section-title">Dịch vụ PCCC toàn diện</h2>
                <p class="section-copy mt-3 mb-0">Đồng hành cùng doanh nghiệp từ khảo sát, thiết kế, thi công đến cải tạo, bảo trì và tư vấn hệ thống PCCC.</p>
            </div>

            @if ($featuredServiceCategories->isNotEmpty())
                <ul class="nav site-tabs flex-nowrap overflow-x-auto mt-5" role="tablist">
                    @foreach ($featuredServiceCategories as $category)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($loop->first) active @endif" id="service-tab-{{ $category->id }}" aria-controls="service-pane-{{ $category->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" data-bs-toggle="tab" data-bs-target="#service-pane-{{ $category->id }}" type="button" role="tab">{{ $category->name }}</button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content pt-4">
                    @foreach ($featuredServiceCategories as $category)
                        <div class="tab-pane fade @if($loop->first) show active @endif" id="service-pane-{{ $category->id }}" role="tabpanel" aria-labelledby="service-tab-{{ $category->id }}" tabindex="0">
                            <div class="row g-4 g-lg-5 align-items-center">
                                <div class="col-lg-7">
                                    <a class="site-service-image d-block" href="{{ route('services.category', ['category' => $category->slug]) }}">
                                        @if ($category->home_image_url)
                                            <img src="{{ $category->home_image_url }}" alt="{{ $category->home_image_alt ?: $category->name }}" loading="lazy">
                                        @endif
                                    </a>
                                </div>
                                <div class="col-lg-5">
                                    <p class="section-kicker">Dịch vụ nổi bật</p>
                                    <h3 class="h2 fw-bold text-uppercase">{{ $category->name }}</h3>
                                    @if ($category->description)
                                        <p class="section-copy mt-3">{{ $category->description }}</p>
                                    @endif
                                    <ul class="site-check-list">
                                        @foreach ($category->services->take(5) as $service)
                                            <li><a class="text-dark" href="{{ route('slug.show', ['slug' => $service->slug]) }}">{{ $service->title }}</a></li>
                                        @endforeach
                                    </ul>
                                    <div class="d-flex flex-wrap gap-2 mt-4">
                                        <a class="btn btn-primary" href="{{ route('services.category', ['category' => $category->slug]) }}">Xem chi tiết</a>
                                        <a class="btn btn-outline-secondary" href="{{ route('services.index') }}">Tất cả dịch vụ</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="row g-4 mt-3">
                    @forelse ($services as $service)
                        <div class="col-md-6 col-lg-4">
                            <article class="site-project-card">
                                <a class="site-project-image d-block" href="{{ route('slug.show', ['slug' => $service->slug]) }}">
                                    @if ($service->image_url)<img src="{{ $service->image_url }}" alt="{{ $service->title }}" loading="lazy">@endif
                                </a>
                                <div class="p-4"><h3 class="h5 mb-0"><a class="text-dark" href="{{ route('slug.show', ['slug' => $service->slug]) }}">{{ $service->title }}</a></h3></div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12"><div class="alert alert-light border">Dịch vụ sẽ được cập nhật sớm.</div></div>
                    @endforelse
                </div>
            @endif
        </div>
    </section>

    {{-- SOLUTIONS --}}
    <section class="section-space site-dark-section" id="giai-phap">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-3">
                    <p class="section-kicker">Giải pháp PCCC</p>
                    <h2 class="section-title">Theo loại công trình</h2>
                    <p class="text-white-50 mt-3">Mỗi công trình có yêu cầu vận hành và mức độ rủi ro khác nhau. Giải pháp cần được thiết kế phù hợp ngay từ đầu.</p>

                    <div class="nav flex-column site-solution-nav mt-4" role="tablist">
                        @foreach ($solutions as $solution)
                            <button class="nav-link @if($loop->first) active @endif" data-bs-toggle="pill" data-bs-target="#solution-{{ $solution['key'] }}" type="button" role="tab">{{ $solution['name'] }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="tab-content">
                        @foreach ($solutions as $solution)
                            <div class="tab-pane fade @if($loop->first) show active @endif" id="solution-{{ $solution['key'] }}" role="tabpanel" tabindex="0">
                                <article class="site-solution-card">
                                    <div class="row g-0">
                                        <div class="col-lg-8">
                                            <div class="site-solution-image">
                                                @if ($solutionImageUrl)<img src="{{ $solutionImageUrl }}" alt="{{ $solution['title'] }}" loading="lazy">@endif
                                                <div class="site-solution-copy">
                                                    <h3 class="h2 fw-bold text-white">{{ $solution['title'] }}</h3>
                                                    <p class="text-white-50 mb-0">{{ $solution['description'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="site-solution-list">
                                                <ul class="site-check-list mt-0">
                                                    @foreach ($solution['items'] as $item)<li>{{ $item }}</li>@endforeach
                                                </ul>
                                                <a class="btn btn-primary mt-4" href="{{ route('services.index') }}">Xem giải pháp</a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ABOUT --}}
    <section class="section-space" id="gioi-thieu">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <p class="section-kicker">Về chúng tôi</p>
                    <h2 class="section-title">Một hệ thống PCCC tốt không chỉ nằm ở thiết bị</h2>
                    @if ($about['title'])<p class="fw-semibold text-dark mt-4">{{ $about['title'] }}</p>@endif
                    @if ($about['content'])<p class="section-copy">{{ $about['content'] }}</p>@endif
                    <ul class="site-check-list">
                        <li>Đội ngũ kỹ sư và kỹ thuật viên có kinh nghiệm</li>
                        <li>Quy trình triển khai rõ ràng, minh bạch</li>
                        <li>Giải pháp tối ưu theo từng loại công trình</li>
                        <li>Hỗ trợ vận hành và bảo trì sau bàn giao</li>
                    </ul>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a class="btn btn-primary" href="{{ route('about') }}">Tìm hiểu về chúng tôi</a>
                        @if ($companyProfileUrl)<a class="btn btn-outline-secondary" href="{{ $companyProfileUrl }}" download>Tải hồ sơ năng lực</a>@endif
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="site-about-grid">
                        <div class="site-about-main">
                            @if ($aboutImageUrl)<img src="{{ $aboutImageUrl }}" alt="{{ $companyName }}" loading="lazy">@endif
                        </div>
                        <div class="site-about-side">
                            @if ($solutionImageUrl)<img src="{{ $solutionImageUrl }}" alt="Hệ thống PCCC" loading="lazy">@endif
                        </div>
                        <div class="site-about-side site-about-quote">Giải pháp an toàn cho hôm nay và tương lai bền vững.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- WHY US --}}
    <section id="tai-sao-chon-chung-toi">
        <div class="container-fluid px-0">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="site-why-image h-100" @if($whyImageUrl) style="background-image:url('{{ $whyImageUrl }}')" @endif></div>
                </div>
                <div class="col-lg-7">
                    <div class="site-why-content">
                        <div class="w-100">
                            <p class="section-kicker">Tại sao chọn chúng tôi</p>
                            <h2 class="section-title text-white">Năng lực thực tế · Cam kết lâu dài</h2>
                            <div class="row g-4 mt-3">
                                @foreach ($whyChooseUs as $item)
                                    <div class="col-sm-6 col-xl-3">
                                        <article class="site-why-card">
                                            <div class="site-why-number">{{ $item['number'] }}</div>
                                            <h3 class="h6 fw-bold text-white text-uppercase mt-3">{{ $item['title'] }}</h3>
                                            <p class="small text-white-50 mb-0">{{ $item['description'] }}</p>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PROJECTS --}}
    <section class="section-space" id="du-an">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                <div>
                    <p class="section-kicker">Công trình đã triển khai</p>
                    <h2 class="section-title">Dự án tiêu biểu</h2>
                </div>
                <a class="section-link" href="{{ route('projects.index') }}">Xem tất cả dự án →</a>
            </div>

            <div class="row g-4 mt-2">
                @forelse ($featuredProjects as $project)
                    <div class="col-md-6 col-lg-3">
                        <article class="site-project-card">
                            <a class="site-project-image d-block" href="{{ route('projects.show', ['slug' => $project->slug]) }}">
                                @if ($project->image_url)<img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy">@endif
                            </a>
                            <div class="p-4">
                                @if ($project->category)<p class="small fw-bold text-primary text-uppercase mb-2">{{ $project->category->name }}</p>@endif
                                <h3 class="h6 fw-bold mb-0"><a class="text-dark" href="{{ route('projects.show', ['slug' => $project->slug]) }}">{{ $project->title }}</a></h3>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12"><div class="alert alert-light border">Dự án đang được cập nhật.</div></div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- PROCESS --}}
    <section class="section-space bg-light border-top border-bottom" id="quy-trinh">
        <div class="container">
            <p class="section-kicker">Quy trình triển khai</p>
            <h2 class="section-title">Rõ ràng · Chuyên nghiệp · Minh bạch</h2>

            <div class="row g-3 mt-4">
                @foreach ($processSteps as $step)
                    <div class="col-6 col-md-4 col-xl-2">
                        <article class="site-process-card">
                            <div class="site-process-number">{{ $step['number'] }}</div>
                            <h3 class="h6 fw-bold text-uppercase mt-4">{{ $step['title'] }}</h3>
                            <p class="small text-secondary mb-0">{{ $step['description'] }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- STATS --}}
    @if ($stats->isNotEmpty())
        <section class="py-5 bg-white">
            <div class="container">
                <div class="row g-4">
                    @foreach ($stats as $stat)
                        <div class="col-6 col-md-3">
                            <article class="site-stat">
                                <p class="site-stat__value">
                                    @if (filled($stat['prefix'])){{ $stat['prefix'] }}@endif
                                    @foreach ($stat['segments'] as $segment)
                                        {{ $segment['value'] }}
                                    @endforeach
                                    @if (filled($stat['suffix'])){{ $stat['suffix'] }}@endif
                                </p>
                                <p class="small text-secondary mt-2 mb-0">{{ $stat['label'] }}</p>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- PRODUCTS --}}
    <section class="section-space border-top" id="san-pham">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                <div>
                    <p class="section-kicker">Thiết bị PCCC</p>
                    <h2 class="section-title">Sản phẩm · Thiết bị</h2>
                    <p class="section-copy mt-3 mb-0">Danh mục thiết bị phục vụ thi công, lắp đặt và vận hành hệ thống PCCC.</p>
                </div>
                <a class="section-link" href="{{ route('products.index') }}">Xem tất cả sản phẩm →</a>
            </div>

            <div class="row g-3 mt-4 row-cols-2 row-cols-sm-3 row-cols-lg-7">
                @foreach ($productGroups as $product)
                    <div class="col">
                        <a class="site-product-card d-block text-dark" href="{{ route('products.index') }}">
                            <span class="site-product-icon">{{ $product['code'] }}</span>
                            <span class="d-block small fw-semibold mt-3">{{ $product['name'] }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PARTNERS --}}
    <section class="site-partners py-5" aria-label="Đối tác và thương hiệu">
        <div class="container mb-4">
            <p class="section-kicker">Hợp tác & đồng hành</p>
            <h2 class="h3 fw-bold text-uppercase mb-0">Đối tác · Thương hiệu</h2>
        </div>
        @if ($marqueePartners->isNotEmpty())
            <div class="partner-marquee">
                <div class="partner-marquee__track">
                    @foreach ($marqueePartners as $partner)
                        @if ($partner->website_url)
                            <a class="partner-marquee__item" href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer">
                                @if ($partner->curatorMedia?->url)<img src="{{ $partner->curatorMedia->url }}" alt="{{ $partner->name }}" loading="lazy">@else {{ $partner->name }} @endif
                            </a>
                        @else
                            <span class="partner-marquee__item">@if ($partner->curatorMedia?->url)<img src="{{ $partner->curatorMedia->url }}" alt="{{ $partner->name }}" loading="lazy">@else {{ $partner->name }} @endif</span>
                        @endif
                    @endforeach
                    @foreach ($marqueePartners as $partner)
                        <span class="partner-marquee__item" aria-hidden="true">@if ($partner->curatorMedia?->url)<img src="{{ $partner->curatorMedia->url }}" alt="" loading="lazy">@else {{ $partner->name }} @endif</span>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- CERTIFICATES --}}
    <section class="section-space">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-3">
                    <p class="section-kicker">Hồ sơ doanh nghiệp</p>
                    <h2 class="section-title">Chứng nhận · Năng lực</h2>
                    <p class="section-copy mt-3">Minh bạch hồ sơ, năng lực và tài liệu liên quan.</p>
                </div>
                <div class="col-lg-9">
                    <div class="row g-3 row-cols-2 row-cols-md-5">
                        @foreach (['Hồ sơ năng lực','Hồ sơ pháp lý','Chứng chỉ 01','Chứng chỉ 02','Chứng chỉ 03'] as $certificate)
                            <div class="col">
                                @if ($loop->first && $companyProfileUrl)<a class="site-certificate text-dark" href="{{ $companyProfileUrl }}" target="_blank">@else<div class="site-certificate">@endif
                                    <div><div class="display-6 text-primary">▤</div><div class="small fw-bold text-uppercase mt-3">{{ $certificate }}</div></div>
                                @if ($loop->first && $companyProfileUrl)</a>@else</div>@endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CONSULTATION --}}
    <section class="section-space site-consultation" id="tu-van">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <p class="section-kicker">Tư vấn miễn phí</p>
                    <h2 class="section-title text-white">Cần khảo sát hệ thống PCCC cho công trình?</h2>
                    <p class="text-white-50 mt-3">Để lại thông tin, đội ngũ của chúng tôi sẽ liên hệ và tư vấn phương án phù hợp.</p>
                    <div class="d-grid gap-2 mt-4">
                        @if ($primaryPhone)<a class="text-white fw-semibold" href="tel:{{ preg_replace('/\s+/', '', $primaryPhone) }}">☎ {{ $primaryPhone }}</a>@endif
                        @if ($website->contact_email)<a class="text-white fw-semibold" href="mailto:{{ $website->contact_email }}">✉ {{ $website->contact_email }}</a>@endif
                    </div>
                </div>
                <div class="col-lg-7">
                    <form method="POST" action="{{ route('contact.store') }}" class="site-contact-form">
                        @csrf
                        <input type="hidden" name="return_to" value="{{ request()->getPathInfo() }}#tu-van">
                        @if ($errors->any())
                            <div class="col-12"><div class="alert alert-danger" role="alert">
                                @foreach ($errors->all() as $error)<p class="mb-1">{{ $error }}</p>@endforeach
                            </div></div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ và tên</label>
                                <input class="form-control" id="home-name" name="name" autocomplete="name" maxlength="255" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại</label>
                                <input class="form-control" id="home-phone" name="phone" autocomplete="tel" maxlength="32" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Dịch vụ quan tâm</label>
                                <select class="form-select" id="home-service_id" name="service_id">
                                    <option value="">Chọn dịch vụ</option>
                                    @foreach ($contactServices as $service)<option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->title }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nhu cầu của bạn</label>
                                <input class="form-control" id="home-message" name="message" maxlength="5000" value="{{ old('message') }}" placeholder="Ví dụ: Khảo sát nhà xưởng..." required>
                            </div>
                            <div class="col-12"><button class="btn btn-primary w-100" type="submit">Gửi yêu cầu tư vấn →</button></div>
                            <div class="col-12"><p class="small text-success mb-0" data-form-success hidden></p></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- NEWS --}}
    <section class="section-space" id="tin-tuc">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                <div>
                    <p class="section-kicker">Kiến thức & cập nhật</p>
                    <h2 class="section-title">Tin tức PCCC</h2>
                </div>
                <a class="section-link" href="{{ route('posts.index') }}">Xem tất cả bài viết →</a>
            </div>

            @if ($featuredPost)
                <div class="row g-4 mt-2">
                    <div class="col-lg-7">
                        <article class="site-post-card">
                            <a class="site-post-image d-block" href="{{ route('posts.show', ['slug' => $featuredPost->slug]) }}">
                                @if ($featuredPost->image_url)<img src="{{ $featuredPost->image_url }}" alt="{{ $featuredPost->title }}" loading="lazy">@endif
                            </a>
                            <div class="p-4">
                                @if ($featuredPost->categories->first())<p class="small fw-bold text-primary text-uppercase mb-2">{{ $featuredPost->categories->first()->name }}</p>@endif
                                <h3 class="h4 fw-bold"><a class="text-dark" href="{{ route('posts.show', ['slug' => $featuredPost->slug]) }}">{{ $featuredPost->title }}</a></h3>
                                @if ($featuredPost->excerpt)<p class="text-secondary mb-0">{{ $featuredPost->excerpt }}</p>@endif
                            </div>
                        </article>
                    </div>
                    <div class="col-lg-5">
                        <div class="d-grid gap-3">
                            @foreach ($sidePosts as $post)
                                <article class="d-flex gap-3 border rounded-3 p-3 bg-white">
                                    <a class="site-post-side-image" href="{{ route('posts.show', ['slug' => $post->slug]) }}">@if ($post->image_url)<img src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">@endif</a>
                                    <div class="align-self-center"><h3 class="h6 fw-bold mb-1"><a class="text-dark" href="{{ route('posts.show', ['slug' => $post->slug]) }}">{{ $post->title }}</a></h3>@if ($post->published_at)<small class="text-secondary">{{ $post->published_at->format('d/m/Y') }}</small>@endif</div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- FAQ --}}
    @if ($faqItems->isNotEmpty())
        <section class="section-space bg-light border-top" id="cau-hoi">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-4">
                        <p class="section-kicker">Giải đáp nhanh</p>
                        <h2 class="section-title">{{ $faqTitle ?: 'Câu hỏi thường gặp' }}</h2>
                        @if (filled($faqDescription))<p class="section-copy mt-3">{{ $faqDescription }}</p>@endif
                    </div>
                    <div class="col-lg-8">
                        <div class="accordion" id="homeFaq">
                            @foreach ($faqItems as $item)
                                <div class="accordion-item">
                                    <h3 class="accordion-header"><button class="accordion-button @unless($loop->first) collapsed @endunless" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $loop->index }}" aria-controls="faq-{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">{{ $item['question'] }}</button></h3>
                                    <div id="faq-{{ $loop->index }}" class="accordion-collapse collapse @if($loop->first) show @endif" data-bs-parent="#homeFaq"><div class="accordion-body text-secondary">{{ $item['answer'] }}</div></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- FINAL CTA --}}
    <section class="site-final-cta py-5">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                <div>
                    <h2 class="h2 fw-bold text-white mb-2">Cần một phương án PCCC phù hợp cho công trình của bạn?</h2>
                    <p class="text-white-50 mb-0">Gửi thông tin công trình để đội ngũ kỹ thuật tư vấn giải pháp tối ưu.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-primary" href="#tu-van">Nhận tư vấn ngay</a>
                    @if ($primaryPhone)<a class="btn btn-outline-light" href="tel:{{ preg_replace('/\s+/', '', $primaryPhone) }}">Gọi hotline</a>@endif
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
