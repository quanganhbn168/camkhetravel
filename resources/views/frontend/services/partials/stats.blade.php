@if ($statsItems->isNotEmpty())
    <section class="section-space site-services-partials-stats__section-1">
        <div class="site-container w-100 mx-auto site-services-partials-stats__div-2">
            <header class="mx-auto text-center site-services-partials-stats__element-3">
                <h2 class="display-title site-services-partials-stats__heading-4">{{ $service->stats_title ?: 'Những con số đáng chú ý' }}</h2>
                @if ($service->stats_description)
                    <p class="site-services-partials-stats__copy-5">{{ $service->stats_description }}</p>
                @endif
            </header>
            <div class="service-stats-grid mx-auto site-services-partials-stats__div-6">
                @foreach ($statsItems as $item)
                    <article class="text-center site-services-partials-stats__article-7">
                        <p class="service-stat__value">{{ $item['value'] ?? '—' }}</p>
                        <p class="service-stat__label">{{ $item['label'] }}</p>
                        @if (filled($item['description'] ?? null))
                            <p class="site-services-partials-stats__copy-8">{{ $item['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
