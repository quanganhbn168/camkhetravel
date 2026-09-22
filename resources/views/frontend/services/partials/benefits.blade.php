@if ($benefitItems !== [])
    <section id="loi-ich" class="section-space bg-light">
        <div class="container">
            <header class="mx-auto text-center mb-4">
                <h2 class="display-title h2">{{ $service->benefit_title ?: 'Lợi ích của '.$service->title }}</h2>
                @if ($service->benefit_description)
                    <p class="mx-auto lead">{{ $service->benefit_description }}</p>
                @endif
            </header>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach ($benefitItems as $item)
                    <div class="col"><article class="overflow-hidden card h-100">
                        @if (($item['media_url'] ?? null) ?: ($service->image_url ?: $defaultBannerUrl))
                            <img class="w-100 object-fit-cover benefit-image" src="{{ ($item['media_url'] ?? null) ?: ($service->image_url ?: $defaultBannerUrl) }}" alt="{{ $item['title'] ?? $service->title }}" loading="lazy">
                        @endif
                        <div class="card-body">
                            <h3 class="fw-bold h4">{{ $item['title'] ?? '' }}</h3>
                            @if (filled($item['description'] ?? null))
                                <p class="text-body">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </article></div>
                @endforeach
            </div>
        </div>
    </section>
@endif
