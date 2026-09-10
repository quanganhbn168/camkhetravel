@php
    $variant = $variant ?? 'compact';
    $isFeatured = $variant === 'featured';
    $eventUrl = $event['url'] ?: '#';
@endphp

<a class="bni-activity-card bni-activity-card--{{ $variant }}" href="{{ $eventUrl }}" @if (! $event['url']) aria-disabled="true" @endif>
    <div class="bni-activity-card__inner">
        <div class="bni-activity-card__media">
            @if ($event['image'])
                <img src="{{ $event['image'] }}" alt="" loading="lazy">
            @else
                <span class="bni-activity-card__placeholder"><strong>THT MEDIA</strong><small>Ảnh sự kiện sẽ được cập nhật trong CMS BNI.</small></span>
            @endif
        </div>
        <div class="bni-activity-card__body">
            <p>{{ $event['category'] }}</p>
            <h3>{{ $event['title'] }}</h3>
            @if ($event['summary'])
                <span class="bni-activity-card__summary">{{ $event['summary'] }}</span>
            @endif
            @if ($isFeatured)
                <div class="bni-activity-card__meta">
                    <span>{{ $event['date'] }}</span>
                    <span>{{ $event['venue'] }}</span>
                </div>
            @endif
            <b>Xem sự kiện <span aria-hidden="true">→</span></b>
        </div>
    </div>
</a>
