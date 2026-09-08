import Swiper from 'swiper';
import { A11y, Keyboard, Navigation } from 'swiper/modules';

const initialiseTiktok = () => {
    document.querySelectorAll('[data-tt-carousel]').forEach((element) => {
        const videos = element.dataset.ttCarousel === 'videos';
        new Swiper(element.querySelector('.swiper'), {
            modules: [A11y, Keyboard, Navigation],
            slidesPerView: 1.65,
            spaceBetween: 16,
            watchOverflow: true,
            keyboard: { enabled: true, onlyInViewport: true },
            navigation: { prevEl: element.querySelector('[data-tt-prev]'), nextEl: element.querySelector('[data-tt-next]') },
            a11y: { prevSlideMessage: 'Mục trước', nextSlideMessage: 'Mục tiếp theo', slideLabelMessage: '{{index}} / {{slidesLength}}' },
            breakpoints: { 640: { slidesPerView: 3 }, 1024: { slidesPerView: videos ? 5 : 4 }, 1280: { slidesPerView: videos ? 6 : 4 } },
        });
    });
};
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialiseTiktok, { once: true });
else initialiseTiktok();
