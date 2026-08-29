@use(App\Support\Localization\LocalizedUrl)

@php
    $heroBlock = collect($landingBlocks)->first(fn (array $block): bool => $block['type'] === 'hero');
    $hero = $heroBlock['data'] ?? [];
    $heroPoster = $heroBlock['media'] ?? $service->curatorMedia;
    $heroVideo = $landingTemplateMedia['hero_video'] ?? null;
    $showreel = $landingTemplateMedia['showreel'] ?? null;
    $contentBlocks = collect($landingBlocks)
        ->reject(fn (array $block): bool => $heroBlock !== null && $block['id'] === $heroBlock['id'])
        ->values()
        ->all();
@endphp

<div
    class="landing-page landing-page--landing07-film"
    data-landing-page
    data-landing-id="{{ $service->id }}"
    data-track-endpoint="{{ $landingTrackingUrl }}"
    data-campaign-state="{{ $landingCampaignState }}"
    data-template-source="landing07-corporate-film"
    style="--landing-primary: {{ $landingTheme['primary'] }}; --landing-accent: {{ $landingTheme['accent'] }}; --landing-surface: {{ $landingTheme['surface'] }}; --landing-ink: {{ $landingTheme['ink'] }};"
>
    <header class="landing07-film-nav">
        <a class="landing07-film-brand" href="{{ LocalizedUrl::route('home') }}" aria-label="Về trang chủ THT Media">
            @if ($websiteMediaUrls[$website->logo_media_id] ?? null)<img src="{{ $websiteMediaUrls[$website->logo_media_id] }}" alt="" aria-hidden="true">@endif
            <span>{{ $landingTemplateSettings['film_brand_label'] }}</span>
        </a>
        <nav aria-label="Điều hướng landing phim doanh nghiệp">
            <a href="#du-an">Dự án</a>
            <a href="#bang-gia">Bảng giá</a>
            <a class="landing07-film-nav__cta" href="#tu-van" data-landing-event="cta_click" data-block-id="film-nav">{{ $landingTemplateSettings['film_nav_cta'] }}</a>
        </nav>
    </header>

    @include('frontend.services.partials.campaign-notice')

    <section class="landing07-film-hero" id="{{ $heroBlock['id'] ?? 'hero' }}" data-landing-block="hero">
        <div class="landing07-film-hero__media" aria-hidden="true">
            @if ($heroVideo?->url)
                <video autoplay muted loop playsinline preload="auto" @if ($heroPoster?->url) poster="{{ $heroPoster->url }}" @endif>
                    <source src="{{ $heroVideo->url }}" type="{{ $heroVideo->type ?: 'video/mp4' }}">
                </video>
            @elseif ($heroPoster?->url)
                <img src="{{ $heroPoster->url }}" alt="" loading="eager">
            @endif
        </div>
        <div class="landing07-film-hero__shade"></div>
        <div class="landing-shell landing07-film-hero__inner">
            <div class="landing07-film-hero__copy" data-aos="fade-up">
                @if (filled($hero['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $hero['eyebrow'] }}</p>@endif
                <p class="landing07-film-hero__service-line">{{ $landingTemplateSettings['film_service_line'] }}</p>
                <h1>{{ $hero['title'] ?? $service->title }}</h1>
                @if (filled($hero['subtitle'] ?? null))<p class="landing07-film-hero__summary">{{ $hero['subtitle'] }}</p>@endif
                <div class="landing07-film-hero__actions">
                    <a class="landing-button landing-button--primary" href="{{ $hero['cta_url'] ?? '#tu-van' }}" data-landing-event="cta_click" data-block-id="{{ $heroBlock['id'] ?? 'hero' }}">{{ $hero['cta_label'] ?? $landingTemplateSettings['film_nav_cta'] }} <span aria-hidden="true">↗</span></a>
                    @if ($showreel?->url)
                        <a class="glightbox landing07-film-showreel" href="{{ $showreel->url }}" data-type="video" data-landing-event="video_play" data-block-id="film-showreel">
                            <span aria-hidden="true">▶</span>{{ $landingTemplateSettings['film_showreel_label'] }}
                        </a>
                    @elseif (filled($hero['secondary_label'] ?? null))
                        <a class="landing07-film-showreel" href="{{ $hero['secondary_url'] ?? '#du-an' }}" data-landing-event="cta_click" data-block-id="{{ $heroBlock['id'] ?? 'hero' }}"><span aria-hidden="true">↓</span>{{ $hero['secondary_label'] }}</a>
                    @endif
                </div>
            </div>
            @if (filled($landingTemplateSettings['film_quote'] ?? null))
                <blockquote class="landing07-film-hero__quote" data-aos="fade-left">“{{ $landingTemplateSettings['film_quote'] }}”</blockquote>
            @endif
        </div>
        <div class="landing07-film-hero__scroll"><span>01 / THT FILMS</span><a href="#gia-tri-bo-phim">Cuộn để khám phá <span aria-hidden="true">↓</span></a></div>
    </section>

    @include('frontend.services.partials.builder-content', ['landingBlocks' => $contentBlocks])

    <footer class="landing07-film-footer">
        <span>{{ $landingTemplateSettings['film_footer_text'] }}</span>
        <a href="#tu-van" data-landing-event="cta_click" data-block-id="film-footer">{{ $landingTemplateSettings['film_nav_cta'] }} <span aria-hidden="true">→</span></a>
    </footer>
</div>
