<article class="swiper-slide h-auto">
    <div class="d-flex h-100 flex-column overflow-hidden card">
        <a class="position-relative d-block overflow-hidden post-card__image" href="{{ route('slug.show', ['slug' => $post->slug]) }}">
            @if ($post->image_url)
                <img class="h-100 w-100 object-fit-cover" src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">
            @else
                <span class="image-placeholder">DV</span>
            @endif

            @if ($post->category)
                <span class="position-absolute fw-bold text-uppercase badge text-bg-primary bottom-0 start-0 m-3">{{ $post->category->name }}</span>
            @endif
        </a>
        <div class="d-flex flex-column card-body">
            @if ($post->published_at)<time class="fw-medium small text-body-secondary" datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('d/m/Y') }}</time>@endif
            <h3 class="display-title h4 mt-2"><a class="link-body-emphasis text-decoration-none" href="{{ route('slug.show', ['slug' => $post->slug]) }}">{{ $post->title }}</a></h3>
            @if ($post->excerpt)<p class="text-body">{{ $post->excerpt }}</p>@endif
            <a class="section-link mt-auto pt-3" href="{{ route('slug.show', ['slug' => $post->slug]) }}">Xem thêm <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
        </div>
    </div>
</article>
