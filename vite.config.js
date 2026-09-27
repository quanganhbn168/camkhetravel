import fs from 'node:fs';
import path from 'node:path';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import frontendBrand from './build/frontend-brand.mjs';

export default defineConfig({
    css: { postcss: { plugins: [frontendBrand()] } },
    plugins: [
        laravel({
            input: [
                'resources/css/frontend.css',
                'resources/js/app.js',
                ...fs.readdirSync('resources/css/pages').filter((file) => file.endsWith('.css')).map((file) => path.posix.join('resources/css/pages', file)),
                'resources/css/filament/admin/theme.css',
                'resources/js/filament/curator-rich-editor-integration.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
