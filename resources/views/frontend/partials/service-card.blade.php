@use(App\Support\Localization\LocalizedUrl)

<article class="resource-card group">
    <a class="resource-card__media" href="{{ LocalizedUrl::slug($service->slug) }}" aria-label="Xem {{ $service->title }}">
        @if ($service->image_url)
            <img src="{{ $service->image_url }}" alt="{{ $service->title }}" loading="lazy">
        @else
            <span class="image-placeholder">THT</span>
        @endif
        @if (($showCategoryBadge ?? true) && $service->category)
            <span class="resource-card__badge">{{ $service->category->name }}</span>
        @endif
    </a>
    <div class="resource-card__body">
        <h3 class="text-lg leading-6 font-bold text-ink md:text-xl"><a class="hover:text-accent" href="{{ LocalizedUrl::slug($service->slug) }}">{{ $service->title }}</a></h3>
        @if ($service->excerpt)
            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-500">{{ $service->excerpt }}</p>
        @endif
        <a class="resource-card__link" href="{{ LocalizedUrl::slug($service->slug) }}">Xem chi tiết <span aria-hidden="true">→</span></a>
    </div>
</article>
