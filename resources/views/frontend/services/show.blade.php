@extends('layouts.master')
@push('schema')    <x-json-ld :seo="$seo" />@endpush

@push('styles')
    @vite('resources/css/pages/service.css')
@endpush

@section('content')
    @php
        $heroImage = $service->image_url ?: $defaultBannerUrl;
        $contactUrl = route('contact', ['service' => $service->id]);
        $hotlineTel = $website->hotline ? preg_replace('/\s+/', '', $website->hotline) : null;
        $introHtml = filled(strip_tags((string) $service->body_html)) ? $service->body_html : null;
        $highlights = collect($benefitItems)->pluck('title')->filter()->take(4);
    @endphp

    {{-- 1. Banner --}}
    <section class="resource-detail-hero resource-detail-hero--service">
        @if ($bannerVideoUrl)
            <video class="resource-detail-hero__video" autoplay muted loop playsinline preload="metadata" @if ($service->image_url) poster="{{ $service->image_url }}" @endif>
                <source src="{{ $bannerVideoUrl }}" @if ($bannerVideoType) type="{{ $bannerVideoType }}" @endif>
            </video>
        @elseif ($heroImage)
            <img class="resource-detail-hero__image" src="{{ $heroImage }}" alt="" aria-hidden="true">
        @endif
        <div class="resource-detail-hero__overlay"></div>
        <div class="container resource-detail-hero__content">
            <h1 class="display-title text-white">{{ $service->title }}</h1>
            <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                <a class="btn btn-primary" href="{{ $contactUrl }}">Đặt xe ngay <span aria-hidden="true">→</span></a>
                @if ($hotlineTel)
                    <a class="btn btn-outline-light" href="tel:{{ $hotlineTel }}">Gọi {{ $website->hotline }}</a>
                @endif
            </div>
        </div>
    </section>

    <div>
        <div class="container">
            <nav aria-label="Breadcrumb">
                <ol class="d-flex flex-wrap align-items-center list-unstyled gap-2 py-3 mb-0 small">
                    <li><a href="{{ route('home') }}">Trang chủ</a></li><li aria-hidden="true">/</li>
                    <li><a href="{{ route('services.index') }}">Dịch vụ</a></li>
                    @if ($service->category)<li aria-hidden="true">/</li><li><a href="{{ route('services.category', ['category' => $service->category->slug]) }}">{{ $service->category->name }}</a></li>@endif
                    <li aria-hidden="true">/</li><li class="fw-medium text-body-secondary" aria-current="page">{{ $service->title }}</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- 2. Giới thiệu dịch vụ: mô tả ngắn cạnh ảnh, nội dung dài full width ngay bên dưới --}}
    @if ($service->excerpt || $highlights->isNotEmpty())
        <section id="gioi-thieu" class="service-intro section-space">
            <div class="container service-intro__grid">
                <div>
                    <h2 class="display-title">Giới thiệu {{ $service->title }}</h2>
                    @if ($service->excerpt)
                        <p class="service-intro__lead">{{ $service->excerpt }}</p>
                    @endif
                    @if ($highlights->isNotEmpty())
                        <ul class="service-intro__checks" aria-label="Điểm nổi bật của dịch vụ">
                            @foreach ($highlights as $title)
                                <li><span aria-hidden="true">✓</span>{{ $title }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <a class="btn btn-primary" href="{{ $contactUrl }}">Nhận báo giá <span aria-hidden="true">→</span></a>
                </div>
                <div class="service-intro__visual">
                    <figure class="service-intro__visual-main">
                        @if ($heroImage)
                            <img src="{{ $heroImage }}" alt="{{ $service->title }}" loading="eager">
                        @else
                            <span class="image-placeholder">{{ $website->site_name }}</span>
                        @endif
                    </figure>
                    @foreach (collect($galleryImages)->take(2) as $imageUrl)
                        <figure class="service-intro__visual-small">
                            <img src="{{ $imageUrl }}" alt="{{ $service->title }} — ảnh thực tế {{ $loop->iteration }}" loading="lazy">
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($introHtml)
        {{-- Nội dung chi tiết full width: mục lục tự tạo từ các H2/H3 trong nội dung, mỗi H2 là một phần riêng --}}
        <section id="noi-dung" class="service-content pb-5 @unless ($service->excerpt || $highlights->isNotEmpty()) pt-5 @endunless">
            <div class="container">
                @if (count($tableOfContents) >= 2)
                    <nav class="service-toc mb-4" aria-labelledby="service-toc-title">
                        <button class="service-toc__toggle" type="button" data-bs-toggle="collapse" data-bs-target="#service-toc-list" aria-expanded="true" aria-controls="service-toc-list">
                            <span class="fw-bold" id="service-toc-title">Nội dung chính</span>
                            <span class="service-toc__indicator" aria-hidden="true">
                                <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                            </span>
                        </button>
                        <div class="collapse show" id="service-toc-list">
                            <ol class="service-toc__list">
                                @foreach ($tableOfContents as $item)
                                    <li @class(['service-toc__item', 'service-toc__item--sub' => $item['level'] === 3])>
                                        <a href="#{{ $item['id'] }}">{{ $item['title'] }}</a>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </nav>
                @endif
                <div class="article-prose service-prose">{!! $introHtml !!}</div>
            </div>
        </section>
    @endif

    {{-- 3. Vì sao chọn --}}
    @if ($benefitItems !== [])
        <section id="vi-sao-chon" class="section-space bg-light">
            <div class="container">
                <header class="mx-auto text-center mb-4">
                    <h2 class="display-title h2">{{ $service->benefit_title ?: 'Vì sao chọn '.$service->title }}</h2>
                    @if ($service->benefit_description)<p class="mx-auto lead">{{ $service->benefit_description }}</p>@endif
                </header>
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">
                    @foreach ($benefitItems as $item)
                        <div class="col">
                            <article class="card h-100">
                                <div class="card-body">
                                    <span class="service-card-num" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <h3 class="fw-bold h5">{{ $item['title'] ?? '' }}</h3>
                                    @if (filled($item['description'] ?? null))<p class="mb-0">{{ $item['description'] }}</p>@endif
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 4. Các nhu cầu đặt xe --}}
    @if ($usecaseItems !== [])
        <section id="nhu-cau" class="section-space">
            <div class="container">
                <header class="mx-auto text-center mb-4">
                    <h2 class="display-title h2">{{ $service->usecase_title ?: 'Các nhu cầu đặt xe' }}</h2>
                    @if ($service->usecase_description)<p class="mx-auto lead">{{ $service->usecase_description }}</p>@endif
                </header>
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">
                    @foreach ($usecaseItems as $item)
                        <div class="col">
                            <article class="overflow-hidden card h-100">
                                @if ($item['media_url'] ?? null)
                                    <img class="w-100 object-fit-cover benefit-image" src="{{ $item['media_url'] }}" alt="{{ $item['title'] ?? '' }}" loading="lazy">
                                @endif
                                <div class="card-body">
                                    <h3 class="fw-bold h5">{{ $item['title'] ?? '' }}</h3>
                                    @if (filled($item['description'] ?? null))<p class="mb-0">{{ $item['description'] }}</p>@endif
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 5. Các dòng xe phục vụ --}}
    @if ($vehicleItems !== [])
        <section id="dong-xe" class="section-space bg-light">
            <div class="container">
                <header class="mx-auto text-center mb-4">
                    <h2 class="display-title h2">{{ $service->vehicle_title ?: 'Các dòng xe phục vụ' }}</h2>
                    @if ($service->vehicle_description)<p class="mx-auto lead">{{ $service->vehicle_description }}</p>@endif
                </header>
                <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
                    @foreach ($vehicleItems as $item)
                        <div class="col">
                            <article class="overflow-hidden card h-100">
                                @if ($item['media_url'] ?? null)
                                    <img class="w-100 object-fit-cover benefit-image" src="{{ $item['media_url'] }}" alt="{{ $item['title'] ?? '' }}" loading="lazy">
                                @endif
                                <div class="card-body">
                                    <h3 class="fw-bold h4">{{ $item['title'] ?? '' }}</h3>
                                    @if (filled($item['seats'] ?? null))<p class="fw-semibold text-primary-emphasis">{{ $item['seats'] }}</p>@endif
                                    @if (filled($item['description'] ?? null))<p class="mb-0">{{ $item['description'] }}</p>@endif
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 6. Khu vực nhận đón khách --}}
    @if (filled($service->area_title) || filled($service->area_description) || $areaItems !== [])
        <section id="khu-vuc" class="section-space">
            <div class="container">
                <header class="mx-auto text-center mb-4">
                    <h2 class="display-title h2">{{ $service->area_title ?: 'Khu vực nhận đón khách' }}</h2>
                    @if ($service->area_description)<p class="mx-auto lead">{{ $service->area_description }}</p>@endif
                </header>
                @if ($areaItems !== [])
                    <ul class="service-area list-unstyled mb-0">
                        @foreach ($areaItems as $item)
                            <li class="service-area__item">
                                <h3 class="fw-bold h6 mb-1">{{ $item['title'] ?? '' }}</h3>
                                @if (filled($item['description'] ?? null))<p class="small mb-0">{{ $item['description'] }}</p>@endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    @endif

    {{-- 7. Quy trình đặt xe --}}
    @if ($processItems !== [])
        <section id="quy-trinh" class="service-process section-space position-relative overflow-hidden">
            @if ($processBackgroundUrl)
                <img class="service-process__image" src="{{ $processBackgroundUrl }}" alt="" aria-hidden="true" loading="lazy">
            @endif
            <div class="service-process__overlay"></div>
            <div class="container position-relative">
                <header class="mx-auto text-center mb-5">
                    <h2 class="display-title h2 text-white">{{ $service->process_title ?: 'Quy trình đặt xe' }}</h2>
                    @if ($service->process_description)<p class="mx-auto lead">{{ $service->process_description }}</p>@endif
                </header>
                <ol class="service-process__timeline">
                    @foreach ($processItems as $item)
                        <li class="service-process__item">
                            <span class="service-process__marker" aria-hidden="true">{{ $item['step'] ?? $loop->iteration }}</span>
                            <div>
                                <p class="text-primary-on-dark fw-semibold text-uppercase small">Bước {{ $item['step'] ?? $loop->iteration }}</p>
                                <h3 class="fw-bold h4">{{ $item['title'] ?? '' }}</h3>
                                @if (filled($item['description'] ?? null))<p class="mb-0">{{ $item['description'] }}</p>@endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- 8. Bảng giá / Báo giá --}}
    @if ($pricingItems->isNotEmpty())
        <section id="bang-gia" class="section-space bg-light">
            <div class="container">
                <header class="mx-auto text-center mb-4">
                    <h2 class="display-title h2">{{ $service->pricing_title ?: 'Bảng giá tham khảo' }}</h2>
                    @if ($service->pricing_description)<p class="mx-auto lead">{{ $service->pricing_description }}</p>@endif
                </header>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle bg-white service-pricing">
                        <thead>
                            <tr><th scope="col">Hành trình</th><th scope="col">Loại xe</th><th scope="col">Giá tham khảo</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($pricingItems as $row)
                                <tr>
                                    <th scope="row">{{ $row['route'] }}</th>
                                    <td>{{ $row['vehicle'] ?? '' }}</td>
                                    <td class="fw-semibold">{{ filled($row['price'] ?? null) ? $row['price'] : 'Liên hệ' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($service->pricing_note)<p class="small text-body-secondary mt-3 mb-0">{{ $service->pricing_note }}</p>@endif
                <div class="text-center mt-4"><a class="btn btn-primary" href="#tu-van">Nhận báo giá chính xác <span aria-hidden="true">↓</span></a></div>
            </div>
        </section>
    @endif

    {{-- 9. Đánh giá khách hàng: chỉ đánh giá đã duyệt của chính dịch vụ này --}}
    @php
        $reviews = $service->approvedComments;
        $visibleReviews = $reviews->take(6);
        $moreReviews = $reviews->slice(6);
        $reviewFormOpen = $errors->hasAny(['author_name', 'author_email', 'rating', 'body']);
    @endphp
    <section class="service-reviews section-space bg-light" id="danh-gia">
        <div class="container">
            <header class="mx-auto text-center mb-5">
                <h2 class="display-title h2">Đánh giá từ khách hàng</h2>
                <p class="text-body mb-0">Chia sẻ thật từ khách đã đi xe, được kiểm duyệt trước khi hiển thị.</p>
            </header>

            <div class="service-reviews__layout">
                {{-- Tổng quan điểm đánh giá --}}
                <aside class="review-summary">
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

                {{-- Danh sách đánh giá --}}
                <div class="review-list">
                    @forelse ($visibleReviews as $comment)
                        @include('frontend.services.partials.review-card', ['comment' => $comment])
                    @empty
                        <div class="review-empty">
                            <span class="review-empty__icon" aria-hidden="true">★</span>
                            <p class="fw-semibold mb-1">Chưa có đánh giá nào được hiển thị</p>
                            <p class="small text-body-secondary mb-0">Đánh giá mới sẽ xuất hiện tại đây sau khi được kiểm duyệt.</p>
                        </div>
                    @endforelse

                    @if ($moreReviews->isNotEmpty())
                        <div class="collapse review-list__more" id="danh-gia-them">
                            @foreach ($moreReviews as $comment)
                                @include('frontend.services.partials.review-card', ['comment' => $comment])
                            @endforeach
                        </div>
                        <button class="btn btn-outline-primary review-list__toggle" type="button" data-bs-toggle="collapse" data-bs-target="#danh-gia-them" aria-expanded="false" aria-controls="danh-gia-them">Xem thêm {{ $moreReviews->count() }} đánh giá</button>
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

    {{-- 10. FAQ --}}
    @if ($faqItems->isNotEmpty())
        <section class="faq section-space" id="cau-hoi-thuong-gap">
            <div class="container"><header class="faq__header"><h2 class="display-title h2">Câu hỏi thường gặp</h2><p class="text-body">Thông tin cần biết trước khi đặt xe.</p></header><div class="faq__list">@foreach ($faqItems as $item)<details class="faq__item" @if ($loop->first) open @endif><summary class="faq__question"><span>{{ $item['question'] }}</span><span class="faq__indicator" aria-hidden="true">+</span></summary><div class="faq__answer"><p>{{ $item['answer'] }}</p></div></details>@endforeach</div></div>
        </section>
    @endif

    {{-- 11. CTA đặt xe --}}
    <x-service-consultation-form :content="$service" heading="Đặt xe ngay" button-label="Gửi yêu cầu đặt xe" description="Cho chúng tôi biết điểm đón, điểm đến và thời gian đi. CamKheTravel sẽ liên hệ xác nhận xe và giá." />

    @if ($relatedServices->isNotEmpty())
        <section class="resource-related-section" id="dich-vu-tham-khao"><div class="container flex-column d-flex gap-3 mb-4"><h2 class="display-title h2">Dịch vụ tham khảo</h2><a class="section-link" href="{{ route('services.index') }}">Xem tất cả dịch vụ <span aria-hidden="true">→</span></a></div><div class="container"><div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">@foreach ($relatedServices as $relatedService) <div class="col">@include('frontend.partials.service-card', ['service' => $relatedService])</div> @endforeach</div></div></section>
    @endif
@endsection
