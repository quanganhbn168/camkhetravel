// JS riêng của trang chi tiết dịch vụ: hiệu ứng cuộn + slider (Swiper).
// Được gọi bằng @vite('resources/js/pages/service.js') trong frontend/services/show.blade.php; trang khác không tải.
import { initialiseReveal } from '../frontend/reveal';
import { initialiseSliders } from '../frontend/sliders';
import { initialiseExpandables } from '../frontend/expandable';

const start = () => {
    initialiseReveal();
    initialiseSliders();
    initialiseExpandables();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start, { once: true });
} else {
    start();
}
