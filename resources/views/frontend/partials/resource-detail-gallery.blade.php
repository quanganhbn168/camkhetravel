<section class="resource-detail-gallery">
    <div class="container">
        <h2 class="display-title h2 mb-4">{{ $galleryTitle }}</h2>
        <div class="gallery-grid">
            @foreach ($galleryImages as $imageUrl)
                <a class="resource-detail-gallery__item" href="{{ $imageUrl }}" target="_blank" rel="noopener" aria-label="Mở ảnh {{ $loop->iteration }} của {{ $galleryAlt }}">
                    <img src="{{ $imageUrl }}" alt="{{ $galleryAlt }} — ảnh {{ $loop->iteration }}" loading="lazy">
                    <span class="resource-detail-gallery__zoom" aria-hidden="true">↗</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
