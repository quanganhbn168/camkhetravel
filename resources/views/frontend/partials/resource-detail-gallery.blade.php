<section class="resource-detail-gallery">
    <div class="site-container w-100 mx-auto dv-partials-resource-detail-gallery__div-1">
        <h2 class="display-title dv-partials-resource-detail-gallery__heading-2">{{ $galleryTitle }}</h2>
        <div class="resource-detail-gallery__grid dv-partials-resource-detail-gallery__div-3">
            @foreach ($galleryImages as $imageUrl)
                <a class="resource-detail-gallery__item dv-hover-group" href="{{ $imageUrl }}" target="_blank" rel="noopener" aria-label="Mở ảnh {{ $loop->iteration }} của {{ $galleryAlt }}">
                    <img src="{{ $imageUrl }}" alt="{{ $galleryAlt }} — ảnh {{ $loop->iteration }}" loading="lazy">
                    <span class="resource-detail-gallery__zoom" aria-hidden="true">↗</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
