const menuButton = document.querySelector('.branding-menu-toggle');
const mobileMenu = document.getElementById('branding-mobile-menu');

if (menuButton && mobileMenu) {
    const setMenu = (open) => {
        mobileMenu.hidden = !open;
        menuButton.setAttribute('aria-expanded', String(open));
        menuButton.setAttribute('aria-label', open ? 'Đóng menu' : 'Mở menu');
    };

    menuButton.addEventListener('click', () => setMenu(mobileMenu.hidden));
    mobileMenu.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setMenu(false)));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !mobileMenu.hidden) {
            setMenu(false);
            menuButton.focus();
        }
    });
    const desktop = window.matchMedia('(min-width: 1024px)');
    desktop.addEventListener('change', (event) => { if (event.matches) setMenu(false); });
}

const form = document.getElementById('branding-lead-form');
if (form?.querySelector('[role="alert"], .branding-form-success')) {
    form.scrollIntoView({ block: 'center', behavior: 'instant' });
}
