import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
function walk(folder) {
    return fs.readdirSync(folder, { withFileTypes: true }).flatMap((entry) => entry.isDirectory() ? walk(path.join(folder, entry.name)) : [path.join(folder, entry.name)]);
}
const sources = [...walk('resources/views'), ...walk('resources/scss'), ...walk('resources/css/frontend')].filter((file) => !file.replaceAll('\\', '/').includes('/filament/'));
for (const file of sources) {
    const source = fs.readFileSync(file, 'utf8');
    assert.ok(!/resources\/css\/app\.css/.test(source), `${file}: obsolete asset entry`);
    assert.ok(!/@(?:apply|tailwind|theme|source)\b/.test(source), `${file}: frontend utility compiler directive`);
    assert.ok(!/(?:class=["'][^"']*\b|\s)(?:sm|md|lg|xl|2xl):[\w\[]/.test(source), `${file}: unconverted utility class`);
    assert.ok(!/--tw-/.test(source), `${file}: old utility state variable`);
}
const vite = fs.readFileSync('vite.config.js', 'utf8');
assert.ok(!/quietDeps|silenceDeprecations|logLevel\s*:\s*['"]silent/.test(vite), 'Build warnings must not be suppressed.');
assert.ok(vite.includes('resources/scss/frontend.scss'), 'Frontend entry is not configured.');
assert.ok(!fs.existsSync('resources/css/app.css'), 'Unused frontend stylesheet not removed.');
console.log(`PASS frontend source audit (${sources.length} files)`);
