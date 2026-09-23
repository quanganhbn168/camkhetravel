import Swiper from 'swiper';
import { A11y, Autoplay, EffectFade, Keyboard, Navigation, Pagination, Thumbs } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/thumbs';

const initialiseHeroSwipers = () => {
    document.querySelectorAll('[data-hero-swiper]').forEach((element) => {
        if (element.swiper) return;

        new Swiper(element, {
            modules: [A11y, Autoplay, EffectFade, Keyboard],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            autoHeight: true,
            speed: 700,
            loop: element.querySelectorAll('.swiper-slide').length > 1,
            autoplay: {
                delay: 7000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            keyboard: { enabled: true },
        });
    });
};

const initialisePostSwipers = () => {
    document.querySelectorAll('[data-post-swiper]').forEach((element) => {
        if (element.swiper) return;
        const section = element.parentElement;

        new Swiper(element, {
            modules: [A11y, Keyboard, Navigation],
            slidesPerView: 1.1,
            spaceBetween: 16,
            keyboard: { enabled: true },
            watchOverflow: true,
            navigation: {
                prevEl: section?.querySelector('[data-post-swiper-prev]'),
                nextEl: section?.querySelector('[data-post-swiper-next]'),
            },
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 20 },
            },
        });
    });
};

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


const initialisePartnerSwipers = () => {
    document.querySelectorAll('[data-partner-swiper]').forEach((element) => {
        if (element.swiper) return;
        const section = element.closest('.partners');
        new Swiper(element, {
            modules: [A11y, Keyboard, Navigation],
            a11y: { prevSlideMessage: 'Đối tác trước', nextSlideMessage: 'Đối tác tiếp theo', slideLabelMessage: '{{index}} / {{slidesLength}}' },
            slidesPerView: 1.5,
            spaceBetween: 20,
            speed: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 450,
            keyboard: { enabled: true },
            watchOverflow: true,
            navigation: {
                prevEl: section.querySelector('[data-partner-swiper-prev]'),
                nextEl: section.querySelector('[data-partner-swiper-next]'),
            },
            breakpoints: {
                576: { slidesPerView: 2 },
                992: { slidesPerView: 3 },
            },
        });
    });
};

export function initialiseSliders() {
    initialiseProjectSwipers();
    initialisePartnerSwipers();
    initialiseHeroSwipers();
    initialisePostSwipers();
    initialiseTestimonialSwipers();
}

function initialiseProjectSwipers() {
    const a11y = { prevSlideMessage: 'Ảnh trước', nextSlideMessage: 'Ảnh tiếp theo', paginationBulletMessage: 'Đến nhóm ảnh {{index}}' };
    const speed = matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 400;
    document.querySelectorAll('[data-project-gallery]').forEach((element) => {
        if (element.swiper) return;
        const shell = element.closest('.project-case__gallery');
        const thumbElement = shell.querySelector('[data-project-thumbs]');
        const thumbs = new Swiper(thumbElement, {
            modules: [A11y], slidesPerView: 3, spaceBetween: 8, watchSlidesProgress: true,
            breakpoints: { 576: { slidesPerView: 5 } },
        });
        const gallery = new Swiper(element, {
            modules: [A11y, Navigation, Keyboard, Thumbs], a11y, speed,
            keyboard: { enabled: true }, watchOverflow: true,
            thumbs: { swiper: thumbs },
            navigation: { prevEl: shell.querySelector('[data-project-prev]'), nextEl: shell.querySelector('[data-project-next]') },
        });
        const buttons = [...thumbElement.querySelectorAll('button')];
        const update = () => buttons.forEach((button, index) => button.setAttribute('aria-pressed', String(index === gallery.activeIndex)));
        buttons.forEach((button, index) => button.addEventListener('click', () => gallery.slideTo(index)));
        gallery.on('slideChange', update);
        update();
    });
    document.querySelectorAll('[data-project-site-swiper], [data-project-related-swiper]').forEach((element) => {
        if (element.swiper) return;
        const shell = element.closest('.project-case__slider-shell');
        new Swiper(element, {
            modules: [A11y, Navigation, Keyboard, Pagination], a11y, speed,
            slidesPerView: 1.2, spaceBetween: 16, keyboard: { enabled: true }, watchOverflow: true,
            navigation: { prevEl: shell.querySelector('[data-strip-prev]'), nextEl: shell.querySelector('[data-strip-next]') },
            pagination: { el: element.querySelector('.swiper-pagination'), clickable: true },
            breakpoints: { 576: { slidesPerView: 2 }, 992: { slidesPerView: 3 }, 1200: { slidesPerView: 4 } },
        });
    });
}
