@use(App\Support\Localization\LocalizedUrl)

<article class="swiper-slide h-auto">
    <div class="site-hover-group d-flex h-100 flex-column overflow-hidden site-partials-home-post-slide__div-2">
        <a class="position-relative d-block overflow-hidden site-partials-home-post-slide__action-3" href="{{ LocalizedUrl::post($post) }}">
            @if ($post->image_url)
                <img class="h-100 w-100 object-fit-cover site-partials-home-post-slide__media-4" src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">
            @else
                <span class="image-placeholder">DV</span>
            @endif

            @if ($post->categories->first())
                <span class="position-absolute fw-bold text-uppercase site-partials-home-post-slide__copy-5">{{ $post->categories->first()->name }}</span>
            @endif
        </a>
        <div class="d-flex flex-column site-partials-home-post-slide__div-6">
            @if ($post->published_at)<time class="fw-medium site-partials-home-post-slide__copy-7" datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('d/m/Y') }}</time>@endif
            <h3 class="display-title site-partials-home-post-slide__heading-8"><a class="site-partials-home-post-slide__action-9" href="{{ LocalizedUrl::post($post) }}">{{ $post->title }}</a></h3>
            @if ($post->excerpt)<p class="site-partials-home-post-slide__copy-10">{{ $post->excerpt }}</p>@endif
            <a class="section-link mt-auto site-partials-home-post-slide__action-11" href="{{ LocalizedUrl::post($post) }}">{{ __('site.read_more') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
        </div>
    </div>
</article>
