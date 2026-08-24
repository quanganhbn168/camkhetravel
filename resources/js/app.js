import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import Swiper from 'swiper';
import { A11y, Autoplay, EffectFade, Keyboard, Navigation } from 'swiper/modules';
import 'aos/dist/aos.css';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/navigation';

window.Alpine = Alpine;

Alpine.start();

const initialiseHeroSwipers = () => {
    document.querySelectorAll('[data-hero-swiper]').forEach((element) => {
        if (element.swiper) {
            return;
        }

        new Swiper(element, {
            modules: [A11y, Autoplay, EffectFade, Keyboard],
            effect: 'fade',
            fadeEffect: { crossFade: true },
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
        if (element.swiper) {
            return;
        }

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

const initialiseScrollTop = () => {
    const button = document.querySelector('[data-scroll-top]');

    if (!button) {
        return;
    }

    const toggle = () => button.classList.toggle('is-visible', window.scrollY > 480);

    toggle();
    window.addEventListener('scroll', toggle, { passive: true });
    button.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
};

const initialiseAos = () => {
    AOS.init({
        once: true,
        duration: 650,
        easing: 'ease-out-cubic',
        offset: 80,
        disable: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    });
};

const initialiseCountUps = () => {
    const counters = document.querySelectorAll('[data-count-up]');

    if (!counters.length) {
        return;
    }

    const formatter = new Intl.NumberFormat(document.documentElement.lang || 'vi');
    const renderValue = (element, value) => {
        element.textContent = formatter.format(value);
    };
    const animateCounter = (element) => {
        if (element.dataset.countStarted === 'true') {
            return;
        }

        element.dataset.countStarted = 'true';

        const target = Number(element.dataset.countUp || 0);
        const duration = Math.min(1800, Math.max(800, 650 + target * 4));
        const startedAt = performance.now();
        const update = (now) => {
            const progress = Math.min((now - startedAt) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);

            renderValue(element, Math.round(target * eased));

            if (progress < 1) {
                window.requestAnimationFrame(update);
            }
        };

        window.requestAnimationFrame(update);
    };

    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        counters.forEach((element) => renderValue(element, Number(element.dataset.countUp || 0)));

        return;
    }

    const observer = new IntersectionObserver((entries, activeObserver) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            animateCounter(entry.target);
            activeObserver.unobserve(entry.target);
        });
    }, { threshold: 0.45 });

    counters.forEach((counter) => {
        renderValue(counter, 0);
        observer.observe(counter);
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initialiseHeroSwipers();
        initialisePostSwipers();
        initialiseScrollTop();
        initialiseAos();
        initialiseCountUps();
    }, { once: true });
} else {
    initialiseHeroSwipers();
    initialisePostSwipers();
    initialiseScrollTop();
    initialiseAos();
    initialiseCountUps();
}
