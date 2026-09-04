(() => {
    const init = (root) => {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        root.querySelectorAll('a[href^="#"]').forEach((link) => {
            link.addEventListener('click', (event) => {
                const selector = link.getAttribute('href');
                const target = selector && document.querySelector(selector);

                if (!target) {
                    return;
                }

                event.preventDefault();
                target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
            });
        });

        const menuButton = root.querySelector('[data-communications-menu]');
        const mobileMenu = root.querySelector('#communications-mobile-nav');

        if (menuButton && mobileMenu) {
            menuButton.addEventListener('click', () => {
                const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
                menuButton.setAttribute('aria-expanded', String(!isOpen));
                mobileMenu.hidden = isOpen;
            });

            mobileMenu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    menuButton.setAttribute('aria-expanded', 'false');
                    mobileMenu.hidden = true;
                });
            });
        }

        const solutionRoot = root.querySelector('[data-communications-solutions]');

        if (solutionRoot) {
            const tabs = [...solutionRoot.querySelectorAll('[data-solution-tab]')];
            const panels = [...solutionRoot.querySelectorAll('[data-solution-panel]')];

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const active = tab.dataset.solutionTab;

                    tabs.forEach((item) => {
                        const selected = item === tab;
                        item.classList.toggle('is-active', selected);
                        item.setAttribute('aria-selected', String(selected));
                    });
                    panels.forEach((panel) => {
                        const selected = panel.dataset.solutionPanel === active;
                        panel.classList.toggle('is-active', selected);
                        panel.hidden = !selected;
                    });
                });
            });
        }

        const pricingRoot = root.querySelector('[data-communications-pricing]');

        if (pricingRoot) {
            const tabs = [...pricingRoot.querySelectorAll('[data-pricing-tab]')];
            const panels = [...pricingRoot.querySelectorAll('[data-pricing-panel]')];

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const active = tab.dataset.pricingTab;

                    tabs.forEach((item) => {
                        const selected = item === tab;
                        item.classList.toggle('is-active', selected);
                        item.setAttribute('aria-selected', String(selected));
                    });
                    panels.forEach((panel) => {
                        const selected = panel.dataset.pricingPanel === active;
                        panel.classList.toggle('is-active', selected);
                        panel.hidden = !selected;
                    });
                });
            });
        }

        const dialog = root.querySelector('[data-gallery-dialog]');
        const dialogImage = dialog?.querySelector('[data-gallery-dialog-image]');
        const dialogTitle = dialog?.querySelector('[data-gallery-dialog-title]');

        root.querySelectorAll('[data-gallery-image]').forEach((trigger) => {
            trigger.addEventListener('click', () => {
                if (!dialog || !dialogImage) {
                    return;
                }

                dialogImage.src = trigger.dataset.galleryImage || '';
                dialogImage.alt = trigger.dataset.galleryTitle || '';
                if (dialogTitle) {
                    dialogTitle.textContent = trigger.dataset.galleryTitle || '';
                }

                if (typeof dialog.showModal === 'function') {
                    dialog.showModal();
                } else {
                    dialog.setAttribute('open', '');
                }
            });
        });

        dialog?.querySelector('[data-gallery-close]')?.addEventListener('click', () => dialog.close());
        dialog?.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    };

    const boot = () => {
        document.querySelectorAll('.landing-page--landing07-communications').forEach(init);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
