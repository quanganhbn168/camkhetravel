(function () {
    'use strict';

    function initLanding() {
        const root = document.querySelector('.tht-landing');

        if (!root) {
            return;
        }

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // 1. AOS Animation Init
        if (typeof window.AOS !== 'undefined' && !reduceMotion) {
            window.AOS.init({
                duration: 650,
                easing: 'ease-out-quart',
                offset: 0,
                once: true,
                mirror: false
            });
        } else {
            root.querySelectorAll('[data-aos]').forEach(function (element) {
                element.classList.add('aos-animate');
            });
        }


        // 2. Testimonials Swiper
        if (typeof window.Swiper !== 'undefined') {
            new window.Swiper('.tht-landing-testimonials-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                grabCursor: true,
                pagination: {
                    el: '.tht-landing-testimonials-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    },
                    992: {
                        slidesPerView: 3,
                    }
                }
            });

        }

        // 3. GLightbox Init
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

        // 4. Smooth Anchor Scrolling
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

        // 6. Film package details reuse the page's single contact modal.
        if (root.classList.contains('tht-landing-theme-film')) {
            const filmModal = root.querySelector('#tht-landing-contact-modal');

            if (filmModal) {
                const modalBody = filmModal.querySelector('.modal-body');
                const modalTitle = filmModal.querySelector('.tht-landing-modal__title');
                const modalEyebrow = filmModal.querySelector('.tht-landing-modal__eyebrow');
                const contactIntro = filmModal.querySelector('.tht-landing-modal__contact-intro');
                const contactForm = filmModal.querySelector('.tht-landing-contact__form');
                const defaultTitle = modalTitle ? modalTitle.textContent.trim() : 'Đăng ký tư vấn';
                const defaultEyebrow = modalEyebrow ? modalEyebrow.textContent.trim() : 'Nhận tư vấn miễn phí';
                const detailMount = document.createElement('div');

                detailMount.className = 'tht-landing-film-package-modal';
                detailMount.hidden = true;

                if (modalBody) {
                    modalBody.prepend(detailMount);
                }

                const setPackageInterest = function (packageName) {
                    if (!contactForm) {
                        return;
                    }

                    let packageInput = contactForm.querySelector('input[name="package_interest"]');
                    const message = contactForm.querySelector('textarea[name="message"]');

                    if (!packageInput) {
                        packageInput = document.createElement('input');
                        packageInput.type = 'hidden';
                        packageInput.name = 'package_interest';
                        contactForm.prepend(packageInput);
                    }

                    packageInput.value = packageName || '';

                    if (!message) {
                        return;
                    }

                    if (!packageName && message.dataset.filmPackagePrefill === 'true') {
                        message.value = '';
                        delete message.dataset.filmPackagePrefill;
                    } else if (packageName && !message.value.trim()) {
                        message.value = 'Tôi muốn được tư vấn chi tiết gói ' + packageName + '.';
                        message.dataset.filmPackagePrefill = 'true';
                    }
                };

                const showContactMode = function (packageName) {
                    detailMount.hidden = true;

                    if (contactIntro) {
                        contactIntro.hidden = false;
                    }

                    if (contactForm) {
                        contactForm.hidden = false;
                    }

                    if (modalEyebrow) {
                        modalEyebrow.textContent = defaultEyebrow;
                    }

                    if (modalTitle) {
                        modalTitle.textContent = packageName ? 'Tư vấn gói ' + packageName : defaultTitle;
                    }

                    setPackageInterest(packageName || '');

                    if (modalBody) {
                        modalBody.scrollTop = 0;
                    }
                };

                const showDetailMode = function (trigger) {
                    const templateId = trigger.dataset.filmPackageTemplate;
                    const detailTemplate = templateId ? document.getElementById(templateId) : null;
                    const packageName = trigger.dataset.filmPackageName || '';

                    if (!detailTemplate) {
                        showContactMode(packageName);
                        return;
                    }

                    detailMount.replaceChildren(detailTemplate.content.cloneNode(true));
                    detailMount.hidden = false;

                    if (contactIntro) {
                        contactIntro.hidden = true;
                    }

                    if (contactForm) {
                        contactForm.hidden = true;
                    }

                    if (modalEyebrow) {
                        modalEyebrow.textContent = 'Chi tiết gói sản xuất';
                    }

                    if (modalTitle) {
                        modalTitle.textContent = 'Gói ' + packageName;
                    }

                    const consultButton = detailMount.querySelector('[data-film-package-consult]');
                    if (consultButton) {
                        consultButton.addEventListener('click', function () {
                            showContactMode(packageName);
                        }, { once: true });
                    }
                };

                filmModal.addEventListener('show.bs.modal', function (event) {
                    const trigger = event.relatedTarget;

                    if (trigger && trigger.matches('[data-film-package-detail]')) {
                        showDetailMode(trigger);
                    } else {
                        showContactMode('');
                    }
                });

                filmModal.addEventListener('hidden.bs.modal', function () {
                    detailMount.hidden = true;
                    detailMount.replaceChildren();
                    showContactMode('');
                });
            }
        }

        // Lead forms use the same native POST/redirect flow as the branding landing.
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLanding, { once: true });
    } else {
        initLanding();
    }
}());
