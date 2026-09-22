<article class="resource-card">
    <a class="resource-card__media" href="{{ route('slug.show', ['slug' => $service->slug]) }}" aria-label="Xem {{ $service->title }}">
        @if ($service->image_url ?: ($defaultBannerUrl ?? null))
            <img src="{{ $service->image_url ?: $defaultBannerUrl }}" alt="{{ $service->title }}" loading="lazy">
        @else
            <span class="image-placeholder">DV</span>
        @endif
        @if (($showCategoryBadge ?? true) && $service->category)
            <span class="resource-card__badge">{{ $service->category->name }}</span>
        @endif
    </a>
    <div class="resource-card__body">
        <h3 class="fw-bold h4"><a class="link-body-emphasis text-decoration-none" href="{{ route('slug.show', ['slug' => $service->slug]) }}">{{ $service->title }}</a></h3>
        @if ($service->excerpt)
            <p class="text-body">{{ $service->excerpt }}</p>
        @endif
        <a class="resource-card__link" href="{{ route('slug.show', ['slug' => $service->slug]) }}">Xem chi tiết <span aria-hidden="true">→</span></a>
    </div>
</article>
