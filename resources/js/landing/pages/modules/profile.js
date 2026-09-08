(function () {
    'use strict';

    function initTabs() {
        document.querySelectorAll('[data-profile-tabs]').forEach(function (tabs) {
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
        document.querySelectorAll('[data-profile-accordion]').forEach(function (accordion) {
            accordion.querySelectorAll('.tht-landing-profile-accordion__item > h3 > button').forEach(function (button) {
                button.addEventListener('click', function () {
                    var item = button.closest('.tht-landing-profile-accordion__item');
                    var wasOpen = item.classList.contains('is-open');

                    accordion.querySelectorAll('.tht-landing-profile-accordion__item').forEach(function (sibling) {
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

    function initFilters() {
        document.querySelectorAll('[data-profile-filter] button').forEach(function (button) {
            button.addEventListener('click', function () {
                button.parentElement.querySelectorAll('button').forEach(function (item) {
                    item.classList.remove('is-active');
                });
                button.classList.add('is-active');

                var filter = button.getAttribute('data-filter');
                document.querySelectorAll('[data-profile-item]').forEach(function (item) {
                    var category = item.getAttribute('data-profile-category');
                    item.hidden = filter !== 'Tất cả' && category !== filter;
                });
            });
        });
    }

    function initPromotionCountdown() {
        document.querySelectorAll('[data-profile-countdown]').forEach(function (countdown) {
            var deadline = Date.parse(countdown.getAttribute('data-deadline') || '');
            var days = countdown.querySelector('[data-countdown-days]');
            var hours = countdown.querySelector('[data-countdown-hours]');
            var minutes = countdown.querySelector('[data-countdown-minutes]');
            var seconds = countdown.querySelector('[data-countdown-seconds]');
            var status = countdown.querySelector('[data-countdown-status]');
            var activeMessage = countdown.getAttribute('data-active-message') || '';
            var expiredMessage = countdown.getAttribute('data-expired-message') || 'Ưu đãi đã kết thúc.';

            if (Number.isNaN(deadline) || !days || !hours || !minutes || !seconds) {
                return;
            }

            function setValue(element, value) {
                element.textContent = String(value).padStart(2, '0');
            }

            function updateCountdown() {
                var remaining = deadline - Date.now();

                if (remaining <= 0) {
                    setValue(days, 0);
                    setValue(hours, 0);
                    setValue(minutes, 0);
                    setValue(seconds, 0);
                    countdown.classList.add('is-expired');
                    if (status) {
                        status.textContent = expiredMessage;
                    }
                    return;
                }

                var totalSeconds = Math.floor(remaining / 1000);
                var remainingDays = Math.floor(totalSeconds / 86400);
                var remainingHours = Math.floor((totalSeconds % 86400) / 3600);
                var remainingMinutes = Math.floor((totalSeconds % 3600) / 60);
                var remainingSeconds = totalSeconds % 60;

                setValue(days, remainingDays);
                setValue(hours, remainingHours);
                setValue(minutes, remainingMinutes);
                setValue(seconds, remainingSeconds);

                if (status) {
                    status.textContent = activeMessage;
                }
            }

            updateCountdown();
            window.setInterval(updateCountdown, 1000);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initTabs();
        initAccordions();
        initFilters();
        initPromotionCountdown();
    });
}());
