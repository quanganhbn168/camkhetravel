@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/services-show.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)

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
        <div class="site-container resource-detail-hero__content w-100 mx-auto site-services-show__div-1">
            <h1 class="site-services-show__heading-2">{{ $service->title }}</h1>
            <div class="d-flex flex-wrap justify-content-center site-services-show__div-3"><a class="btn btn-primary button-primary" href="{{ LocalizedUrl::route('contact', ['service' => $service->id]) }}">Nhận tư vấn ngay <span aria-hidden="true">→</span></a><a class="btn btn-outline-light button-secondary" href="#noi-dung-dich-vu">Khám phá dịch vụ <span aria-hidden="true">↓</span></a></div>
        </div>
    </section>

    <div class="site-services-show__div-4">
        <div class="site-container w-100 mx-auto site-services-show__div-1">
            <nav aria-label="Breadcrumb">
                <ol class="d-flex flex-wrap align-items-center site-services-show__element-5">
                    <li><a class="site-services-show__action-6" href="{{ LocalizedUrl::route('home') }}">Trang chủ</a></li><li aria-hidden="true">/</li>
                    <li><a class="site-services-show__action-6" href="{{ LocalizedUrl::route('services.index') }}">{{ __('site.services') }}</a></li>
                    @if ($service->category)<li aria-hidden="true">/</li><li><a class="site-services-show__action-6" href="{{ LocalizedUrl::serviceCategory($service->category) }}">{{ $service->category->name }}</a></li>@endif
                    <li aria-hidden="true">/</li><li class="fw-medium site-services-show__li-7" aria-current="page">{{ $service->title }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($service->excerpt)
        <section id="noi-dung-dich-vu" class="service-intro section-space">
            <div class="site-container service-intro__grid w-100 mx-auto site-services-show__div-1">
                <div class="service-intro__copy">
                    <p class="site-eyebrow">Giải pháp PCCC theo nhu cầu thực tế</p>
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
                    <a class="btn btn-primary button-primary" href="{{ LocalizedUrl::route('contact', ['service' => $service->id]) }}">Nhận tư vấn dịch vụ <span aria-hidden="true">→</span></a>
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
        <div class="site-container w-100 mx-auto site-services-show__div-1">
            <header class="resource-list-heading service-equipment__heading">
                <div>
                    <p class="resource-list-heading__eyebrow">Thiết bị & hạng mục liên quan</p>
                    <h2 class="display-title text-uppercase site-services-show__heading-8">Vật tư, thiết bị PCCC đồng bộ</h2>
                    <p class="site-services-show__copy-9">Các thiết bị được lựa chọn theo hồ sơ, tiêu chuẩn áp dụng và điều kiện vận hành của từng công trình.</p>
                </div>
                <a class="section-link" href="{{ LocalizedUrl::route('products.index') }}">Xem toàn bộ thiết bị <span aria-hidden="true">→</span></a>
            </header>
            <div class="service-equipment-grid">
                @forelse ($featuredProducts as $product)
                    <article class="service-equipment-card">
                        <a class="service-equipment-card__media" href="{{ LocalizedUrl::product($product) }}" aria-label="Xem {{ $product->title }}">
                            @if ($product->image_url ?: $defaultBannerUrl)
                                <img src="{{ $product->image_url ?: $defaultBannerUrl }}" alt="{{ $product->title }}" loading="lazy">
                            @else
                                <span class="image-placeholder">DV</span>
                            @endif
                        </a>
                        <div class="service-equipment-card__body">
                            @if ($product->category)<p class="service-equipment-card__category">{{ $product->category->name }}</p>@endif
                            <h3><a href="{{ LocalizedUrl::product($product) }}">{{ $product->title }}</a></h3>
                            <a class="service-equipment-card__link" href="{{ LocalizedUrl::product($product) }}">Xem thiết bị <span aria-hidden="true">→</span></a>
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
            <div class="site-container w-100 mx-auto site-services-show__div-1">
                <article class="article-prose service-content-body">
                    {!! $service->body_html !!}
                </article>
            </div>
        </section>
    @endif

    @include('frontend.services.partials.stats')
    @include('frontend.services.partials.testimonials')

    @if ($backstageImages !== [])
        <section class="resource-related-section site-services-show__section-10" id="hau-truong">
            <div class="site-container w-100 mx-auto site-services-show__div-1">
                <header class="mx-auto text-center site-services-show__element-11"><h2 class="display-title site-services-show__heading-12">HÌNH ẢNH HẬU TRƯỜNG</h2><p class="site-services-show__copy-13">Tư liệu được chọn lọc từ quá trình triển khai {{ mb_strtolower($service->title) }}.</p></header>
                <div class="resource-detail-gallery__grid site-services-show__div-14">
                    @foreach ($backstageImages as $imageUrl)
                        <a class="resource-detail-gallery__item" href="{{ $imageUrl }}" target="_blank" rel="noopener" aria-label="Mở ảnh hậu trường {{ $loop->iteration }} của {{ $service->title }}"><img src="{{ $imageUrl }}" alt="{{ $service->title }} — ảnh hậu trường {{ $loop->iteration }}" loading="lazy"><span class="resource-detail-gallery__zoom" aria-hidden="true">↗</span></a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($faqItems->isNotEmpty())
        <section class="home-faq section-space" id="cau-hoi-thuong-gap">
            <div class="site-container w-100 mx-auto site-services-show__div-1"><header class="home-faq__header"><h2 class="display-title site-services-show__heading-12">Câu hỏi thường gặp</h2><p class="site-services-show__copy-16">Thông tin cần biết trước khi triển khai dịch vụ PCCC.</p></header><div class="home-faq__list">@foreach ($faqItems as $item)<details class="home-faq__item" @if ($loop->first) open @endif><summary class="home-faq__question"><span>{{ $item['question'] }}</span><span class="home-faq__indicator" aria-hidden="true">+</span></summary><div class="home-faq__answer"><p>{{ $item['answer'] }}</p></div></details>@endforeach</div></div>
        </section>
    @endif

    <x-service-consultation-form :content="$service" />

    <section class="resource-related-section" id="phan-hoi">
        <div class="site-container w-100 mx-auto site-services-show__div-1"><header class="mx-auto text-center site-services-show__element-11"><h2 class="display-title site-services-show__heading-12">Đánh giá từ khách hàng</h2>@if ($ratingSummary['count'])<p class="site-services-show__copy-13"><strong class="site-services-show__element-17">{{ number_format($ratingSummary['average'], 1) }}/5</strong> từ {{ $ratingSummary['count'] }} đánh giá đã được duyệt.</p>@else<p class="site-services-show__copy-13">Những đánh giá đầu tiên sẽ được hiển thị sau khi đội ngũ kiểm duyệt.</p>@endif</header><div class="site-services-show__div-18"><div class="d-grid site-services-show__div-19">@forelse ($service->approvedComments as $comment)<article class="d-flex site-services-show__article-20"><span class="d-grid flex-shrink-0 fw-bold site-services-show__copy-21" aria-hidden="true">{{ mb_strtoupper(mb_substr($comment->author_name, 0, 1)) }}</span><div class="site-services-show__div-22"><div class="d-flex flex-wrap align-items-baseline site-services-show__div-23"><h3 class="fw-semibold site-services-show__heading-24">{{ $comment->author_name }}</h3><time class="fw-medium site-services-show__copy-25" datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y') }}</time></div>@if ($comment->rating)<p class="site-services-show__copy-26" aria-label="{{ $comment->rating }} trên 5 sao">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $comment->rating ? '' : 'site-services-show__element-27' }}" aria-hidden="true">★</span>@endfor</p>@endif<p class="site-services-show__copy-28">{{ $comment->body }}</p></div></article>@empty<p class="site-services-show__copy-29">Chưa có đánh giá nào. Anh/chị có thể là người đầu tiên chia sẻ trải nghiệm.</p>@endforelse</div><x-comment-form :service="$service" :rating-enabled="true" :compact="true" /></div></div>
    </section>

    @if ($relatedServices->isNotEmpty())
        <section class="resource-related-section" id="dich-vu-tham-khao"><div class="site-container w-100 mx-auto flex-column site-services-show__div-30"><h2 class="display-title site-services-show__heading-12">Dịch vụ tham khảo</h2><a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">Xem tất cả dịch vụ <span aria-hidden="true">→</span></a></div><div class="site-container w-100 mx-auto site-services-show__div-31">@foreach ($relatedServices as $relatedService) @include('frontend.partials.service-card', ['service' => $relatedService]) @endforeach</div></section>
    @endif
@endsection
