@use(App\Support\Localization\LocalizedUrl)

<div
    class="landing-page landing-page--conversion"
    data-landing-page
    data-landing-id="{{ $landingPage->id }}"
    data-track-endpoint="{{ $landingTrackingUrl }}"
    data-campaign-state="{{ $landingCampaignState }}"
    style="--landing-primary: {{ $landingTheme['primary'] }}; --landing-accent: {{ $landingTheme['accent'] }}; --landing-surface: {{ $landingTheme['surface'] }}; --landing-ink: {{ $landingTheme['ink'] }};"
>
    <header class="landing-conversion-nav">
        <a class="landing-conversion-brand" href="{{ LocalizedUrl::route('home') }}" aria-label="Về trang chủ THT Media">
            @if ($websiteMediaUrls[$website->logo_media_id] ?? null)<img src="{{ $websiteMediaUrls[$website->logo_media_id] }}" alt="" aria-hidden="true">@endif
            <span>{{ $landingTemplateSettings['conversion_brand_label'] }}</span>
        </a>
        <a class="landing-conversion-nav__cta" href="#tu-van" data-landing-event="cta_click" data-block-id="conversion-nav">{{ $landingTemplateSettings['conversion_nav_cta'] }} <span aria-hidden="true">↗</span></a>
    </header>

    <div class="landing-conversion-ribbon">
        <strong>{{ $landingTemplateSettings['conversion_badge'] }}</strong>
        <span>{{ $landingTemplateSettings['conversion_proof_text'] }}</span>
    </div>

    @include('frontend.landing-pages.partials.campaign-notice')
        @include('frontend.landing-pages.partials.builder-content')

    <footer class="landing-conversion-footer">
        <strong>{{ $landingTemplateSettings['conversion_footer_text'] }}</strong>
        <a href="#tu-van" data-landing-event="cta_click" data-block-id="conversion-footer">{{ $landingTemplateSettings['conversion_nav_cta'] }} <span aria-hidden="true">→</span></a>
    </footer>
</div>
