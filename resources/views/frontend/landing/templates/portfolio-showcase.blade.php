@use(App\Support\Localization\LocalizedUrl)

<div
    class="landing-page landing-page--portfolio"
    data-landing-page
    data-landing-id="{{ $landingPage->id }}"
    data-track-endpoint="{{ $landingTrackingUrl }}"
    data-campaign-state="{{ $landingCampaignState }}"
    style="--landing-primary: {{ $landingTheme['primary'] }}; --landing-accent: {{ $landingTheme['accent'] }}; --landing-surface: {{ $landingTheme['surface'] }}; --landing-ink: {{ $landingTheme['ink'] }};"
>
    <header class="landing-portfolio-masthead">
        <div class="landing-portfolio-masthead__top">
            <a href="{{ LocalizedUrl::route('home') }}">{{ $landingTemplateSettings['portfolio_brand_label'] }}</a>
            <span>{{ $landingTemplateSettings['portfolio_issue_label'] }}</span>
            <a href="#tu-van" data-landing-event="cta_click" data-block-id="portfolio-nav">{{ $landingTemplateSettings['portfolio_nav_cta'] }} ↗</a>
        </div>
        <p>{{ $landingTemplateSettings['portfolio_intro'] }}</p>
    </header>

    @include('frontend.landing.partials.campaign-notice')
        @include('frontend.landing.partials.builder-content')

    <footer class="landing-portfolio-footer">
        <span>{{ $landingTemplateSettings['portfolio_footer_text'] }}</span>
        <a href="#tu-van" data-landing-event="cta_click" data-block-id="portfolio-footer">{{ $landingTemplateSettings['portfolio_nav_cta'] }} <span aria-hidden="true">→</span></a>
    </footer>
</div>
