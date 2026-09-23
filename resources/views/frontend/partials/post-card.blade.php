<article class="post-card h-100">
    <a class="post-card__image" href="{{ route('slug.show', ['slug' => $post->slug]) }}" aria-label="Đọc {{ $post->title }}">
        <img src="{{ $post->image_url ?: asset('images/no-image.svg') }}" alt="{{ $post->title }}" loading="lazy">
    </a>
    <div class="post-card__body">
        <div class="post-card__meta">
            @if ($post->category)<span class="post-card__category">{{ $post->category->name }}</span>@endif
            @if ($post->published_at)<time datetime="{{ $post->published_at->toDateString() }}"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i> {{ $post->published_at->translatedFormat('d/m/Y') }}</time>@endif
        </div>
        <h3><a href="{{ route('slug.show', ['slug' => $post->slug]) }}">{{ $post->title }}</a></h3>
        @if (($showExcerpt ?? false) && filled($post->excerpt))
            <p>{{ $post->excerpt }}</p>
        @endif
        <a class="post-card__link" href="{{ route('slug.show', ['slug' => $post->slug]) }}">Đọc bài viết <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</article>
