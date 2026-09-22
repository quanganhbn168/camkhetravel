<article class="card h-100">
    <a class="card-image post-card__image" href="{{ route('slug.show', ['slug' => $post->slug]) }}">
        @if ($post->image_url)
            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">
        @else
            <span class="image-placeholder">DV</span>
        @endif
        @if ($post->published_at)
            <time class="position-absolute d-inline-flex align-items-center fw-bold badge text-bg-light bottom-0 start-0 m-3 gap-2" datetime="{{ $post->published_at->toDateString() }}"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i>{{ $post->published_at->translatedFormat('d/m/Y') }}</time>
        @endif
    </a>
    <div class="card-body">
        <h3 class="display-title h4"><a class="link-body-emphasis text-decoration-none" href="{{ route('slug.show', ['slug' => $post->slug]) }}">{{ $post->title }}</a></h3>
        @if (($showExcerpt ?? false) && filled($post->excerpt))
            <p class="blog-card-excerpt text-body-secondary mb-3">{{ $post->excerpt }}</p>
        @endif
        @if ($showExcerpt ?? false)
            <a class="section-link" href="{{ route('slug.show', ['slug' => $post->slug]) }}">Đọc bài viết →</a>
        @endif
    </div>
</article>
