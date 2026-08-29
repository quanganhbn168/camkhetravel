@php($data = $block['data'])

@if ($block['media_items']->isNotEmpty())
    <section class="landing-section landing-gallery" id="{{ $block['id'] }}" data-landing-block="gallery">
        <div class="landing-shell">
            <header class="landing-section-heading">
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                <h2>{{ $data['title'] ?? 'Hình ảnh chương trình' }}</h2>
                @if (filled($data['description'] ?? null))<p>{{ $data['description'] }}</p>@endif
            </header>
            <div class="landing-gallery__grid">
                @foreach ($block['media_items'] as $media)
                    <a class="glightbox" href="{{ $media->url }}" data-gallery="landing-gallery-{{ $landing->id }}" aria-label="Mở ảnh {{ $loop->iteration }}">
                        <img src="{{ $media->url }}" alt="{{ $media->alt ?: $media->title ?: $landing->title }}" loading="lazy">
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
