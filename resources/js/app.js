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

function initialiseHomeNavigation() {
    if (!document.querySelector('[data-camkhe-home]')) return;

    const header = document.querySelector('[data-site-header]');
    const drawer = document.getElementById('mobile-drawer');
    const links = Array.from(document.querySelectorAll('.header-nav__link, .mobile-nav__link'));
    const samePageLinks = links.filter(link => link.origin === location.origin
        && link.pathname === location.pathname && link.getAttribute('href') !== '#');
    const sections = samePageLinks
        .map(link => {
            try {
                return { link, section: link.hash ? document.getElementById(decodeURIComponent(link.hash.slice(1))) : null };
            } catch {
                return { link, section: null };
            }
        })
        .filter(({ section }) => section);

    if (!sections.length) return;

    const setActive = hash => {
        samePageLinks.forEach(link => {
            const active = link.hash === hash;
            link.classList.toggle('is-active', active);
            if (active) link.setAttribute('aria-current', hash ? 'location' : 'page');
            else link.removeAttribute('aria-current');
        });
    };

    const update = () => {
        const threshold = (header?.offsetHeight || 0) + 24;
        let activeHash = '';
        sections.forEach(({ link, section }) => {
            if (section.getBoundingClientRect().top <= threshold) activeHash = link.hash;
        });
        setActive(activeHash);
    };

    sections.forEach(({ link }) => link.addEventListener('click', () => {
        setActive(link.hash);
        if (drawer?.contains(link)) Offcanvas.getInstance(drawer)?.hide();
    }));

    update();
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('hashchange', update);
    window.addEventListener('load', update, { once: true });
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
    initialiseHomeNavigation();
    initialiseScrollTop();
    const solutions = document.getElementById('giai-phap');
    const background = solutions?.querySelector('[data-solution-background]');
    if (background) {
        solutions.addEventListener('shown.bs.tab', (event) => {
            const panelId = event.target.getAttribute('aria-controls');
            const panel = panelId ? document.getElementById(panelId) : null;
            if (!panel || !solutions.contains(panel)) return;
            const source = panel.querySelector('.solution-image img')?.getAttribute('src');
            if (source) {
                background.src = source;
                background.hidden = false;
            } else {
                background.hidden = true;
                background.removeAttribute('src');
            }
        });
    }

    if (document.querySelector('[data-hero-swiper], [data-post-swiper], [data-testimonial-swiper], [data-partner-swiper], [data-project-gallery], [data-project-site-swiper], [data-project-related-swiper]')) {
        void import('./frontend/sliders').then((module) => module.initialiseSliders());
    }

    if (document.querySelector('[data-camkhe-home]')) {
        void import('./frontend/camkhetravel-home');
    }

}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialise, { once: true });
} else {
    initialise();
}
