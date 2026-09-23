<article class="resource-card service-list-card">
    <a class="resource-card__media" href="{{ route('slug.show', ['slug' => $service->slug]) }}" aria-label="Xem {{ $service->title }}">
        <img src="{{ $service->image_url ?: asset('images/no-image.svg') }}" alt="{{ $service->title }}" loading="lazy">
        @if (($showCategoryBadge ?? true) && $service->category)
            <span class="resource-card__badge">{{ $service->category->name }}</span>
        @endif
    </a>
    <div class="resource-card__body">
        <span class="service-list-card__icon" aria-hidden="true"><i class="fa-solid fa-car-side"></i></span>
        <h3 class="fw-bold h4"><a class="link-body-emphasis text-decoration-none" href="{{ route('slug.show', ['slug' => $service->slug]) }}">{{ $service->title }}</a></h3>
        @if ($service->excerpt)
            <p class="text-body">{{ $service->excerpt }}</p>
        @endif
        <a class="resource-card__link" href="{{ route('slug.show', ['slug' => $service->slug]) }}">Khám phá dịch vụ <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</article>
