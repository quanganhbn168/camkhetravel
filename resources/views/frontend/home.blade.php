@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/home.scss')
@endpush

@section('body_class', 'home-page bg-white')
@section('main_class', '')

@section('content')
<div class="home-content" data-system-page="home">
    <h1 class="visually-hidden">{{ $page['title'] }}</h1>

    @if ($pageBannerUrl)
        <section class="resource-archive-hero" aria-label="{{ $page['title'] }}">
            <img class="resource-archive-hero__image" data-page-banner-image src="{{ $pageBannerUrl }}" alt="{{ $page['title'] }}">
            <div class="resource-archive-hero__overlay"></div>
            <div class="container resource-archive-hero__content">
                <h2 class="display-title text-white">{{ $page['title'] }}</h2>
            </div>
        </section>
    @endif

    {{-- HERO --}}
    <section class="hero" data-hero-section>
        @forelse ($heroSlides as $slide)
            @if ($loop->first)
                <div class="swiper" data-hero-swiper>
                    <div class="swiper-wrapper">
            @endif

            <article class="swiper-slide">
                @if ($slide->curatorMedia?->url)
                    <img class="hero__media" src="{{ $slide->curatorMedia->url }}" alt="" @if ($slide->curatorMedia->width && $slide->curatorMedia->height) width="{{ $slide->curatorMedia->width }}" height="{{ $slide->curatorMedia->height }}" @endif @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                @endif
                @if ($slide->has_content)
                    <div class="hero__overlay"></div>

                    @if ($slide->video_url)
                        <a class="hero-video-play btn btn-light btn-lg rounded-circle" href="{{ $slide->video_url }}" target="_blank" rel="noopener" aria-label="Phát video {{ $slide->title ?: $website->site_name }}">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.1v13.8L19 12 8 5.1Z"/></svg>
                        </a>
                    @endif

                    <div class="container hero__content-wrap position-relative z-2">
                        <div class="hero__content">
                            @if ($slide->title)
                                <h2 class="display-4 fw-bold text-white mb-3">{{ $slide->title }}</h2>
                            @endif
                            @if ($slide->description)
                                <p class="lead text-white-50 mb-0">{{ $slide->description }}</p>
                            @endif
                            @if (($slide->primary_label && $slide->primary_url) || ($slide->secondary_label && $slide->secondary_url))
                                <div class="d-flex flex-wrap gap-2 mt-4">
                                    @if ($slide->primary_label && $slide->primary_url)
                                        <a class="btn btn-primary" href="{{ $slide->primary_url }}">{{ $slide->primary_label }} <span aria-hidden="true">↗</span></a>
                                    @endif
                                    @if ($slide->secondary_label && $slide->secondary_url)
                                        <a class="btn btn-outline-light" href="{{ $slide->secondary_url }}">{{ $slide->secondary_label }}</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </article>

            @if ($loop->last)
                    </div>
                </div>
            @endif
        @empty
            <article class="swiper-slide">
                <div class="hero__overlay"></div>
                <div class="container hero__content-wrap position-relative z-2">
                    <div class="hero__content">
                        <h2 class="display-4 fw-bold text-white mb-0">Kiến tạo hệ thống PCCC an toàn, đồng bộ và bền vững.</h2>
                    </div>
                </div>
            </article>
        @endforelse
    </section>

    {{-- USP --}}
    <section class="usp">
        <div class="container">
            <div class="row g-0">
                @foreach ($uspItems as $item)
                    <div class="col-6 col-lg-3">
                        <article class="usp__item h-100">
                            <span class="icon-circle">✓</span>
                            <div>
                                <h3 class="h6 fw-bold text-uppercase mb-1">{{ $item['title'] }}</h3>
                                <p class="small text-body mb-0">{{ $item['description'] }}</p>
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
                <h2 class="section-title">Dịch vụ PCCC toàn diện</h2>
                <p class="section-copy mt-3 mb-0">Đồng hành cùng doanh nghiệp từ khảo sát, thiết kế, thi công đến cải tạo, bảo trì và tư vấn hệ thống PCCC.</p>
            </div>

            @if ($featuredServiceCategories->isNotEmpty())
                <ul class="nav tabs flex-nowrap overflow-x-auto mt-5" role="tablist">
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
                                    <a class="service-image d-block" href="{{ route('services.category', ['category' => $category->slug]) }}">
                                        @if ($category->image_url)
                                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" loading="lazy">
                                        @endif
                                    </a>
                                </div>
                                <div class="col-lg-5">
                                    <h3 class="h2 fw-bold text-uppercase">{{ $category->name }}</h3>
                                    @if ($category->description)
                                        <p class="section-copy mt-3">{{ $category->description }}</p>
                                    @endif
                                    <ul class="check-list">
                                        @foreach ($category->services->take(5) as $service)
                                            <li><a class="text-body-emphasis" href="{{ route('slug.show', ['slug' => $service->slug]) }}">{{ $service->title }}</a></li>
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
                <div class="alert alert-light border mt-5">Dịch vụ sẽ được cập nhật sớm.</div>
            @endif
        </div>
    </section>

    {{-- SOLUTIONS --}}
    @if ($solutions->isNotEmpty())
    <section class="section-space dark-section home-solutions" id="giai-phap">
        <img class="home-solutions__section-background" data-solution-background @if ($solutions->first()->image_url) src="{{ $solutions->first()->image_url }}" @else hidden @endif alt="" aria-hidden="true">
        <div class="container">
            <header class="text-center mb-5">
                <h2 class="section-title">Giải pháp cho từng loại công trình</h2>
                <p class="text-white-50 mt-3 mb-0 mx-auto">Mỗi công trình có yêu cầu vận hành và mức độ rủi ro khác nhau. Giải pháp cần được thiết kế phù hợp ngay từ đầu.</p>
            </header>
            <div class="row g-4 g-lg-5">
                <div class="col-lg-3">
                    <div class="nav flex-column solution-nav" role="tablist" aria-label="Loại công trình" aria-orientation="vertical">
                        @foreach ($solutions as $solution)
                            <button class="nav-link @if($loop->first) active @endif" id="solution-tab-{{ $solution->id }}" data-bs-toggle="pill" data-bs-target="#solution-{{ $solution->id }}" type="button" role="tab" aria-controls="solution-{{ $solution->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $solution->short_title ?: $solution->title }}</button>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="tab-content">
                        @foreach ($solutions as $solution)
                            <div class="tab-pane fade @if($loop->first) show active @endif" id="solution-{{ $solution->id }}" role="tabpanel" aria-labelledby="solution-tab-{{ $solution->id }}" tabindex="0">
                                <article class="solution-card">
                                    <div class="row g-0">
                                        <div class="col-lg-8">
                                            <div class="solution-image">
                                                @if ($solution->image_url)
                                                    <img src="{{ $solution->image_url }}" alt="{{ $solution->title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                                                @endif
                                                <div class="solution-copy">
                                                    <h3 class="h2 fw-bold text-white">{{ $solution->title }}</h3>
                                                    @if ($solution->excerpt)<p class="mb-0">{{ $solution->excerpt }}</p>@endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="solution-list">
                                                @if ($solution->highlights)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach ($solution->highlights as $item)<li>{{ $item }}</li>@endforeach
                                                    </ul>
                                                @endif
                                                <a class="btn btn-primary mt-4" href="{{ route('solutions.show', ['solution' => $solution->slug]) }}">Xem giải pháp <i class="fa-solid fa-arrow-right ms-2" aria-hidden="true"></i></a>
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
    @endif

    {{-- ABOUT --}}
    <section class="section-space" id="gioi-thieu">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-5">
                    <h2 class="section-title">Về chúng tôi — {{ $website->company_name }}</h2>
                    @if ($about['title'])<p class="fw-semibold text-body-emphasis mt-4">{{ $about['title'] }}</p>@endif
                    @if ($about['content'])<p class="section-copy">{{ $about['content'] }}</p>@endif
                    <ul class="check-list">
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
                    <div class="about-grid">
                        <div class="about-main">
                            @if ($aboutImageUrl)<img src="{{ $aboutImageUrl }}" alt="{{ $companyName }}" loading="lazy">@endif
                        </div>
                        <div class="about-side">
                            @if ($aboutImageUrl)<img src="{{ $aboutImageUrl }}" alt="Hệ thống PCCC" loading="lazy">@endif
                        </div>
                        <div class="about-side about-quote">Giải pháp an toàn cho hôm nay và tương lai bền vững.</div>
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
                    <div class="why-image h-100" @if($whyImageUrl) style="background-image:url('{{ $whyImageUrl }}')" @endif></div>
                </div>
                <div class="col-lg-7">
                    <div class="why-content">
                        <div class="w-100">
                            <h2 class="section-title text-white">Tại sao chọn chúng tôi</h2>
                            <div class="row g-4 mt-3">
                                @foreach ($whyChooseUs as $item)
                                    <div class="col-sm-6 col-xl-3">
                                        <article class="why-card">
                                            <div class="why-number">{{ $item['number'] }}</div>
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
                    <h2 class="section-title">Dự án tiêu biểu</h2>
                </div>
                <a class="section-link" href="{{ route('projects.index') }}">Xem tất cả dự án →</a>
            </div>

            <div class="row g-4 mt-2">
                @forelse ($featuredProjects as $project)
                    <div class="col-md-6 col-lg-3">
                        <article class="project-card">
                            <a class="project-image d-block" href="{{ route('projects.show', ['slug' => $project->slug]) }}">
                                @if ($project->image_url)<img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy">@endif
                            </a>
                            <div class="p-4">
                                @if ($project->category)<p class="small fw-bold text-primary text-uppercase mb-2">{{ $project->category->name }}</p>@endif
                                <h3 class="h6 fw-bold mb-0"><a class="text-body-emphasis" href="{{ route('projects.show', ['slug' => $project->slug]) }}">{{ $project->title }}</a></h3>
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
            <h2 class="section-title">Quy trình triển khai</h2>

            <div class="row g-3 mt-4">
                @foreach ($processSteps as $step)
                    <div class="col-6 col-md-4 col-xl-2">
                        <article class="process-card">
                            <div class="process-number">{{ $step['number'] }}</div>
                            <h3 class="h6 fw-bold text-uppercase mt-4">{{ $step['title'] }}</h3>
                            <p class="small text-body mb-0">{{ $step['description'] }}</p>
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
                            <article class="stat">
                                <p class="stat__value">
                                    @if (filled($stat['prefix'])){{ $stat['prefix'] }}@endif
                                    @foreach ($stat['segments'] as $segment)
                                        {{ $segment['value'] }}
                                    @endforeach
                                    @if (filled($stat['suffix'])){{ $stat['suffix'] }}@endif
                                </p>
                                <p class="small text-body mt-2 mb-0">{{ $stat['label'] }}</p>
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
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                <div>
                    <h2 class="section-title">Danh mục thiết bị</h2>
                    <p class="section-copy mt-3 mb-0">Thiết bị phục vụ thi công, lắp đặt và vận hành hệ thống PCCC.</p>
                </div>
                <a class="section-link" href="{{ route('products.index') }}">Xem tất cả sản phẩm →</a>
            </div>
            <div class="nav nav-pills gap-2 mb-4" role="tablist" aria-label="Danh mục thiết bị">
                @foreach ($equipmentCategories as $category)
                    <button class="nav-link @if($loop->first) active @endif" id="equipment-tab-{{ $category->id }}" data-bs-toggle="tab" data-bs-target="#equipment-{{ $category->id }}" type="button" role="tab" aria-controls="equipment-{{ $category->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $category->name }}</button>
                @endforeach
            </div>
            <div class="tab-content">
                @foreach ($equipmentCategories as $category)
                    <div class="tab-pane fade @if($loop->first) show active @endif" id="equipment-{{ $category->id }}" role="tabpanel" aria-labelledby="equipment-tab-{{ $category->id }}" tabindex="0">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                            @forelse ($category->products as $product)
                                <div class="col">
                                    <a class="equipment-card" href="{{ route('products.show', ['slug' => $product->slug]) }}">
                                        @if ($product->image_url)<img src="{{ $product->image_url }}" alt="{{ $product->title }}" loading="lazy">@endif
                                        <span class="d-flex justify-content-between gap-3 p-4 fw-semibold">{{ $product->title }}<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                                    </a>
                                </div>
                            @empty
                                <p class="text-body-secondary w-100">Chưa có sản phẩm trong danh mục này.</p>
                            @endforelse
                        </div>
                        <a class="section-link d-inline-block mt-4" href="{{ route('products.category', ['slug' => $category->slug]) }}">Xem danh mục {{ $category->name }} →</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PARTNERS --}}
    @if ($homePartners->isNotEmpty())
        <section class="partners py-5" aria-labelledby="partners-heading">
            <div class="container">
                <h2 class="section-title text-center mb-5" id="partners-heading">Đối tác của chúng tôi</h2>
                <div class="swiper" data-partner-swiper>
                    <div class="swiper-wrapper">
                        @foreach ($homePartners as $partner)
                            <div class="swiper-slide">
                                @if ($partner->website_url)
                                    <a class="partner-slide" href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer">
                                @else
                                    <div class="partner-slide">
                                @endif
                                    @if ($partner->curatorMedia?->url)
                                        <img src="{{ $partner->curatorMedia->url }}" alt="{{ $partner->name }}" loading="lazy">
                                    @else
                                        {{ $partner->name }}
                                    @endif
                                @if ($partner->website_url)</a>@else</div>@endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-outline-secondary" data-partner-swiper-prev aria-label="Đối tác trước">←</button>
                    <button type="button" class="btn btn-outline-secondary" data-partner-swiper-next aria-label="Đối tác tiếp theo">→</button>
                </div>
            </div>
        </section>
    @endif

    {{-- CERTIFICATES --}}
    <section class="section-space">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-3">
                    <h2 class="section-title">Chứng nhận · Năng lực</h2>
                    <p class="section-copy mt-3">Minh bạch hồ sơ, năng lực và tài liệu liên quan.</p>
                </div>
                <div class="col-lg-9">
                    <div class="row g-3 row-cols-2 row-cols-md-5">
                        @foreach ($certificateItems as $certificate)
                            <div class="col">
                                @if ($loop->first && $companyProfileUrl)<a class="certificate text-body-emphasis" href="{{ $companyProfileUrl }}" target="_blank">@else<div class="certificate">@endif
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
    <section class="section-space consultation" id="tu-van">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-5">
                    <h2 class="section-title text-white">Liên hệ tư vấn miễn phí</h2>
                    <p class="text-white-50 mt-3">Gửi thông tin công trình để đội ngũ kỹ thuật tư vấn giải pháp tối ưu.</p>
                    <div class="d-grid gap-2 mt-4">
                        @if ($primaryPhone)<a class="text-white fw-semibold" href="tel:{{ preg_replace('/\s+/', '', $primaryPhone) }}">☎ {{ $primaryPhone }}</a>@endif
                        @if ($website->contact_email)<a class="text-white fw-semibold" href="mailto:{{ $website->contact_email }}">✉ {{ $website->contact_email }}</a>@endif
                    </div>
                </div>
                <div class="col-lg-7">
                    <form method="POST" action="{{ route('contact.store') }}" class="consultation__form">
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
                    <h2 class="section-title">Tin tức PCCC</h2>
                </div>
                <a class="section-link" href="{{ route('posts.index') }}">Xem tất cả bài viết →</a>
            </div>

            @if ($featuredPost)
                <div class="row g-4 mt-2">
                    <div class="col-lg-7">
                        <article class="post-card">
                            <a class="post-image d-block" href="{{ route('posts.show', ['slug' => $featuredPost->slug]) }}">
                                @if ($featuredPost->image_url)<img src="{{ $featuredPost->image_url }}" alt="{{ $featuredPost->title }}" loading="lazy">@endif
                            </a>
                            <div class="p-4">
                                @if ($featuredPost->category)<p class="small fw-bold text-primary text-uppercase mb-2">{{ $featuredPost->category->name }}</p>@endif
                                <h3 class="h4 fw-bold"><a class="text-body-emphasis" href="{{ route('posts.show', ['slug' => $featuredPost->slug]) }}">{{ $featuredPost->title }}</a></h3>
                                @if ($featuredPost->excerpt)<p class="text-body mb-0">{{ $featuredPost->excerpt }}</p>@endif
                            </div>
                        </article>
                    </div>
                    <div class="col-lg-5">
                        <div class="d-grid gap-3">
                            @foreach ($sidePosts as $post)
                                <article class="d-flex gap-3 border rounded-3 p-3 bg-white">
                                    <a class="post-side-image" href="{{ route('posts.show', ['slug' => $post->slug]) }}">@if ($post->image_url)<img src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">@endif</a>
                                    <div class="align-self-center"><h3 class="h6 fw-bold mb-1"><a class="text-body-emphasis" href="{{ route('posts.show', ['slug' => $post->slug]) }}">{{ $post->title }}</a></h3>@if ($post->published_at)<small class="text-secondary">{{ $post->published_at->format('d/m/Y') }}</small>@endif</div>
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
                <div class="row g-4 g-lg-5">
                    <div class="col-lg-4">
                        <h2 class="section-title">{{ $faqTitle ?: 'Câu hỏi thường gặp' }}</h2>
                        @if (filled($faqDescription))<p class="section-copy mt-3">{{ $faqDescription }}</p>@endif
                    </div>
                    <div class="col-lg-8">
                        <div class="accordion" id="homeFaq">
                            @foreach ($faqItems as $item)
                                <div class="accordion-item">
                                    <h3 class="accordion-header"><button class="accordion-button @unless($loop->first) collapsed @endunless" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $loop->index }}" aria-controls="faq-{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">{{ $item['question'] }}</button></h3>
                                    <div id="faq-{{ $loop->index }}" class="accordion-collapse collapse @if($loop->first) show @endif" data-bs-parent="#homeFaq"><div class="accordion-body text-body">{{ $item['answer'] }}</div></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

</div>
@endsection
