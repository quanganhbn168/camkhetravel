@use(App\Support\Localization\LocalizedUrl)

<article class="resource-card group">
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
        <h2 class="text-lg leading-6 font-bold text-ink md:text-xl"><a class="hover:text-accent" href="{{ LocalizedUrl::product($product) }}">{{ $product->title }}</a></h2>
        @if ($product->sku)<p class="mt-2 text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Mã: {{ $product->sku }}</p>@endif
        @if ($product->excerpt)<p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-500">{{ $product->excerpt }}</p>@endif
        <a class="resource-card__link" href="{{ LocalizedUrl::product($product) }}">Xem chi tiết <span aria-hidden="true">→</span></a>
    </div>
</article>
