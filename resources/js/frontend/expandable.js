/**
 * Khối "Xem thêm": nội dung cao hơn data-expandable-max (px) thì thu gọn, mờ dần ở đáy và hiện nút.
 * Ngắn hơn thì để nguyên, không hiện nút. Không có JS thì nội dung hiện đầy đủ.
 *
 * <div data-expandable data-expandable-max="240">
 *   <div class="expandable__body">...</div>
 *   <button data-expandable-toggle hidden><span data-expandable-label>Xem thêm</span></button>
 * </div>
 */
const setup = (root) => {
    const body = root.querySelector('.expandable__body');
    const toggle = root.querySelector('[data-expandable-toggle]');
    const label = root.querySelector('[data-expandable-label]');
    if (!body || !toggle) return;

    const max = Number.parseInt(root.dataset.expandableMax || '240', 10);

    const sync = () => {
        const tooLong = body.scrollHeight > max + 40;
        root.classList.toggle('is-collapsible', tooLong);
        toggle.hidden = !tooLong;
        if (!tooLong) root.classList.remove('is-expanded');
        body.style.maxHeight = tooLong && !root.classList.contains('is-expanded') ? `${max}px` : '';
    };

    toggle.addEventListener('click', () => {
        const expanded = !root.classList.contains('is-expanded');
        root.classList.toggle('is-expanded', expanded);
        toggle.setAttribute('aria-expanded', String(expanded));
        if (label) label.textContent = expanded ? 'Thu gọn' : 'Xem thêm';
        body.style.maxHeight = expanded ? `${body.scrollHeight}px` : `${max}px`;
        if (!expanded) root.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    });

    // Bấm một mục trong mục lục đang thu gọn thì mở ra trước khi nhảy tới.
    body.addEventListener('focusin', () => {
        if (root.classList.contains('is-collapsible') && !root.classList.contains('is-expanded')) toggle.click();
    });

    sync();
    window.addEventListener('resize', sync, { passive: true });
};

export function initialiseExpandables() {
    document.querySelectorAll('[data-expandable]').forEach(setup);
}
