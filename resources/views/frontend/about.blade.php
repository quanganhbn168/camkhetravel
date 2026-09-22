@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/about.scss')
@endpush

@section('content')
    <div data-system-page="about">
    @if ($pageBannerUrl)
        <section class="resource-archive-hero" aria-label="{{ $page['title'] }}">
            <img class="resource-archive-hero__image" data-page-banner-image src="{{ $pageBannerUrl }}" alt="{{ $page['title'] }}">
            <div class="resource-archive-hero__overlay"></div>
        </section>
    @endif
    <section class="about-page-hero">
        <div class="container about-page-hero__shell">
            <div >
                <h1>{{ $about['title'] }}</h1>
                @if ($about['intro'])
                    <p class="about-page-hero__intro">{{ $about['intro'] }}</p>
                @endif
                <a class="btn btn-dark mt-4" href="{{ route('contact') }}">Trao đổi dự án</a>
            </div>

            <div class="about-page-hero__media">
                @if ($about['image_url'])
                    <img src="{{ $about['image_url'] }}" alt="{{ $about['title'] }}" fetchpriority="high">
                @else
                    <span class="image-placeholder">DV</span>
                @endif
            </div>
        </div>
    </section>

    @if (filled(trim(strip_tags($about['story'] ?? ''))))
        <section class="about-page-story section-space">
            <div class="container about-page-story__shell">
                <div class="about-page-story__media">
                    @if ($about['story_image_url'])
                        <img src="{{ $about['story_image_url'] }}" alt="{{ $about['story_title'] ?: $about['title'] }}" loading="lazy">
                    @else
                    <span class="image-placeholder">DV</span>
                    @endif
                </div>
                <div >
                    @if ($about['story_title'])
                        <div >
                            <h2 class="display-title">{{ $about['story_title'] }}</h2>
                        </div>
                    @endif
                    <div class="article-prose">
                        {!! $about['story'] !!}
                    </div>
                </div>
            </div>
        </section>
    @endif
    </div>

    @if ($about['video'])
        <section class="section-space" aria-labelledby="about-page-video-title">
            <div class="container">
                <header class="about-page-section-heading">
                    <h2 class="display-title" id="about-page-video-title">Video giới thiệu</h2>
                </header>
                <div class="about-intro-video">
                    @if ($about['video']['source'] === 'upload')
                        <video controls playsinline preload="metadata" @if ($about['video']['poster_url']) poster="{{ $about['video']['poster_url'] }}" @endif>
                            <source src="{{ $about['video']['url'] }}">
                            Trình duyệt của bạn chưa hỗ trợ phát video.
                        </video>
                    @else
                    <iframe src="{{ $about['video']['url'] }}" title="Video giới thiệu {{ $website->site_name }}" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($services->isNotEmpty())
        <section class="section-space">
            <div class="container">
                @if ($about['services_title'] || $about['services_link_label'])
                    <header class="about-page-section-heading about-page-section-heading--split">
                        @if ($about['services_title'])
                            <h2 class="display-title">{{ $about['services_title'] }}</h2>
                        @endif
                        @if ($about['services_link_label'])
                            <a class="section-link" href="{{ route('services.index') }}">{{ $about['services_link_label'] }} <span aria-hidden="true">→</span></a>
                        @endif
                    </header>
                @endif

                <div class="about-services__grid">
                    @foreach ($services as $service)
                        <article class="about-service-card">
                            <a class="about-service-card__media" href="{{ route('slug.show', ['slug' => $service->slug]) }}" aria-label="{{ $service->title }}">
                                @if ($service->image_url)
                                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}" loading="lazy">
                                @else
                                    <span class="image-placeholder">DV</span>
                                @endif
                            </a>
                            <div class="about-service-card__body">
                                <h3><a href="{{ route('slug.show', ['slug' => $service->slug]) }}">{{ $service->title }}</a></h3>
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
            <div class="container">
                @if ($about['principles_title'])
                    <header class="about-page-section-heading">
                        <h2 class="display-title">{{ $about['principles_title'] }}</h2>
                    </header>
                @endif

                <div >
                    <div class="about-page-principles__top">
                        @if ($about['vision'])
                            <article class="about-principle-card">
                                <span class="about-principle-card__icon" aria-hidden="true">
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                                <h3>Tầm nhìn</h3>
                                <p>{{ $about['vision'] }}</p>
                            </article>
                        @endif

                        @if ($about['mission'])
                            <article class="about-principle-card">
                                <span class="about-principle-card__icon" aria-hidden="true">
                                    <i class="fa-solid fa-crosshairs"></i>
                                </span>
                                <h3>Sứ mệnh</h3>
                                <p>{{ $about['mission'] }}</p>
                            </article>
                        @endif
                    </div>

                    @if ($about['core_values'])
                        <article class="about-principle-values">
                            <div class="about-principle-values__media">
                                @if ($about['core_values_image_url'])
                                    <img src="{{ $about['core_values_image_url'] }}" alt="Giá trị cốt lõi của {{ $website->site_name }}" loading="lazy">
                                @else
                                    <span class="image-placeholder">DV</span>
                                @endif
                            </div>
                            <div >
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
        <section class="section-space">
            <div class="container">
                @if ($about['history_title'] || $about['history_description'])
                    <header class="about-page-section-heading">
                        @if ($about['history_title'])
                            <h2 class="display-title">{{ $about['history_title'] }}</h2>
                        @endif
                        @if ($about['history_description'])
                            <p >{{ $about['history_description'] }}</p>
                        @endif
                    </header>
                @endif

                <div class="about-history__tabs" role="tablist" aria-label="Các mốc lịch sử">
                    @foreach ($historyTimeline as $item)
                        <button
                            class="about-history__tab {{ $loop->first ? 'active' : '' }}"
                            type="button"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#history-panel-{{ $loop->index }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                            aria-controls="history-panel-{{ $loop->index }}"
                        >
                            <span >{{ $item['year'] }}</span>
                            <span  aria-hidden="true"></span>
                        </button>
                    @endforeach
                </div>

                <div class="tab-content">
                    @foreach ($historyTimeline as $item)
                        <article
                            id="history-panel-{{ $loop->index }}"
                            class="about-history__panel tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            role="tabpanel"
                        >
                            <div class="about-history__media">
                                @if ($item['image_url'])
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                                @else
                                    <span class="image-placeholder" aria-hidden="true">•</span>
                                @endif
                            </div>
                            <div >
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
        <section class="section-space">
            <div class="container">
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
        <section class="section-space">
            <div class="container">
                @if ($about['stats_title'])
                    <header class="about-page-section-heading">
                        <h2 class="display-title">{{ $about['stats_title'] }}</h2>
                    </header>
                @endif
                <div class="about-stats__grid">
                    @foreach ($stats as $stat)
                        <article class="about-stat">
                            <strong>{{ $stat['value'] }}</strong>
                            <span>{{ $stat['label'] }}</span>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($about['office_title'] || $about['office_description'] || $about['office_gallery']->isNotEmpty())
        <section class="section-space">
            <div class="container">
                @if ($about['office_title'] || $about['office_description'])
                    <header class="about-page-section-heading">
                        @if ($about['office_title'])
                            <h2 class="display-title">{{ $about['office_title'] }}</h2>
                        @endif
                        @if ($about['office_description'])
                            <p >{{ $about['office_description'] }}</p>
                        @endif
                    </header>
                @endif
                @if ($about['office_gallery']->isNotEmpty())
                    <div @class([
                        'about-office-gallery',
                        'about-office-gallery--featured' => $about['office_gallery']->count() >= 4,
                    ])>
                        @foreach ($about['office_gallery'] as $image)
                            <a
                                class="about-office-gallery__item"
                                href="{{ $image['url'] }}"
                                target="_blank"
                                rel="noopener"
                                aria-label="Mở {{ $image['alt'] }}"
                            >
                                <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" loading="lazy">
                                <span aria-hidden="true">↗</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($about['cta_title'])
        <section class="about-page-cta">
            <div class="container about-page-cta__shell">
                <div>
                    <h2>{{ $about['cta_title'] }}</h2>
                </div>
                @if ($about['cta_button_label'])
                    <a class="btn btn-light" href="{{ route('contact') }}">{{ $about['cta_button_label'] }} <span aria-hidden="true">↗</span></a>
                @endif
            </div>
        </section>
    @endif
@endsection
