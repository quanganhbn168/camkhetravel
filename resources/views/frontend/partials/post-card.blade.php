@use(App\Support\Localization\LocalizedUrl)

<article class="content-card site-hover-group site-partials-post-card__article-1">
    <a class="card-image site-partials-post-card__action-2" href="{{ LocalizedUrl::post($post) }}">
        @if ($post->image_url)
            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">
        @else
            <span class="image-placeholder">DV</span>
        @endif
        @if ($post->published_at)
            <time class="position-absolute d-inline-flex align-items-center fw-bold site-partials-post-card__copy-3" datetime="{{ $post->published_at->toDateString() }}"><i class="fa-regular fa-calendar-days site-partials-post-card__element-4" aria-hidden="true"></i>{{ $post->published_at->translatedFormat('d/m/Y') }}</time>
        @endif
    </a>
    <div class="card-body site-partials-post-card__div-5">
        <h3 class="display-title site-partials-post-card__heading-6"><a class="site-partials-post-card__action-7" href="{{ LocalizedUrl::post($post) }}">{{ $post->title }}</a></h3>
    </div>
</article>
