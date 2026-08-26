@use(App\Support\Localization\LocalizedUrl)

<article class="swiper-slide h-auto">
    <div class="group flex h-full flex-col overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white">
        <a class="relative block aspect-[4/3] overflow-hidden bg-mist" href="{{ LocalizedUrl::post($post) }}">
            @if ($post->image_url)
                <img class="h-full w-full object-cover transition duration-700 group-hover:scale-105" src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy">
            @else
                <span class="image-placeholder">THT</span>
            @endif

            @if ($post->categories->first())
                <span class="absolute top-4 left-4 rounded-full bg-white/95 px-3 py-1.5 text-[0.68rem] font-bold tracking-[0.08em] text-ink uppercase shadow-sm backdrop-blur-sm">{{ $post->categories->first()->name }}</span>
            @endif
        </a>
        <div class="flex flex-1 flex-col p-6">
            @if ($post->published_at)<time class="text-xs font-medium text-slate-500" datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('d/m/Y') }}</time>@endif
            <h3 class="display-title mt-3 text-xl leading-tight"><a class="hover:text-primary" href="{{ LocalizedUrl::post($post) }}">{{ $post->title }}</a></h3>
            @if ($post->excerpt)<p class="mt-4 line-clamp-3 text-sm leading-7 text-slate-500">{{ $post->excerpt }}</p>@endif
            <a class="section-link mt-auto pt-7" href="{{ LocalizedUrl::post($post) }}">{{ __('site.read_more') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
        </div>
    </div>
</article>
