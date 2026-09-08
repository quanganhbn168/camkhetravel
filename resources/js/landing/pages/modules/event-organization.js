(function () {
    'use strict';

    function initEventAccordions() {
        document.querySelectorAll('[data-event-accordion]').forEach(function (accordion) {
            accordion.querySelectorAll('.tht-landing-event-accordion__item > h3 > button').forEach(function (button) {
                button.addEventListener('click', function () {
                    var item = button.closest('.tht-landing-event-accordion__item');
                    var isOpen = item.classList.contains('is-open');

                    accordion.querySelectorAll('.tht-landing-event-accordion__item').forEach(function (sibling) {
                        sibling.classList.remove('is-open');
                        var siblingButton = sibling.querySelector('h3 > button');
                        if (siblingButton) {
                            siblingButton.setAttribute('aria-expanded', 'false');
                        }
                    });

                    if (!isOpen) {
                        item.classList.add('is-open');
                        button.setAttribute('aria-expanded', 'true');
                    }
                });
            });
        });
    }

    function initBriefForm() {
        document.querySelectorAll('.tht-landing-event-brief-form[action="#"]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                var fullname = form.querySelector('[name="fullname"]');
                var phone = form.querySelector('[name="phone"]');
                var message = form.querySelector('[name="message"]');

                if (fullname && phone && message) {
                    message.value = message.value || 'Tôi muốn được tư vấn tổ chức sự kiện trọn gói.';
                }

                var modal = document.getElementById('tht-landing-contact-modal');
                if (modal && window.landingOpenModal) {
                    window.landingOpenModal(modal);

                    if (fullname && modal.querySelector('[name="fullname"]')) {
                        modal.querySelector('[name="fullname"]').value = fullname.value;
                    }
                    if (phone && modal.querySelector('[name="phone"]')) {
                        modal.querySelector('[name="phone"]').value = phone.value;
                    }
                    if (message && modal.querySelector('[name="message"]')) {
                        modal.querySelector('[name="message"]').value = message.value;
                    }
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initEventAccordions();
        initBriefForm();
    });
}());
