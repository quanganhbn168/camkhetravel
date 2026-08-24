@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    @if ($usesLandingLayout)
        <section class="section-space bg-white pb-10 md:pb-14">
            <div class="site-shell">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center justify-center gap-x-2 gap-y-1 text-xs text-slate-500">
                        <li><a class="hover:text-ink" href="{{ LocalizedUrl::route('home') }}">Trang chủ</a></li><li aria-hidden="true">/</li>
                        <li><a class="hover:text-ink" href="{{ LocalizedUrl::route('services.index') }}">{{ __('site.services') }}</a></li>
                        @if ($service->category)<li aria-hidden="true">/</li><li><a class="hover:text-ink" href="{{ LocalizedUrl::slug($service->category->slug) }}">{{ $service->category->name }}</a></li>@endif
                    </ol>
                </nav>
                <h1 class="mx-auto mt-10 max-w-5xl text-center font-display text-4xl leading-[1.08] font-bold tracking-[-0.045em] text-red-600 md:text-6xl">{{ $service->title }}</h1>
                @if ($introMediaUrl)
                    <div @if ($introMediaIsPrice) id="bang-gia-dich-vu" @endif class="mx-auto mt-10 max-w-6xl overflow-hidden rounded-[1.5rem] border border-slate-200 bg-mist">
                        @if (! $introMediaIsPrice || $pricingMediaIsImage)
                            <img class="h-auto w-full" src="{{ $introMediaUrl }}" alt="{{ $introMediaIsPrice ? 'Bảng giá '.$service->title : $service->title }}" loading="eager">
                        @else
                            <a class="flex min-h-56 flex-col items-center justify-center gap-3 p-8 text-center text-ink hover:text-accent" href="{{ $introMediaUrl }}" target="_blank" rel="noopener">
                                <span class="text-4xl" aria-hidden="true">↗</span>
                                <span class="font-bold">Mở bảng giá dịch vụ</span>
                                <span class="text-sm text-slate-600">Xem hoặc tải file bảng giá.</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @else
    <section class="resource-detail-hero resource-detail-hero--service">
        @if ($service->image_url)
            <img class="resource-detail-hero__image" src="{{ $service->image_url }}" alt="" aria-hidden="true">
        @endif
        <div class="resource-detail-hero__overlay"></div>
        <div class="site-shell resource-detail-hero__content">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-white/65">
                    <li><a class="hover:text-white" href="{{ LocalizedUrl::route('home') }}">Trang chủ</a></li><li aria-hidden="true">/</li>
                    <li><a class="hover:text-white" href="{{ LocalizedUrl::route('services.index') }}">{{ __('site.services') }}</a></li>
                    @if ($service->category)<li aria-hidden="true">/</li><li><a class="hover:text-white" href="{{ LocalizedUrl::slug($service->category->slug) }}">{{ $service->category->name }}</a></li>@endif
                </ol>
            </nav>
            @if ($service->category)<p class="eyebrow mt-8 text-primary-soft">{{ $service->category->name }}</p>@endif
            <h1 class="mt-3 max-w-3xl font-display text-4xl leading-[1.08] tracking-[-0.045em] text-white md:text-6xl">{{ $service->title }}</h1>
            @if ($service->excerpt)<p class="mt-5 max-w-2xl text-sm leading-7 text-white/75 md:text-base md:leading-8">{{ $service->excerpt }}</p>@endif
            <div class="mt-8 flex flex-wrap gap-3"><a class="button-primary" href="{{ LocalizedUrl::route('contact', ['landing' => $service->id]) }}">Nhận tư vấn ngay <span aria-hidden="true">→</span></a><a class="button-secondary" href="#noi-dung-dich-vu">Khám phá dịch vụ <span aria-hidden="true">↓</span></a></div>
        </div>
    </section>
    @endif

    @if ($isLegacyLanding && filled(strip_tags((string) $service->body_html)))
        <section id="noi-dung-dich-vu" class="border-y border-slate-100 bg-mist/55 py-12 md:py-16">
            <div class="site-shell"><article class="article-prose mx-auto max-w-4xl">{!! $service->body_html !!}</article></div>
        </section>
    @elseif (! $usesLandingLayout || filled(strip_tags((string) $service->body_html)) || $service->excerpt)
    <section id="noi-dung-dich-vu" class="resource-detail-content">
        <div class="site-shell grid gap-12 lg:grid-cols-[minmax(0,1fr)_19rem] lg:items-start">
            <article class="article-prose max-w-3xl">
                <p class="eyebrow">Về dịch vụ</p>
                <h2 class="!mt-3">Giải pháp được xây dựng từ mục tiêu thực tế.</h2>
                @if (filled(strip_tags((string) $service->body_html)))
                    {!! $service->body_html !!}
                @elseif ($service->excerpt)
                    <p>{{ $service->excerpt }}</p>
                @endif
            </article>
            <aside class="resource-detail-aside lg:sticky lg:top-28">
                <p class="eyebrow">Trao đổi cùng THT</p>
                <h2 class="mt-3 text-2xl leading-tight font-bold text-ink">Cần đề xuất phù hợp cho nhu cầu của anh/chị?</h2>
                <p class="mt-4 text-sm leading-7 text-slate-600">Gửi yêu cầu để đội ngũ THT Media cùng làm rõ phạm vi và hướng triển khai.</p>
                <a class="button-dark mt-6 w-full" href="{{ LocalizedUrl::route('contact', ['landing' => $service->id]) }}">Gửi yêu cầu tư vấn <span aria-hidden="true">→</span></a>
                @if ($service->category)<a class="resource-detail-aside__link" href="{{ LocalizedUrl::slug($service->category->slug) }}">Xem toàn bộ {{ mb_strtolower($service->category->name) }} <span aria-hidden="true">→</span></a>@endif
            </aside>
        </div>
    </section>
    @endif

    @if (! $usesLandingLayout)
    <section class="border-y border-slate-100 bg-mist/55 py-8 md:py-10">
        <div class="site-shell grid gap-4 md:grid-cols-2">
            <a class="group rounded-[1.5rem] border border-slate-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-primary hover:shadow-[0_16px_36px_rgba(31,43,37,0.10)]" href="{{ $pricingMediaUrl ? '#bang-gia-dich-vu' : LocalizedUrl::route('pricing.index', ['landing' => $service->id]) }}">
                <p class="eyebrow text-accent">Media & đầu tư</p>
                <h2 class="mt-3 text-2xl font-bold leading-tight text-ink">Bảng giá dịch vụ</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $pricingMediaUrl ? 'Xem bảng giá Media được quản lý riêng cho dịch vụ này.' : 'Xem các gói và mức đầu tư tham khảo phù hợp với dịch vụ này.' }}</p>
                <span class="mt-5 inline-flex text-sm font-bold text-accent">{{ $pricingMediaUrl ? 'Xem bảng giá' : 'Xem các gói giá' }} <span class="ml-2 transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
            </a>
            <a class="group rounded-[1.5rem] border border-slate-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-primary hover:shadow-[0_16px_36px_rgba(31,43,37,0.10)]" href="{{ $backstageProjects->isNotEmpty() ? LocalizedUrl::route('projects.index', ['landing' => $service->id]) : LocalizedUrl::route('projects.index') }}">
                <p class="eyebrow text-accent">Dự án thực tế</p>
                <h2 class="mt-3 text-2xl font-bold leading-tight text-ink">Hậu trường dự án</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $backstageProjects->isNotEmpty() ? 'Khám phá các dự án và tư liệu hậu trường đã được gắn với dịch vụ này.' : 'Khám phá các dự án THT Media đã triển khai.' }}</p>
                <span class="mt-5 inline-flex text-sm font-bold text-accent">Xem dự án <span class="ml-2 transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
            </a>
        </div>
    </section>
    @endif

    @if ($pricingMediaUrl && ! $usesLandingLayout)
        <section id="bang-gia-dich-vu" class="section-space bg-white">
            <div class="site-shell grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
                <div class="overflow-hidden rounded-[1.5rem] border border-slate-200 bg-mist">
                    @if ($pricingMediaIsImage)
                        <img class="h-auto w-full" src="{{ $pricingMediaUrl }}" alt="Bảng giá {{ $service->title }}" loading="lazy">
                    @else
                        <a class="flex min-h-56 flex-col items-center justify-center gap-3 p-8 text-center text-ink hover:text-accent" href="{{ $pricingMediaUrl }}" target="_blank" rel="noopener">
                            <span class="text-4xl" aria-hidden="true">↗</span>
                            <span class="font-bold">Mở bảng giá dịch vụ</span>
                            <span class="text-sm text-slate-600">Xem hoặc tải file bảng giá.</span>
                        </a>
                    @endif
                </div>
                <aside class="rounded-[1.5rem] bg-ink p-7 text-white lg:sticky lg:top-28">
                    <p class="eyebrow text-primary-soft">Bảng giá dịch vụ</p>
                    <h2 class="mt-3 text-2xl font-bold leading-tight">Mức đầu tư tham khảo</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300">Bảng giá có thể được điều chỉnh theo phạm vi và thời điểm triển khai thực tế.</p>
                    <a class="button-primary mt-6 w-full" href="{{ LocalizedUrl::route('contact', ['landing' => $service->id]) }}">Nhận tư vấn chi tiết <span aria-hidden="true">→</span></a>
                    <a class="mt-5 inline-flex text-sm font-semibold text-primary-soft hover:text-white" href="{{ LocalizedUrl::route('pricing.index', ['landing' => $service->id]) }}">Xem các gói giá <span class="ml-2" aria-hidden="true">→</span></a>
                </aside>
            </div>
        </section>
    @endif

    @if ($referenceVideos !== [] || $galleryImages !== [])
        <section class="resource-related-section border-y border-slate-100 bg-mist/55">
            <div class="site-shell">
                <h2 class="display-title text-center text-3xl leading-tight md:text-4xl">Tài liệu tham khảo</h2>
                @if ($referenceVideos !== [])
                    <p class="eyebrow mt-9 text-center">Video</p>
                    <div class="mt-5 grid gap-5 md:grid-cols-3">
                        @foreach ($referenceVideos as $video)
                            <a class="group overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:border-primary hover:shadow-[0_16px_36px_rgba(31,43,37,0.10)]" href="{{ $video['url'] }}" target="_blank" rel="noopener">
                                <div class="relative aspect-video bg-ink">
                                    @if ($video['thumbnail_url'])<img class="h-full w-full object-cover opacity-85 transition duration-300 group-hover:scale-105 group-hover:opacity-100" src="{{ $video['thumbnail_url'] }}" alt="" loading="lazy">@endif
                                    <span class="absolute inset-0 flex items-center justify-center"><span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/95 pl-0.5 text-ink shadow-lg" aria-hidden="true">▶</span></span>
                                </div>
                                <h3 class="p-5 text-base font-bold leading-6 text-ink">{{ $video['title'] }}</h3>
                            </a>
                        @endforeach
                    </div>
                @endif
                @if ($galleryImages !== [])
                    <p class="eyebrow mt-12 text-center">Hình ảnh</p>
                    <div class="resource-detail-gallery__grid mt-5">
                        @foreach ($galleryImages as $imageUrl)
                            <a class="resource-detail-gallery__item group" href="{{ $imageUrl }}" target="_blank" rel="noopener" aria-label="Mở ảnh {{ $loop->iteration }} của {{ $service->title }}">
                                <img src="{{ $imageUrl }}" alt="{{ $service->title }} — ảnh {{ $loop->iteration }}" loading="lazy">
                                <span class="resource-detail-gallery__zoom" aria-hidden="true">↗</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($backstageGalleryImages !== [])
        @include('frontend.partials.resource-detail-gallery', [
            'hideGalleryEyebrow' => true,
            'galleryTitle' => 'HÌNH ẢNH HẬU TRƯỜNG',
            'galleryAlt' => $service->title,
            'galleryImages' => $backstageGalleryImages,
        ])
    @endif

    @if ($backstageProjects->isNotEmpty())
        <section id="hau-truong" class="resource-related-section">
            <div class="site-shell flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div><p class="eyebrow">Hậu trường dự án</p><h2 class="display-title mt-3 text-3xl leading-tight md:text-4xl">Những dự án đã triển khai cùng dịch vụ này</h2></div>
                <a class="section-link" href="{{ LocalizedUrl::route('projects.index', ['landing' => $service->id]) }}">Xem tất cả <span aria-hidden="true">→</span></a>
            </div>
            <div class="site-shell mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">@foreach ($backstageProjects as $project) @include('frontend.partials.project-card') @endforeach</div>
        </section>
    @endif

    @if ($relatedServices->isNotEmpty())
        <section class="resource-related-section">
            <div class="site-shell flex flex-col gap-5 md:flex-row md:items-end md:justify-between"><div><h2 class="display-title text-3xl leading-tight md:text-4xl">Dịch vụ khác</h2></div><a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">Xem tất cả dịch vụ <span aria-hidden="true">→</span></a></div>
            <div class="site-shell mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">@foreach ($relatedServices as $service) @include('frontend.partials.service-card') @endforeach</div>
        </section>
    @endif
@endsection
