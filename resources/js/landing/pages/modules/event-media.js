(function () {
    'use strict';

    function initTabs() {
        document.querySelectorAll('[data-media-tabs]').forEach(function (tabs) {
            var buttons = tabs.querySelectorAll('[role="tab"]');
            var panels = tabs.querySelectorAll('[role="tabpanel"]');

            buttons.forEach(function (button, index) {
                button.addEventListener('click', function () {
                    buttons.forEach(function (item) {
                        item.classList.remove('is-active');
                        item.setAttribute('aria-selected', 'false');
                    });
                    panels.forEach(function (panel) {
                        panel.classList.remove('is-active');
                    });

                    button.classList.add('is-active');
                    button.setAttribute('aria-selected', 'true');
                    if (panels[index]) {
                        panels[index].classList.add('is-active');
                    }
                });
            });
        });
    }

    function initAccordions() {
        document.querySelectorAll('[data-media-accordion]').forEach(function (accordion) {
            accordion.querySelectorAll('.tht-landing-media-accordion__item > h3 > button').forEach(function (button) {
                button.addEventListener('click', function () {
                    var item = button.closest('.tht-landing-media-accordion__item');
                    var wasOpen = item.classList.contains('is-open');

                    accordion.querySelectorAll('.tht-landing-media-accordion__item').forEach(function (sibling) {
                        sibling.classList.remove('is-open');
                        var siblingButton = sibling.querySelector('h3 > button');
                        if (siblingButton) {
                            siblingButton.setAttribute('aria-expanded', 'false');
                        }
                    });

                    if (!wasOpen) {
                        item.classList.add('is-open');
                        button.setAttribute('aria-expanded', 'true');
                    }
                });
            });
        });
    }

    function initBriefForm() {
        document.querySelectorAll('.tht-landing-media-brief-form[action="#"]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                var values = {
                    event_date: form.querySelector('[name="event_date"]'),
                    event_location: form.querySelector('[name="event_location"]'),
                    event_duration: form.querySelector('[name="event_duration"]'),
                    message: form.querySelector('[name="message"]')
                };
                var modal = document.getElementById('tht-landing-contact-modal');

                if (!modal || !window.landingOpenModal) {
                    return;
                }

                var modalMessage = modal.querySelector('[name="message"]');
                if (modalMessage) {
                    modalMessage.value = [
                        'Tôi cần quay/chụp sự kiện.',
                        'Ngày tổ chức: ' + (values.event_date ? values.event_date.value : ''),
                        'Địa điểm: ' + (values.event_location ? values.event_location.value : ''),
                        'Thời lượng: ' + (values.event_duration ? values.event_duration.value : ''),
                        'Nhu cầu: ' + (values.message ? values.message.value : '')
                    ].join('\n');
                }

                window.landingOpenModal(modal);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initTabs();
        initAccordions();
        initBriefForm();
    });
}());
