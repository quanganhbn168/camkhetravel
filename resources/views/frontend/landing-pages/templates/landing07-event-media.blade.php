@use(App\Support\Localization\LocalizedUrl)

@php
    $heroBlock = collect($landingBlocks)->first(fn (array $block): bool => $block['type'] === 'hero');
    $hero = $heroBlock['data'] ?? [];
    $heroPoster = $heroBlock['media'] ?? $landingPage->curatorMedia;
    $heroVideo = $landingTemplateMedia['hero_video'] ?? null;
    $contentBlocks = collect($landingBlocks)
        ->reject(fn (array $block): bool => $heroBlock !== null && $block['id'] === $heroBlock['id'])
        ->values()
        ->all();
@endphp

<div
    class="landing-page landing-page--landing07-event"
    data-landing-page
    data-landing-id="{{ $landingPage->id }}"
    data-track-endpoint="{{ $landingTrackingUrl }}"
    data-campaign-state="{{ $landingCampaignState }}"
    data-template-source="landing07-event-media"
    style="--landing-primary: {{ $landingTheme['primary'] }}; --landing-accent: {{ $landingTheme['accent'] }}; --landing-surface: {{ $landingTheme['surface'] }}; --landing-ink: {{ $landingTheme['ink'] }};"
>
    <header class="landing07-event-nav">
        <a class="landing07-event-brand" href="{{ LocalizedUrl::route('home') }}" aria-label="Về trang chủ THT Media">
            @if ($websiteMediaUrls[$website->logo_media_id] ?? null)<img src="{{ $websiteMediaUrls[$website->logo_media_id] }}" alt="" aria-hidden="true">@endif
            <span>{{ $landingTemplateSettings['event_brand_label'] }}</span>
        </a>
        <nav aria-label="Điều hướng landing quay chụp sự kiện">
            <a href="#bo-dau-ra">Bộ đầu ra</a>
            <a href="#du-an">Dự án</a>
            <a class="landing07-event-nav__cta" href="#tu-van" data-landing-event="cta_click" data-block-id="event-nav">{{ $landingTemplateSettings['event_nav_cta'] }}</a>
        </nav>
    </header>

    @include('frontend.landing-pages.partials.campaign-notice')

    <section class="landing07-event-hero" id="{{ $heroBlock['id'] ?? 'hero' }}" data-landing-block="hero">
        <div class="landing07-event-hero__media" aria-hidden="true">
            @if ($heroVideo?->url)
                <video autoplay muted loop playsinline preload="metadata" @if ($heroPoster?->url) poster="{{ $heroPoster->url }}" @endif>
                    <source src="{{ $heroVideo->url }}" type="{{ $heroVideo->type ?: 'video/mp4' }}">
                </video>
            @elseif ($heroPoster?->url)
                <img src="{{ $heroPoster->url }}" alt="" loading="eager">
            @endif
        </div>
        <div class="landing07-event-hero__shade"></div>
        <div class="landing-shell landing07-event-hero__grid">
            <div class="landing07-event-hero__copy" data-aos="fade-up">
                @if (filled($hero['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $hero['eyebrow'] }}</p>@endif
                <p class="landing07-event-hero__service-line">{{ $landingTemplateSettings['event_service_line'] }}</p>
                <h1>{{ $hero['title'] ?? $landingPage->title }}</h1>
                @if (filled($hero['subtitle'] ?? null))<p class="landing07-event-hero__summary">{{ $hero['subtitle'] }}</p>@endif
                <div class="landing07-event-hero__actions">
                    <a class="landing-button landing-button--primary" href="{{ $hero['cta_url'] ?? '#tu-van' }}" data-landing-event="cta_click" data-block-id="{{ $heroBlock['id'] ?? 'hero' }}">{{ $hero['cta_label'] ?? $landingTemplateSettings['event_nav_cta'] }} <span aria-hidden="true">→</span></a>
                    @if (filled($hero['secondary_label'] ?? null))
                        <a class="landing07-event-hero__secondary" href="{{ $hero['secondary_url'] ?? '#bo-dau-ra' }}" data-landing-event="cta_click" data-block-id="{{ $heroBlock['id'] ?? 'hero' }}">{{ $hero['secondary_label'] }} <span aria-hidden="true">↓</span></a>
                    @endif
                </div>
            </div>
            <aside class="landing07-event-brief" data-aos="fade-left">
                <span>{{ $landingTemplateSettings['event_brief_label'] }}</span>
                <h2>{{ $landingTemplateSettings['event_brief_title'] }}</h2>
                <p>{{ $landingTemplateSettings['event_brief_text'] }}</p>
                <ul>
                    @foreach (($landingTemplateSettings['event_brief_items'] ?? []) as $briefItem)
                        <li>{{ $briefItem }}</li>
                    @endforeach
                </ul>
                <a class="landing-button landing-button--primary" href="#tu-van" data-landing-event="cta_click" data-block-id="event-brief">Gửi thông tin <span aria-hidden="true">↗</span></a>
            </aside>
        </div>
        <div class="landing07-event-hero__caption"><span>01 / EVENT MEDIA</span><span>{{ $landingTemplateSettings['event_caption'] }}</span></div>
    </section>

        @include('frontend.landing-pages.partials.builder-content', ['landingBlocks' => $contentBlocks])

    <footer class="landing07-event-footer">
        <span>{{ $landingTemplateSettings['event_footer_text'] }}</span>
        <a href="#tu-van" data-landing-event="cta_click" data-block-id="event-footer">{{ $landingTemplateSettings['event_nav_cta'] }} <span aria-hidden="true">→</span></a>
    </footer>
</div>
