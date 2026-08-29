@php
    $landing = $service;
    $definition = $landingTemplateDefinition ?? [];
    $settings = $landingTemplateSettings ?? [];
    $brand = $settings['landing07_brand_label'] ?? 'THT MEDIA';
    $serviceLine = $settings['landing07_service_line'] ?? ($definition['use_case'] ?? '');
    $navCta = $settings['landing07_nav_cta'] ?? 'Nhận tư vấn';
    $footerText = $settings['landing07_footer_text'] ?? 'THT Media · Đồng hành từ mục tiêu đến đầu ra';
    $hero = collect($landingBlocks)->firstWhere('type', 'hero');
    $heroMedia = $hero['media'] ?? null;
    $heroData = $hero['data'] ?? [];
@endphp

<div class="landing-page {{ $definition['css_class'] ?? 'landing-page--landing07-source' }} landing-page--source" data-landing-page data-landing-id="{{ $landing->id }}" data-track-endpoint="{{ $landingTrackingUrl }}" data-campaign-state="{{ $landingCampaignState }}" data-template-source="landing07-source" style="--landing-primary: {{ $landingTheme['primary'] ?? '#12372a' }}; --landing-accent: {{ $landingTheme['accent'] ?? '#d8a84e' }}; --landing-surface: {{ $landingTheme['surface'] ?? '#f4f7ef' }}; --landing-ink: {{ $landingTheme['ink'] ?? '#14251d' }}">
    <div class="landing07-source-nav">
        <div class="landing-shell landing07-source-nav__inner">
            <a class="landing07-source-brand" href="{{ route('home') }}">{{ $brand }}</a>
            <div class="landing07-source-nav__meta"><span>{{ $serviceLine }}</span><a href="#tu-van">{{ $navCta }} <span aria-hidden="true">↗</span></a></div>
        </div>
    </div>

    <section class="landing07-source-hero" id="hero">
        @if ($heroMedia?->url)<img class="landing07-source-hero__media" src="{{ $heroMedia->url }}" alt="{{ $heroMedia->alt ?: $heroMedia->title ?: $landing->title }}" loading="eager">@endif
        <div class="landing07-source-hero__shade"></div>
        <div class="landing-shell landing07-source-hero__inner">
            <div class="landing07-source-hero__copy">
                @if (filled($heroData['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $heroData['eyebrow'] }}</p>@endif
                <h1>{{ $heroData['title'] ?? $landing->title }}</h1>
                @if (filled($heroData['subtitle'] ?? null))<p class="landing07-source-hero__subtitle">{{ $heroData['subtitle'] }}</p>@endif
                @if (filled($heroData['description'] ?? null))<p class="landing07-source-hero__description">{{ $heroData['description'] }}</p>@endif
                <div class="landing07-source-hero__actions">
                    <a class="landing-button landing-button--primary" href="{{ $heroData['cta_url'] ?? '#tu-van' }}" data-landing-event="cta_click" data-block-id="hero">{{ $heroData['cta_label'] ?? 'Nhận tư vấn' }} <span aria-hidden="true">↗</span></a>
                    @if (filled($heroData['secondary_label'] ?? null))<a class="landing-button landing-button--ghost" href="{{ $heroData['secondary_url'] ?? '#bang-gia' }}">{{ $heroData['secondary_label'] }} <span aria-hidden="true">↓</span></a>@endif
                </div>
            </div>
            <div class="landing07-source-hero__aside"><span>01</span><p>{{ $landing->excerpt }}</p></div>
        </div>
    </section>

    @include('frontend.services.partials.builder-content', ['landingBlocks' => $landingBlocks])

    <footer class="landing07-source-footer">
        <div class="landing-shell landing07-source-footer__inner"><span>{{ $brand }}</span><p>{{ $footerText }}</p><a href="#tu-van">{{ $navCta }} <span aria-hidden="true">↗</span></a></div>
    </footer>
</div>
