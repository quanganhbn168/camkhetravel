// JS riêng của trang chi tiết bài viết: mục lục thu gọn "Xem thêm".
// Gọi bằng @vite('resources/js/pages/post.js') trong frontend/posts/show.blade.php; trang khác không tải.
import { initialiseExpandables } from '../frontend/expandable';

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialiseExpandables, { once: true });
} else {
    initialiseExpandables();
}
