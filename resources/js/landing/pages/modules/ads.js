(function () {
    'use strict';

    function initLanding() {
        const root = document.querySelector('.tht-landing');

        if (!root) {
            return;
        }

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (typeof window.AOS !== 'undefined' && !reduceMotion) {
            window.AOS.init({
                duration: 720,
                easing: 'ease-out-cubic',
                offset: 72,
                once: true,
                mirror: false
            });
        } else {
            root.querySelectorAll('[data-aos]').forEach(function (element) {
                element.classList.add('aos-animate');
            });
        }

        if (typeof window.Swiper !== 'undefined') {
            new window.Swiper('.tht-landing-showcase-swiper', {
                slidesPerView: 1.08,
                spaceBetween: 16,
                speed: reduceMotion ? 0 : 650,
                grabCursor: !reduceMotion,
                watchOverflow: true,
                keyboard: {
                    enabled: true,
                    onlyInViewport: true
                },
                pagination: {
                    el: '.tht-landing-showcase-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.tht-landing-showcase-next',
                    prevEl: '.tht-landing-showcase-prev'
                },
                breakpoints: {
                    768: {
                        slidesPerView: 1.55,
                        spaceBetween: 20
                    },
                    1200: {
                        slidesPerView: 2.18,
                        spaceBetween: 24
                    }
                }
            });
        }

        if (typeof window.GLightbox !== 'undefined') {
            window.GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true,
                autoplayVideos: false,
                openEffect: reduceMotion ? 'none' : 'fade',
                closeEffect: reduceMotion ? 'none' : 'fade'
            });
        }

        root.querySelectorAll('a[href^="#"]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                const targetId = link.getAttribute('href');

                if (!targetId || targetId === '#') {
                    return;
                }

                const target = document.querySelector(targetId);

                if (!target) {
                    return;
                }

                event.preventDefault();
                target.scrollIntoView({
                    behavior: reduceMotion ? 'auto' : 'smooth',
                    block: 'start'
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLanding, { once: true });
    } else {
        initLanding();
    }
}());
