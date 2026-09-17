@use(App\Support\Localization\LocalizedUrl)

<article class="resource-card dv-hover-group">
    <a class="resource-card__media" href="{{ LocalizedUrl::slug($service->slug) }}" aria-label="Xem {{ $service->title }}">
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
        <h3 class="fw-bold dv-partials-service-card__heading-1"><a class="dv-partials-service-card__action-2" href="{{ LocalizedUrl::slug($service->slug) }}">{{ $service->title }}</a></h3>
        @if ($service->excerpt)
            <p class="dv-partials-service-card__copy-3">{{ $service->excerpt }}</p>
        @endif
        <a class="resource-card__link" href="{{ LocalizedUrl::slug($service->slug) }}">Xem chi tiết <span aria-hidden="true">→</span></a>
    </div>
</article>
