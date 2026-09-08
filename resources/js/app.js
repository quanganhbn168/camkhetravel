import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import GLightbox from 'glightbox';
import Swiper from 'swiper';
import { A11y, Autoplay, EffectFade, Keyboard, Navigation } from 'swiper/modules';
import 'aos/dist/aos.css';
import 'glightbox/dist/css/glightbox.css';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/navigation';

window.Alpine = Alpine;
window.AOS = AOS;
window.Swiper = Swiper;
window.GLightbox = GLightbox;

const openLandingModal = (modal) => {
    if (!modal) {
        return;
    }

    modal.classList.add('is-open', 'show');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    modal.querySelector('.tht-landing-modal-close')?.focus();
};

const closeLandingModal = (modal) => {
    if (!modal) {
        return;
    }

    modal.classList.remove('is-open', 'show');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
};

window.landingOpenModal = openLandingModal;
window.landingCloseModal = closeLandingModal;

const initialiseLandingModals = () => {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-landing-modal-open]');
        const dismiss = event.target.closest('[data-landing-modal-close]');

        if (trigger) {
            const selector = trigger.dataset.landingModalOpen;
            const modal = selector ? document.querySelector(selector) : null;
            if (modal) {
                event.preventDefault();
                openLandingModal(modal);
            }
        }

        if (dismiss) {
            event.preventDefault();
            closeLandingModal(dismiss.closest('.modal'));
        }

        if (event.target.classList?.contains('modal')) {
            closeLandingModal(event.target);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal.is-open').forEach(closeLandingModal);
        }
    });
};

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

