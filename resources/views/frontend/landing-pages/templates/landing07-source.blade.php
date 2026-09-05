@extends($landingLayout ?? 'layouts.landing')

@php
    $definition = $landingTemplateDefinition ?? [];
    $settings = $landingTemplateSettings ?? [];
    $brand = $settings['landing07_brand_label'] ?? 'THT MEDIA';
    $serviceLine = $settings['landing07_service_line'] ?? ($definition['use_case'] ?? '');
    $navCta = $settings['landing07_nav_cta'] ?? 'Nhận tư vấn';
    $footerText = $settings['landing07_footer_text'] ?? 'THT Media · Đồng hành từ mục tiêu đến đầu ra';
    $hero = collect($landingBlocks)->firstWhere('type', 'hero');
    $heroMedia = $hero['media'] ?? null;
    $heroData = $hero['data'] ?? [];
    $contentBlocks = collect($landingBlocks)
        ->reject(fn (array $block): bool => ($block['type'] ?? null) === 'hero')
        ->values()
        ->all();
    $logoUrl = $websiteMediaUrls[$website->logo_media_id] ?? null;
    $zaloUrl = $website->zalo_url ?? null;
    $navigation = [
        ['label' => 'Giá trị', 'url' => '#gia-tri'],
        ['label' => 'Quy trình', 'url' => '#quy-trinh'],
        ['label' => 'Dự án', 'url' => '#du-an'],
        ['label' => 'Bảng giá', 'url' => '#bang-gia'],
        ['label' => 'Câu hỏi', 'url' => '#cau-hoi'],
    ];
@endphp

@section('body_class', 'tht07-landing min-h-screen overflow-x-clip')
@section('main_id', 'tht07-main')
@section('main_class', 'tht07-main')

@section('head')
    <x-landing-theme-tokens selector=".landing-page--source" :theme="$landingTheme" />
@endsection

@section('landing_header')
    <x-landing.header
        variant="landing07"
        :brand="$brand"
        :logo="$logoUrl"
        :navigation="$navigation"
        :cta-label="$navCta"
        :zalo-url="$zaloUrl"
    />
@endsection

@section('content')
    <div
        class="landing-page {{ $definition['css_class'] ?? 'landing-page--landing07-source' }} landing-page--source"
        data-landing-page
        data-landing-id="{{ $landingPage->id }}"
        data-track-endpoint="{{ $landingTrackingUrl }}"
        data-campaign-state="{{ $landingCampaignState }}"
        data-template-source="landing07-source"
    >
        <x-landing.campaign-notice :state="$landingCampaignState" :message="$landingPage->expired_message" />

        <section class="landing07-source-hero" id="hero" data-landing-block="hero" aria-labelledby="landing07-source-hero-title">
            @if ($heroMedia?->url)
                <img class="landing07-source-hero__media" src="{{ $heroMedia->url }}" alt="{{ $heroMedia->alt ?: $heroMedia->title ?: $landingPage->title }}" loading="eager" fetchpriority="high">
            @endif
            <div class="landing07-source-hero__shade"></div>
            <div class="landing-shell landing07-source-hero__inner">
                <div class="landing07-source-hero__copy">
                    @if (filled($heroData['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $heroData['eyebrow'] }}</p>@endif
                    <p class="landing07-source-hero__service-line">{{ $serviceLine }}</p>
                    <h1 id="landing07-source-hero-title">{{ $heroData['title'] ?? $landingPage->title }}</h1>
                    @if (filled($heroData['subtitle'] ?? null))<p class="landing07-source-hero__subtitle">{{ $heroData['subtitle'] }}</p>@endif
                    @if (filled($heroData['description'] ?? null))<p class="landing07-source-hero__description">{{ $heroData['description'] }}</p>@endif
                    <div class="landing07-source-hero__actions">
                        <a class="landing-button landing-button--primary tht07-zalo-cta" href="{{ $zaloUrl ?: ($heroData['cta_url'] ?? '#tu-van') }}" @if ($zaloUrl) target="_blank" rel="noopener noreferrer" @endif data-landing-event="cta_click" data-block-id="hero">{{ $heroData['cta_label'] ?? $navCta }} <span aria-hidden="true">↗</span></a>
                        @if (filled($heroData['secondary_label'] ?? null))<a class="landing-button landing-button--ghost" href="{{ $heroData['secondary_url'] ?? '#bang-gia' }}" data-landing-event="nav_click" data-block-id="hero">{{ $heroData['secondary_label'] }} <span aria-hidden="true">↓</span></a>@endif
                    </div>
                </div>
                <div class="landing07-source-hero__aside"><span>01</span><p>{{ $landingPage->excerpt }}</p></div>
            </div>
        </section>

        @include('frontend.landing-pages.partials.builder-content', ['landingBlocks' => $contentBlocks])
    </div>
@endsection

@section('landing_footer')
    <x-landing.footer
        variant="landing07"
        :brand="$brand"
        :logo="$logoUrl"
        :text="$footerText"
        :contact="[
            'hotline_1' => $website->hotline ?: $website->contact_phone,
            'email' => $website->contact_email,
            'address_1' => $website->address,
        ]"
        :zalo-url="$zaloUrl"
        :facebook-url="$website->facebook_url"
    />
@endsection
