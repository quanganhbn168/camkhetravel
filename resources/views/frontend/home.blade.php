@extends('layouts.master')
@push('styles')
    @vite('resources/css/pages/home.css')
@endpush
@section('body_class', 'home-page')
@section('content')
<div class="site-home" data-home-page data-system-page="home">
    <div hidden data-home-config data-phone="{{ $frontendConfig['phone'] }}" data-zalo-url="{{ $frontendConfig['zaloUrl'] }}" data-lead-endpoint="{{ $frontendConfig['leadEndpoint'] }}"></div>
    @if ($showPageBanner)<div class="home-page-banner"><img data-page-banner-image src="{{ $page['banner_url'] }}" alt="{{ $page['title'] }}" width="1800" height="400" loading="lazy"></div>@endif
    <section class="hero" id="trang-chu" data-hero-section aria-label="{{ $page['title'] }}">
        @if (blank($heroSlides->first()['title']))<h1 class="visually-hidden">{{ $website->site_name ?: $page['title'] }}</h1>@endif
        <div id="homeHero" class="carousel slide" data-home-carousel><div class="carousel-inner">
            @foreach ($heroSlides as $slide)
                <div @class(['carousel-item hero-slide', 'active' => $loop->first, 'is-image-only' => ! $slide['has_copy']])>
                    <picture class="hero-picture">
                        @if ($slide['mobile_image_url'])<source media="(max-width: 767px)" srcset="{{ $slide['mobile_image_url'] }}">@endif
                        <img src="{{ $slide['image_url'] }}" @if ($slide['uses_page_banner']) data-page-banner-image @endif alt="" width="1536" height="1024" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                    </picture>
                    @if ($slide['has_copy'])<div class="container hero-inner"><div class="hero-copy">
                        @if (filled($sectionContent['hero_eyebrow'] ?? ''))<p class="eyebrow"><span class="eyebrow-line"></span>{{ $sectionContent['hero_eyebrow'] }}</p>@endif
                        @if ($slide['title'])@if ($loop->first)<h1 class="hero-title">{{ $slide['title'] }}</h1>@else<h2 class="hero-title">{{ $slide['title'] }}</h2>@endif @endif
                        @if ($slide['description'])<p class="hero-description">{{ $slide['description'] }}</p>@endif
                        <div class="hero-actions">
                            @if ($slide['primary_label'] && $slide['primary_url'])<a class="btn btn-brand" href="{{ $slide['primary_url'] }}" @if ($slide['primary_is_quote']) data-quote-type="trip" @endif>{{ $slide['primary_label'] }} <x-site-icon name="arrow" /></a>@endif
                            @if ($slide['secondary_label'] && $slide['secondary_url'])<a class="hero-secondary" href="{{ $slide['secondary_url'] }}" @if ($slide['secondary_is_quote']) data-quote-type="trip" @endif>{{ $slide['secondary_label'] }} <x-site-icon name="arrow" /></a>@endif
                        </div>
                    </div></div>@endif
                </div>
            @endforeach
        </div>
        @if ($heroSlides->count() > 1)<div class="hero-controls" aria-label="Điều khiển slide"><button class="hero-control" type="button" data-bs-target="#homeHero" data-bs-slide="prev" aria-label="Slide trước">←</button><div class="carousel-indicators">@foreach ($heroSlides as $slide)<button type="button" data-bs-target="#homeHero" data-bs-slide-to="{{ $loop->index }}" @class(['active' => $loop->first]) @if ($loop->first) aria-current="true" @endif aria-label="Slide {{ $loop->iteration }}"></button>@endforeach</div><button class="hero-control" type="button" data-bs-target="#homeHero" data-bs-slide="next" aria-label="Slide tiếp theo">→</button></div>@endif
        </div>
    </section>

    <div class="container booking-wrap" id="bao-gia"><div class="booking-panel">
        <div class="booking-heading"><span>Chuyến đi của bạn bắt đầu từ đây</span><span class="booking-hint">Chia sẻ nhu cầu, cùng chọn phương án.</span></div>
        <div class="audience-tabs" role="group" aria-label="Chọn nhu cầu tư vấn">
            <button type="button" class="is-active" data-quick-type="trip" aria-pressed="true"><x-site-icon name="car" /><span>Chuyến đi riêng</span></button>
            <button type="button" data-quick-type="partner" aria-pressed="false"><x-site-icon name="handshake" /><span>Hợp tác đối tác</span></button>
            <button type="button" data-quick-type="wedding" aria-pressed="false"><x-site-icon name="heart" /><span>Xe cưới</span></button>
        </div>
        <form class="quick-quote" id="quickQuote" aria-label="Yêu cầu tư vấn nhanh">
            <input type="hidden" name="type" id="quickType" value="trip">
            <div class="quick-field"><label for="quickPickup">Điểm đón</label><div><x-site-icon name="pin" /><input id="quickPickup" name="pickup" placeholder="Bạn khởi hành từ đâu?" maxlength="180"></div></div>
            <div class="quick-field"><label for="quickDestination">Điểm đến</label><div><x-site-icon name="pin" /><input id="quickDestination" name="destination" placeholder="Nơi bạn muốn đến" maxlength="180"></div></div>
            <div class="quick-field"><label for="quickDate">Ngày dự kiến</label><div><input id="quickDate" name="departure" type="date"></div></div>
            <div class="quick-field"><label for="quickPassengers">Số khách / Quy mô</label><div><x-site-icon name="users" /><input id="quickPassengers" name="passengers" placeholder="Ví dụ: 4 người" maxlength="80"></div></div>
            <button type="submit" class="btn btn-brand">Tiếp tục <x-site-icon name="arrow" /></button>
        </form>
    </div></div>

    <section class="home-section services-section" id="dich-vu" aria-labelledby="services-title"><div class="container">
        <div class="section-heading section-heading-centered"><p class="eyebrow">{{ $sectionContent['services_eyebrow'] ?? 'DỊCH VỤ' }}</p><h2 id="services-title">{{ $sectionContent['services_title'] ?? 'Dịch vụ của '.$website->site_name }}</h2>@if (filled($sectionContent['services_description'] ?? ''))<p>{{ $sectionContent['services_description'] }}</p>@endif</div>
        @if ($audienceGroups->isNotEmpty())<div class="audience-grid">
            @foreach ($audienceGroups as $group)<article class="audience-card" id="{{ $group['anchor'] }}" data-audience="{{ $group['key'] }}">
                <div class="audience-image">@include('frontend.partials.home.image', ['image' => $group['image'], 'alt' => $group['title']])<span class="audience-number">0{{ $loop->iteration }}</span></div>
                <div class="audience-body"><span class="audience-icon"><x-site-icon :name="$group['icon']" /></span><h3>{{ $group['title'] }}</h3><p>{{ $group['description'] ?? '' }}</p>
                    <ul class="audience-highlights">@foreach ($group['highlights']->take(3) as $highlight)<li><x-site-icon name="check" />{{ $highlight }}</li>@endforeach</ul>
                    @if ($group['services']->isNotEmpty())<div class="audience-services" aria-label="Dịch vụ liên quan">@foreach ($group['services'] as $service)<a href="{{ route('slug.show', ['slug' => $service->slug]) }}">{{ $service->title }} <x-site-icon name="arrow" /></a>@endforeach</div>@endif
                    <button type="button" class="audience-cta" data-quote-type="{{ $group['key'] }}" @if ($group['services']->count() === 1) data-service="{{ $group['services']->first()->title }}" data-service-id="{{ $group['services']->first()->id }}" @endif>{{ $group['cta_label'] ?? 'Nhận tư vấn' }} <x-site-icon name="arrow" /></button>
                </div>
            </article>@endforeach
        </div>@else
            <div class="audience-grid">@forelse ($services as $service)<article class="service-card"><img src="{{ $service->image_url }}" alt="{{ $service->title }}" width="768" height="512" loading="lazy"><div class="audience-body"><h3><a href="{{ route('slug.show', ['slug' => $service->slug]) }}">{{ $service->title }}</a></h3><p>{{ $service->excerpt }}</p><button class="text-link" type="button" data-quote-type="{{ $service->quote_type }}" data-service="{{ $service->title }}" data-service-id="{{ $service->id }}">Nhận tư vấn <x-site-icon name="arrow" /></button></div></article>@empty<p>Dịch vụ đang được cập nhật.</p>@endforelse</div>
        @endif
    </div></section>

    @if ($fleetTypes->isNotEmpty())<section class="home-section fleet-section" id="doi-xe" aria-labelledby="fleet-title"><div class="container">
        <div class="section-heading heading-split"><div><p class="eyebrow">{{ $sectionContent['fleet_eyebrow'] ?? 'ĐỘI XE' }}</p><h2 id="fleet-title">{{ $sectionContent['fleet_title'] ?? 'Lựa chọn xe cho hành trình' }}</h2><p>{{ $sectionContent['fleet_description'] ?? '' }}</p></div><button type="button" class="text-link" data-quote-type="trip">Cùng chọn xe <x-site-icon name="arrow" /></button></div>
        <div class="fleet-grid">@foreach ($fleetTypes as $vehicle)<article class="fleet-card"><div class="fleet-image">@include('frontend.partials.home.image', ['image' => $vehicle['image'], 'alt' => $vehicle['title']])</div><div class="fleet-content"><h3>{{ $vehicle['title'] }}</h3>@if ($vehicle['description'])<p>{{ $vehicle['description'] }}</p>@endif<ul>@foreach ($vehicle['features'] as $feature)<li><x-site-icon name="check" />{{ $feature }}</li>@endforeach</ul><button type="button" class="fleet-detail" data-vehicle="{{ $vehicle['code'] }}" data-vehicle-name="{{ $vehicle['title'] }}" data-vehicle-description="{{ $vehicle['detail_text'] }}" data-vehicle-image="{{ $vehicle['image']['url'] }}">Khám phá xe <x-site-icon name="arrow" /></button></div></article>@endforeach</div>
        <p class="section-note">Ảnh minh họa. Dòng xe, số ghế và hành lý được xác nhận theo từng yêu cầu.</p>
    </div></section>@endif

    @if ($hasAbout || $stats->isNotEmpty() || $commitmentCards->isNotEmpty() || $capabilities->isNotEmpty())<section class="home-section home-about" id="gioi-thieu" aria-labelledby="about-title"><div class="container"><div class="row g-4 g-lg-5 align-items-center">
        @if ($about['image_url'])<div class="col-lg-6"><div class="about-image-wrap"><img class="home-about-image" src="{{ $about['image_url'] }}" alt="{{ $about['title'] }}" width="768" height="512" loading="lazy" decoding="async"><span class="about-image-accent" aria-hidden="true"></span></div></div>@endif
        <div class="{{ $about['image_url'] ? 'col-lg-6' : 'col-12' }}"><p class="eyebrow">{{ $sectionContent['about_eyebrow'] ?? 'VỀ CHÚNG TÔI' }}</p><h2 id="about-title">{{ $about['title'] ?: $website->site_name }}</h2>@if ($about['content'])<p class="home-managed-text about-copy">{{ $about['content'] }}</p>@endif<ul class="commitment-list">@foreach ($commitmentCards as $commitment)<li><span><x-site-icon name="check" /></span><div>{{ $commitment['title'] }}@if (filled($commitment['description'] ?? ''))<p>{{ $commitment['description'] }}</p>@endif</div></li>@endforeach</ul>@if ($capabilities->isNotEmpty())<div class="capability-list">@foreach ($capabilities as $capability)<span>{{ $capability }}</span>@endforeach</div>@endif<a class="text-link" href="{{ route('about') }}">Hiểu thêm về {{ $website->site_name }} <x-site-icon name="arrow" /></a></div>
    </div>@if ($stats->isNotEmpty())<div class="home-stats" data-home-stats>@foreach ($stats as $stat)<div class="home-stat"><strong data-stat-value>{{ $stat['value'] }}</strong><span>{{ $stat['label'] }}</span></div>@endforeach</div>@endif</div></section>@endif

    @if ($routeItems->isNotEmpty())<section class="route-section" id="hanh-trinh" aria-labelledby="routes-title"><div class="container"><div class="route-heading"><x-site-icon name="pin" /><h2 id="routes-title">{{ $sectionContent['routes_title'] ?? 'Tuyến và phạm vi hỗ trợ' }}</h2></div><div class="route-grid">@foreach ($routeItems as $item)<div class="route-item"><h3>{{ $item['title'] }}</h3><p>{{ $item['description'] ?? '' }}</p>@if ($item['url'])<a href="{{ $item['url'] }}">Xem hành trình <x-site-icon name="arrow" /></a>@else<button type="button" class="text-link" data-quote-type="trip">Trao đổi lịch trình <x-site-icon name="arrow" /></button>@endif</div>@endforeach</div></div></section>@endif

    @if (count($homepage->partner_steps))<section class="home-section process-section" id="quy-trinh" aria-labelledby="process-title"><div class="container"><div class="section-heading section-heading-centered"><p class="eyebrow">{{ $sectionContent['process_eyebrow'] ?? 'QUY TRÌNH' }}</p><h2 id="process-title">{{ $sectionContent['process_title'] ?? 'Cùng chuẩn bị cho chuyến đi' }}</h2></div><ol class="process-grid">@foreach ($homepage->partner_steps as $step)<li><span class="step-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p></li>@endforeach</ol></div></section>@endif

    @if ($testimonials->isNotEmpty())<section class="home-section reviews-section" id="danh-gia" aria-labelledby="reviews-title"><div class="container"><div class="section-heading"><p class="eyebrow">SAU MỖI HÀNH TRÌNH</p><h2 id="reviews-title">Khách hàng chia sẻ</h2></div><div class="reviews-grid">@foreach ($testimonials as $testimonial)<article class="review-card"><div class="review-stars" aria-label="{{ $testimonial->rating }} trên 5 sao">@for ($star = 1; $star <= (int) $testimonial->rating; $star++)<x-site-icon name="star" />@endfor</div><blockquote>“{{ $testimonial->quote }}”</blockquote><h3>{{ $testimonial->client_name }}</h3><p>{{ $testimonial->client_role ?: $testimonial->company_name }}</p></article>@endforeach</div></div></section>@endif

    @if ($latestPosts->isNotEmpty())<section class="home-section journal-section" id="tin-tuc" aria-labelledby="journal-title"><div class="container"><div class="section-heading heading-split"><div><p class="eyebrow">{{ $sectionContent['journal_eyebrow'] ?? 'KINH NGHIỆM HÀNH TRÌNH' }}</p><h2 id="journal-title">{{ $sectionContent['journal_title'] ?? 'Tin tức & kinh nghiệm hành trình' }}</h2></div><a class="text-link" href="{{ route('posts.index') }}">Tất cả bài viết <x-site-icon name="arrow" /></a></div><div class="journal-grid">@foreach ($latestPosts as $post)@include('frontend.partials.post-card', ['showExcerpt' => $loop->first])@endforeach</div></div></section>@endif

    @if ($faqs->isNotEmpty())<section class="home-section home-faq" id="cau-hoi-thuong-gap" aria-labelledby="faq-title"><div class="container"><div class="row g-4 g-lg-5"><div class="col-lg-4"><p class="eyebrow">THÔNG TIN HỮU ÍCH</p><h2 id="faq-title">{{ $homepage->faq_title ?: 'Câu hỏi thường gặp' }}</h2>@if ($homepage->faq_description)<p class="home-managed-text">{{ $homepage->faq_description }}</p>@endif<button type="button" class="text-link" data-quote-type="trip">Trao đổi thêm với chúng tôi <x-site-icon name="arrow" /></button></div><div class="col-lg-8 home-faq-list">@foreach ($faqs as $faq)<details class="home-faq-item"><summary>{{ $faq->question }}</summary><p class="home-managed-text">{{ $faq->answer }}</p></details>@endforeach</div></div></div></section>@endif

    <section class="contact-banner" id="lien-he" aria-labelledby="contact-title">
        @if ($homepage->consultation_media_id)<div class="contact-background">@include('frontend.partials.home.image', ['image' => $consultationImage, 'alt' => '', 'sizes' => '100vw'])</div>@endif
        <div class="container contact-inner"><div class="contact-copy"><p class="eyebrow">SẴN SÀNG CHO HÀNH TRÌNH TIẾP THEO</p><h2 id="contact-title">{{ $homepage->consultation_title ?: 'Bạn đã có lịch trình trong đầu?' }}</h2><p>{{ $homepage->consultation_content }}</p><div class="contact-actions"><button type="button" class="btn btn-brand" data-quote-type="trip">Cùng lên phương án xe <x-site-icon name="arrow" /></button>@if ($frontendConfig['phone'])<button type="button" class="btn btn-white" data-contact="phone"><x-site-icon name="phone" />{{ $frontendConfig['phone'] }}</button>@endif</div></div></div>
    </section>
</div>
@include('frontend.partials.home.dialogs')
@endsection
