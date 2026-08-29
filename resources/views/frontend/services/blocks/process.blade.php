@php($data = $block['data'])

@if ($block['items']->isNotEmpty())
    <section class="landing-section landing-process" id="{{ $block['id'] }}" data-landing-block="process">
        <div class="landing-shell">
            <header class="landing-section-heading">
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                <h2>{{ $data['title'] ?? 'Quy trình triển khai' }}</h2>
                @if (filled($data['description'] ?? null))<p>{{ $data['description'] }}</p>@endif
            </header>
            <ol class="landing-process__items">
                @foreach ($block['items'] as $item)
                    <li class="landing-process__item" data-aos="fade-up" data-aos-delay="{{ min(240, $loop->index * 60) }}">
                        <span class="landing-process__number">{{ $item['step'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <h3>{{ $item['title'] ?? $item['label'] }}</h3>
                            @if (filled($item['description'] ?? $item['text'] ?? null))<p>{{ $item['description'] ?? $item['text'] }}</p>@endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endif
