/**
 * Hiệu ứng hiện dần khi cuộn tới (thay cho AOS).
 *
 * - `data-reveal` (mặc định trượt lên), `data-reveal="fade"`, `data-reveal="zoom"`, `data-reveal="left"`, `data-reveal="right"`.
 * - `data-reveal-stagger` trên phần tử cha: các phần tử con hiện lần lượt, cách nhau ~90ms.
 *
 * Nội dung chỉ bị ẩn khi JS đã chạy (class `has-reveal` trên <html>), nên JS lỗi thì trang vẫn hiển thị đủ.
 * Người dùng bật "giảm chuyển động" thì bỏ qua hoàn toàn.
 */
export function initialiseReveal() {
    const groups = document.querySelectorAll('[data-reveal-stagger]');
    groups.forEach((group) => {
        Array.from(group.children).forEach((child, index) => {
            if (!child.hasAttribute('data-reveal')) child.setAttribute('data-reveal', group.dataset.revealStagger || '');
            child.style.setProperty('--reveal-delay', `${Math.min(index, 6) * 90}ms`);
        });
    });

    const items = document.querySelectorAll('[data-reveal]');
    if (!items.length) return;
    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-revealed');
            observer.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });

    document.documentElement.classList.add('has-reveal');
    items.forEach((item) => observer.observe(item));
}
