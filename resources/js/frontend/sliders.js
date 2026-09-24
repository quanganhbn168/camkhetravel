import Swiper from 'swiper';
import { A11y, Autoplay, Keyboard, Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';

const initialiseTestimonialSwipers = () => {
    document.querySelectorAll('[data-testimonial-swiper]').forEach((element) => {
        if (element.swiper) return;
        const section = element.parentElement;

        new Swiper(element, {
            modules: [A11y, Autoplay, Keyboard, Navigation],
            slidesPerView: 1,
            spaceBetween: 16,
            loop: element.querySelectorAll('.swiper-slide').length > 2,
            autoplay: {
                delay: 6500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            keyboard: { enabled: true },
            watchOverflow: true,
            navigation: {
                prevEl: section?.querySelector('[data-testimonial-swiper-prev]'),
                nextEl: section?.querySelector('[data-testimonial-swiper-next]'),
            },
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 20 },
            },
        });
    });
};


export function initialiseSliders() {
    initialiseTestimonialSwipers();
}
