@forelse ($landingBlocks as $block)
    @continue($landingCampaignState === 'expired' && $service->expired_behavior === 'hide_offer' && in_array($block['type'], ['countdown', 'benefits', 'pricing'], true))
    @include($block['view'], ['block' => $block, 'landing' => $service])
@empty
    <section class="landing-empty-state">
        <div class="landing-shell">
            <h1>{{ $service->title }}</h1>
            <p>Landing page đang được hoàn thiện trong quản trị.</p>
        </div>
    </section>
@endforelse
