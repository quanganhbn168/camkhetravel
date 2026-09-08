@forelse ($landingBlocks as $block)
    @continue($landingCampaignState === 'expired' && $landingPage->expired_behavior === 'hide_offer' && in_array($block['type'], ['countdown', 'benefits', 'pricing'], true))
    @include($block['view'], ['block' => $block, 'landingPage' => $landingPage])
@empty
    <section class="landing-empty-state">
        <div class="landing-shell">
            <h1>{{ $landingPage->title }}</h1>
            <p>Landing page đang được hoàn thiện trong quản trị.</p>
        </div>
    </section>
@endforelse
