<img src="{{ $image['url'] }}"
    @if ($image['small_url']) srcset="{{ $image['small_url'] }} 768w, {{ $image['url'] }} 1536w" sizes="{{ $sizes ?? '(max-width: 767px) 100vw, 33vw' }}" @endif
    alt="{{ $alt ?? $image['alt'] }}" width="1536" height="1024" loading="lazy" decoding="async">
