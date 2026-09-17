import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { gzipSync } from 'node:zlib';
const build = 'public/build';
const manifest = JSON.parse(fs.readFileSync(path.join(build, 'manifest.json'), 'utf8'));
const frontend = 'resources/scss/frontend.scss';
const admin = 'resources/css/filament/admin/theme.css';
assert.ok(manifest[frontend], 'Frontend SCSS entry is missing.');
assert.ok(manifest[admin], 'Filament theme entry is missing.');
assert.ok(!manifest['resources/css/app.css'], 'Old frontend Tailwind entry remains.');
function filesFor(key, seen = new Set()) {
    if (seen.has(key)) return [];
    seen.add(key);
    const entry = manifest[key];
    assert.ok(entry, `Missing manifest import: ${key}`);
    return [entry.file, ...(entry.css || []), ...(entry.imports || []).flatMap((dependency) => filesFor(dependency, seen))];
}
const initial = [...new Set([...filesFor(frontend), ...filesFor('resources/js/app.js')])];
assert.ok(!initial.includes(manifest[admin].file), 'Filament CSS leaked into the website entry graph.');
const frontendCss = fs.readFileSync(path.join(build, manifest[frontend].file), 'utf8');
assert.ok(!frontendCss.includes('--tw-'), 'A Tailwind utility bundle is still included in frontend CSS.');
assert.ok(!frontendCss.includes('.fi-fo-'), 'Filament form CSS leaked into frontend.');
assert.ok(frontendCss.includes('--bs-primary'), 'Bootstrap variables missing.');
const report = {
    initial_frontend: initial.map((file) => {
        const data = fs.readFileSync(path.join(build, file));
        return { file, bytes: data.length, gzip_bytes: gzipSync(data).length };
    }),
    admin_css: manifest[admin].file,
    page_css_entries: Object.keys(manifest).filter((key) => key.startsWith('resources/css/frontend/pages/')),
    fonts_emitted: fs.readdirSync(path.join(build, 'assets')).filter((file) => /\.woff2?$/.test(file)),
};
assert.ok(report.fonts_emitted.every((file) => file.endsWith('.woff2')), 'Unexpected legacy WOFF font output.');
fs.mkdirSync('test-results', { recursive: true });
fs.writeFileSync('test-results/assets.json', JSON.stringify(report, null, 2));
console.log('PASS frontend/admin asset isolation');
console.table(report.initial_frontend);
console.log(`Font assets: ${report.fonts_emitted.length}; separate page styles: ${report.page_css_entries.length}`);
