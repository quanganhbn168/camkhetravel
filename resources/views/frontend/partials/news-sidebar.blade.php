@use(App\Support\Localization\LocalizedUrl)

<aside class="news-sidebar d-grid dv-partials-news-sidebar__aside-1">
    <nav class="overflow-hidden dv-partials-news-sidebar__nav-2" aria-label="Danh mục tin tức">
        <p class="fw-bold text-uppercase dv-partials-news-sidebar__copy-3">Danh mục tin tức</p>
        <div class="dv-partials-news-sidebar__div-4">
            <a class="{{ ! $activeCategory ? 'dv-partials-news-sidebar__element-5' : 'dv-partials-news-sidebar__element-6' }} d-flex align-items-center justify-content-between fw-semibold dv-partials-news-sidebar__action-7" href="{{ LocalizedUrl::route('posts.index') }}"><span>Tất cả tin tức</span><span aria-hidden="true">›</span></a>
            @foreach ($categories as $category)
                <a class="{{ $activeCategory?->is($category) ? 'fw-semibold dv-partials-news-sidebar__element-8' : 'dv-partials-news-sidebar__element-6' }} d-flex align-items-center justify-content-between dv-partials-news-sidebar__action-9" href="{{ LocalizedUrl::postCategory($category) }}"><span>{{ $category->name }}</span><small class="dv-partials-news-sidebar__copy-10">{{ $category->posts_count }}</small></a>
            @endforeach
        </div>
    </nav>

    @if (isset($featuredPosts) && $featuredPosts->isNotEmpty())
        <section class="overflow-hidden dv-partials-news-sidebar__nav-2" aria-labelledby="featured-posts-heading">
            <h2 class="fw-bold text-uppercase dv-partials-news-sidebar__heading-11" id="featured-posts-heading">Bài viết nổi bật</h2>
            <div class="dv-partials-news-sidebar__div-4">
                @foreach ($featuredPosts as $featuredPost)
                    <a class="dv-hover-group d-flex dv-partials-news-sidebar__action-12" href="{{ LocalizedUrl::post($featuredPost) }}">
                        @if ($featuredPost->image_url)
                            <img class="flex-shrink-0 object-fit-cover dv-partials-news-sidebar__media-13" src="{{ $featuredPost->image_url }}" alt="" loading="lazy">
                        @else
                            <span class="d-grid flex-shrink-0 dv-partials-news-sidebar__copy-14">DV</span>
                        @endif
                        <span class="dv-partials-news-sidebar__copy-15">
                            <span class="d-block fw-semibold dv-partials-news-sidebar__copy-16">{{ $featuredPost->title }}</span>
                            @if ($featuredPost->published_at)<time class="d-block fw-medium dv-partials-news-sidebar__copy-17" datetime="{{ $featuredPost->published_at->toDateString() }}">{{ $featuredPost->published_at->translatedFormat('d/m/Y') }}</time>@endif
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="position-relative overflow-hidden dv-partials-news-sidebar__div-18">
        @if (($heroImageUrl ?: $defaultBannerUrl))<img class="position-absolute h-100 w-100 object-fit-cover dv-partials-news-sidebar__media-19" src="{{ ($heroImageUrl ?: $defaultBannerUrl) }}" alt="" aria-hidden="true">@endif
        <div class="position-absolute dv-partials-news-sidebar__div-20"></div>
        <p class="text-primary-soft fw-bold text-uppercase dv-partials-news-sidebar__copy-21">Tư vấn dự án</p>
        <h2 class="news-sidebar__cta-title dv-partials-news-sidebar__heading-22">Cùng DVTEC triển khai ý tưởng của bạn.</h2>
        <a class="btn btn-primary button-primary dv-partials-news-sidebar__action-23" href="{{ LocalizedUrl::route('contact') }}">Liên hệ ngay <span aria-hidden="true">↗</span></a>
    </div>
</aside>
