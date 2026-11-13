@props([
    'perView' => '1,2,3',
    'label' => null,
    'autoplay' => false,
])
{{-- Slider dùng chung (Swiper). Mỗi phần tử con đặt trong <div class="swiper-slide">. --}}
<div {{ $attributes->class('site-slider') }} data-slider data-slider-per-view="{{ $perView }}" @if ($autoplay) data-slider-autoplay @endif>
    <div class="swiper" @if ($label) role="region" aria-label="{{ $label }}" @endif>
        <div class="swiper-wrapper">
            {{ $slot }}
        </div>
    </div>
    <div class="site-slider__controls">
        <button class="site-slider__nav" type="button" data-slider-prev aria-label="Trang trước">
            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.83 10l3.94 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd"/></svg>
        </button>
        <div class="site-slider__pagination" data-slider-pagination></div>
        <button class="site-slider__nav" type="button" data-slider-next aria-label="Trang tiếp theo">
            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.17 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd"/></svg>
        </button>
    </div>
</div>
