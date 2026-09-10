@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

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

    $contactBranches = collect($website->branches ?? [])
        ->filter(fn ($branch) => is_array($branch) && ($branch['is_active'] ?? true) && filled($branch['address'] ?? null))
        ->values();
@endphp

@section('body_class', 'home-page min-h-screen bg-white')

@section('content')
    <section class="pccc-hero brand-gradient-dark relative isolate w-full overflow-hidden text-white" data-hero-section>
        @forelse ($heroSlides as $slide)
            @if ($loop->first)
                <div class="swiper hero-swiper w-full" data-hero-swiper>
                    <div class="swiper-wrapper">
            @endif

            <article class="pccc-hero__slide swiper-slide relative h-auto overflow-hidden">
                @if ($slide['has_content'])
                    <div class="pccc-hero__veil hero-brand-glow absolute inset-0"></div>
                @endif
                @if ($slide['image_url'])
                    <img class="pccc-hero__image" src="{{ $slide['image_url'] }}" alt="" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                @endif

                @if ($slide['video_url'])
                    <a class="hero-video-play glightbox" href="{{ $slide['video_url'] }}" data-type="video" data-source="{{ $slide['video_source'] === 'youtube' ? 'youtube' : 'local' }}" data-gallery="hero-video-{{ $loop->index }}" data-title="{{ $slide['title'] ?: 'Video DVTEC' }}" target="_blank" rel="noopener" aria-label="Phát video {{ $slide['title'] ?: 'DVTEC' }}">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.1v13.8L19 12 8 5.1Z"/></svg>
                    </a>
                @endif

                @if ($slide['has_content'])
                    <div class="pccc-hero__inner site-container w-full max-w-7xl mx-auto px-4 lg:px-8 relative z-10 grid items-end py-16 md:py-24 lg:py-28">
                        <div class="pccc-hero__copy max-w-4xl pb-24 lg:pb-16">
                            @if ($slide['title'])
                                <h2 class="font-display max-w-4xl text-3xl leading-[1.12] tracking-[-0.045em] text-white sm:text-4xl lg:text-6xl">{{ $slide['title'] }}</h2>
                            @endif
                            @if ($slide['description'])<p class="mt-7 max-w-2xl text-base leading-8 text-slate-300 md:text-lg">{{ $slide['description'] }}</p>@endif
                            @if ($slide['has_primary_cta'] || $slide['has_secondary_cta'])
                                <div class="mt-9 flex flex-wrap gap-3">
                                    @if ($slide['has_primary_cta'])
                                        <a class="button-primary" href="{{ $slide['primary_url'] }}">{{ $slide['primary_label'] }} <span aria-hidden="true">↗</span></a>
                                    @endif
                                    @if ($slide['has_secondary_cta'])
                                        <a class="button-secondary" href="{{ $slide['secondary_url'] }}">{{ $slide['secondary_label'] }}</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($slide['image_url'])
                        <div class="hero-image-overlay" aria-hidden="true"></div>
                    @endif
                @endif
            </article>

            @if ($loop->last)
                    </div>
                </div>
            @endif
        @empty
            <div class="pccc-hero__veil hero-brand-glow absolute inset-0"></div>
            <div class="pccc-hero__inner site-container w-full max-w-7xl mx-auto px-4 lg:px-8 relative grid items-end py-24 md:py-32 lg:py-40">
                <div class="pccc-hero__copy max-w-4xl pb-16">
                    <h2 class="font-display max-w-4xl text-4xl leading-[1.12] tracking-[-0.045em] text-white lg:text-6xl">Kiến tạo hệ thống PCCC an toàn, đồng bộ và bền vững.</h2>
                </div>
            </div>
        @endforelse
    </section>

    <section class="border-y border-slate-200 bg-white py-5" aria-label="Khách hàng và đối tác">
        @if ($marqueePartners->isNotEmpty())
            <div class="partner-marquee">
                <div class="partner-marquee__track">
                    @foreach ($marqueePartners as $partner)
                        @if ($partner->website_url)
                            <a class="partner-marquee__item" href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer">
                                @if ($partner->curatorMedia?->url)<img src="{{ $partner->curatorMedia->url }}" alt="{{ $partner->name }}" loading="lazy">@else {{ $partner->name }} @endif
                            </a>
                        @else
                            <span class="partner-marquee__item">
                                @if ($partner->curatorMedia?->url)<img src="{{ $partner->curatorMedia->url }}" alt="{{ $partner->name }}" loading="lazy">@else {{ $partner->name }} @endif
                            </span>
                        @endif
                    @endforeach
                    @foreach ($marqueePartners as $partner)
                        <span class="partner-marquee__item" aria-hidden="true">
                            @if ($partner->curatorMedia?->url)<img src="{{ $partner->curatorMedia->url }}" alt="" loading="lazy">@else {{ $partner->name }} @endif
                        </span>
                    @endforeach
                </div>
            </div>
        @else
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 flex items-center gap-4 text-sm text-slate-500">
                <span class="size-2 rounded-full bg-primary"></span>
                Thông tin khách hàng và đối tác sẽ xuất hiện tại đây sau khi được thêm trong quản trị.
            </div>
        @endif
    </section>

    <section class="section-space" id="gioi-thieu">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 grid items-center gap-8 lg:grid-cols-2 lg:gap-16">
            <div class="aspect-[4/3] overflow-hidden rounded-[1.5rem] bg-mist shadow-[0_20px_48px_color-mix(in_srgb,var(--site-color-ink)_12%,transparent)]" data-aos="fade-right">
                @if ($aboutImageUrl)
                    <img class="h-full w-full object-cover" src="{{ $aboutImageUrl }}" alt="{{ $about['title'] }}" loading="lazy">
                @else
                    <span class="image-placeholder">DV</span>
                @endif
            </div>
            <div data-aos="fade-left">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">{{ $about['eyebrow'] }}</p>
                <h1 class="display-title mt-3 text-3xl leading-tight uppercase md:text-4xl">{{ $companyName }}</h1>
                @if ($about['title'])<p class="mt-5 max-w-xl text-base leading-8 text-slate-600 md:text-lg">{{ $about['title'] }}</p>@endif
                <p class="mt-4 text-base leading-8 text-slate-600">{{ $about['content'] }}</p>
                <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-4">
                    <a class="button-dark" href="{{ LocalizedUrl::route('about') }}">Xem chi tiết <span aria-hidden="true">↗</span></a>
                    @if ($companyProfileUrl)
                        <a class="inline-flex min-h-12 items-center gap-2 text-sm font-semibold text-ink underline decoration-primary/50 decoration-2 underline-offset-6 hover:text-primary" href="{{ $companyProfileUrl }}" download>
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 21h16"/></svg>
                            Tải hồ sơ năng lực
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 mt-12 grid grid-cols-2 gap-7 md:grid-cols-4 md:gap-10" data-aos="fade-up">
            @foreach ($stats as $stat)
                <div class="home-stat" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                    <p class="home-stat__value" aria-label="{{ $stat['prefix'] }}{{ collect($stat['segments'])->pluck('value')->join('') }}{{ $stat['suffix'] }}">
                        @if (filled($stat['prefix']))<span class="home-stat__affix">{{ $stat['prefix'] }}</span>@endif
                        @foreach ($stat['segments'] as $segment)
                            @if ($segment['is_number'])<span class="home-stat__number" data-count-up="{{ $segment['value'] }}">{{ $segment['value'] }}</span>@else<span class="home-stat__affix">{{ $segment['value'] }}</span>@endif
                        @endforeach
                        @if (filled($stat['suffix']))<span class="home-stat__affix">{{ $stat['suffix'] }}</span>@endif
                    </p>
                    <p class="home-stat__label">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @include('frontend.partials.home-featured-services')

    <section class="section-space border-t border-slate-100" id="dich-vu">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div>
                <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">Các dịch vụ khác</h2>
                <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600 md:text-lg">Khám phá thêm các dịch vụ của DVTEC.</p>
            </div>
            <a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">{{ __('site.all_services') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
        </div>
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($services as $service)
                @include('frontend.partials.service-card')
            @empty
                <p class="rounded-3xl border border-dashed border-slate-300 p-8 text-sm text-slate-500 sm:col-span-2 xl:col-span-4">Dịch vụ sẽ được cập nhật sớm.</p>
            @endforelse
        </div>
    </section>

    <section class="home-projects section-space" id="du-an" x-data="{ activeTab: '{{ $projectTabs->first()['id'] ?? 'all' }}' }">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 flex flex-col justify-between gap-7 lg:flex-row lg:items-end">
            <div>
                <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">Dự án</h2>
                <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600 md:text-lg">Những dự án được kể bằng kết quả và trải nghiệm.</p>
            </div>
            <a class="section-link" href="{{ LocalizedUrl::route('projects.index') }}">{{ __('site.all_projects') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
        </div>

        @if ($projectTabs->isNotEmpty())
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 mt-8 flex gap-2 overflow-x-auto pb-2">
                @foreach ($projectTabs as $tab)
                    <button class="project-tab shrink-0" type="button" :class="{ 'is-active': activeTab === '{{ $tab['id'] }}' }" x-on:click="activeTab = '{{ $tab['id'] }}'">{{ $tab['label'] }}</button>
                @endforeach
            </div>
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 mt-7">
                @foreach ($projectTabs as $tab)
                    <div x-cloak x-show="activeTab === '{{ $tab['id'] }}'" x-transition.opacity.duration.250ms>
                        <div class="grid gap-4 lg:grid-cols-[1.18fr_1fr]">
                            <article class="project-feature-card group relative isolate min-h-[28rem] overflow-hidden rounded-[1.5rem] bg-ink text-white md:min-h-[34rem]">
                                @if ($tab['primary']->image_url)
                                    <img class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105" src="{{ $tab['primary']->image_url }}" alt="{{ $tab['primary']->title }}" loading="lazy">
                                @endif
                                <div class="project-card-overlay absolute inset-0"></div>
                                <a class="absolute inset-0 z-10" href="{{ LocalizedUrl::slug($tab['primary']->slug) }}" aria-label="{{ $tab['primary']->title }}"></a>
                                <div class="project-card-content relative z-20 flex h-full flex-col justify-end p-7 pointer-events-none md:p-9">
                                    <h3 class="project-card-title text-xl md:text-2xl">{{ $tab['primary']->title }}</h3>
                                    @if ($tab['primary']->category || $tab['primary']->excerpt)
                                        <div class="project-card-reveal">
                                            <div class="project-card-reveal__inner">
                                                <div class="project-card-reveal__content max-w-xl">
                                                    <p class="text-sm font-semibold text-primary-soft">{{ $tab['primary']->category?->name ?: 'Dự án tiêu biểu' }}</p>
                                                    @if ($tab['primary']->excerpt)<p class="project-card-reveal__excerpt">{{ $tab['primary']->excerpt }}</p>@endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </article>
                            <div class="grid gap-4 sm:grid-cols-2">
                                @foreach ($tab['secondary'] as $project)
                                    <article class="project-small-card group relative isolate min-h-52 overflow-hidden rounded-[1.5rem] bg-ink text-white">
                                        @if ($project->image_url)
                                            <img class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105" src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy">
                                        @endif
                                        <div class="project-card-overlay-soft absolute inset-0"></div>
                                        <a class="absolute inset-0 z-10" href="{{ LocalizedUrl::project($project) }}" aria-label="{{ $project->title }}"></a>
                                        <div class="project-card-content relative z-20 flex h-full flex-col justify-end p-5 pointer-events-none">
                                            <h3 class="project-card-title text-base md:text-lg">{{ $project->title }}</h3>
                                            @if ($project->category || $project->excerpt)
                                                <div class="project-card-reveal">
                                                    <div class="project-card-reveal__inner">
                                                        <div class="project-card-reveal__content">
                                                            @if ($project->category)<p class="text-xs font-semibold text-primary-soft">{{ $project->category->name }}</p>@endif
                                                            @if ($project->excerpt)<p class="project-card-reveal__excerpt">{{ $project->excerpt }}</p>@endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 mt-10"><p class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500">Dự án sẽ được cập nhật sớm.</p></div>
        @endif
    </section>

    <section class="home-testimonials section-space" id="phan-hoi">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 home-testimonials__layout">
            <div class="home-testimonials__intro" data-aos="fade-up">
                <h2 class="uppercase">Khách hàng nói về chúng tôi</h2>
                <p class="mt-4 text-sm leading-7 text-slate-300">Những chia sẻ từ các hành trình đã đồng hành.</p>
            </div>
            <div class="home-testimonials__slider" data-aos="fade-up" data-aos-delay="100">
                <div class="swiper" data-testimonial-swiper>
                    <div class="swiper-wrapper">
                        @forelse ($testimonials as $testimonial)
                            <div class="swiper-slide h-auto">
                                <article class="home-testimonial">
                                    @if ($testimonial->rating)
                                        <div class="home-testimonial__rating" aria-label="{{ $testimonial->rating }} trên 5 sao">
                                            @for ($star = 1; $star <= $testimonial->rating; $star++)
                                                <span aria-hidden="true">★</span>
                                            @endfor
                                        </div>
                                    @endif
                                    <blockquote class="home-testimonial__quote">“{{ $testimonial->quote }}”</blockquote>
                                    <div class="home-testimonial__person">
                                        @if ($testimonial->curatorMedia?->url)
                                            <img src="{{ $testimonial->curatorMedia->url }}" alt="{{ $testimonial->client_name }}" loading="lazy">
                                        @else
                                            <span>{{ mb_substr($testimonial->client_name, 0, 1) }}</span>
                                        @endif
                                        <div><p>{{ $testimonial->client_name }}</p><p>{{ collect([$testimonial->client_role, $testimonial->company_name])->filter()->implode(' · ') }}</p></div>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="swiper-slide"><p class="home-testimonials__empty">Phản hồi khách hàng sẽ hiển thị tại đây sau khi được thêm và bật tại mục “Phản hồi khách hàng” trong quản trị.</p></div>
                        @endforelse
                    </div>
                </div>
                @if ($testimonials->isNotEmpty())
                    <button class="absolute top-1/2 -left-3 z-10 hidden size-11 -translate-y-1/2 place-items-center rounded-full border border-white/30 bg-white text-ink shadow-sm transition hover:border-primary hover:bg-primary hover:text-white md:grid" type="button" aria-label="Phản hồi trước" data-testimonial-swiper-prev>
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button class="absolute top-1/2 -right-3 z-10 hidden size-11 -translate-y-1/2 place-items-center rounded-full border border-white/30 bg-white text-ink shadow-sm transition hover:border-primary hover:bg-primary hover:text-white md:grid" type="button" aria-label="Phản hồi tiếp theo" data-testimonial-swiper-next>
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                @endif
            </div>
        </div>
    </section>

    <section class="home-news section-space" id="tin-tuc">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 home-news__header">
            <div data-aos="fade-up"><h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">Tin tức & kiến thức</h2><p class="mt-4 text-base leading-8 text-slate-600 md:text-lg">Cập nhật mới nhất từ DVTEC.</p></div>
            <a class="section-link" href="{{ LocalizedUrl::route('posts.index') }}">{{ __('site.view_news') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
        </div>
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 home-news__slider" data-aos="fade-up" data-aos-delay="100">
            <div class="swiper" data-post-swiper>
                <div class="swiper-wrapper">
                    @forelse ($posts as $post)
                        @include('frontend.partials.home-post-slide')
                    @empty
                        <div class="swiper-slide"><p class="rounded-[1.5rem] border border-dashed border-slate-300 p-8 text-sm leading-7 text-slate-500">Bài viết sẽ được cập nhật sớm.</p></div>
                    @endforelse
                </div>
            </div>
            <button class="absolute top-1/2 -left-3 z-10 hidden size-11 -translate-y-1/2 place-items-center rounded-full border border-slate-200 bg-white text-ink shadow-sm transition hover:border-primary hover:bg-primary hover:text-white md:grid" type="button" aria-label="Bài viết trước" data-post-swiper-prev>
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button class="absolute top-1/2 -right-3 z-10 hidden size-11 -translate-y-1/2 place-items-center rounded-full border border-slate-200 bg-white text-ink shadow-sm transition hover:border-primary hover:bg-primary hover:text-white md:grid" type="button" aria-label="Bài viết tiếp theo" data-post-swiper-next>
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>

    </section>

    @if ($faqItems->isNotEmpty())
        <section class="home-faq section-space border-t border-slate-100" id="cau-hoi">
            <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[.75fr_1.25fr] lg:items-start">
                    <header class="home-faq__header">
                        <p class="text-sm font-bold uppercase tracking-[0.16em] text-primary">Giải đáp nhanh</p>
                        <h2 class="display-title mt-3 text-3xl leading-tight uppercase md:text-4xl">{{ $faqTitle ?: 'Câu hỏi thường gặp' }}</h2>
                        @if (filled($faqDescription))
                            <p class="mt-4 text-base leading-8 text-slate-600">{{ $faqDescription }}</p>
                        @endif
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
            </div>
        </section>
    @endif

    <section class="home-consultation section-space" id="tu-van">
        <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
            <header class="mx-auto mb-8 max-w-2xl text-center md:mb-10" data-aos="fade-up">
                <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">Liên hệ với chúng tôi</h2>
            </header>

            <div class="home-consultation__shell">
                <div class="home-consultation__info" data-aos="fade-right">
                    <aside class="home-consultation__intro">
                        <p class="text-sm font-bold text-accent uppercase">Miễn phí tư vấn</p>
                        <dl class="home-consultation__contacts mt-8">
                            @if ($contactPhones->isNotEmpty())
                                <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                                    @foreach ($contactPhones as $phone)
                                        @if (! $loop->first)<span class="text-slate-400" aria-hidden="true">-</span>@endif
                                        <a class="font-semibold text-ink hover:text-primary" href="tel:{{ preg_replace('/\s+/', '', $phone['number']) }}">{{ $phone['number'] }}</a>
                                    @endforeach
                                </div>
                            @endif
                            @if ($website->contact_email)
                                <div><dt class="text-xs font-bold tracking-[0.14em] text-slate-500 uppercase">Email</dt><dd class="mt-1.5"><a class="font-semibold text-ink hover:text-primary" href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a></dd></div>
                            @endif
                            @if ($contactBranches->isNotEmpty())
                                @foreach ($contactBranches as $branch)
                                    <div><dt class="text-xs font-bold tracking-[0.14em] text-slate-500 uppercase">{{ $branch['name'] ?? 'Địa chỉ' }}</dt><dd class="mt-1.5 leading-6 text-slate-600">{{ $branch['address'] }}</dd></div>
                                @endforeach
                            @elseif ($website->address)
                                <div><dt class="text-xs font-bold tracking-[0.14em] text-slate-500 uppercase">Địa chỉ</dt><dd class="mt-1.5 leading-6 text-slate-600">{{ $website->address }}</dd></div>
                            @endif
                        </dl>
                    </aside>

                    <div class="home-consultation__map">
                        @if ($googleMapsEmbedUrl)
                            <iframe src="{{ $googleMapsEmbedUrl }}" title="Bản đồ vị trí {{ $website->company_name ?: $website->site_name }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                        @else
                            <div class="home-consultation__map-empty">
                                <span class="text-sm leading-7 text-slate-500">Bản đồ nhúng chưa được cấu hình trong Cài đặt chung.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ LocalizedUrl::route('contact.store') }}" class="home-consultation__form" data-aos="fade-up">
                    @csrf
                    <label class="text-sm font-semibold text-ink">Họ và tên<input class="form-field" name="name" value="{{ old('name') }}" required></label>
                    <label class="text-sm font-semibold text-ink">Số điện thoại<input class="form-field" name="phone" value="{{ old('phone') }}"></label>
                    <label class="text-sm font-semibold text-ink">Dịch vụ quan tâm<select class="form-field" name="service_id"><option value="">Chọn dịch vụ</option>@foreach ($contactServices as $service)<option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->title }}</option>@endforeach</select></label>
                    <label class="text-sm font-semibold text-ink">Nhu cầu của bạn<textarea class="form-field" name="message" rows="5" required>{{ old('message') }}</textarea></label>
                    <button class="button-primary justify-self-start" type="submit">Gửi yêu cầu <span aria-hidden="true">↗</span></button>
                </form>
            </div>
        </div>
    </section>
@endsection
