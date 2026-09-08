@php($data = $block['data'])

@if ($block['items']->isNotEmpty())
    <section class="landing-section landing-content-grid" id="{{ $block['id'] }}" data-landing-block="content-grid">
        <div class="landing-shell">
            <header class="landing-section-heading">
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                <h2>{{ $data['title'] ?? 'Giải pháp theo mục tiêu' }}</h2>
                @if (filled($data['description'] ?? null))<p>{{ $data['description'] }}</p>@endif
            </header>
            <div class="landing-content-grid__items">
                @foreach ($block['items'] as $item)
                    <article class="landing-content-card" data-aos="fade-up" data-aos-delay="{{ min(240, $loop->index * 80) }}">
                        <span class="landing-content-card__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $item['title'] ?? $item['label'] }}</h3>
                        @if (filled($item['description'] ?? $item['text'] ?? null))<p>{{ $item['description'] ?? $item['text'] }}</p>@endif
                        @if (collect($item['features'] ?? [])->filter()->isNotEmpty())
                            <ul>@foreach (collect($item['features'])->filter() as $feature)<li>{{ $feature }}</li>@endforeach</ul>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
