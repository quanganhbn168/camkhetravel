@php($data = $block['data'])

@if ($block['services']->isNotEmpty())
    <section class="landing-section landing-services" id="{{ $block['id'] }}" data-landing-block="services">
        <div class="landing-shell">
            <header class="landing-section-heading">
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                <h2>{{ $data['title'] ?? 'Dịch vụ phù hợp' }}</h2>
                @if (filled($data['description'] ?? null))<p>{{ $data['description'] }}</p>@endif
            </header>
            <div class="landing-projects__grid">
                @foreach ($block['services'] as $service)
                    @include('frontend.partials.service-card', ['service' => $service])
                @endforeach
            </div>
        </div>
    </section>
@endif
