@if ($statsItems->isNotEmpty())
    <section class="section-space bg-light">
        <div class="container">
            <header class="mx-auto text-center mb-4">
                <h2 class="display-title h2">{{ $service->stats_title ?: 'Những con số đáng chú ý' }}</h2>
                @if ($service->stats_description)
                    <p class="lead">{{ $service->stats_description }}</p>
                @endif
            </header>
            <div class="service-stats-grid mx-auto">
                @foreach ($statsItems as $item)
                    <article class="text-center p-3">
                        <p class="service-stat__value">{{ $item['value'] ?? '—' }}</p>
                        <p class="service-stat__label">{{ $item['label'] }}</p>
                        @if (filled($item['description'] ?? null))
                            <p class="small text-body">{{ $item['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
