import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/filament/admin/theme.css',
                'resources/css/landing/pages/ads.css',
                'resources/css/landing/pages/wedding.css',
                'resources/css/landing/pages/corporate-film.css',
                'resources/css/landing/pages/communications.css',
                'resources/css/landing/pages/academy.css',
                'resources/css/landing/pages/academy-v2.css',
                'resources/css/landing/pages/branding.css',
                'resources/css/landing/pages/tiktok.css',
                'resources/css/landing/pages/outsourced-marketing.css',
                'resources/css/landing/pages/event-media.css',
                'resources/css/landing/pages/profile.css',
                'resources/css/landing/pages/event-organization.css',
                'resources/js/app.js',
                'resources/js/filament/curator-rich-editor-integration.js',
                'resources/js/landing/pages/ads.js',
                'resources/js/landing/pages/wedding.js',
                'resources/js/landing/pages/corporate-film.js',
                'resources/js/landing/pages/communications.js',
                'resources/js/landing/pages/academy.js',
                'resources/js/landing/pages/branding.js',
                'resources/js/landing/pages/tiktok.js',
                'resources/js/landing/pages/outsourced-marketing.js',
                'resources/js/landing/pages/event-media.js',
                'resources/js/landing/pages/profile.js',
                'resources/js/landing/pages/event-organization.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
