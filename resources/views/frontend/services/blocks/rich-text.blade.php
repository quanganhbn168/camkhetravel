@php($data = $block['data'])

@if (filled(strip_tags((string) ($data['body'] ?? ''))))
    <section class="landing-section landing-rich-text" id="{{ $block['id'] }}" data-landing-block="rich-text">
        <div class="landing-shell landing-rich-text__grid">
            <header class="landing-section-heading">
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                @if (filled($data['title'] ?? null))<h2>{{ $data['title'] }}</h2>@endif
            </header>
            <article class="article-prose">{!! $data['body'] !!}</article>
        </div>
    </section>
@endif
