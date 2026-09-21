@use(App\Support\Localization\LocalizedUrl)

<article class="resource-card site-hover-group">
    <a class="resource-card__media" href="{{ LocalizedUrl::product($product) }}" aria-label="Xem {{ $product->title }}">
        @if ($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->title }}" loading="lazy">
        @else
            <span class="image-placeholder">SP</span>
        @endif
        @if ($product->category)
            <span class="resource-card__badge">{{ $product->category->name }}</span>
        @endif
    </a>
    <div class="resource-card__body">
        <h2 class="fw-bold site-partials-product-card__heading-1"><a class="site-partials-product-card__action-2" href="{{ LocalizedUrl::product($product) }}">{{ $product->title }}</a></h2>
        @if ($product->sku)<p class="fw-semibold text-uppercase site-partials-product-card__copy-3">Mã: {{ $product->sku }}</p>@endif
        @if ($product->excerpt)<p class="site-partials-product-card__copy-4">{{ $product->excerpt }}</p>@endif
        <a class="resource-card__link" href="{{ LocalizedUrl::product($product) }}">Xem chi tiết <span aria-hidden="true">→</span></a>
    </div>
</article>
