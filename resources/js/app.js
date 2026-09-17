import Alpine from 'alpinejs';
import Collapse from 'bootstrap/js/dist/collapse';
import Dropdown from 'bootstrap/js/dist/dropdown';
import Modal from 'bootstrap/js/dist/modal';
import Offcanvas from 'bootstrap/js/dist/offcanvas';
import Tab from 'bootstrap/js/dist/tab';
import { initialiseCountUps } from './frontend/counters';
import { initialiseLeadForms } from './frontend/forms';

// Filament uses its own scripts; this entry belongs exclusively to the website.
window.Alpine = Alpine;
window.bootstrap = { Collapse, Dropdown, Modal, Offcanvas, Tab };
const loadLightbox = () => import('./frontend/lightbox').then((module) => module.refreshLightboxes());
window.refreshLightboxes = loadLightbox;

function initialiseHeader() {
    const header = document.querySelector('[data-site-header]');
    const drawer = document.getElementById('mobile-drawer');
    const search = document.getElementById('header-search-modal');
    let lastY = window.scrollY;
    let queued = false;
    const sync = () => {
        queued = false;
        const y = Math.max(0, window.scrollY);
        const overlayOpen = document.querySelector('.offcanvas.show, .modal.show, .dropdown-menu.show');
        const focused = header?.contains(document.activeElement);
        if (header && (Math.abs(y - lastY) >= 6 || y <= header.offsetHeight || overlayOpen || focused)) {
            header.classList.toggle('is-scroll-hidden', y > header.offsetHeight && y > lastY && !overlayOpen && !focused);
            lastY = y;
        }
    };
    if (header) {
        window.addEventListener('scroll', () => { if (!queued) { queued = true; requestAnimationFrame(sync); } }, { passive: true });
        header.addEventListener('focusin', () => header.classList.remove('is-scroll-hidden'));
    }
    search?.addEventListener('shown.bs.modal', () => document.getElementById('header-search-query')?.focus());
    drawer?.addEventListener('show.bs.offcanvas', () => header?.classList.remove('is-scroll-hidden'));
    const desktop = matchMedia('(min-width: 1200px)');
    desktop.addEventListener('change', ({ matches }) => { if (matches && drawer) Offcanvas.getInstance(drawer)?.hide(); });
}

function initialiseScrollTop() {
    const button = document.querySelector('[data-scroll-top]');
    if (!button) return;
    const toggle = () => button.classList.toggle('is-visible', window.scrollY > 480);
    toggle();
    window.addEventListener('scroll', toggle, { passive: true });
    button.addEventListener('click', () => window.scrollTo({ top: 0, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }));
}

function initialise() {
    Alpine.start();
    initialiseHeader();
    initialiseCountUps();
    initialiseLeadForms();
    initialiseScrollTop();
    const tasks = [];
    if (document.querySelector('[data-hero-swiper], [data-post-swiper], [data-testimonial-swiper]')) {
        tasks.push(import('./frontend/sliders').then((module) => module.initialiseSliders()));
    }
    if (document.querySelector('.glightbox')) tasks.push(loadLightbox());
    if (document.querySelector('[data-aos]') && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
        tasks.push(import('./frontend/motion').then((module) => module.initialiseMotion()));
    }
    if (document.querySelector('.fa-solid, .fa-regular, .fa-brands, .fas, .far, .fab, .fa')) {
        tasks.push(import('./frontend/icons'));
    }
    Promise.all(tasks).catch((error) => console.error('Không thể tải thành phần giao diện:', error));
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialise, { once: true });
else initialise();
