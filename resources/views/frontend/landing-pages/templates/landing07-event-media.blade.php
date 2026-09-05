@extends($landingLayout ?? 'layouts.landing')

@php
    $settings = $landingTemplateSettings ?? [];
    $heroBlock = collect($landingBlocks)->first(fn (array $block): bool => $block['type'] === 'hero');
    $hero = $heroBlock['data'] ?? [];
    $heroPoster = $heroBlock['media'] ?? $landingPage->curatorMedia;
    $heroVideo = $landingTemplateMedia['hero_video'] ?? null;
    $contentBlocks = collect($landingBlocks)
        ->reject(fn (array $block): bool => $heroBlock !== null && $block['id'] === $heroBlock['id'])
        ->values()
        ->all();
    $logoUrl = $websiteMediaUrls[$website->logo_media_id] ?? null;
    $navigation = [
        ['label' => 'Bộ đầu ra', 'url' => '#bo-dau-ra'],
        ['label' => 'Dự án', 'url' => '#du-an'],
    ];
@endphp

@section('body_class', 'tht07-landing min-h-screen overflow-x-clip')
@section('main_id', 'tht07-main')
@section('main_class', 'tht07-main')

@section('head')
    <x-landing-theme-tokens selector=".landing-page--landing07-event" :theme="$landingTheme" />
@endsection

@section('landing_header')
    <x-landing.header
        variant="landing07"
        :brand="$settings['event_brand_label'] ?? 'THT MEDIA EVENT'"
        :logo="$logoUrl"
        :navigation="$navigation"
        :cta-label="$settings['event_nav_cta'] ?? 'Nhận tư vấn'"
        :zalo-url="$website->zalo_url"
    />
@endsection

@section('content')
<div
    class="landing-page landing-page--landing07-event"
    data-landing-page
    data-landing-id="{{ $landingPage->id }}"
    data-track-endpoint="{{ $landingTrackingUrl }}"
    data-campaign-state="{{ $landingCampaignState }}"
    data-template-source="landing07-event-media"
    style="--landing-primary: {{ $landingTheme['primary'] }}; --landing-accent: {{ $landingTheme['accent'] }}; --landing-surface: {{ $landingTheme['surface'] }}; --landing-ink: {{ $landingTheme['ink'] }};"
>
    <x-landing.campaign-notice :state="$landingCampaignState" :message="$landingPage->expired_message" />

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
                <p class="landing07-event-hero__service-line">{{ $settings['event_service_line'] ?? '' }}</p>
                <h1>{{ $hero['title'] ?? $landingPage->title }}</h1>
                @if (filled($hero['subtitle'] ?? null))<p class="landing07-event-hero__summary">{{ $hero['subtitle'] }}</p>@endif
                <div class="landing07-event-hero__actions">
                    <a class="landing-button landing-button--primary" href="{{ $hero['cta_url'] ?? '#tu-van' }}" data-landing-event="cta_click" data-block-id="{{ $heroBlock['id'] ?? 'hero' }}">{{ $hero['cta_label'] ?? ($settings['event_nav_cta'] ?? 'Nhận tư vấn') }} <span aria-hidden="true">→</span></a>
                    @if (filled($hero['secondary_label'] ?? null))
                        <a class="landing07-event-hero__secondary" href="{{ $hero['secondary_url'] ?? '#bo-dau-ra' }}" data-landing-event="cta_click" data-block-id="{{ $heroBlock['id'] ?? 'hero' }}">{{ $hero['secondary_label'] }} <span aria-hidden="true">↓</span></a>
                    @endif
                </div>
            </div>
            <aside class="landing07-event-brief" data-aos="fade-left">
                <span>{{ $settings['event_brief_label'] ?? 'Gửi brief' }}</span>
                <h2>{{ $settings['event_brief_title'] ?? 'Để THT Media hiểu đúng chương trình' }}</h2>
                <p>{{ $settings['event_brief_text'] ?? 'Chia sẻ mục tiêu, quy mô và thời gian dự kiến để nhận phương án phù hợp.' }}</p>
                <ul>
                    @foreach (($settings['event_brief_items'] ?? []) as $briefItem)
                        <li>{{ $briefItem }}</li>
                    @endforeach
                </ul>
                <a class="landing-button landing-button--primary" href="#tu-van" data-landing-event="cta_click" data-block-id="event-brief">Gửi thông tin <span aria-hidden="true">↗</span></a>
            </aside>
        </div>
        <div class="landing07-event-hero__caption"><span>01 / EVENT MEDIA</span><span>{{ $settings['event_caption'] ?? '' }}</span></div>
    </section>

        @include('frontend.landing-pages.partials.builder-content', ['landingBlocks' => $contentBlocks])

</div>
@endsection

@section('landing_footer')
    <x-landing.footer
        variant="landing07"
        :brand="$settings['event_brand_label'] ?? 'THT MEDIA EVENT'"
        :logo="$logoUrl"
        :text="$settings['event_footer_text'] ?? 'THT Media · Đồng hành từ ý tưởng đến hiện trường'"
        :contact="[
            'hotline_1' => $website->hotline ?: $website->contact_phone,
            'email' => $website->contact_email,
            'address_1' => $website->address,
        ]"
        :zalo-url="$website->zalo_url"
        :facebook-url="$website->facebook_url"
    />
@endsection
