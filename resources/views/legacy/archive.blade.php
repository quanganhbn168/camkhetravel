<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="{{ implode(', ', $robots) }}">
    @if ($canonicalUrl)
        <link rel="canonical" href="{{ $canonicalUrl }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    @if ($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}">
    @endif
    <meta name="twitter:card" content="{{ $ogImageUrl ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    @if ($ogImageUrl)
        <meta name="twitter:image" content="{{ $ogImageUrl }}">
    @endif
    @if ($previousUrl)
        <link rel="prev" href="{{ $previousUrl }}">
    @endif
    @if ($nextUrl)
        <link rel="next" href="{{ $nextUrl }}">
    @endif
    @if ($structuredData)
        <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endif
    <style>
        body { margin: 0; color: #172033; background: #f7f8fa; font: 16px/1.6 system-ui, sans-serif; }
        main { width: min(1180px, calc(100% - 32px)); margin: 48px auto; }
        h1 { color: #ed6b21; font-size: clamp(34px, 5vw, 56px); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; }
        article { overflow: hidden; background: #fff; border-radius: 16px; box-shadow: 0 12px 35px rgba(23, 32, 51, .08); }
        article img, .placeholder { display: block; width: 100%; aspect-ratio: 16 / 10; object-fit: cover; background: #e8ebf0; }
        article div { padding: 20px; }
        article h2 { margin: 0; font-size: 20px; line-height: 1.35; }
        a { color: inherit; text-decoration: none; }
        nav { display: flex; justify-content: space-between; gap: 16px; margin-top: 32px; }
    </style>
</head>
<body>
    <main>
        <h1>{{ $term->name }}</h1>
        <div class="grid">
            @forelse ($items as $item)
                @php($image = $media->get($item->featured_media_source_id))
                <article>
                    <a href="{{ $item->canonical_path }}">
                        @if ($image?->public_url)
                            <img src="{{ $image->public_url }}" alt="{{ $image->effective_alt_text ?: $image->alt_text ?: $item->title }}" loading="lazy" width="{{ $image->width ?: 800 }}" height="{{ $image->height ?: 500 }}">
                        @else
                            <span class="placeholder" aria-hidden="true"></span>
                        @endif
                        <div><h2>{{ $item->title }}</h2></div>
                    </a>
                </article>
            @empty
                <p>Chưa có nội dung trong chuyên mục này.</p>
            @endforelse
        </div>
        @if ($items->lastPage() > 1)
            <nav aria-label="Phân trang">
                <span>@if ($previousUrl)<a href="{{ $previousUrl }}">← Trang trước</a>@endif</span>
                <span>Trang {{ $items->currentPage() }} / {{ $items->lastPage() }}</span>
                <span>@if ($nextUrl)<a href="{{ $nextUrl }}">Trang sau →</a>@endif</span>
            </nav>
        @endif
    </main>
</body>
</html>
