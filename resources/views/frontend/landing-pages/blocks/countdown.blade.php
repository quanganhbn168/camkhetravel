@php($data = $block['data'])

@if ($block['ends_at'])
    <section
        class="landing-countdown"
        id="{{ $block['id'] }}"
        data-landing-countdown
        data-countdown-end="{{ $block['ends_at']->toIso8601String() }}"
        data-block-id="{{ $block['id'] }}"
    >
        <div class="landing-shell landing-countdown__inner">
            <div>
                @if (filled($data['eyebrow'] ?? null))<p class="landing-eyebrow">{{ $data['eyebrow'] }}</p>@endif
                <h2>{{ $data['title'] ?? 'Thời gian chương trình còn lại' }}</h2>
                @if ($block['starts_at'])
                    <p>{{ $block['starts_at']->translatedFormat('d/m/Y') }} – {{ $block['ends_at']->translatedFormat('d/m/Y') }}</p>
                @elseif (filled($data['description'] ?? null))
                    <p>{{ $data['description'] }}</p>
                @endif
            </div>
            <div class="landing-countdown__clock" role="timer" aria-live="polite">
                <span><strong data-countdown-days>00</strong><small>Ngày</small></span>
                <span><strong data-countdown-hours>00</strong><small>Giờ</small></span>
                <span><strong data-countdown-minutes>00</strong><small>Phút</small></span>
                <span><strong data-countdown-seconds>00</strong><small>Giây</small></span>
            </div>
        </div>
    </section>
@endif
