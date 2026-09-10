@php
    $data = $block['data'];
    $media = $block['media'];
    $mediaUrl = $media?->url;
@endphp

<section class="landing-hero" id="{{ $block['id'] }}" data-landing-block="hero">
    <div class="landing-shell landing-hero__grid">
        <div class="landing-hero__copy" data-aos="fade-up">
            @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
            <h1>{{ $data['title'] ?? $landingPage->title }}</h1>
            @if (filled($data['subtitle'] ?? null))<p class="landing-hero__subtitle">{{ $data['subtitle'] }}</p>@endif
            <div class="landing-hero__actions">
                <a class="landing-button landing-button--primary" href="{{ $data['cta_url'] ?? '#tu-van' }}">{{ $data['cta_label'] ?? 'Nhận tư vấn' }} <span aria-hidden="true">↗</span></a>
                @if (filled($data['secondary_label'] ?? null))
                    <a class="landing-button landing-button--ghost" href="{{ $data['secondary_url'] ?? '#uu-dai' }}">{{ $data['secondary_label'] }} <span aria-hidden="true">↓</span></a>
                @endif
            </div>
            @if (filled($data['note'] ?? null))<p class="landing-hero__note">{{ $data['note'] }}</p>@endif
        </div>

        @if ($mediaUrl)
            <figure class="landing-hero__media" data-aos="fade-left">
                <img src="{{ $mediaUrl }}" alt="{{ $media->alt ?: $media->title ?: $landingPage->title }}" loading="eager">
                @if (filled($data['media_caption'] ?? null))<figcaption>{{ $data['media_caption'] }}</figcaption>@endif
            </figure>
        @endif
    </div>
</section>
