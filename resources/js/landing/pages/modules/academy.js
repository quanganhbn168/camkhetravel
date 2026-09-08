document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 750,
            once: true,
            offset: 80
        });
    }

    const faqItems = document.querySelectorAll('.academy-faq-item');

    faqItems.forEach(function (item) {
        const question = item.querySelector('.academy-faq-question');

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

    if (typeof GLightbox !== 'undefined') {
        GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true
        });
    }

});
