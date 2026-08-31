@php($data = $block['data'])

@if ($block['categories']->isNotEmpty())
    <section class="landing-section landing-service-categories" id="{{ $block['id'] }}" data-landing-block="service_categories">
        <div class="landing-shell">
            <header class="landing-section-heading">
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                <h2>{{ $data['title'] ?? 'Danh mục dịch vụ' }}</h2>
                @if (filled($data['description'] ?? null))<p>{{ $data['description'] }}</p>@endif
            </header>
            <div class="landing-content-grid__items">
                @foreach ($block['categories'] as $category)
                    <a class="landing-content-card" href="{{ \App\Support\Localization\LocalizedUrl::serviceCategory($category) }}">
                        <span class="landing-content-card__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $category->name }}</h3>
                        @if ($category->description)<p>{{ $category->description }}</p>@endif
                        <span class="resource-card__link">Xem dịch vụ <span aria-hidden="true">→</span></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
