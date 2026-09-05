@extends($landingLayout ?? 'layouts.landing')

@php
    $settings = $landingTemplateSettings ?? [];
    $heroBlock = collect($landingBlocks)->first(fn (array $block): bool => $block['type'] === 'hero');
    $hero = $heroBlock['data'] ?? [];
    $heroPoster = $heroBlock['media'] ?? $landingPage->curatorMedia;
    $heroVideo = $landingTemplateMedia['hero_video'] ?? null;
    $showreel = $landingTemplateMedia['showreel'] ?? null;
    $contentBlocks = collect($landingBlocks)
        ->reject(fn (array $block): bool => $heroBlock !== null && $block['id'] === $heroBlock['id'])
        ->values()
        ->all();
    $logoUrl = $websiteMediaUrls[$website->logo_media_id] ?? null;
    $navigation = [
        ['label' => 'Dự án', 'url' => '#du-an'],
        ['label' => 'Bảng giá', 'url' => '#bang-gia'],
    ];
@endphp

@section('body_class', 'tht07-landing min-h-screen overflow-x-clip')
@section('main_id', 'tht07-main')
@section('main_class', 'tht07-main')

@section('head')
    <x-landing-theme-tokens selector=".landing-page--landing07-film" :theme="$landingTheme" />
@endsection

@section('landing_header')
    <x-landing.header
        variant="landing07"
        :brand="$settings['film_brand_label'] ?? 'THT MEDIA FILMS'"
        :logo="$logoUrl"
        :navigation="$navigation"
        :cta-label="$settings['film_nav_cta'] ?? 'Nhận tư vấn'"
        :zalo-url="$website->zalo_url"
    />
@endsection

@section('content')
<div
    class="landing-page landing-page--landing07-film"
    data-landing-page
    data-landing-id="{{ $landingPage->id }}"
    data-track-endpoint="{{ $landingTrackingUrl }}"
    data-campaign-state="{{ $landingCampaignState }}"
    data-template-source="landing07-corporate-film"
>

    <x-landing.campaign-notice :state="$landingCampaignState" :message="$landingPage->expired_message" />

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
                <p class="landing07-film-hero__service-line">{{ $settings['film_service_line'] ?? '' }}</p>
                <h1>{{ $hero['title'] ?? $landingPage->title }}</h1>
                @if (filled($hero['subtitle'] ?? null))<p class="landing07-film-hero__summary">{{ $hero['subtitle'] }}</p>@endif
                <div class="landing07-film-hero__actions">
                    <a class="landing-button landing-button--primary" href="{{ $hero['cta_url'] ?? '#tu-van' }}" data-landing-event="cta_click" data-block-id="{{ $heroBlock['id'] ?? 'hero' }}">{{ $hero['cta_label'] ?? ($settings['film_nav_cta'] ?? 'Nhận tư vấn') }} <span aria-hidden="true">↗</span></a>
                    @if ($showreel?->url)
                        <a class="glightbox landing07-film-showreel" href="{{ $showreel->url }}" data-type="video" data-landing-event="video_play" data-block-id="film-showreel">
                            <span aria-hidden="true">▶</span>{{ $settings['film_showreel_label'] ?? 'Xem showreel' }}
                        </a>
                    @elseif (filled($hero['secondary_label'] ?? null))
                        <a class="landing07-film-showreel" href="{{ $hero['secondary_url'] ?? '#du-an' }}" data-landing-event="cta_click" data-block-id="{{ $heroBlock['id'] ?? 'hero' }}"><span aria-hidden="true">↓</span>{{ $hero['secondary_label'] }}</a>
                    @endif
                </div>
            </div>
            @if (filled($settings['film_quote'] ?? null))
                <blockquote class="landing07-film-hero__quote" data-aos="fade-left">“{{ $settings['film_quote'] }}”</blockquote>
            @endif
        </div>
        <div class="landing07-film-hero__scroll"><span>01 / THT FILMS</span><a href="#gia-tri-bo-phim">Cuộn để khám phá <span aria-hidden="true">↓</span></a></div>
    </section>

        @include('frontend.landing-pages.partials.builder-content', ['landingBlocks' => $contentBlocks])

</div>
@endsection

@section('landing_footer')
    <x-landing.footer
        variant="landing07"
        :brand="$settings['film_brand_label'] ?? 'THT MEDIA FILMS'"
        :logo="$logoUrl"
        :text="$settings['film_footer_text'] ?? 'THT Media · Đồng hành từ ý tưởng đến thước phim'"
        :contact="[
            'hotline_1' => $website->hotline ?: $website->contact_phone,
            'email' => $website->contact_email,
            'address_1' => $website->address,
        ]"
        :zalo-url="$website->zalo_url"
        :facebook-url="$website->facebook_url"
    />
@endsection
