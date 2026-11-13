@extends('layouts.master')

@push('schema')
    <x-json-ld :seo="$seo" />
@endpush

@push('styles')
    @vite('resources/css/pages/service.css')
@endpush

@push('scripts')
    @vite('resources/js/pages/service.js')
@endpush

@section('content')
    @php
        $heroImage = $service->image_url ?: $defaultBannerUrl;
        $contactUrl = route('contact', ['service' => $service->id]);
        $hotlineTel = $website->hotline ? preg_replace('/\s+/', '', $website->hotline) : null;
        $zaloUrl = $website->zalo_url ?: null;
        $introHtml = filled(strip_tags((string) $service->body_html)) ? $service->body_html : null;
        $hasWhyChoose = $benefitItems !== [];
        $hasPricing = $pricingGroups !== [];
        $hasVehicles = $vehicleItems !== [];
        $hasArea = filled($service->area_title) || filled($service->area_description) || $areaItems->isNotEmpty();
        $hasProcess = $processItems !== [];
        $hasFaq = $faqItems->isNotEmpty();
    @endphp

    {{-- ===== 1. Banner: chữ bên trái, ảnh/video bên phải ===== --}}
    <section class="svc-hero">
        <div class="svc-hero__media">
            @if ($bannerVideoUrl)
                <video autoplay muted loop playsinline preload="metadata" @if ($service->image_url) poster="{{ $service->image_url }}" @endif>
                    <source src="{{ $bannerVideoUrl }}" @if ($bannerVideoType) type="{{ $bannerVideoType }}" @endif>
                </video>
            @elseif ($heroImage)
                <img src="{{ $heroImage }}" alt="{{ $service->title }}" fetchpriority="high">
            @endif
        </div>
        <div class="container svc-hero__inner">
            <div class="svc-hero__content">
                @if ($hero['badge'])<p class="svc-badge">{{ $hero['badge'] }}</p>@endif
                <h1 class="svc-hero__title">{{ $service->title }}</h1>
                @if ($hero['subtitle'])<p @class(['svc-hero__subtitle', 'svc-hero__subtitle--long' => blank($service->hero_subtitle)])>{{ $hero['subtitle'] }}</p>@endif
                @if ($hero['points'] !== [])
                    <ul class="svc-checks">
                        @foreach ($hero['points'] as $point)
                            <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>{{ $point }}</li>
                        @endforeach
                    </ul>
                @endif
                <div class="svc-actions">
                    @if ($hotlineTel)
                        <a class="btn btn-primary btn-lg svc-btn" href="tel:{{ $hotlineTel }}"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i>Gọi đặt xe</a>
                    @else
                        <a class="btn btn-primary btn-lg svc-btn" href="#dat-xe"><i class="fa-solid fa-car-side" aria-hidden="true"></i>Đặt xe ngay</a>
                    @endif
                    @if ($zaloUrl)
                        <a class="btn btn-lg svc-btn svc-btn--zalo" href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/zalo.svg') }}" alt="" width="22" height="22">Nhắn Zalo</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <nav class="svc-breadcrumb" aria-label="Breadcrumb">
        <div class="container">
            <ol>
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><a href="{{ route('services.index') }}">Dịch vụ</a></li>
                @if ($service->category)<li><a href="{{ route('services.category', ['category' => $service->category->slug]) }}">{{ $service->category->name }}</a></li>@endif
                <li aria-current="page">{{ $service->title }}</li>
            </ol>
        </div>
    </nav>

    {{-- ===== 2. Gói dịch vụ & giá "Từ ..." ===== --}}
    @if ($packageItems !== [])
        <section class="svc-section" id="goi-dich-vu">
            <div class="container">
                <header class="svc-heading svc-heading--center" data-reveal>
                    <h2>{{ $service->package_title ?: 'Tổng quan dịch vụ & giá' }}</h2>
                    @if ($service->package_description)<p>{{ $service->package_description }}</p>@endif
                </header>
                <div class="svc-packages" data-reveal-stagger>
                    @foreach ($packageItems as $item)
                        <article class="svc-package">
                            <div class="svc-package__head">
                                <span class="svc-icon svc-icon--lg" aria-hidden="true"><i class="{{ $item['icon_class'] }}"></i></span>
                                <h3>{{ $item['title'] }}</h3>
                            </div>
                            @if (filled($item['price'] ?? null))
                                <p class="svc-package__price">{{ $item['price'] }}@if (filled($item['unit'] ?? null))<span>{{ $item['unit'] }}</span>@endif</p>
                            @endif
                            @if (filled($item['description'] ?? null))<p class="svc-package__text">{{ $item['description'] }}</p>@endif
                            <a class="btn btn-outline-primary svc-package__cta" href="#dat-xe">{{ filled($item['button_label'] ?? null) ? $item['button_label'] : 'Nhận báo giá' }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===== 3 + 4. Bảng giá theo điểm đến | Vì sao chọn ===== --}}
    @if ($hasPricing || $hasWhyChoose)
        <section class="svc-section svc-section--tint">
            <div class="container">
                <div @class(['svc-split', 'svc-split--wide-left' => $hasPricing && $hasWhyChoose])>
                    @if ($hasPricing)
                        <div class="svc-split__col" id="bang-gia">
                            <header class="svc-heading" data-reveal>
                                <h2>{{ $service->pricing_title ?: 'Giá đặt xe đi các địa điểm' }}</h2>
                                @if ($service->pricing_description)<p>{{ $service->pricing_description }}</p>@endif
                            </header>
                            <div class="svc-prices" data-reveal-stagger>
                                @foreach ($pricingGroups as $group)
                                    <article class="svc-price-card">
                                        <h3 class="svc-price-card__head"><i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ $group['destination'] }}</h3>
                                        <div class="svc-price-card__body">
                                            @foreach ($group['sections'] as $section)
                                                <div class="svc-price-card__section">
                                                    <p class="svc-price-card__kind"><i class="{{ $section['icon'] }}" aria-hidden="true"></i>{{ $section['label'] }}</p>
                                                    <dl>
                                                        @foreach ($section['rows'] as $row)
                                                            <div><dt>{{ $row['label'] }}</dt><dd>{{ $row['price'] }}</dd></div>
                                                        @endforeach
                                                    </dl>
                                                </div>
                                            @endforeach
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            <p class="svc-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i>{{ $service->pricing_note ?: 'Chi phí thực tế được tư vấn dựa trên quãng đường, loại xe và lịch trình cụ thể.' }}</p>
                        </div>
                    @endif

                    @if ($hasWhyChoose)
                        <div class="svc-split__col" id="vi-sao-chon">
                            <div class="svc-panel" data-reveal="fade">
                                <header class="svc-heading">
                                    <h2>{{ $service->benefit_title ?: 'Vì sao chọn '.$service->title.'?' }}</h2>
                                    @if ($service->benefit_description)<p>{{ $service->benefit_description }}</p>@endif
                                </header>
                                <ul @class(['svc-reasons', 'svc-reasons--grid' => ! $hasPricing])>
                                    @foreach ($benefitItems as $item)
                                        <li>
                                            <span class="svc-icon" aria-hidden="true"><i class="{{ $item['icon_class'] }}"></i></span>
                                            <div>
                                                <h3>{{ $item['title'] ?? '' }}</h3>
                                                @if (filled($item['description'] ?? null))<p>{{ $item['description'] }}</p>@endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ===== 5. Các nhu cầu đặt xe ===== --}}
    @if ($usecaseItems !== [])
        <section class="svc-section" id="nhu-cau">
            <div class="container">
                <header class="svc-heading svc-heading--center" data-reveal>
                    <h2>{{ $service->usecase_title ?: 'Các nhu cầu đặt xe' }}</h2>
                    @if ($service->usecase_description)<p>{{ $service->usecase_description }}</p>@endif
                </header>
                <div class="svc-usecases" data-reveal-stagger>
                    @foreach ($usecaseItems as $item)
                        <article class="svc-usecase">
                            <div class="svc-usecase__media">
                                @if ($item['media_url'] ?? null)
                                    <img src="{{ $item['media_url'] }}" alt="{{ $item['title'] ?? '' }}" loading="lazy">
                                @endif
                                @if (filled($item['icon'] ?? null))
                                    <span class="svc-icon svc-usecase__icon" aria-hidden="true"><i class="{{ \App\Support\Services\ServiceIcons::classFor($item['icon']) }}"></i></span>
                                @endif
                            </div>
                            <div class="svc-usecase__body">
                                <h3>{{ $item['title'] ?? '' }}</h3>
                                @if (filled($item['description'] ?? null))<p>{{ $item['description'] }}</p>@endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===== 6 + 7. Các dòng xe | Khu vực nhận đón ===== --}}
    @if ($hasVehicles || $hasArea)
        <section class="svc-section svc-section--tint">
            <div class="container">
                <div class="svc-split">
                    @if ($hasVehicles)
                        <div class="svc-split__col" id="dong-xe">
                            <header class="svc-heading" data-reveal>
                                <h2>{{ $service->vehicle_title ?: 'Các dòng xe phục vụ' }}</h2>
                                @if ($service->vehicle_description)<p>{{ $service->vehicle_description }}</p>@endif
                            </header>
                            <x-slider per-view="1.4,3,3" label="Các dòng xe phục vụ" data-reveal>
                                @foreach ($vehicleItems as $item)
                                    <div class="swiper-slide">
                                        <article class="svc-vehicle">
                                            <div class="svc-vehicle__media">
                                                @if ($item['media_url'] ?? null)
                                                    <img src="{{ $item['media_url'] }}" alt="{{ $item['title'] ?? '' }}" loading="lazy">
                                                @else
                                                    <i class="fa-solid fa-car-side" aria-hidden="true"></i>
                                                @endif
                                            </div>
                                            <h3>{{ $item['title'] ?? '' }}</h3>
                                            @if (filled($item['seats'] ?? null) && ! str_contains(mb_strtolower($item['title'] ?? ''), mb_strtolower($item['seats'])))<p class="svc-vehicle__seats">{{ $item['seats'] }}</p>@endif
                                            @if (filled($item['description'] ?? null))<p class="svc-vehicle__text">{{ $item['description'] }}</p>@endif
                                        </article>
                                    </div>
                                @endforeach
                            </x-slider>
                        </div>
                    @endif

                    @if ($hasArea)
                        <div class="svc-split__col" id="khu-vuc">
                            <div class="svc-panel svc-panel--area" data-reveal="fade">
                                <header class="svc-heading svc-heading--icon">
                                    <span class="svc-icon svc-icon--lg" aria-hidden="true"><i class="fa-solid fa-map-location-dot"></i></span>
                                    <div>
                                        <h2>{{ $service->area_title ?: 'Khu vực nhận đón khách' }}</h2>
                                        @if ($service->area_description)<p>{{ $service->area_description }}</p>@endif
                                    </div>
                                </header>
                                @if ($areaItems->isNotEmpty())
                                    <ul class="svc-checks svc-checks--grid">
                                        @foreach ($areaItems as $area)
                                            <li><i class="fa-solid fa-check" aria-hidden="true"></i>{{ $area }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if ($areaImageUrl)
                                    <img class="svc-panel__image" src="{{ $areaImageUrl }}" alt="{{ $service->area_title ?: 'Khu vực nhận đón khách' }}" loading="lazy">
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ===== 8. Quy trình đặt xe | FAQ ===== --}}
    @if ($hasProcess || $hasFaq)
        <section class="svc-section">
            <div class="container">
                <div class="svc-split">
                    @if ($hasProcess)
                        <div class="svc-split__col" id="quy-trinh">
                            <header class="svc-heading" data-reveal>
                                <h2>{{ $service->process_title ?: 'Quy trình đặt xe' }}</h2>
                                @if ($service->process_description)<p>{{ $service->process_description }}</p>@endif
                            </header>
                            <ol class="svc-steps" data-reveal-stagger>
                                @foreach ($processItems as $item)
                                    <li class="svc-step">
                                        <span class="svc-step__icon" aria-hidden="true"><i class="{{ $item['icon_class'] }}"></i></span>
                                        <p class="svc-step__num">Bước {{ $loop->iteration }}</p>
                                        <h3>{{ $item['title'] ?? '' }}</h3>
                                        @if (filled($item['description'] ?? null))<p>{{ $item['description'] }}</p>@endif
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    @if ($hasFaq)
                        <div class="svc-split__col" id="cau-hoi-thuong-gap">
                            <header class="svc-heading" data-reveal>
                                <h2>Câu hỏi thường gặp</h2>
                                <p>Những câu hỏi khách hàng quan tâm khi đặt {{ mb_strtolower($service->title) }}.</p>
                            </header>
                            <div class="svc-faq" data-reveal>
                                @foreach ($faqItems as $item)
                                    <details class="svc-faq__item" @if ($loop->first) open @endif>
                                        <summary>{{ $item['question'] }}<i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary>
                                        <div class="svc-faq__answer">{!! nl2br(e($item['answer'])) !!}</div>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ===== 9. Giới thiệu & nội dung chi tiết (SEO) ===== --}}
    {{-- Mô tả ngắn đã hiện ở banner (khi chưa nhập "Câu phụ") thì không in lại ở đây. --}}
    @php
        $introExcerpt = filled($service->hero_subtitle) ? $service->excerpt : null;
    @endphp
    @if ($introExcerpt || $introHtml)
        <section class="svc-section svc-section--tint" id="gioi-thieu">
            <div class="container">
                <header class="svc-heading" data-reveal>
                    <h2>Giới thiệu {{ $service->title }}</h2>
                    @if ($introExcerpt)<p class="svc-lead">{{ $introExcerpt }}</p>@endif
                </header>
                @if ($introHtml)
                    <div class="svc-article">
                        <x-content-toc :toc="$tableOfContents" class="mb-4" />
                        <div class="article-prose service-prose">{!! $introHtml !!}</div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ===== Hashtag & bài viết liên quan (theo thẻ của dịch vụ) ===== --}}
    @if ($relatedPosts->isNotEmpty() || $service->tags->isNotEmpty())
        <section class="svc-section" id="bai-viet-lien-quan">
            <div class="container">
                <header class="svc-heading svc-heading--row" data-reveal>
                    <h2>{{ $relatedPosts->isNotEmpty() ? 'Bài viết liên quan' : 'Chủ đề liên quan' }}</h2>
                    @if ($relatedPosts->isNotEmpty())
                        <a class="section-link" href="{{ route('posts.index') }}">Xem tất cả bài viết <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    @endif
                </header>
                <x-hashtags :tags="$service->tags" @class(['mb-4' => $relatedPosts->isNotEmpty()]) data-reveal />
                @if ($relatedPosts->isNotEmpty())
                    <x-slider per-view="1,2,3" label="Bài viết liên quan" data-reveal>
                        @foreach ($relatedPosts as $post)
                            <div class="swiper-slide">@include('frontend.partials.post-card', ['post' => $post, 'showExcerpt' => true])</div>
                        @endforeach
                    </x-slider>
                @endif
            </div>
        </section>
    @endif

    {{-- ===== 10. Đánh giá khách hàng (chỉ đánh giá đã duyệt của chính dịch vụ này) ===== --}}
    @php
        $reviews = $service->approvedComments;
        $reviewFormOpen = $errors->hasAny(['author_name', 'author_email', 'rating', 'body']);
    @endphp
    <section class="svc-section service-reviews" id="danh-gia">
        <div class="container">
            <header class="svc-heading svc-heading--center" data-reveal>
                <h2>Đánh giá từ khách hàng</h2>
                <p>Chia sẻ thật từ khách đã đi xe, được kiểm duyệt trước khi hiển thị.</p>
            </header>

            <div class="service-reviews__layout">
                <aside class="review-summary" data-reveal="fade">
                    @if ($ratingSummary['count'])
                        <p class="review-summary__score mb-1">{{ number_format($ratingSummary['average'], 1, ',', '') }}<span>/5</span></p>
                        <p class="review-stars review-stars--lg mb-1" role="img" aria-label="{{ number_format($ratingSummary['average'], 1, ',', '') }} trên 5 sao">
                            @for ($star = 1; $star <= 5; $star++)
                                <span @class(['is-on' => $star <= round($ratingSummary['average'])]) aria-hidden="true">★</span>
                            @endfor
                        </p>
                        <p class="small text-body-secondary mb-4">Dựa trên {{ $ratingSummary['count'] }} đánh giá</p>
                        <ul class="review-bars list-unstyled mb-4">
                            @foreach ($ratingSummary['distribution'] as $star => $total)
                                @php($percent = $ratingSummary['count'] ? round($total / $ratingSummary['count'] * 100) : 0)
                                <li class="review-bars__row">
                                    <span class="review-bars__label">{{ $star }} ★</span>
                                    <span class="review-bars__track" role="img" aria-label="{{ $total }} đánh giá {{ $star }} sao"><span class="review-bars__fill" style="width: {{ $percent }}%"></span></span>
                                    <span class="review-bars__count">{{ $total }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="review-summary__score review-summary__score--empty mb-2">Chưa có đánh giá</p>
                        <p class="small text-body-secondary mb-4">Anh/chị đã đi xe có thể là người đầu tiên chia sẻ trải nghiệm.</p>
                    @endif
                    <button class="btn btn-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#viet-danh-gia" aria-expanded="{{ $reviewFormOpen ? 'true' : 'false' }}" aria-controls="viet-danh-gia">Viết đánh giá</button>
                </aside>

                <div class="review-list" data-reveal>
                    @if ($reviews->isNotEmpty())
                        <x-slider per-view="1,1,2" label="Đánh giá của khách hàng" :autoplay="true">
                            @foreach ($reviews as $comment)
                                <div class="swiper-slide">@include('frontend.services.partials.review-card', ['comment' => $comment])</div>
                            @endforeach
                        </x-slider>
                    @else
                        <div class="review-empty">
                            <span class="review-empty__icon" aria-hidden="true">★</span>
                            <p class="fw-semibold mb-1">Chưa có đánh giá nào được hiển thị</p>
                            <p class="small text-body-secondary mb-0">Đánh giá mới sẽ xuất hiện tại đây sau khi được kiểm duyệt.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div @class(['collapse', 'show' => $reviewFormOpen]) id="viet-danh-gia">
                <div class="review-form-card mt-4">
                    <h3 class="h5 fw-bold mb-1">Chia sẻ trải nghiệm của anh/chị</h3>
                    <p class="small text-body-secondary mb-4">Đánh giá giúp khách sau chọn xe dễ hơn và giúp {{ $website->site_name ?: 'chúng tôi' }} phục vụ tốt hơn.</p>
                    <x-comment-form :service="$service" :rating-enabled="true" :compact="true" button-label="Gửi đánh giá" />
                </div>
            </div>
        </div>
    </section>

    {{-- ===== 11. CTA đặt xe ===== --}}
    <section class="svc-cta" id="dat-xe">
        @if ($heroImage)<img class="svc-cta__image" src="{{ $heroImage }}" alt="" aria-hidden="true" loading="lazy">@endif
        <div class="container svc-cta__inner" data-reveal>
            <div>
                <h2>Đặt {{ $service->title }} ngay hôm nay</h2>
                <p>Liên hệ {{ $website->site_name ?: 'chúng tôi' }} để được tư vấn lịch trình, báo giá và xác nhận chuyến xe nhanh chóng.</p>
            </div>
            <div class="svc-actions">
                @if ($hotlineTel)
                    <a class="btn btn-light btn-lg svc-btn" href="tel:{{ $hotlineTel }}"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i>{{ $website->hotline }}</a>
                @endif
                @if ($zaloUrl)
                    <a class="btn btn-outline-light btn-lg svc-btn" href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/zalo.svg') }}" alt="" width="22" height="22">Nhắn Zalo</a>
                @endif
                <a class="btn btn-outline-light btn-lg svc-btn" href="{{ $contactUrl }}"><i class="fa-regular fa-paper-plane" aria-hidden="true"></i>Gửi yêu cầu đặt xe</a>
            </div>
        </div>
    </section>

    @if ($relatedServices->isNotEmpty())
        <section class="svc-section" id="dich-vu-tham-khao">
            <div class="container">
                <header class="svc-heading svc-heading--row" data-reveal>
                    <h2>Dịch vụ tham khảo</h2>
                    <a class="section-link" href="{{ route('services.index') }}">Xem tất cả dịch vụ <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </header>
                <x-slider per-view="1,2,3" label="Dịch vụ tham khảo" data-reveal>
                    @foreach ($relatedServices as $relatedService)
                        <div class="swiper-slide">@include('frontend.partials.service-card', ['service' => $relatedService])</div>
                    @endforeach
                </x-slider>
            </div>
        </section>
    @endif
@endsection
