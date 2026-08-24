<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seo->title }}</title>
    @if ($seo->description)
        <meta name="description" content="{{ $seo->description }}">
    @endif
    <meta name="robots" content="{{ implode(', ', $seo->robots) }}">
    @if ($emitCanonical ?? true)
        <link rel="canonical" href="{{ $seo->canonicalUrl }}">
    @endif
    <meta property="og:type" content="{{ $item->type === 'post' ? 'article' : 'website' }}">
    <meta property="og:title" content="{{ $seo->ogTitle }}">
    @if ($seo->ogDescription)
        <meta property="og:description" content="{{ $seo->ogDescription }}">
    @endif
    @if ($emitCanonical ?? true)
        <meta property="og:url" content="{{ $seo->canonicalUrl }}">
    @endif
    @if ($seo->ogImageUrl)
        <meta property="og:image" content="{{ $seo->ogImageUrl }}">
    @endif
    <meta name="twitter:card" content="{{ $seo->twitterImageUrl ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seo->twitterTitle }}">
    @if ($seo->twitterDescription)
        <meta name="twitter:description" content="{{ $seo->twitterDescription }}">
    @endif
    @if ($seo->twitterImageUrl)
        <meta name="twitter:image" content="{{ $seo->twitterImageUrl }}">
    @endif
    @foreach ($seo->structuredData as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endforeach
    <style>
        body { margin: 0; color: #172033; background: #f7f8fa; font: 16px/1.7 system-ui, sans-serif; }
        main { width: min(1080px, calc(100% - 32px)); margin: 48px auto; padding: clamp(24px, 5vw, 64px); background: #fff; box-sizing: border-box; }
        h1 { color: #ed6b21; font-size: clamp(32px, 5vw, 56px); line-height: 1.12; }
        img { max-width: 100%; height: auto; }
        .migration-note { padding: 12px 16px; border-left: 4px solid #ed6b21; background: #fff4ed; }
    </style>
</head>
<body>
    <main>
        @if (app()->environment('local'))
            <p class="migration-note">Bản Laravel kiểm tra dữ liệu và SEO; giao diện chính thức đang được dựng.</p>
        @endif
        <article>
            <h1>{{ $item->title }}</h1>
            {!! $bodyHtml !!}
        </article>
    </main>
</body>
</html>
