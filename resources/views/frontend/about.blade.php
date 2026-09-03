@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="about-page-hero">
        <div class="site-shell about-page-hero__shell">
            <div class="about-page-hero__content" data-aos="fade-right">
                <h1>{{ $about['title'] }}</h1>
                @if ($about['intro'])
                    <p class="about-page-hero__intro">{{ $about['intro'] }}</p>
                @endif
                <a class="button-dark mt-8" href="{{ LocalizedUrl::route('contact') }}">{{ __('site.discuss_project') }}</a>
            </div>

            <div class="about-page-hero__media" data-aos="fade-left">
                @if ($about['image_url'])
                    <img src="{{ $about['image_url'] }}" alt="{{ $about['title'] }}" fetchpriority="high">
                @else
                    <span class="image-placeholder">THT</span>
                @endif
            </div>
        </div>
    </section>

    @if (filled(trim(strip_tags($about['story'] ?? ''))))
        <section class="about-page-story section-space">
            <div class="site-shell about-page-story__shell">
                <div class="about-page-story__media" data-aos="fade-right">
                    @if ($about['image_url'])
                        <img src="{{ $about['image_url'] }}" alt="{{ $about['story_title'] ?: $about['title'] }}" loading="lazy">
                    @else
                        <span class="image-placeholder">THT</span>
                    @endif
                </div>
                <div class="about-page-story__content" data-aos="fade-left">
                    @if ($about['story_title'])
                        <div class="about-page-story__heading">
                            <h2 class="display-title">{{ $about['story_title'] }}</h2>
                        </div>
                    @endif
                    <div class="article-prose about-page-story__body">
                        {!! $about['story'] !!}
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($about['video'])
        <section class="about-page-video section-space" aria-labelledby="about-page-video-title">
            <div class="site-shell">
                <header class="about-page-section-heading" data-aos="fade-up">
                    <h2 class="display-title" id="about-page-video-title">Video giới thiệu</h2>
                </header>
                <div class="about-intro-video" data-aos="fade-up" data-aos-delay="100">
                    @if ($about['video']['source'] === 'upload')
                        <video controls playsinline preload="metadata" @if ($about['video']['poster_url']) poster="{{ $about['video']['poster_url'] }}" @endif>
                            <source src="{{ $about['video']['url'] }}">
                            Trình duyệt của bạn chưa hỗ trợ phát video.
                        </video>
                    @else
                        <iframe src="{{ $about['video']['url'] }}" title="Video giới thiệu THT Media" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($services->isNotEmpty())
        <section class="about-page-services section-space">
            <div class="site-shell">
                @if ($about['services_title'] || $about['services_link_label'])
                    <header class="about-page-section-heading about-page-section-heading--split" data-aos="fade-up">
                        @if ($about['services_title'])
                            <h2 class="display-title">{{ $about['services_title'] }}</h2>
                        @endif
                        @if ($about['services_link_label'])
                            <a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">{{ $about['services_link_label'] }} <span aria-hidden="true">→</span></a>
                        @endif
                    </header>
                @endif

                <div class="about-services__grid">
                    @foreach ($services as $service)
                        <article class="about-service-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                            <a class="about-service-card__media" href="{{ LocalizedUrl::slug($service->slug) }}" aria-label="{{ $service->title }}">
                                @if ($service->image_url)
                                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}" loading="lazy">
                                @else
                                    <span class="image-placeholder">THT</span>
                                @endif
                            </a>
                            <div class="about-service-card__body">
                                <h3><a href="{{ LocalizedUrl::slug($service->slug) }}">{{ $service->title }}</a></h3>
                                @if ($service->excerpt)
                                    <p>{{ $service->excerpt }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($about['mission'] || $about['vision'] || $about['core_values'])
        <section class="about-page-principles section-space">
            <div class="site-shell">
                @if ($about['principles_title'])
                    <header class="about-page-section-heading" data-aos="fade-up">
                        <h2 class="display-title">{{ $about['principles_title'] }}</h2>
                    </header>
                @endif

                <div class="about-page-principles__grid">
                    <div class="about-page-principles__top">
                        @if ($about['vision'])
                            <article class="about-principle-card" data-aos="fade-up">
                                <span class="about-principle-card__icon" aria-hidden="true">
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                                <h3>Tầm nhìn</h3>
                                <p>{{ $about['vision'] }}</p>
                            </article>
                        @endif

                        @if ($about['mission'])
                            <article class="about-principle-card" data-aos="fade-up" data-aos-delay="80">
                                <span class="about-principle-card__icon" aria-hidden="true">
                                    <i class="fa-solid fa-crosshairs"></i>
                                </span>
                                <h3>Sứ mệnh</h3>
                                <p>{{ $about['mission'] }}</p>
                            </article>
                        @endif
                    </div>

                    @if ($about['core_values'])
                        <article class="about-principle-values" data-aos="fade-up" data-aos-delay="160">
                            <div class="about-principle-values__media">
                                @if ($about['image_url'])
                                    <img src="{{ $about['image_url'] }}" alt="Giá trị cốt lõi của THT Media" loading="lazy">
                                @else
                                    <span class="image-placeholder">THT</span>
                                @endif
                            </div>
                            <div class="about-principle-values__content">
                                <span class="about-principle-card__icon" aria-hidden="true">
                                    <i class="fa-solid fa-gem"></i>
                                </span>
                                <h3>Giá trị cốt lõi</h3>
                                <div class="article-prose">{!! $about['core_values'] !!}</div>
                            </div>
                        </article>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($historyTimeline->isNotEmpty())
        <section class="about-page-history section-space" x-data="{ activeHistory: 0 }">
            <div class="site-shell">
                @if ($about['history_title'] || $about['history_description'])
                    <header class="about-page-section-heading" data-aos="fade-up">
                        @if ($about['history_title'])
                            <h2 class="display-title">{{ $about['history_title'] }}</h2>
                        @endif
                        @if ($about['history_description'])
                            <p class="about-page-section-heading__intro">{{ $about['history_description'] }}</p>
                        @endif
                    </header>
                @endif

                <div class="about-history__tabs" role="tablist" aria-label="Các mốc lịch sử">
                    @foreach ($historyTimeline as $item)
                        <button
                            class="about-history__tab"
                            :class="{ 'is-active': activeHistory === {{ $loop->index }} }"
                            type="button"
                            role="tab"
                            :aria-selected="activeHistory === {{ $loop->index }} ? 'true' : 'false'"
                            aria-controls="history-panel-{{ $loop->index }}"
                            x-on:click="activeHistory = {{ $loop->index }}"
                        >
                            <span class="about-history__tab-year">{{ $item['year'] }}</span>
                            <span class="about-history__tab-line" aria-hidden="true"></span>
                        </button>
                    @endforeach
                </div>

                <div class="about-history__panels">
                    @foreach ($historyTimeline as $item)
                        <article
                            id="history-panel-{{ $loop->index }}"
                            class="about-history__panel"
                            role="tabpanel"
                            x-cloak
                            x-show="activeHistory === {{ $loop->index }}"
                            x-transition.opacity.duration.250ms
                            data-aos="fade-up"
                        >
                            <div class="about-history__media">
                                @if ($item['image_url'])
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                                @else
                                    <span class="image-placeholder">THT</span>
                                @endif
                            </div>
                            <div class="about-history__content">
                                <p class="about-history__year">{{ $item['year'] }}</p>
                                <h3>{{ $item['title'] }}</h3>
                                @if ($item['description'])
                                    <p>{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @elseif ($about['history'])
        <section class="about-page-history section-space">
            <div class="site-shell about-page-history__fallback">
                @if ($about['history_title'])
                    <h2 class="display-title">{{ $about['history_title'] }}</h2>
                @endif
                @if ($about['history_description'])
                    <p>{{ $about['history_description'] }}</p>
                @endif
                <p>{{ $about['history'] }}</p>
            </div>
        </section>
    @endif

    @if ($stats->isNotEmpty())
        <section class="about-page-stats section-space">
            <div class="site-shell">
                @if ($about['stats_title'])
                    <header class="about-page-section-heading" data-aos="fade-up">
                        <h2 class="display-title">{{ $about['stats_title'] }}</h2>
                    </header>
                @endif
                <div class="about-stats__grid">
                    @foreach ($stats as $stat)
                        <article class="about-stat" data-aos="fade-up" data-aos-delay="{{ $loop->index * 70 }}">
                            <strong>{{ $stat['value'] }}</strong>
                            <span>{{ $stat['label'] }}</span>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($about['cta_title'])
        <section class="about-page-cta">
            <div class="site-shell about-page-cta__shell">
                <div>
                    <h2>{{ $about['cta_title'] }}</h2>
                </div>
                @if ($about['cta_button_label'])
                    <a class="button-primary" href="{{ LocalizedUrl::route('contact') }}">{{ $about['cta_button_label'] }} <span aria-hidden="true">↗</span></a>
                @endif
            </div>
        </section>
    @endif
@endsection
