@use(App\Support\Localization\LocalizedUrl)

<article class="content-card group bg-white">
    <a class="card-image aspect-[1.48]" href="{{ LocalizedUrl::post($post) }}">
        @if ($post->image_url)
            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">
        @else
            <span class="image-placeholder">THT</span>
        @endif
        @if ($post->published_at)
            <time class="absolute bottom-4 left-4 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-[0.68rem] font-bold tracking-[0.04em] text-ink shadow-sm backdrop-blur-sm" datetime="{{ $post->published_at->toDateString() }}"><i class="fa-regular fa-calendar-days text-primary" aria-hidden="true"></i>{{ $post->published_at->translatedFormat('d/m/Y') }}</time>
        @endif
    </a>
    <div class="card-body p-5">
        <h3 class="display-title text-[1.08rem] leading-snug md:text-lg"><a class="hover:text-primary" href="{{ LocalizedUrl::post($post) }}">{{ $post->title }}</a></h3>
    </div>
</article>
