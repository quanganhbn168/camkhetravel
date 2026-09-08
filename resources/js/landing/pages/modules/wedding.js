(function () {
    'use strict';

    function showSuccess() {
        if (typeof window.Swal !== 'undefined') {
            window.Swal.fire({
                title: 'Đã nhận yêu cầu',
                text: 'THT Media sẽ liên hệ lại với anh/chị trong thời gian sớm nhất.',
                icon: 'success',
                confirmButtonText: 'Đóng',
                confirmButtonColor: '#5cb811'
            });
            return;
        }

        window.alert('THT Media đã nhận được thông tin. Bộ phận tư vấn sẽ liên hệ lại với anh/chị trong thời gian sớm nhất.');
    }

    function initAddonSelector() {
        var selector = document.querySelector('.wedding-addon-selector');

        if (!selector) {
            return;
        }

        var choices = Array.prototype.slice.call(selector.querySelectorAll('.wedding-addon-choice'));
        var recommendationName = selector.querySelector('[data-recommendation-name]');
        var recommendationPrice = selector.querySelector('[data-recommendation-price]');
        var recommendationDescription = selector.querySelector('[data-recommendation-description]');
        var selectedAddons = selector.querySelector('[data-selected-addons]');
        var packages = {
            combo: {
                name: 'Combo quay và chụp phóng sự cưới',
                price: '7.000.000 VNĐ',
                description: 'Phù hợp nhất để lưu giữ trọn vẹn lễ ăn hỏi và lễ đón dâu, đồng bộ cả ảnh lẫn video.'
            },
            traditional: {
                name: 'Combo chụp và video cưới truyền thống',
                price: '9.000.000 VNĐ',
                description: 'Phù hợp khi anh/chị muốn lưu giữ đầy đủ cả lễ ăn hỏi, đón dâu và tiệc cưới.'
            }
        };

        function updateRecommendation() {
            var selectedChoices = choices.filter(function (choice) {
                return choice.getAttribute('aria-pressed') === 'true';
            });
            var selectedLabels = selectedChoices.map(function (choice) {
                return choice.getAttribute('data-addon-label');
            });
            var packageKey = selectedChoices.some(function (choice) {
                return choice.getAttribute('data-package') === 'traditional';
            }) ? 'traditional' : 'combo';
            var recommendedPackage = packages[packageKey];

            if (recommendationName) {
                recommendationName.textContent = recommendedPackage.name;
            }

            if (recommendationPrice) {
                recommendationPrice.textContent = recommendedPackage.price;
            }

            if (recommendationDescription) {
                recommendationDescription.textContent = recommendedPackage.description;
            }

            if (selectedAddons) {
                selectedAddons.textContent = selectedLabels.length
                    ? 'Hạng mục đã chọn: ' + selectedLabels.join(' · ') + '. Ekip sẽ xác nhận chi phí bổ sung khi tư vấn.'
                    : 'Chọn hạng mục để tinh chỉnh gói theo nhu cầu của anh/chị.';
            }
        }

        choices.forEach(function (choice) {
            choice.addEventListener('click', function () {
                var isSelected = choice.getAttribute('aria-pressed') === 'true';

                choice.setAttribute('aria-pressed', String(!isSelected));
                choice.classList.toggle('is-selected', !isSelected);
                updateRecommendation();
            });
        });

        updateRecommendation();
    }

    function initInvitationScrollPreviews() {
        document.querySelectorAll('.wedding-invitation-card--scroll').forEach(function (card) {
            var viewport = card.querySelector('.wedding-invitation-card__image');
            var image = viewport ? viewport.querySelector('img') : null;

            if (!viewport || !image) {
                return;
            }

            function setScrollDistance() {
                window.requestAnimationFrame(function () {
                    var distance = Math.max(0, image.getBoundingClientRect().height - viewport.clientHeight);

                    card.style.setProperty('--invitation-scroll-offset', '-' + distance + 'px');
                });
            }

            if (image.complete) {
                setScrollDistance();
            }

            image.addEventListener('load', setScrollDistance, { once: true });
            window.addEventListener('resize', setScrollDistance);
        });
    }

    function initWeddingLanding() {
        document.querySelectorAll('.wedding-lead-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                var action = form.getAttribute('action');

                if (!action || action === '#') {
                    event.preventDefault();
                    form.reset();
                    showSuccess();
                }
            });
        });

        initAddonSelector();
        initInvitationScrollPreviews();

        var testimonials = document.querySelector('.wedding-testimonials-swiper');

        if (testimonials && typeof window.Swiper !== 'undefined') {
            new window.Swiper(testimonials, {
                slidesPerView: 1.05,
                spaceBetween: 16,
                grabCursor: true,
                watchOverflow: true,
                pagination: {
                    el: testimonials.querySelector('.wedding-testimonials-pagination'),
                    clickable: true
                },
                breakpoints: {
                    768: { slidesPerView: 1.65, spaceBetween: 20 },
                    1200: { slidesPerView: 2.25, spaceBetween: 24 }
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWeddingLanding, { once: true });
    } else {
        initWeddingLanding();
    }
}());
