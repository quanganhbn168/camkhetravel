@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/service.scss')
@endpush

@section('content')
    <section class="resource-detail-hero resource-detail-hero--service">
        @if ($bannerVideoUrl)
            <video class="resource-detail-hero__video" autoplay muted loop playsinline preload="metadata" @if ($service->image_url) poster="{{ $service->image_url }}" @endif>
                <source src="{{ $bannerVideoUrl }}" @if ($bannerVideoType) type="{{ $bannerVideoType }}" @endif>
            </video>
        @elseif ($service->image_url ?: $defaultBannerUrl)
            <img class="resource-detail-hero__image" src="{{ $service->image_url ?: $defaultBannerUrl }}" alt="" aria-hidden="true">
        @endif
        <div class="resource-detail-hero__overlay"></div>
        <div class="container resource-detail-hero__content">
            <h1 class="display-title text-white">{{ $service->title }}</h1>
            <div class="d-flex flex-wrap justify-content-center gap-3 mt-4"><a class="btn btn-primary" href="{{ route('contact', ['service' => $service->id]) }}">Nhận tư vấn ngay <span aria-hidden="true">→</span></a><a class="btn btn-outline-light" href="#noi-dung-dich-vu">Khám phá dịch vụ <span aria-hidden="true">↓</span></a></div>
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

    @if ($service->excerpt)
        <section id="noi-dung-dich-vu" class="service-intro section-space">
            <div class="container service-intro__grid">
                <div >
                    <h2 class="display-title">{{ $service->benefit_title ?: 'Giải pháp phù hợp từng công trình' }}</h2>
                    <p class="service-intro__lead">{{ $service->excerpt }}</p>
                    <ul class="service-intro__checks" aria-label="Điểm nổi bật của dịch vụ">
                        @forelse (collect($benefitItems)->pluck('title')->filter()->take(4) as $benefitTitle)
                            <li><span aria-hidden="true">✓</span>{{ $benefitTitle }}</li>
                        @empty
                            <li><span aria-hidden="true">✓</span>Đúng tiêu chuẩn và hồ sơ được duyệt</li>
                            <li><span aria-hidden="true">✓</span>Đồng bộ thiết bị, vật tư và thi công</li>
                            <li><span aria-hidden="true">✓</span>Dễ kiểm tra, nghiệm thu và vận hành</li>
                        @endforelse
                    </ul>
                    <a class="btn btn-primary" href="{{ route('contact', ['service' => $service->id]) }}">Nhận tư vấn dịch vụ <span aria-hidden="true">→</span></a>
                </div>
                <div class="service-intro__visual">
                    <figure class="service-intro__visual-main">
                        @if ($service->image_url ?: $defaultBannerUrl)
                            <img src="{{ $service->image_url ?: $defaultBannerUrl }}" alt="{{ $service->title }}" loading="eager">
                        @else
                            <span class="image-placeholder">{{ $website->site_name }}</span>
                        @endif
                    </figure>
                    @foreach (collect([
                        $referenceImages[0] ?? null,
                        $referenceImages[1] ?? ($service->image_url ?: $defaultBannerUrl),
                    ])->filter()->take(2) as $imageUrl)
                        <figure class="service-intro__visual-small">
                            <img src="{{ $imageUrl }}" alt="{{ $service->title }} — ảnh tham khảo {{ $loop->iteration }}" loading="eager">
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('frontend.services.partials.benefits')

    <section id="thiet-bi" class="service-equipment section-space">
        <div class="container">
            <header class="resource-list-heading">
                <div>
                    <h2 class="display-title text-uppercase h2">Vật tư, thiết bị PCCC đồng bộ</h2>
                    <p class="lead">Các thiết bị được lựa chọn theo hồ sơ, tiêu chuẩn áp dụng và điều kiện vận hành của từng công trình.</p>
                </div>
                <a class="section-link" href="{{ route('products.index') }}">Xem toàn bộ thiết bị <span aria-hidden="true">→</span></a>
            </header>
            <div class="service-equipment-grid">
                @forelse ($featuredProducts as $product)
                    <article class="service-equipment-card">
                        <a class="service-equipment-card__media" href="{{ route('products.show', ['slug' => $product->slug]) }}" aria-label="Xem {{ $product->title }}">
                            @if ($product->image_url ?: $defaultBannerUrl)
                                <img src="{{ $product->image_url ?: $defaultBannerUrl }}" alt="{{ $product->title }}" loading="lazy">
                            @else
                                <span class="image-placeholder">DV</span>
                            @endif
                        </a>
                        <div class="service-equipment-card__body">
                            @if ($product->category)<p class="service-equipment-card__category">{{ $product->category->name }}</p>@endif
                            <h3><a href="{{ route('products.show', ['slug' => $product->slug]) }}">{{ $product->title }}</a></h3>
                            <a class="service-equipment-card__link" href="{{ route('products.show', ['slug' => $product->slug]) }}">Xem thiết bị <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                @empty
                    @foreach (['Hệ thống báo cháy tự động', 'Hệ thống chữa cháy Sprinkler', 'Máy bơm chữa cháy', 'Van & phụ kiện đường ống', 'Tủ điều khiển PCCC', 'Thiết bị thoát nạn'] as $equipment)
                        <article class="service-equipment-card service-equipment-card--fallback">
                            <span class="service-equipment-card__icon" aria-hidden="true">+</span>
                            <div class="service-equipment-card__body"><h3>{{ $equipment }}</h3><p>Hạng mục được tư vấn theo đặc thù công trình.</p></div>
                        </article>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    @include('frontend.services.partials.reference-videos')
    @include('frontend.services.partials.process')
    @include('frontend.services.partials.commitments')

    @if (filled(strip_tags((string) $service->body_html)))
        <section id="noi-dung-chi-tiet" class="resource-detail-content">
            <div class="container">
                <article class="article-prose">
                    {!! $service->body_html !!}
                </article>
            </div>
        </section>
    @endif

    @include('frontend.services.partials.stats')
    @include('frontend.services.partials.testimonials')

    @if ($backstageImages !== [])
        <section class="resource-related-section" id="hau-truong">
            <div class="container">
                <header class="mx-auto text-center mb-4"><h2 class="display-title h2">HÌNH ẢNH HẬU TRƯỜNG</h2><p class="text-body">Tư liệu được chọn lọc từ quá trình triển khai {{ mb_strtolower($service->title) }}.</p></header>
                <div class="gallery-grid">
                    @foreach ($backstageImages as $imageUrl)
                        <a class="resource-detail-gallery__item" href="{{ $imageUrl }}" target="_blank" rel="noopener" aria-label="Mở ảnh hậu trường {{ $loop->iteration }} của {{ $service->title }}"><img src="{{ $imageUrl }}" alt="{{ $service->title }} — ảnh hậu trường {{ $loop->iteration }}" loading="lazy"><span class="resource-detail-gallery__zoom" aria-hidden="true">↗</span></a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($faqItems->isNotEmpty())
        <section class="faq section-space" id="cau-hoi-thuong-gap">
            <div class="container"><header class="faq__header"><h2 class="display-title h2">Câu hỏi thường gặp</h2><p class="text-body">Thông tin cần biết trước khi triển khai dịch vụ PCCC.</p></header><div class="faq__list">@foreach ($faqItems as $item)<details class="faq__item" @if ($loop->first) open @endif><summary class="faq__question"><span>{{ $item['question'] }}</span><span class="faq__indicator" aria-hidden="true">+</span></summary><div class="faq__answer"><p>{{ $item['answer'] }}</p></div></details>@endforeach</div></div>
        </section>
    @endif

    <x-service-consultation-form :content="$service" />

    <section class="resource-related-section" id="phan-hoi">
        <div class="container"><header class="mx-auto text-center mb-4"><h2 class="display-title h2">Đánh giá từ khách hàng</h2>@if ($ratingSummary['count'])<p class="text-body"><strong class="text-primary">{{ number_format($ratingSummary['average'], 1) }}/5</strong> từ {{ $ratingSummary['count'] }} đánh giá đã được duyệt.</p>@else<p class="text-body">Những đánh giá đầu tiên sẽ được hiển thị sau khi đội ngũ kiểm duyệt.</p>@endif</header><div class="review-layout"><div class="d-grid gap-4">@forelse ($service->approvedComments as $comment)<article class="d-flex gap-3"><span class="d-grid flex-shrink-0 fw-bold review-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($comment->author_name, 0, 1)) }}</span><div class="flex-grow-1"><div class="d-flex flex-wrap align-items-baseline gap-2"><h3 class="fw-semibold h6 mb-0">{{ $comment->author_name }}</h3><time class="fw-medium small text-body-secondary" datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y') }}</time></div>@if ($comment->rating)<p class="review-rating mt-2 mb-0" aria-label="{{ $comment->rating }} trên 5 sao">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $comment->rating ? '' : 'text-body-tertiary' }}" aria-hidden="true">★</span>@endfor</p>@endif<p class="mt-2 mb-0">{{ $comment->body }}</p></div></article>@empty<p class="empty-state w-100">Chưa có đánh giá nào. Anh/chị có thể là người đầu tiên chia sẻ trải nghiệm.</p>@endforelse</div><x-comment-form :service="$service" :rating-enabled="true" :compact="true" /></div></div>
    </section>

    @if ($relatedServices->isNotEmpty())
        <section class="resource-related-section" id="dich-vu-tham-khao"><div class="container flex-column d-flex gap-3 mb-4"><h2 class="display-title h2">Dịch vụ tham khảo</h2><a class="section-link" href="{{ route('services.index') }}">Xem tất cả dịch vụ <span aria-hidden="true">→</span></a></div><div class="container"><div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">@foreach ($relatedServices as $relatedService) <div class="col">@include('frontend.partials.service-card', ['service' => $relatedService])</div> @endforeach</div></div></section>
    @endif
@endsection
