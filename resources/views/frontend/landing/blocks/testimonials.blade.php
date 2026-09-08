@php($data = $block['data'])

@if ($block['items']->isNotEmpty())
    <section class="landing-section landing-testimonials" id="{{ $block['id'] }}" data-landing-block="testimonials">
        <div class="landing-shell">
            <header class="landing-section-heading">
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                <h2>{{ $data['title'] ?? 'Góc nhìn khách hàng' }}</h2>
            </header>
            <div class="landing-testimonials__grid">
                @foreach ($block['items'] as $item)
                    <blockquote class="landing-testimonial"><p>“{{ $item['quote'] ?? $item['text'] ?? '' }}”</p><footer>{{ $item['author'] ?? $item['name'] ?? '' }}@if (filled($item['role'] ?? null)), {{ $item['role'] }}@endif</footer></blockquote>
                @endforeach
            </div>
        </div>
    </section>
@endif
