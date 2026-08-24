<section class="resource-detail-gallery">
    <div class="site-shell">
        @unless ($hideGalleryEyebrow ?? false)<p class="eyebrow">{{ $galleryEyebrow ?? 'Thư viện hình ảnh' }}</p>@endunless
        <h2 class="display-title mt-3 text-3xl leading-tight md:text-4xl">{{ $galleryTitle }}</h2>
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
