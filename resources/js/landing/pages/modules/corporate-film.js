document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 80
        });
    }

    const modal = document.getElementById('tvcVideoModal');
    const iframe = document.getElementById('tvcVideoFrame');
    const openButtons = document.querySelectorAll('.js-open-video');
    const closeButtons = document.querySelectorAll('.js-close-video');

    openButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const videoUrl = this.getAttribute('data-video');

            if (!videoUrl || !modal || !iframe) {
                return;
            }

            iframe.setAttribute('src', videoUrl + '?autoplay=1&rel=0');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeVideoModal);
    });

    document.addEventListener('keyup', function (event) {
        if (event.key === 'Escape') {
            closeVideoModal();
        }
    });

    function closeVideoModal() {
        if (!modal || !iframe) {
            return;
        }

        modal.classList.remove('active');
        iframe.setAttribute('src', '');
        document.body.style.overflow = '';
    }

    const faqItems = document.querySelectorAll('.tvc-faq-item');

    faqItems.forEach(function (item) {
        const question = item.querySelector('.tvc-faq-question');

        if (!question) {
            return;
        }

        question.addEventListener('click', function () {
            const isActive = item.classList.contains('active');

            faqItems.forEach(function (otherItem) {
                otherItem.classList.remove('active');
            });

            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    if (typeof Swiper !== 'undefined') {
        new Swiper('.tvc-bts-swiper', {
            slidesPerView: 1,
            spaceBetween: 18,
            loop: true,
            autoplay: {
                delay: 2800,
                disableOnInteraction: false
            },
            pagination: {
                el: '.tvc-bts-pagination',
                clickable: true
            },
            breakpoints: {
                768: {
                    slidesPerView: 2
                },
                1024: {
                    slidesPerView: 3
                }
            }
        });
    }

    const leadForm = document.querySelector('.tvc-lead-form');

    if (leadForm) {
        leadForm.addEventListener('submit', function (event) {
            event.preventDefault();

            alert('Cảm ơn anh/chị. THT Media sẽ liên hệ tư vấn trong thời gian sớm nhất.');
        });
    }
});