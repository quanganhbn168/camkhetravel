@php($data = $block['data'])

@if ($block['items']->isNotEmpty())
    <section class="landing-section landing-stats" id="{{ $block['id'] }}" data-landing-block="stats">
        <div class="landing-shell landing-stats__grid">
            @foreach ($block['items'] as $item)
                <div class="landing-stat"><strong>{{ $item['value'] ?? $item['number'] ?? '' }}</strong><span>{{ $item['title'] ?? $item['label'] }}</span></div>
            @endforeach
        </div>
    </section>
@endif
