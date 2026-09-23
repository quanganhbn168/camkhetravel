@if ($featuredServiceCategories->isNotEmpty())
    <section class="section-space home-featured-services" id="dich-vu-noi-bat" aria-labelledby="home-featured-services-title">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-4 mb-4">
                <h2 class="display-title text-uppercase" id="home-featured-services-title">Dịch vụ xe nổi bật</h2>
                <a class="section-link" href="{{ route('services.index') }}">Tất cả dịch vụ <span aria-hidden="true">↗</span></a>
            </div>
            <div class="home-service-groups">
                @foreach ($featuredServiceCategories as $category)
                    <article @class(['home-service-group', 'home-service-group--reverse' => $loop->even]) aria-labelledby="home-service-group-{{ $category->id }}">
                        <a class="home-service-group__image" href="{{ route('services.category', ['category' => $category->slug]) }}" aria-label="Xem danh mục {{ $category->name }}">
                            @if ($category->home_image_url)
                                <img src="{{ $category->home_image_url }}" alt="{{ $category->home_image_alt }}" loading="lazy">
                            @else
                                <span class="image-placeholder">{{ $website->site_name }}</span>
                            @endif
                        </a>
                        <div class="home-service-group__content">
                            <h3 id="home-service-group-{{ $category->id }}"><a href="{{ route('services.category', ['category' => $category->slug]) }}">{{ $category->name }}</a></h3>
                            @if ($category->description)
                                <p class="home-service-group__description">{{ $category->description }}</p>
                            @endif
                            <ul class="home-service-group__links">
                                @foreach ($category->services as $service)
                                    <li><a href="{{ route('slug.show', ['slug' => $service->slug]) }}"><span>{{ $service->title }}</span><span aria-hidden="true">↗</span></a></li>
                                @endforeach
                            </ul>
                            <a class="section-link" href="{{ route('services.category', ['category' => $category->slug]) }}">Khám phá dịch vụ <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
