@if ($benefitItems !== [])
    <section id="loi-ich" class="section-space site-services-partials-benefits__section-1">
        <div class="site-container w-100 mx-auto site-services-partials-benefits__div-2">
            <header class="mx-auto text-center site-services-partials-benefits__element-3">
                <h2 class="display-title site-services-partials-benefits__heading-4">{{ $service->benefit_title ?: 'Lợi ích của '.$service->title }}</h2>
                @if ($service->benefit_description)
                    <p class="mx-auto site-services-partials-benefits__copy-5">{{ $service->benefit_description }}</p>
                @endif
            </header>
            <div class="site-services-partials-benefits__div-6">
                @foreach ($benefitItems as $item)
                    <article class="overflow-hidden site-services-partials-benefits__article-7">
                        @if (($item['media_url'] ?? null) ?: ($service->image_url ?: $defaultBannerUrl))
                            <img class="w-100 object-fit-cover site-services-partials-benefits__media-8" src="{{ ($item['media_url'] ?? null) ?: ($service->image_url ?: $defaultBannerUrl) }}" alt="{{ $item['title'] ?? $service->title }}" loading="lazy">
                        @endif
                        <div class="site-services-partials-benefits__div-9">
                            <h3 class="fw-bold site-services-partials-benefits__heading-10">{{ $item['title'] ?? '' }}</h3>
                            @if (filled($item['description'] ?? null))
                                <p class="site-services-partials-benefits__copy-11">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
