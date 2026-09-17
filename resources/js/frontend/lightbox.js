import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.css';
let lightbox;
export function refreshLightboxes() {
    if (!document.querySelector('.glightbox')) return;
    if (lightbox) { lightbox.reload(); return; }
    lightbox = GLightbox({ selector: '.glightbox', touchNavigation: true, keyboardNavigation: true, closeOnOutsideClick: true, autoplayVideos: true, loop: true });
}
