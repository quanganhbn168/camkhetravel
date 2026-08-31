<div
    class="landing-page landing-page--builder"
    data-landing-page
    data-landing-id="{{ $landingPage->id }}"
    data-track-endpoint="{{ $landingTrackingUrl }}"
    data-campaign-state="{{ $landingCampaignState }}"
    style="--landing-primary: {{ $landingTheme['primary'] }}; --landing-accent: {{ $landingTheme['accent'] }}; --landing-surface: {{ $landingTheme['surface'] }}; --landing-ink: {{ $landingTheme['ink'] }};"
>
    @include('frontend.landing-pages.partials.builder-content')
</div>
