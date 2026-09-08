@php($data = $block['data'])

@if ($block['items']->isNotEmpty())
    <section class="landing-section landing-faqs" id="{{ $block['id'] }}" data-landing-block="faqs">
        <div class="landing-shell landing-faqs__grid">
            <header class="landing-section-heading">
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                <h2>{{ $data['title'] ?? 'Câu hỏi thường gặp' }}</h2>
                @if (filled($data['description'] ?? null))<p>{{ $data['description'] }}</p>@endif
            </header>
            <div class="landing-faqs__list">
                @foreach ($block['items'] as $item)
                    <details @if ($loop->first) open @endif>
                        <summary><span>{{ $item['question'] }}</span><span aria-hidden="true">+</span></summary>
                        <p>{{ $item['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
@endif
