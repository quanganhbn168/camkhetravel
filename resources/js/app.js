import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import GLightbox from 'glightbox';
import Swal from 'sweetalert2';
import Swiper from 'swiper';
import { A11y, Autoplay, EffectFade, Keyboard, Navigation } from 'swiper/modules';
import 'aos/dist/aos.css';
import 'glightbox/dist/css/glightbox.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/navigation';

window.Alpine = Alpine;
window.AOS = AOS;
window.Swiper = Swiper;
window.GLightbox = GLightbox;
window.Swal = Swal;

const formToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true,
});

const showFormValidationToast = () => formToast.fire({
    icon: 'warning',
    title: 'Anh/chị kiểm tra lại thông tin',
    text: 'Vui lòng điền đủ các trường bắt buộc trước khi gửi.',
});

const initialiseHeroSwipers = () => {
    document.querySelectorAll('[data-hero-swiper]').forEach((element) => {
        if (element.swiper) {
            return;
        }

        new Swiper(element, {
            modules: [A11y, Autoplay, EffectFade, Keyboard],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            autoHeight: false,
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

const initialiseTestimonialSwipers = () => {
    document.querySelectorAll('[data-testimonial-swiper]').forEach((element) => {
        if (element.swiper) {
            return;
        }

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

const preserveHiddenFieldsWhileResetting = (form) => {
    const hiddenFields = Array.from(form.querySelectorAll('input[type="hidden"]'))
        .map((input) => [input, input.value]);

    form.reset();

    hiddenFields.forEach(([input, value]) => {
        input.value = value;
    });
};

const initialiseLeadForms = () => {
    document.querySelectorAll('form[data-lead-form]').forEach((form) => {
        if (form.dataset.leadFormReady === 'true') {
            return;
        }

        form.dataset.leadFormReady = 'true';
        form.noValidate = true;

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!form.checkValidity()) {
                form.querySelector(':invalid')?.focus();
                showFormValidationToast();

                return;
            }

            const successMessage = form.querySelector('[data-form-success]');
            if (successMessage) {
                successMessage.hidden = true;
                successMessage.textContent = '';
            }

            const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
            const originalButtonContent = submitButton?.innerHTML;

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.setAttribute('aria-busy', 'true');
            }

            formToast.fire({
                title: 'Đang gửi thông tin...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                timer: false,
                timerProgressBar: false,
                didOpen: () => Swal.showLoading(),
            });

            try {
                const response = await window.fetch(form.action, {
                    method: (form.getAttribute('method') || 'POST').toUpperCase(),
                    body: new FormData(form),
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const contentType = response.headers.get('content-type') || '';
                const payload = contentType.includes('application/json')
                    ? await response.json().catch(() => ({}))
                    : {};

                if (!response.ok || !contentType.includes('application/json')) {
                    const validationMessages = Object.values(payload.errors || {})
                        .flat()
                        .filter(Boolean)
                        .join('\n');

                    throw new Error(validationMessages || payload.message || 'Không thể gửi thông tin lúc này. Anh/chị vui lòng thử lại.');
                }

                Swal.close();
                preserveHiddenFieldsWhileResetting(form);
                const successCopy = payload.message || 'DVTEC sẽ liên hệ tư vấn trong thời gian sớm nhất.';

                if (successMessage) {
                    successMessage.textContent = successCopy;
                    successMessage.hidden = false;
                }

                formToast.fire({
                    icon: 'success',
                    title: 'Đã nhận thông tin',
                    text: successCopy,
                });

            } catch (error) {
                Swal.close();
                formToast.fire({
                    icon: 'error',
                    title: 'Gửi chưa thành công',
                    text: error.message || 'Không thể gửi thông tin lúc này. Anh/chị vui lòng thử lại.',
                });
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.removeAttribute('aria-busy');

                    if (originalButtonContent !== undefined) {
                        submitButton.innerHTML = originalButtonContent;
                    }
                }
            }
        });
    });
};

let lightbox;

const initialiseLightboxes = () => {
    if (lightbox || !document.querySelector('.glightbox')) {
        return;
    }

    lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        keyboardNavigation: true,
        closeOnOutsideClick: true,
        autoplayVideos: true,
        loop: true,
    });
};

window.refreshLightboxes = () => {
    if (lightbox?.reload) {
        lightbox.reload();

        return;
    }

    initialiseLightboxes();
};

const initialise = () => {
    Alpine.start();
    initialiseHeroSwipers();
    initialisePostSwipers();
    initialiseTestimonialSwipers();
    initialiseScrollTop();
    initialiseAos();
    initialiseCountUps();
    initialiseLeadForms();
    initialiseLightboxes();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialise, { once: true });
} else {
    initialise();
}
