@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    @if ($usesBuilderLayout)
        @include($landingTemplateView)
    @else
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
            @if ($service->category)<p class="mt-8 text-sm font-semibold text-primary-soft">{{ $service->category->name }}</p>@endif
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
                <h2 class="!mt-0">Giải pháp được xây dựng từ mục tiêu thực tế.</h2>
                @if (filled(strip_tags((string) $service->body_html)))
                    {!! $service->body_html !!}
                @elseif ($service->excerpt)
                    <p>{{ $service->excerpt }}</p>
                @endif
            </article>
            <aside class="resource-detail-aside lg:sticky lg:top-28">
                <h2 class="text-2xl leading-tight font-bold text-ink">Cần đề xuất phù hợp cho nhu cầu của anh/chị?</h2>
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
                <h2 class="text-2xl font-bold leading-tight text-ink">Bảng giá dịch vụ</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $pricingMediaUrl ? 'Xem bảng giá Media được quản lý riêng cho dịch vụ này.' : 'Xem các gói và mức đầu tư tham khảo phù hợp với dịch vụ này.' }}</p>
                <span class="mt-5 inline-flex text-sm font-bold text-accent">{{ $pricingMediaUrl ? 'Xem bảng giá' : 'Xem các gói giá' }} <span class="ml-2 transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
            </a>
            <a class="group rounded-[1.5rem] border border-slate-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-primary hover:shadow-[0_16px_36px_rgba(31,43,37,0.10)]" href="{{ $backstageProjects->isNotEmpty() ? LocalizedUrl::route('projects.index', ['landing' => $service->id]) : LocalizedUrl::route('projects.index') }}">
                <h2 class="text-2xl font-bold leading-tight text-ink">Hậu trường dự án</h2>
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
                    <h2 class="text-2xl font-bold leading-tight">Mức đầu tư tham khảo</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300">Bảng giá có thể được điều chỉnh theo phạm vi và thời điểm triển khai thực tế.</p>
                    <a class="button-primary mt-6 w-full" href="{{ LocalizedUrl::route('contact', ['landing' => $service->id]) }}">Nhận tư vấn chi tiết <span aria-hidden="true">→</span></a>
                    <a class="mt-5 inline-flex text-sm font-semibold text-primary-soft hover:text-white" href="{{ LocalizedUrl::route('pricing.index', ['landing' => $service->id]) }}">Xem các gói giá <span class="ml-2" aria-hidden="true">→</span></a>
                </aside>
            </div>
        </section>
    @endif

    @if ($referenceVideos !== [] || $referenceImages !== [])
        <section class="resource-related-section border-y border-slate-100 bg-mist/55" x-data="{ activeTab: @js($referenceVideos !== [] ? 'video' : 'images'), videoLimit: 3, imageLimit: 6 }">
            <div class="site-shell">
                <header class="mx-auto max-w-2xl text-center">
                    <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">Tài liệu tham khảo</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600">Video và hình ảnh được chọn lọc từ quá trình triển khai {{ mb_strtolower($service->title) }}.</p>
                </header>

                <div class="mt-8 flex flex-wrap justify-center gap-2" role="tablist" aria-label="Loại tài liệu tham khảo">
                    @if ($referenceVideos !== [])
                        <button class="rounded-full border px-5 py-2.5 text-sm font-bold transition" type="button" role="tab" @click="activeTab = 'video'" :aria-selected="(activeTab === 'video').toString()" :class="activeTab === 'video' ? 'border-primary bg-primary text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-primary hover:text-primary'">Video ({{ count($referenceVideos) }})</button>
                    @endif
                    @if ($referenceImages !== [])
                        <button class="rounded-full border px-5 py-2.5 text-sm font-bold transition" type="button" role="tab" @click="activeTab = 'images'" :aria-selected="(activeTab === 'images').toString()" :class="activeTab === 'images' ? 'border-primary bg-primary text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-primary hover:text-primary'">Hình ảnh ({{ count($referenceImages) }})</button>
                    @endif
                </div>

                @if ($referenceVideos !== [])
                    <div class="mt-7 grid gap-5 md:grid-cols-3" role="tabpanel" x-show="activeTab === 'video'" x-transition.opacity>
                        @foreach ($referenceVideos as $video)
                            <a class="glightbox group overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:border-primary hover:shadow-[0_16px_36px_rgba(31,43,37,0.10)]" href="{{ $video['url'] }}" data-type="video" data-gallery="service-reference-videos-{{ $service->id }}" data-title="{{ $video['title'] }}" target="_blank" rel="noopener" aria-label="Xem video {{ $video['title'] }}" x-show="videoLimit > {{ $loop->index }}" x-transition.opacity>
                                <div class="relative aspect-video bg-ink">
                                    @if ($video['thumbnail_url'])<img class="h-full w-full object-cover opacity-85 transition duration-300 group-hover:scale-105 group-hover:opacity-100" src="{{ $video['thumbnail_url'] }}" alt="" loading="lazy">@endif
                                    <span class="absolute inset-0 flex items-center justify-center"><span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/95 pl-0.5 text-ink shadow-lg" aria-hidden="true">▶</span></span>
                                </div>
                                <h3 class="p-5 text-base font-bold leading-6 text-ink">{{ $video['title'] }}</h3>
                            </a>
                        @endforeach
                    </div>
                    @if (count($referenceVideos) > 3)
                        <div class="mt-7 text-center" x-show="activeTab === 'video' && videoLimit < {{ count($referenceVideos) }}"><button class="button-dark" type="button" @click="videoLimit += 3">Xem thêm video <span aria-hidden="true">↓</span></button></div>
                    @endif
                @endif

                @if ($referenceImages !== [])
                    <div class="resource-detail-gallery__grid mt-7" role="tabpanel" x-show="activeTab === 'images'" x-transition.opacity>
                        @foreach ($referenceImages as $imageUrl)
                            <a class="glightbox resource-detail-gallery__item group" href="{{ $imageUrl }}" data-type="image" data-gallery="service-reference-images-{{ $service->id }}" data-title="{{ $service->title }} — ảnh {{ $loop->iteration }}" target="_blank" rel="noopener" aria-label="Mở ảnh {{ $loop->iteration }} của {{ $service->title }}" x-show="imageLimit > {{ $loop->index }}" x-transition.opacity>
                                <img src="{{ $imageUrl }}" alt="{{ $service->title }} — ảnh {{ $loop->iteration }}" loading="lazy">
                                <span class="resource-detail-gallery__zoom" aria-hidden="true">↗</span>
                            </a>
                        @endforeach
                    </div>
                    @if (count($referenceImages) > 6)
                        <div class="mt-7 text-center" x-show="activeTab === 'images' && imageLimit < {{ count($referenceImages) }}"><button class="button-dark" type="button" @click="imageLimit += 6">Xem thêm hình ảnh <span aria-hidden="true">↓</span></button></div>
                    @endif
                @endif
            </div>
        </section>
    @endif

    @if ($faqItems->isNotEmpty())
        <section class="home-faq section-space" id="cau-hoi-thuong-gap">
            <div class="site-shell">
                <header class="home-faq__header">
                    <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">{{ $service->faq_title ?: 'Câu hỏi thường gặp' }}</h2>
                    @if ($service->faq_description)<p class="mt-4 text-base leading-8 text-slate-600 md:text-lg">{{ $service->faq_description }}</p>@endif
                </header>
                <div class="home-faq__list">
                    @foreach ($faqItems as $item)
                        <details class="home-faq__item" @if ($loop->first) open @endif>
                            <summary class="home-faq__question"><span>{{ $item['question'] }}</span><span class="home-faq__indicator" aria-hidden="true">+</span></summary>
                            <div class="home-faq__answer"><p>{{ $item['answer'] }}</p></div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-service-consultation-form :service="$service" />

    <section class="resource-related-section" id="phan-hoi">
        <div class="site-shell">
            <header class="mx-auto max-w-2xl text-center">
                <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">Đánh giá từ khách hàng</h2>
                @if ($ratingSummary['count'])
                    <p class="mt-4 text-sm leading-7 text-slate-600"><strong class="text-lg text-ink">{{ number_format($ratingSummary['average'], 1) }}/5</strong> từ {{ $ratingSummary['count'] }} đánh giá đã được duyệt.</p>
                @else
                    <p class="mt-4 text-sm leading-7 text-slate-600">Những đánh giá đầu tiên sẽ được hiển thị sau khi đội ngũ kiểm duyệt.</p>
                @endif
            </header>

            <div class="mt-9 grid gap-8 border-t border-slate-200 pt-8 lg:grid-cols-[minmax(0,.85fr)_minmax(0,1.15fr)]">
                <div class="grid content-start gap-6">
                    @forelse ($service->approvedComments as $comment)
                        <article class="flex gap-4">
                            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-mist text-sm font-bold text-primary" aria-hidden="true">{{ mb_strtoupper(mb_substr($comment->author_name, 0, 1)) }}</span>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1"><h3 class="text-sm font-semibold text-ink">{{ $comment->author_name }}</h3><time class="text-xs font-medium text-slate-400" datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y') }}</time></div>
                                @if ($comment->rating)<p class="mt-1 text-sm tracking-[0.08em] text-primary" aria-label="{{ $comment->rating }} trên 5 sao">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $comment->rating ? '' : 'text-slate-200' }}" aria-hidden="true">★</span>@endfor</p>@endif
                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $comment->body }}</p>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm leading-7 text-slate-500">Chưa có đánh giá nào. Anh/chị có thể là người đầu tiên chia sẻ trải nghiệm.</p>
                    @endforelse
                </div>

                <x-comment-form :landing="$service" :rating-enabled="true" :compact="true" />
            </div>
        </div>
    </section>

    @if ($backstageProjects->isNotEmpty())
        <section id="hau-truong" class="resource-related-section">
            <div class="site-shell flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div><h2 class="display-title text-3xl leading-tight md:text-4xl">Những dự án đã triển khai cùng dịch vụ này</h2></div>
                <a class="section-link" href="{{ LocalizedUrl::route('projects.index', ['landing' => $service->id]) }}">Xem tất cả <span aria-hidden="true">→</span></a>
            </div>
            <div class="site-shell mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">@foreach ($backstageProjects as $project) @include('frontend.partials.project-card') @endforeach</div>
        </section>
    @endif

    @if ($relatedServices->isNotEmpty())
        <section class="resource-related-section">
            <div class="site-shell flex flex-col gap-5 md:flex-row md:items-end md:justify-between"><div><h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">Khám phá thêm dịch vụ</h2></div><a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">Xem tất cả dịch vụ <span aria-hidden="true">→</span></a></div>
            <div class="site-shell mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">@foreach ($relatedServices as $service) @include('frontend.partials.service-card') @endforeach</div>
        </section>
    @endif
    @endif
@endsection
