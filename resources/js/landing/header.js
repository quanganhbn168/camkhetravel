import '../../css/landing/components/scroll-header.css';

document.querySelectorAll('[data-landing-scroll-header]').forEach((header) => {
    let lastY = Math.max(0, window.scrollY);
    let scheduled = false;

    const update = () => {
        scheduled = false;
        const currentY = Math.max(0, window.scrollY);
        const delta = currentY - lastY;
        const menuOpen = Boolean(header.querySelector('[aria-expanded="true"]'));
        header.classList.toggle('is-scrolled', currentY > 20);

        if (currentY <= header.offsetHeight || menuOpen || header.querySelector(':focus-visible')) {
            header.classList.remove('landing-header-hidden');
        } else if (Math.abs(delta) >= 6) {
            header.classList.toggle('landing-header-hidden', delta > 0);
        } else {
            return;
        }

        lastY = currentY;
    };

    window.addEventListener('scroll', () => {
        if (!scheduled) {
            scheduled = true;
            requestAnimationFrame(update);
        }
    }, { passive: true });
    header.addEventListener('focusin', () => header.classList.remove('landing-header-hidden'));
    window.addEventListener('resize', update);
    update();
});
