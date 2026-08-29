@php($data = $block['data'])

@if ($block['plans']->isNotEmpty())
    <section class="landing-section landing-pricing" id="{{ $block['id'] }}" data-landing-block="pricing">
        <div class="landing-shell">
            <header class="landing-section-heading">
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                <h2>{{ $data['title'] ?? 'Gói hỗ trợ' }}</h2>
                @if (filled($data['description'] ?? null))<p>{{ $data['description'] }}</p>@endif
            </header>
            <div class="landing-pricing__grid">
                @foreach ($block['plans'] as $plan)
                    <article class="landing-price-card {{ $plan->is_featured ? 'is-featured' : '' }}" data-aos="fade-up">
                        @if ($plan->badge)<span class="landing-price-card__badge">{{ $plan->badge }}</span>@endif
                        <h3>{{ $plan->name }}</h3>
                        @if ($plan->description)<p>{{ $plan->description }}</p>@endif
                        <div class="landing-price-card__price">
                            @if ($plan->price !== null && (int) $plan->price > 0)
                                <strong>{{ number_format((int) $plan->price) }}đ</strong><span>{{ $plan->price_unit }}</span>
                            @else
                                <strong>{{ $plan->price_label ?: 'Liên hệ' }}</strong>
                            @endif
                        </div>
                        @if (collect($plan->features)->filter()->isNotEmpty())
                            <ul>@foreach (collect($plan->features)->filter() as $feature)<li>{{ $feature }}</li>@endforeach</ul>
                        @endif
                        <a class="landing-button landing-button--primary" href="#tu-van" data-landing-event="pricing_view" data-block-id="{{ $block['id'] }}" data-pricing-plan-id="{{ $plan->id }}">Đăng ký hỗ trợ <span aria-hidden="true">↗</span></a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
