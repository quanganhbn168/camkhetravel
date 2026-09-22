import Collapse from 'bootstrap/js/dist/collapse';
import Dropdown from 'bootstrap/js/dist/dropdown';
import Modal from 'bootstrap/js/dist/modal';
import Offcanvas from 'bootstrap/js/dist/offcanvas';
import Tab from 'bootstrap/js/dist/tab';

window.bootstrap = { Collapse, Dropdown, Modal, Offcanvas, Tab };

function initialiseHeader() {
    const header = document.querySelector('[data-site-header]');
    const drawer = document.getElementById('mobile-drawer');
    const search = document.getElementById('header-search-modal');

    search?.addEventListener('shown.bs.modal', () => document.getElementById('header-search-query')?.focus());
    matchMedia('(min-width: 1200px)').addEventListener('change', ({ matches }) => {
        if (matches && drawer) Offcanvas.getInstance(drawer)?.hide();
    });

    if (!header) return;

    let previousScroll = Math.max(0, window.scrollY);

    window.addEventListener('scroll', () => {
        const currentScroll = Math.max(0, window.scrollY);

        if (currentScroll <= header.offsetHeight) {
            header.classList.remove('is-scroll-hidden');
        } else if (Math.abs(currentScroll - previousScroll) < 5) {
            return;
        } else {
            header.classList.toggle('is-scroll-hidden', currentScroll > previousScroll);
        }

        previousScroll = currentScroll;
    }, { passive: true });
}

function initialiseScrollTop() {
    const button = document.querySelector('[data-scroll-top]');
    if (!button) return;

    const toggle = () => button.classList.toggle('is-visible', window.scrollY > 480);
    toggle();
    window.addEventListener('scroll', toggle, { passive: true });
    button.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

function initialise() {
    initialiseHeader();
    initialiseScrollTop();

    if (document.querySelector('[data-hero-swiper], [data-post-swiper], [data-testimonial-swiper]')) {
        void import('./frontend/sliders').then((module) => module.initialiseSliders());
    }

    if (document.querySelector('.fa-solid, .fa-regular, .fa-brands, .fas, .far, .fab, .fa')) {
        void import('./frontend/icons');
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialise, { once: true });
} else {
    initialise();
}
