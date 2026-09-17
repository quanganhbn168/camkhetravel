@if ($statsItems->isNotEmpty())
    <section class="section-space dv-services-partials-stats__section-1">
        <div class="site-container w-100 mx-auto dv-services-partials-stats__div-2">
            <header class="mx-auto text-center dv-services-partials-stats__element-3">
                <h2 class="display-title dv-services-partials-stats__heading-4">{{ $service->stats_title ?: 'Những con số đáng chú ý' }}</h2>
                @if ($service->stats_description)
                    <p class="dv-services-partials-stats__copy-5">{{ $service->stats_description }}</p>
                @endif
            </header>
            <div class="service-stats-grid mx-auto dv-services-partials-stats__div-6">
                @foreach ($statsItems as $item)
                    <article class="text-center dv-services-partials-stats__article-7">
                        <p class="service-stat__value">{{ $item['value'] ?? '—' }}</p>
                        <p class="service-stat__label">{{ $item['label'] }}</p>
                        @if (filled($item['description'] ?? null))
                            <p class="dv-services-partials-stats__copy-8">{{ $item['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
