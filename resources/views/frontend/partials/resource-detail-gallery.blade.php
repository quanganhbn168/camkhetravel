<section class="resource-detail-gallery">
    <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
        <h2 class="display-title text-3xl leading-tight md:text-4xl">{{ $galleryTitle }}</h2>
        <div class="resource-detail-gallery__grid mt-8">
            @foreach ($galleryImages as $imageUrl)
                <a class="resource-detail-gallery__item group" href="{{ $imageUrl }}" target="_blank" rel="noopener" aria-label="Mở ảnh {{ $loop->iteration }} của {{ $galleryAlt }}">
                    <img src="{{ $imageUrl }}" alt="{{ $galleryAlt }} — ảnh {{ $loop->iteration }}" loading="lazy">
                    <span class="resource-detail-gallery__zoom" aria-hidden="true">↗</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
