<article class="resource-card">
    <a class="resource-card__media" href="{{ route('products.show', ['slug' => $product->slug]) }}" aria-label="Xem {{ $product->title }}">
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
        <h2 class="fw-bold h4"><a class="link-body-emphasis text-decoration-none" href="{{ route('products.show', ['slug' => $product->slug]) }}">{{ $product->title }}</a></h2>
        @if ($product->sku)<p class="fw-semibold text-uppercase small text-body">Mã: {{ $product->sku }}</p>@endif
        @if ($product->excerpt)<p class="text-body">{{ $product->excerpt }}</p>@endif
        <a class="resource-card__link" href="{{ route('products.show', ['slug' => $product->slug]) }}">Xem chi tiết <span aria-hidden="true">→</span></a>
    </div>
</article>
