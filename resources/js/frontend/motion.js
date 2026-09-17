import AOS from 'aos';
import 'aos/dist/aos.css';
export function initialiseMotion() {
    AOS.init({ once: true, duration: 650, easing: 'ease-out-cubic', offset: 80, disable: matchMedia('(prefers-reduced-motion: reduce)').matches });
}
