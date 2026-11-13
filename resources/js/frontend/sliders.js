import Swiper from 'swiper';
import { A11y, Autoplay, Keyboard, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import '../../css/components/slider.css';

/**
 * Slider dùng chung cho <x-slider>.
 * `data-slider-per-view="1,2,3"`: số thẻ trên điện thoại, từ 768px và từ 1200px.
 * `data-slider-autoplay`: tự chạy (dừng khi rê chuột hoặc khi người dùng chạm vào).
 */
const initialiseSlider = (root) => {
    const element = root.querySelector('.swiper');
    if (!element || element.swiper) return;

    const [mobile, tablet = mobile, desktop = tablet] = (root.dataset.sliderPerView || '1,2,3')
        .split(',')
        .map((value) => Number.parseFloat(value) || 1);
    const slideCount = element.querySelectorAll('.swiper-slide').length;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const swiper = new Swiper(element, {
        modules: [A11y, Autoplay, Keyboard, Navigation, Pagination],
        slidesPerView: mobile,
        spaceBetween: 16,
        speed: reducedMotion ? 0 : 550,
        grabCursor: true,
        watchOverflow: true,
        keyboard: { enabled: true, onlyInViewport: true },
        a11y: {
            prevSlideMessage: 'Trang trước',
            nextSlideMessage: 'Trang tiếp theo',
            paginationBulletMessage: 'Đến trang {{index}}',
            firstSlideMessage: 'Đây là trang đầu',
            lastSlideMessage: 'Đây là trang cuối',
        },
        autoplay: root.hasAttribute('data-slider-autoplay') && !reducedMotion && slideCount > 1
            ? { delay: 6000, disableOnInteraction: true, pauseOnMouseEnter: true }
            : false,
        navigation: {
            prevEl: root.querySelector('[data-slider-prev]'),
            nextEl: root.querySelector('[data-slider-next]'),
        },
        pagination: {
            el: root.querySelector('[data-slider-pagination]'),
            clickable: true,
        },
        breakpoints: {
            768: { slidesPerView: tablet, spaceBetween: 20 },
            1200: { slidesPerView: desktop, spaceBetween: 24 },
        },
    });

    // Ẩn cả hàng điều khiển khi thẻ vừa đủ hiển thị, không cần trượt.
    const syncLock = () => root.classList.toggle('is-locked', swiper.isLocked);
    swiper.on('lock unlock', syncLock);
    syncLock();
};

export function initialiseSliders() {
    document.querySelectorAll('[data-slider]').forEach(initialiseSlider);
}
