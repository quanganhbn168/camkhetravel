@use(App\Support\Localization\LocalizedUrl)

@if ($featuredServiceCategories->isNotEmpty())
    <section class="section-space home-featured-services" id="dich-vu-pccc-noi-bat" aria-labelledby="home-featured-services-title">
        <div class="site-container mx-auto w-100 dv-partials-home-featured-services__div-1">
            <div class="d-flex flex-wrap align-items-end justify-content-between dv-partials-home-featured-services__div-2">
                <h2 class="display-title text-uppercase" id="home-featured-services-title">Dịch vụ PCCC nổi bật</h2>
                <a class="section-link" href="{{ LocalizedUrl::route('services.index') }}">Tất cả dịch vụ <span aria-hidden="true">↗</span></a>
            </div>
            <div class="home-service-groups">
                @foreach ($featuredServiceCategories as $category)
                    <article @class(['home-service-dv-hover-group', 'home-service-dv-hover-group--reverse' => $loop->even]) aria-labelledby="home-service-group-{{ $category->id }}">
                        <a class="home-service-group__image" href="{{ LocalizedUrl::serviceCategory($category) }}" aria-label="Xem danh mục {{ $category->name }}">
                            @if ($category->home_image_url)
                                <img src="{{ $category->home_image_url }}" alt="{{ $category->home_image_alt }}" loading="lazy">
                            @else
                                <span class="image-placeholder">DVTEC</span>
                            @endif
                        </a>
                        <div class="home-service-group__content">
                            <h3 id="home-service-group-{{ $category->id }}"><a href="{{ LocalizedUrl::serviceCategory($category) }}">{{ $category->name }}</a></h3>
                            @if ($category->description)
                                <p class="home-service-group__description">{{ $category->description }}</p>
                            @endif
                            <ul class="home-service-group__links">
                                @foreach ($category->services as $service)
                                    <li><a href="{{ LocalizedUrl::service($service) }}"><span>{{ $service->title }}</span><span aria-hidden="true">↗</span></a></li>
                                @endforeach
                            </ul>
                            <a class="section-link" href="{{ LocalizedUrl::serviceCategory($category) }}">Khám phá dịch vụ <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
