@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'min-h-screen bg-white')

@section('content')
    <section class="brand-gradient-dark relative isolate min-h-[39rem] overflow-hidden text-white" data-hero-section>
        @forelse ($heroSlides as $slide)
            @if ($loop->first)
                <div class="swiper hero-swiper min-h-[39rem]" data-hero-swiper>
                    <div class="swiper-wrapper">
            @endif

            <article class="swiper-slide relative min-h-[39rem] overflow-hidden">
                <div class="hero-brand-glow absolute inset-0"></div>
                @if ($slide['image_url'])
                    <img class="absolute inset-0 h-full w-full object-cover opacity-35" src="{{ $slide['image_url'] }}" alt="" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                    <div class="hero-image-overlay absolute inset-0"></div>
                @endif

                <div class="site-shell relative grid min-h-[39rem] items-end py-16 md:py-24 lg:py-28">
                    <div class="max-w-4xl pb-24 lg:pb-16">
                        @if ($slide['eyebrow'])<p class="eyebrow text-primary-soft mb-6">{{ $slide['eyebrow'] }}</p>@endif
                        <h1 class="font-display max-w-4xl text-4xl leading-[1.08] tracking-[-0.055em] text-white sm:text-5xl lg:text-7xl">{{ $slide['title'] }}</h1>
                        @if ($slide['description'])<p class="mt-7 max-w-2xl text-base leading-8 text-slate-300 md:text-lg">{{ $slide['description'] }}</p>@endif
                        <div class="mt-9 flex flex-wrap gap-3">
                            <a class="button-primary" href="{{ $slide['primary_url'] }}">{{ $slide['primary_label'] ?: __('site.discuss_project') }} <span aria-hidden="true">↗</span></a>
                            <a class="button-secondary" href="{{ $slide['secondary_url'] }}">{{ $slide['secondary_label'] ?: __('site.view_projects') }}</a>
                        </div>
                    </div>
                </div>
            </article>

            @if ($loop->last)
                    </div>
                </div>
            @endif
        @empty
            <div class="hero-brand-glow absolute inset-0"></div>
            <div class="site-shell relative grid min-h-[39rem] items-end py-16 md:py-24 lg:py-28">
                <div class="max-w-4xl pb-16">
                    <p class="eyebrow text-primary-soft mb-6">THT Media</p>
                    <h1 class="font-display max-w-4xl text-5xl leading-[1.08] tracking-[-0.055em] text-white lg:text-7xl">Biến câu chuyện thương hiệu thành trải nghiệm đáng nhớ.</h1>
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
            <div class="site-shell flex items-center gap-4 text-sm text-slate-500">
                <span class="size-2 rounded-full bg-primary"></span>
                Thông tin khách hàng và đối tác sẽ xuất hiện tại đây sau khi được thêm trong quản trị.
            </div>
        @endif
    </section>

    <section class="section-space" id="gioi-thieu">
        <div class="site-shell grid items-center gap-8 lg:grid-cols-2 lg:gap-16">
            <div class="aspect-[4/3] overflow-hidden rounded-[1.5rem] bg-mist shadow-[0_20px_48px_color-mix(in_srgb,var(--site-color-ink)_12%,transparent)]" data-aos="fade-right">
                @if ($aboutImageUrl)
                    <img class="h-full w-full object-cover" src="{{ $aboutImageUrl }}" alt="{{ $about['title'] }}" loading="lazy">
                @else
                    <span class="image-placeholder">THT</span>
                @endif
            </div>
            <div data-aos="fade-left">
                <p class="eyebrow mb-5">{{ $about['eyebrow'] }}</p>
                <h2 class="display-title text-3xl leading-tight md:text-4xl">{{ $about['title'] }}</h2>
                <p class="mt-6 text-base leading-8 text-slate-600 md:text-lg">{{ $about['content'] }}</p>
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

        <div class="site-shell mt-12 grid divide-y divide-slate-200 overflow-hidden rounded-[1.5rem] border border-slate-200 md:grid-cols-4 md:divide-x md:divide-y-0" data-aos="fade-up">
                @foreach ($stats as $stat)
                    <div class="bg-white px-6 py-7 md:px-7" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                        <p class="font-display text-4xl tracking-[-0.05em] text-ink md:text-5xl" data-count-up="{{ $stat['value'] }}" aria-label="{{ number_format($stat['value']) }}">{{ number_format($stat['value']) }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
        </div>
    </section>

    <section class="section-space border-t border-slate-100" id="dich-vu">
        <div class="site-shell flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div>
                <p class="eyebrow mb-4">Hệ sinh thái dịch vụ</p>
                <h2 class="display-title text-3xl leading-tight md:text-4xl">Một hệ sinh thái để thương hiệu đi từ định hướng đến điểm chạm.</h2>
            </div>
            <a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">{{ __('site.all_services') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
        </div>
        <div class="site-shell mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($services as $service)
                @include('frontend.partials.service-card')
            @empty
                <p class="rounded-3xl border border-dashed border-slate-300 p-8 text-sm text-slate-500 sm:col-span-2 xl:col-span-4">Dịch vụ sẽ được cập nhật sớm.</p>
            @endforelse
        </div>
    </section>

    <section class="home-projects section-space" id="du-an" x-data="{ activeTab: '{{ $projectTabs->first()['id'] ?? 'all' }}' }">
        <div class="site-shell flex flex-col justify-between gap-7 lg:flex-row lg:items-end">
            <div>
                <p class="eyebrow mb-4">Dự án</p>
                <h2 class="display-title text-3xl leading-tight md:text-4xl">Những dự án được kể bằng kết quả và trải nghiệm.</h2>
            </div>
            <a class="section-link" href="{{ LocalizedUrl::route('projects.index') }}">{{ __('site.all_projects') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
        </div>

        @if ($projectTabs->isNotEmpty())
            <div class="site-shell mt-8 flex gap-2 overflow-x-auto pb-2">
                @foreach ($projectTabs as $tab)
                    <button class="project-tab shrink-0" type="button" :class="{ 'is-active': activeTab === '{{ $tab['id'] }}' }" x-on:click="activeTab = '{{ $tab['id'] }}'">{{ $tab['label'] }}</button>
                @endforeach
            </div>
            <div class="site-shell mt-7">
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
                                                    <p class="eyebrow text-primary-soft">{{ $tab['primary']->category?->name ?: 'Dự án tiêu biểu' }}</p>
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
                                                            @if ($project->category)<p class="eyebrow text-primary-soft text-[0.6rem]">{{ $project->category->name }}</p>@endif
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
            <div class="site-shell mt-10"><p class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500">Dự án sẽ được cập nhật sớm.</p></div>
        @endif
    </section>

    <section class="home-approach section-space" id="nang-luc">
        <div class="site-shell home-approach__grid">
            <article class="home-commitments">
                <p class="eyebrow text-primary-soft">Cam kết đồng hành</p>
                <h2 class="home-commitments__title">Một cách làm rõ ràng để mỗi bên cùng nắm được mục tiêu.</h2>
                <div class="home-commitments__list">
                    @foreach ($commitments as $commitment)
                        <div class="home-commitment"><span class="home-commitment__icon">✓</span><p>{{ $commitment }}</p></div>
                    @endforeach
                </div>
            </article>
            <article class="home-capabilities">
                <p class="eyebrow">Năng lực triển khai</p>
                <h2 class="home-capabilities__title">Các đầu việc kết nối trong một nhịp triển khai.</h2>
                <div class="home-capabilities__list">
                    @foreach ($capabilities as $capability)
                        <div class="home-capability"><span aria-hidden="true">→</span><p>{{ $capability }}</p></div>
                    @endforeach
                </div>
            </article>
        </div>
    </section>

    <section class="home-testimonials section-space" id="phan-hoi">
        <div class="site-shell home-testimonials__layout">
            <div class="home-testimonials__intro">
                <p class="eyebrow text-primary-soft">Phản hồi khách hàng</p>
                <h2>Những chia sẻ từ các hành trình đã đồng hành.</h2>
            </div>
            <div class="home-testimonials__grid">
                @forelse ($testimonials as $testimonial)
                    <article class="home-testimonial">
                        <div class="home-testimonial__rating" aria-label="{{ $testimonial->rating }} trên 5 sao">
                            @for ($star = 1; $star <= $testimonial->rating; $star++)
                                <span aria-hidden="true">★</span>
                            @endfor
                        </div>
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
                @empty
                    <div class="home-testimonials__empty">Phản hồi khách hàng sẽ hiển thị tại đây sau khi được thêm và bật tại mục “Phản hồi khách hàng” trong quản trị.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="home-news section-space" id="tin-tuc">
        <div class="site-shell home-news__header">
            <div data-aos="fade-up"><p class="eyebrow mb-4">Tin tức & kiến thức</p><h2 class="display-title text-3xl leading-tight md:text-4xl">Cập nhật mới nhất từ THT Media.</h2></div>
            <a class="section-link" href="{{ LocalizedUrl::route('posts.index') }}">{{ __('site.view_news') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
        </div>
        <div class="site-shell home-news__slider" data-aos="fade-up" data-aos-delay="100">
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

    <section class="home-consultation section-space" id="tu-van">
        <div class="site-shell home-consultation__shell">
            <aside class="home-consultation__intro" data-aos="fade-right">
                <div>
                    <p class="eyebrow mb-5">Bắt đầu cùng THT Media</p>
                    <h2 class="display-title text-3xl leading-tight md:text-4xl">{{ $consultation['title'] }}</h2>
                    <p class="mt-5 text-sm leading-7 text-slate-600">{{ $consultation['content'] }}</p>
                </div>

                <dl class="home-consultation__contacts">
                    @if ($website->hotline)
                        <div><dt class="text-xs font-bold tracking-[0.14em] text-slate-500 uppercase">Hotline</dt><dd class="mt-1.5"><a class="font-semibold text-ink hover:text-primary" href="tel:{{ preg_replace('/\s+/', '', $website->hotline) }}">{{ $website->hotline }}</a></dd></div>
                    @endif
                    @if ($website->contact_email)
                        <div><dt class="text-xs font-bold tracking-[0.14em] text-slate-500 uppercase">Email</dt><dd class="mt-1.5"><a class="font-semibold text-ink hover:text-primary" href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a></dd></div>
                    @endif
                    @if ($website->address)
                        <div><dt class="text-xs font-bold tracking-[0.14em] text-slate-500 uppercase">Địa chỉ</dt><dd class="mt-1.5 leading-6 text-slate-600">{{ $website->address }}</dd></div>
                    @endif
                </dl>
            </aside>

            <form method="POST" action="{{ LocalizedUrl::route('contact.store') }}" class="home-consultation__form" data-aos="fade-up">
                @csrf
                <label class="text-sm font-semibold text-ink">Họ và tên<input class="form-field" name="name" value="{{ old('name') }}" required></label>
                <label class="text-sm font-semibold text-ink">Số điện thoại<input class="form-field" name="phone" value="{{ old('phone') }}"></label>
                <label class="text-sm font-semibold text-ink">Dịch vụ quan tâm<select class="form-field" name="landing_id"><option value="">Chọn dịch vụ</option>@foreach ($contactServices as $service)<option value="{{ $service->id }}" @selected(old('landing_id') == $service->id)>{{ $service->title }}</option>@endforeach</select></label>
                <label class="text-sm font-semibold text-ink">Nhu cầu của bạn<textarea class="form-field" name="message" rows="5" required>{{ old('message') }}</textarea></label>
                <button class="button-primary justify-self-start" type="submit">Gửi yêu cầu <span aria-hidden="true">↗</span></button>
            </form>

            <div class="home-consultation__visual hidden md:block" data-aos="fade-left">
                @if ($contactImageUrl)
                    <img src="{{ $contactImageUrl }}" alt="Liên hệ THT Media" loading="lazy">
                @else
                    <div class="home-consultation__visual-empty">
                        <span class="text-sm leading-7 text-slate-500">Thêm ảnh tại Cài đặt website để hiển thị ở đây.</span>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