const initialiseBniHeroSwipers = () => {
    document.querySelectorAll('[data-bni-hero-swiper]').forEach((element) => {
        if (element.swiper) {
            return;
        }

        const section = element.closest('.bni-event-slider');
        const slideCount = element.querySelectorAll('.swiper-slide').length;

        new Swiper(element, {
            modules: [A11y, Autoplay, Keyboard, Navigation],
            slidesPerView: 1,
            autoHeight: true,
            speed: 650,
            loop: slideCount > 1,
            watchOverflow: true,
            autoplay: slideCount > 1 ? {
                delay: 6500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            } : false,
            keyboard: { enabled: true },
            navigation: {
                prevEl: section?.querySelector('[data-bni-hero-swiper-prev]'),
                nextEl: section?.querySelector('[data-bni-hero-swiper-next]'),
            },
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

const initialiseBniCountdowns = () => {
    document.querySelectorAll('[data-bni-countdown]').forEach((element) => {
        if (element.dataset.bniCountdownReady === 'true') {
            return;
        }

        const target = new Date(element.dataset.bniCountdown || '').getTime();

        if (Number.isNaN(target)) {
            return;
        }

        element.dataset.bniCountdownReady = 'true';
        const output = {
            days: element.querySelector('[data-bni-countdown-days]'),
            hours: element.querySelector('[data-bni-countdown-hours]'),
            minutes: element.querySelector('[data-bni-countdown-minutes]'),
            seconds: element.querySelector('[data-bni-countdown-seconds]'),
        };
        const render = () => {
            const seconds = Math.max(0, Math.floor((target - Date.now()) / 1000));
            const values = {
                days: Math.floor(seconds / 86400),
                hours: Math.floor((seconds % 86400) / 3600),
                minutes: Math.floor((seconds % 3600) / 60),
                seconds: seconds % 60,
            };

            Object.entries(values).forEach(([key, value]) => {
                if (output[key]) {
                    output[key].firstChild.nodeValue = String(value).padStart(2, '0');
                }
            });
        };

        render();
        window.setInterval(render, 1000);
    });
};

const initialiseBniFlashes = () => {
    document.querySelectorAll('[data-bni-flash]').forEach((flash) => {
        if (flash.dataset.bniFlashReady === 'true') {
            return;
        }

        flash.dataset.bniFlashReady = 'true';
        window.setTimeout(() => {
            flash.classList.add('is-leaving');
            window.setTimeout(() => {
                flash.hidden = true;
            }, 300);
        }, 5000);
    });
};

const initialiseBniAjaxForms = () => {
    document.querySelectorAll('[data-bni-ajax-form]').forEach((form) => {
        if (form.dataset.bniAjaxReady === 'true') {
            return;
        }

        form.dataset.bniAjaxReady = 'true';
        const status = form.querySelector('[data-bni-form-status]');
        const submitButton = form.querySelector('[type="submit"]');
        let hideStatusTimer;

        const showStatus = (message, type) => {
            if (!status) {
                return;
            }

            window.clearTimeout(hideStatusTimer);
            status.textContent = message;
            status.classList.toggle('is-success', type === 'success');
            status.classList.toggle('is-error', type === 'error');
            status.hidden = false;

            if (type === 'success') {
                hideStatusTimer = window.setTimeout(() => {
                    status.hidden = true;
                }, 6000);
            }
        };

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!form.reportValidity()) {
                return;
            }

            submitButton?.setAttribute('aria-busy', 'true');
            if (submitButton) {
                submitButton.disabled = true;
            }

            try {
                const response = await window.fetch(form.action, {
                    method: form.method || 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const firstValidationMessage = Object.values(payload.errors || {}).flat()[0];

                    throw new Error(firstValidationMessage || payload.message || 'Thông tin chưa được gửi. Anh/chị vui lòng kiểm tra lại.');
                }

                if (form.dataset.resetOnSuccess === 'true') {
                    form.reset();
                }

                showStatus(payload.message || 'Thông tin đã được ghi nhận.', 'success');
            } catch (error) {
                showStatus(error.message || 'Không thể gửi thông tin lúc này. Anh/chị vui lòng thử lại.', 'error');
            } finally {
                submitButton?.removeAttribute('aria-busy');
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        });
    });
};

const initialiseBniPwa = () => {
    if (document.body.dataset.bniPage !== 'true' || !('serviceWorker' in navigator)) {
        return;
    }

    const pathname = window.location.pathname.replace(/\/+$/, '') || '/';
    const bniPath = pathname.match(/^((?:\/[^/]+)?\/le-chuyen-giao)(?:\/.*)?$/i);

    if (!bniPath) {
        return;
    }

    navigator.serviceWorker.register('/bni-sw.js', { scope: bniPath[1] }).catch(() => {});

    const banner = document.querySelector('[data-bni-install-banner]');
    const bannerCopy = banner?.querySelector('.bni-pwa-install__copy span');
    const actions = document.querySelectorAll('[data-bni-install-action]');
    const dismiss = document.querySelector('[data-bni-install-dismiss]');
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
    let deferredPrompt = null;

    if (isStandalone || !banner) {
        return;
    }

    const isDismissed = () => {
        try {
            const dismissedAt = Number(window.localStorage.getItem('tht_bni_install_dismissed_at') || 0);

            return dismissedAt > Date.now() - (14 * 24 * 60 * 60 * 1000);
        } catch {
            return false;
        }
    };

    const showBanner = (copy = null) => {
        if (copy && bannerCopy) {
            bannerCopy.textContent = copy;
        }

        banner.hidden = false;
    };

    const hideBanner = () => {
        banner.hidden = true;
    };

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt = event;

        if (!isDismissed()) {
            showBanner();
        }
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        hideBanner();
    });

    actions.forEach((action) => {
        action.addEventListener('click', async () => {
            if (!deferredPrompt) {
                showBanner(isIos
                    ? 'Mở nút Chia sẻ rồi chọn “Thêm vào Màn hình chính”.'
                    : 'Mở menu trình duyệt và chọn “Thêm vào màn hình chính” khi tùy chọn này xuất hiện.');

                return;
            }

            deferredPrompt.prompt();
            const choice = await deferredPrompt.userChoice;
            deferredPrompt = null;

            if (choice.outcome === 'dismissed') {
                try {
                    window.localStorage.setItem('tht_bni_install_dismissed_at', String(Date.now()));
                } catch {
                    // Installation remains available even when local storage is disabled.
                }

                hideBanner();
            }
        });
    });

    dismiss?.addEventListener('click', () => {
        try {
            window.localStorage.setItem('tht_bni_install_dismissed_at', String(Date.now()));
        } catch {
            // The banner can still be dismissed for the current page.
        }

        hideBanner();
    });
};

const initialiseLandingPages = () => {
    document.querySelectorAll('[data-landing-page]').forEach((page) => {
        if (page.dataset.landingReady === 'true') {
            return;
        }

        const endpoint = page.dataset.trackEndpoint;
        const landingId = page.dataset.landingId;

        if (!endpoint || !landingId) {
            return;
        }

        page.dataset.landingReady = 'true';

        const randomId = () => window.crypto?.randomUUID?.()
            ?? `${Date.now().toString(36)}-${Math.random().toString(36).slice(2)}`;
        const visitorKey = 'tht_landing_visitor_id';
        const sessionKey = 'tht_landing_session_id';
        let visitorId;
        let sessionId;

        try {
            visitorId = window.localStorage.getItem(visitorKey) || randomId();
            sessionId = window.sessionStorage.getItem(sessionKey) || randomId();
            window.localStorage.setItem(visitorKey, visitorId);
            window.sessionStorage.setItem(sessionKey, sessionId);
        } catch {
            visitorId = randomId();
            sessionId = randomId();
        }

        const query = new URLSearchParams(window.location.search);
        const attributionKeys = [
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_content',
            'utm_term',
            'gclid',
            'fbclid',
        ];
        const attribution = {
            visitor_id: visitorId,
            session_id: sessionId,
            first_url: window.location.href,
            referrer: document.referrer,
        };

        attributionKeys.forEach((key) => {
            const currentValue = query.get(key);
            const storageKey = `tht_landing_${key}`;

            try {
                if (currentValue) {
                    window.sessionStorage.setItem(storageKey, currentValue);
                }

                attribution[key] = currentValue || window.sessionStorage.getItem(storageKey) || '';
            } catch {
                attribution[key] = currentValue || '';
            }
        });

        page.querySelectorAll('[data-attribution-field]').forEach((input) => {
            input.value = attribution[input.dataset.attributionField] || '';
        });

        const track = (eventName, blockId = '', payload = {}) => {
            const formData = new FormData();
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

            if (csrf) {
                formData.append('_token', csrf);
            }

            formData.append('event_name', eventName);
            formData.append('block_id', blockId);
            formData.append('page_url', window.location.href);

            Object.entries(attribution).forEach(([key, value]) => {
                if (value) {
                    formData.append(key, value);
                }
            });

            Object.entries(payload).forEach(([key, value]) => {
                if (value !== undefined && value !== null && value !== '') {
                    formData.append(`payload[${key}]`, String(value));
                }
            });

            if (navigator.sendBeacon) {
                navigator.sendBeacon(endpoint, formData);

                return;
            }

            window.fetch(endpoint, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                keepalive: true,
            }).catch(() => {});
        };

        let pageViewTracked = false;

        try {
            const pageViewKey = `tht_landing_view_${landingId}`;
            pageViewTracked = window.sessionStorage.getItem(pageViewKey) === '1';

            if (!pageViewTracked) {
                window.sessionStorage.setItem(pageViewKey, '1');
            }
        } catch {
            pageViewTracked = false;
        }

        if (!pageViewTracked) {
            track('page_view', 'page');
        }

        page.addEventListener('click', (event) => {
            const target = event.target.closest('[data-landing-event]');

            if (!target || !page.contains(target)) {
                return;
            }

            track(target.dataset.landingEvent, target.dataset.blockId || '', {
                label: target.textContent?.trim().slice(0, 255),
                target_url: target.href || '',
                project_id: target.dataset.projectId || '',
                pricing_plan_id: target.dataset.pricingPlanId || '',
            });
        });

        page.querySelectorAll('[data-landing-countdown]').forEach((countdown) => {
            const target = new Date(countdown.dataset.countdownEnd || '').getTime();

            if (Number.isNaN(target)) {
                return;
            }

            const output = {
                days: countdown.querySelector('[data-countdown-days]'),
                hours: countdown.querySelector('[data-countdown-hours]'),
                minutes: countdown.querySelector('[data-countdown-minutes]'),
                seconds: countdown.querySelector('[data-countdown-seconds]'),
            };
            let expiredTracked = false;
            let interval;
            const render = () => {
                const remaining = Math.max(0, Math.floor((target - Date.now()) / 1000));
                const values = {
                    days: Math.floor(remaining / 86400),
                    hours: Math.floor((remaining % 86400) / 3600),
                    minutes: Math.floor((remaining % 3600) / 60),
                    seconds: remaining % 60,
                };

                Object.entries(values).forEach(([key, value]) => {
                    if (output[key]) {
                        output[key].textContent = String(value).padStart(2, '0');
                    }
                });

                if (remaining === 0) {
                    countdown.classList.add('is-expired');

                    if (!expiredTracked) {
                        expiredTracked = true;
                        track('countdown_expired', countdown.dataset.blockId || 'countdown');
                    }

                    if (interval) {
                        window.clearInterval(interval);
                    }
                }
            };

            track('countdown_view', countdown.dataset.blockId || 'countdown');
            render();
            interval = window.setInterval(render, 1000);
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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initialiseHeroSwipers();
        initialiseBniHeroSwipers();
        initialisePostSwipers();
        initialiseTestimonialSwipers();
        initialiseScrollTop();
        initialiseAos();
        initialiseCountUps();
        initialiseBniCountdowns();
        initialiseBniFlashes();
        initialiseBniAjaxForms();
        initialiseBniPwa();
        initialiseLandingPages();
        initialiseLightboxes();
        initialiseLandingModals();
    }, { once: true });
} else {
    initialiseHeroSwipers();
    initialiseBniHeroSwipers();
    initialisePostSwipers();
    initialiseTestimonialSwipers();
    initialiseScrollTop();
    initialiseAos();
    initialiseCountUps();
    initialiseBniCountdowns();
    initialiseBniFlashes();
    initialiseBniAjaxForms();
    initialiseBniPwa();
    initialiseLandingPages();
    initialiseLightboxes();
    initialiseLandingModals();
}
