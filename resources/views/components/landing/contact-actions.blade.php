@props([
    'phone' => null,
    'phoneLabel' => null,
    'zaloUrl' => null,
    'zaloLabel' => 'Nhắn Zalo',
])

@php
    $phoneNumber = preg_replace('/[^0-9+]/', '', (string) $phone);
    $displayPhone = (string) ($phoneLabel ?: $phone ?: 'Gọi tư vấn');
@endphp

<aside class="tht-landing-contact-float" aria-label="Liên hệ nhanh">
    @if ($phoneNumber)
        <a class="tht-landing-contact-float__button tht-landing-contact-float__button--phone" href="tel:{{ $phoneNumber }}" data-label="Gọi {{ $displayPhone }}" aria-label="Gọi {{ $displayPhone }}">
            <i class="fa-solid fa-phone" aria-hidden="true"></i>
        </a>
    @endif
    @if ($zaloUrl)
        <a class="tht-landing-contact-float__button tht-landing-contact-float__button--zalo" href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer" data-label="{{ $zaloLabel }}" aria-label="{{ $zaloLabel }}">
            <span class="tht-landing-zalo-icon" aria-hidden="true"></span>
        </a>
    @endif
</aside>

<nav class="tht-landing-contact-bottom" aria-label="Liên hệ nhanh">
    @if ($phoneNumber)
        <a class="tht-landing-contact-bottom__item tht-landing-contact-bottom__item--phone" href="tel:{{ $phoneNumber }}">
            <i class="fa-solid fa-phone" aria-hidden="true"></i>
            <span>Gọi tư vấn</span>
        </a>
    @endif
    @if ($zaloUrl)
        <a class="tht-landing-contact-bottom__item tht-landing-contact-bottom__item--zalo" href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer">
            <span class="tht-landing-zalo-icon" aria-hidden="true"></span>
            <span>{{ $zaloLabel }}</span>
        </a>
    @endif
</nav>
