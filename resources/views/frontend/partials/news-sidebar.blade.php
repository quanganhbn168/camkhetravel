<aside class="news-sidebar d-grid gap-4">
    <nav class="overflow-hidden card" aria-label="Danh mục tin tức">
        <p class="fw-bold text-uppercase card-header mb-0">Danh mục tin tức</p>
        <div class="p-3">
            <a class="{{ ! $activeCategory ? 'text-primary' : 'link-body-emphasis' }} d-flex align-items-center justify-content-between fw-semibold p-2 text-decoration-none" href="{{ route('posts.index') }}"><span>Tất cả tin tức</span><span aria-hidden="true">›</span></a>
            @foreach ($categories as $category)
                <a class="{{ $activeCategory?->is($category) ? 'fw-semibold text-primary' : 'link-body-emphasis' }} d-flex align-items-center justify-content-between p-2 text-decoration-none" href="{{ route('posts.category', ['slug' => $category->slug]) }}"><span>{{ $category->tree_label ?? $category->name }}</span><small class="text-body-secondary">{{ $category->posts_count }}</small></a>
            @endforeach
        </div>
    </nav>

    @if (isset($featuredPosts) && $featuredPosts->isNotEmpty())
        <section class="overflow-hidden card" aria-labelledby="featured-posts-heading">
            <h2 class="fw-bold text-uppercase card-header h6" id="featured-posts-heading">Bài viết nổi bật</h2>
            <div class="p-3">
                @foreach ($featuredPosts as $featuredPost)
                    <a class="d-flex gap-3 py-2 text-decoration-none" href="{{ route('slug.show', ['slug' => $featuredPost->slug]) }}">
                        @if ($featuredPost->image_url)
                            <img class="flex-shrink-0 object-fit-cover news-sidebar__thumbnail" src="{{ $featuredPost->image_url }}" alt="" loading="lazy">
                        @else
                            <span class="d-grid flex-shrink-0 news-sidebar__thumbnail image-placeholder">DV</span>
                        @endif
                        <span class="flex-grow-1">
                            <span class="d-block fw-semibold small text-body-emphasis">{{ $featuredPost->title }}</span>
                            @if ($featuredPost->published_at)<time class="d-block fw-medium small text-body-secondary mt-1" datetime="{{ $featuredPost->published_at->toDateString() }}">{{ $featuredPost->published_at->translatedFormat('d/m/Y') }}</time>@endif
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="position-relative overflow-hidden news-sidebar__cta">
        @if (($heroImageUrl ?: $defaultBannerUrl))<img class="position-absolute h-100 w-100 object-fit-cover news-sidebar__background" src="{{ ($heroImageUrl ?: $defaultBannerUrl) }}" alt="" aria-hidden="true">@endif
        <div class="position-absolute news-sidebar__overlay"></div>
        <p class="text-primary fw-bold text-uppercase small">Tư vấn dự án</p>
        <h2 class="h3">Cùng {{ $website->site_name }} triển khai ý tưởng của bạn.</h2>
        <a class="btn btn-primary mt-3" href="{{ route('contact') }}">Liên hệ ngay <span aria-hidden="true">↗</span></a>
    </div>
</aside>
