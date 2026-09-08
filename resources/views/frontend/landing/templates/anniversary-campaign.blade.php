@use(App\Support\Localization\LocalizedUrl)

<div
    class="landing-page landing-page--anniversary"
    data-landing-page
    data-landing-id="{{ $landingPage->id }}"
    data-track-endpoint="{{ $landingTrackingUrl }}"
    data-campaign-state="{{ $landingCampaignState }}"
    style="--landing-primary: {{ $landingTheme['primary'] }}; --landing-accent: {{ $landingTheme['accent'] }}; --landing-surface: {{ $landingTheme['surface'] }}; --landing-ink: {{ $landingTheme['ink'] }};"
>
    <header class="landing-campaign-nav">
        <a class="landing-campaign-brand" href="{{ LocalizedUrl::route('home') }}" aria-label="Về trang chủ THT Media">
            @if ($websiteMediaUrls[$website->logo_media_id] ?? null)<img src="{{ $websiteMediaUrls[$website->logo_media_id] }}" alt="" aria-hidden="true">@endif
            <span>{{ $landingTemplateSettings['anniversary_brand_label'] }}</span>
        </a>
        <a class="landing-campaign-nav__cta" href="#tu-van" data-landing-event="cta_click" data-block-id="campaign-nav">{{ $landingTemplateSettings['anniversary_nav_cta'] }} <span aria-hidden="true">↗</span></a>
    </header>

    @if (filled($landingTemplateSettings['anniversary_notice'] ?? null))
        <div class="landing-anniversary-ribbon">{{ $landingTemplateSettings['anniversary_notice'] }}</div>
    @endif

    @include('frontend.landing.partials.campaign-notice')

        @include('frontend.landing.partials.builder-content')

    <footer class="landing-campaign-footer">
        <span>{{ $landingTemplateSettings['anniversary_footer_text'] }}</span>
        <a href="#tu-van" data-landing-event="cta_click" data-block-id="campaign-footer">Đăng ký tư vấn</a>
    </footer>
</div>
