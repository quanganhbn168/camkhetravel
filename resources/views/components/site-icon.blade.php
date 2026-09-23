@props(['name'])

@php
    $glyphs = [
        'arrow' => 'fa-arrow-right',
        'car' => 'fa-car-side',
        'pin' => 'fa-location-dot',
        'calendar' => 'fa-calendar-days',
        'users' => 'fa-users',
        'heart' => 'fa-heart',
        'briefcase' => 'fa-briefcase',
        'shield' => 'fa-shield-halved',
        'clock' => 'fa-clock',
        'check' => 'fa-check',
        'phone' => 'fa-phone',
        'chat' => 'fa-comment-dots',
        'handshake' => 'fa-handshake',
        'file' => 'fa-file-lines',
        'headset' => 'fa-headset',
        'tag' => 'fa-tag',
        'mail' => 'fa-envelope',
        'copy' => 'fa-copy',
        'star' => 'fa-star',
        'menu' => 'fa-bars',
        'up' => 'fa-chevron-up',
    ];
@endphp

<i {{ $attributes->class(['icon', 'fa-solid', $glyphs[$name] ?? 'fa-circle-question']) }} aria-hidden="true"></i>
