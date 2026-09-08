(function () {
    'use strict';

    function initAccordions() {
        document.querySelectorAll('[data-marketing-accordion]').forEach(function (accordion) {
            accordion.querySelectorAll('.tht-landing-marketing-accordion__item > h3 > button').forEach(function (button) {
                button.addEventListener('click', function () {
                    var item = button.closest('.tht-landing-marketing-accordion__item');
                    var wasOpen = item.classList.contains('is-open');

                    accordion.querySelectorAll('.tht-landing-marketing-accordion__item').forEach(function (sibling) {
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

    document.addEventListener('DOMContentLoaded', function () {
        initAccordions();
    });
}());
