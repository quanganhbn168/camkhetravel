@extends('layouts.master')

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
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 resource-detail-hero__content">
            <h1 class="max-w-4xl font-display text-4xl leading-[1.08] tracking-[-0.045em] text-white md:text-6xl">{{ $service->title }}</h1>
            <div class="mt-8 flex flex-wrap justify-center gap-3"><a class="button-primary" href="{{ LocalizedUrl::route('contact', ['service' => $service->id]) }}">Nhận tư vấn ngay <span aria-hidden="true">→</span></a><a class="button-secondary" href="#noi-dung-dich-vu">Khám phá dịch vụ <span aria-hidden="true">↓</span></a></div>
        </div>
    </section>

    <div class="border-b border-slate-100 bg-white py-4">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500">
                    <li><a class="transition hover:text-accent" href="{{ LocalizedUrl::route('home') }}">Trang chủ</a></li><li aria-hidden="true">/</li>
                    <li><a class="transition hover:text-accent" href="{{ LocalizedUrl::route('services.index') }}">{{ __('site.services') }}</a></li>
                    @if ($service->category)<li aria-hidden="true">/</li><li><a class="transition hover:text-accent" href="{{ LocalizedUrl::serviceCategory($service->category) }}">{{ $service->category->name }}</a></li>@endif
                    <li aria-hidden="true">/</li><li class="font-medium text-ink" aria-current="page">{{ $service->title }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @include('frontend.services.partials.pricing-media')

    @if ($service->excerpt)
        <section id="noi-dung-dich-vu" class="resource-detail-content">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
                <article class="article-prose service-content-body">
                    <p class="service-content-lead">{{ $service->excerpt }}</p>
                </article>
            </div>
        </section>
    @endif

    @include('frontend.services.partials.benefits')
    @include('frontend.services.partials.reference-videos')
    @include('frontend.services.partials.pricing-plans')
    @include('frontend.services.partials.process')
    @include('frontend.services.partials.commitments')

    @if (filled(strip_tags((string) $service->body_html)))
        <section id="noi-dung-chi-tiet" class="resource-detail-content">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
                <article class="article-prose service-content-body">
                    {!! $service->body_html !!}
                </article>
            </div>
        </section>
    @endif

    @include('frontend.services.partials.stats')
    @include('frontend.services.partials.testimonials')
    @include('frontend.services.partials.projects')

    @if ($backstageImages !== [])
        <section class="resource-related-section border-y border-slate-100 bg-mist/55" id="hau-truong">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
                <header class="mx-auto max-w-2xl text-center"><h2 class="display-title text-3xl leading-tight md:text-4xl">HÌNH ẢNH HẬU TRƯỜNG</h2><p class="mt-4 text-sm leading-7 text-slate-600">Tư liệu được chọn lọc từ quá trình triển khai {{ mb_strtolower($service->title) }}.</p></header>
                <div class="resource-detail-gallery__load-more" x-data="{ visible: 6 }">
                    <div class="resource-detail-gallery__grid mt-7">
                        @foreach ($backstageImages as $imageUrl)
                            <template x-if="visible > {{ $loop->index }}">
                                <a class="resource-detail-gallery__item group glightbox" href="{{ $imageUrl }}" data-type="image" data-gallery="service-backstage-images-{{ $service->id }}" data-title="{{ $service->title }} — ảnh hậu trường {{ $loop->iteration }}" aria-label="Mở ảnh hậu trường {{ $loop->iteration }} của {{ $service->title }}"><img src="{{ $imageUrl }}" alt="{{ $service->title }} — ảnh hậu trường {{ $loop->iteration }}" loading="lazy"><span class="resource-detail-gallery__zoom" aria-hidden="true">↗</span></a>
                            </template>
                        @endforeach
                    </div>
                    @if (count($backstageImages) > 6)
                        <div class="mt-8 text-center" x-show="visible < {{ count($backstageImages) }}">
                            <button class="button-dark" type="button" @click="visible += 6; $nextTick(() => window.refreshLightboxes?.())">Xem thêm hậu trường <span aria-hidden="true">↓</span></button>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($faqItems->isNotEmpty())
        <section class="home-faq section-space" id="cau-hoi-thuong-gap">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8"><header class="home-faq__header"><h2 class="display-title text-3xl leading-tight md:text-4xl">{{ $service->faq_title ?: 'Câu hỏi thường gặp' }}</h2>@if ($service->faq_description)<p class="mt-4 text-base leading-8 text-slate-600 md:text-lg">{{ $service->faq_description }}</p>@endif</header><div class="home-faq__list">@foreach ($faqItems as $item)<details class="home-faq__item" @if ($loop->first) open @endif><summary class="home-faq__question"><span>{{ $item['question'] }}</span><span class="home-faq__indicator" aria-hidden="true">+</span></summary><div class="home-faq__answer"><p>{{ $item['answer'] }}</p></div></details>@endforeach</div></div>
        </section>
    @endif

    <x-service-consultation-form :content="$service" />

    <section class="resource-related-section" id="phan-hoi">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8"><header class="mx-auto max-w-2xl text-center"><h2 class="display-title text-3xl leading-tight md:text-4xl">Đánh giá từ khách hàng</h2>@if ($ratingSummary['count'])<p class="mt-4 text-sm leading-7 text-slate-600"><strong class="text-lg text-ink">{{ number_format($ratingSummary['average'], 1) }}/5</strong> từ {{ $ratingSummary['count'] }} đánh giá đã được duyệt.</p>@else<p class="mt-4 text-sm leading-7 text-slate-600">Những đánh giá đầu tiên sẽ được hiển thị sau khi đội ngũ kiểm duyệt.</p>@endif</header><div class="mt-9 grid gap-8 border-t border-slate-200 pt-8 lg:grid-cols-[minmax(0,.85fr)_minmax(0,1.15fr)]"><div class="grid content-start gap-6">@forelse ($service->approvedComments as $comment)<article class="flex gap-4"><span class="grid size-11 shrink-0 place-items-center rounded-full bg-mist text-sm font-bold text-primary" aria-hidden="true">{{ mb_strtoupper(mb_substr($comment->author_name, 0, 1)) }}</span><div class="min-w-0"><div class="flex flex-wrap items-baseline gap-x-3 gap-y-1"><h3 class="text-sm font-semibold text-ink">{{ $comment->author_name }}</h3><time class="text-xs font-medium text-slate-400" datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y') }}</time></div>@if ($comment->rating)<p class="mt-1 text-sm tracking-[0.08em] text-primary" aria-label="{{ $comment->rating }} trên 5 sao">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $comment->rating ? '' : 'text-slate-200' }}" aria-hidden="true">★</span>@endfor</p>@endif<p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $comment->body }}</p></div></article>@empty<p class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm leading-7 text-slate-500">Chưa có đánh giá nào. Anh/chị có thể là người đầu tiên chia sẻ trải nghiệm.</p>@endforelse</div><x-comment-form :service="$service" :rating-enabled="true" :compact="true" /></div></div>
    </section>

    @if ($relatedServices->isNotEmpty())
        <section class="resource-related-section" id="dich-vu-tham-khao"><div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 flex flex-col gap-5 md:flex-row md:items-end md:justify-between"><h2 class="display-title text-3xl leading-tight md:text-4xl">Dịch vụ tham khảo</h2><a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">Xem tất cả dịch vụ <span aria-hidden="true">→</span></a></div><div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">@foreach ($relatedServices as $relatedService) @include('frontend.partials.service-card', ['service' => $relatedService]) @endforeach</div></section>
    @endif
@endsection
