@props([
    'brand' => 'THT MEDIA',
    'logo' => null,
    'navigation' => [],
    'ctaLabel' => 'Nhận tư vấn',
    'ctaUrl' => '#tu-van',
    'zaloUrl' => null,
])

<header class="tht-landing-header" data-landing-scroll-header x-data="{ navOpen: false }" :class="{ 'is-open': navOpen }" @keydown.escape.window="navOpen = false">
    <x-landing.container class="tht-landing-header__inner">
        <a class="tht-landing-brand" href="#tht-landing-main" aria-label="{{ $brand }}">
            @if ($logo)
                <img src="{{ $logo }}" alt="{{ $brand }}" width="146" height="52">
            @endif
        </a>

        <nav class="tht-landing-desktop-nav" aria-label="Điều hướng landing page">
            @foreach ((array) $navigation as $item)
                <a href="{{ $item['url'] ?? '#' }}" data-landing-event="nav_click" data-block-id="nav">{{ $item['label'] ?? '' }}</a>
            @endforeach
        </nav>

        <a class="tht-landing-header__cta tht-landing-zalo-cta hidden lg:inline-flex" href="{{ $zaloUrl ?: $ctaUrl }}" target="_blank" rel="noopener noreferrer" data-landing-event="cta_click" data-block-id="nav">
            <span class="tht-landing-zalo-icon" aria-hidden="true"></span>
            <span>{{ $ctaLabel }}</span>
        </a>

        <button class="tht-landing-menu-toggle inline-flex lg:hidden" type="button" @click="navOpen = !navOpen" :aria-expanded="navOpen.toString()" aria-controls="tht-landing-mobile-menu" aria-label="Mở hoặc đóng menu">
            <span></span><span></span><span></span>
        </button>
    </x-landing.container>

    <div id="tht-landing-mobile-menu" class="tht-landing-mobile-menu lg:hidden" x-cloak x-show="navOpen" x-transition.opacity.duration.180ms>
        <nav class="tht-landing-container" aria-label="Điều hướng trên điện thoại">
            @foreach ((array) $navigation as $item)
                <a href="{{ $item['url'] ?? '#' }}" @click="navOpen = false" data-landing-event="nav_click" data-block-id="nav">{{ $item['label'] ?? '' }}</a>
            @endforeach
            <a class="tht-landing-mobile-menu__cta tht-landing-zalo-cta" href="{{ $zaloUrl ?: $ctaUrl }}" target="_blank" rel="noopener noreferrer" @click="navOpen = false" data-landing-event="cta_click" data-block-id="nav">{{ $ctaLabel }}</a>
            </nav>
    </div>
</header>
