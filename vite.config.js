import fs from 'node:fs';
import path from 'node:path';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/frontend.scss',
                'resources/js/app.js',
                ...fs.readdirSync('resources/scss/pages').filter((file) => file.endsWith('.scss') && !file.startsWith('_')).map((file) => path.posix.join('resources/scss/pages', file)),
                'resources/css/filament/admin/theme.css',
                'resources/js/filament/curator-rich-editor-integration.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
