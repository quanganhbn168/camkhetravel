export const initialiseCountUps = () => {
    const counters = document.querySelectorAll('[data-count-up]');
    if (!counters.length) return;

    const formatter = new Intl.NumberFormat(document.documentElement.lang || 'vi');
    const renderValue = (element, value) => { element.textContent = formatter.format(value); };
    const animateCounter = (element) => {
        if (element.dataset.countStarted === 'true') return;

        element.dataset.countStarted = 'true';
        const target = Number(element.dataset.countUp || 0);
        const duration = Math.min(1800, Math.max(800, 650 + target * 4));
        const startedAt = performance.now();
        const update = (now) => {
            const progress = Math.min((now - startedAt) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            renderValue(element, Math.round(target * eased));
            if (progress < 1) window.requestAnimationFrame(update);
        };
        window.requestAnimationFrame(update);
    };

    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        counters.forEach((element) => renderValue(element, Number(element.dataset.countUp || 0)));
        return;
    }

    const observer = new IntersectionObserver((entries, activeObserver) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            animateCounter(entry.target);
            activeObserver.unobserve(entry.target);
        });
    }, { threshold: 0.45 });

    counters.forEach((counter) => {
        renderValue(counter, 0);
        observer.observe(counter);
    });
};

