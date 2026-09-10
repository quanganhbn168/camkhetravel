@use(App\Support\Localization\LocalizedUrl)

<aside class="news-sidebar grid gap-7 lg:sticky lg:top-24">
    <nav class="overflow-hidden rounded-2xl border border-slate-200 bg-white" aria-label="Danh mục tin tức">
        <p class="border-b border-slate-200 px-5 py-4 text-sm font-bold tracking-[0.04em] text-ink uppercase">Danh mục tin tức</p>
        <div class="px-5">
            <a class="flex items-center justify-between border-b border-slate-100 py-3.5 text-sm font-semibold {{ ! $activeCategory ? 'text-primary' : 'text-slate-600 hover:text-ink' }}" href="{{ LocalizedUrl::route('posts.index') }}"><span>Tất cả tin tức</span><span aria-hidden="true">›</span></a>
            @foreach ($categories as $category)
                <a class="flex items-center justify-between gap-3 border-b border-slate-100 py-3.5 text-sm {{ $activeCategory?->is($category) ? 'font-semibold text-primary' : 'text-slate-600 hover:text-ink' }}" href="{{ LocalizedUrl::postCategory($category) }}"><span>{{ $category->name }}</span><small class="text-xs text-slate-400">{{ $category->posts_count }}</small></a>
            @endforeach
        </div>
    </nav>

    @if (isset($featuredPosts) && $featuredPosts->isNotEmpty())
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white" aria-labelledby="featured-posts-heading">
            <h2 class="border-b border-slate-200 px-5 py-4 text-xs font-bold tracking-[0.04em] text-ink uppercase" id="featured-posts-heading">Bài viết nổi bật</h2>
            <div class="px-5">
                @foreach ($featuredPosts as $featuredPost)
                    <a class="group flex gap-3 border-b border-slate-100 py-3 last:border-b-0" href="{{ LocalizedUrl::post($featuredPost) }}">
                        @if ($featuredPost->image_url)
                            <img class="size-14 shrink-0 rounded-lg object-cover" src="{{ $featuredPost->image_url }}" alt="" loading="lazy">
                        @else
                            <span class="grid size-14 shrink-0 place-items-center rounded-lg bg-mist font-display text-sm text-primary">DV</span>
                        @endif
                        <span class="min-w-0">
                            <span class="block line-clamp-2 text-xs font-semibold leading-5 text-ink transition group-hover:text-primary">{{ $featuredPost->title }}</span>
                            @if ($featuredPost->published_at)<time class="mt-1 block text-[0.68rem] font-medium text-slate-400" datetime="{{ $featuredPost->published_at->toDateString() }}">{{ $featuredPost->published_at->translatedFormat('d/m/Y') }}</time>@endif
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="relative isolate min-h-60 overflow-hidden rounded-2xl bg-ink p-6 text-white">
        @if (($heroImageUrl ?: $defaultBannerUrl))<img class="absolute inset-0 -z-20 h-full w-full object-cover opacity-35" src="{{ ($heroImageUrl ?: $defaultBannerUrl) }}" alt="" aria-hidden="true">@endif
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(135deg,color-mix(in_srgb,var(--site-color-ink)_92%,transparent),color-mix(in_srgb,var(--site-color-ink)_68%,transparent))]"></div>
        <p class="text-xs font-bold tracking-[0.15em] text-primary-soft uppercase">Tư vấn dự án</p>
        <h2 class="news-sidebar__cta-title font-display mt-4 text-lg leading-snug">Cùng DVTEC triển khai ý tưởng của bạn.</h2>
        <a class="button-primary mt-7 min-h-10 px-4 py-2 text-xs" href="{{ LocalizedUrl::route('contact') }}">Liên hệ ngay <span aria-hidden="true">↗</span></a>
    </div>
</aside>
