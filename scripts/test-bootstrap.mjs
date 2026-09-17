import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';
import { fileURLToPath } from 'node:url';
import * as sass from 'sass';
const require = createRequire(import.meta.url);
const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const bootstrapRoot = path.dirname(require.resolve('bootstrap/package.json'));
const modernPath = path.join(root, '.generated/bootstrap/_bootstrap.scss');
assert.ok(fs.existsSync(modernPath), 'Run prepare-bootstrap first.');
const cases = {
    default: {},
    dvtec: { primary: '#d71920', secondary: '#10233e', dark: '#071620', 'border-radius': '.5rem', 'headings-font-weight': '700' },
    custom: { primary: '#116a64', spacer: '1.25rem', 'border-radius': '1rem', 'enable-rounded': 'false' },
};
const normalize = (css) => css.replace(/\/\*[\s\S]*?\*\//g, '').trim();
const reports = [];
for (const [name, variables] of Object.entries(cases)) {
    const legacyWarnings = [];
    const declarations = Object.entries(variables).map(([key, value]) => `$${key}: ${value};`).join('\n');
    // The old source is compiled ONLY as a comparison oracle. Its warnings are
    // recorded, not hidden in the application build (which never imports it).
    const before = sass.compileString(declarations + '\n@import "bootstrap/scss/bootstrap";', {
        loadPaths: [path.dirname(bootstrapRoot)], style: 'compressed', sourceMap: false,
        logger: { warn(message) { legacyWarnings.push(message); } },
    });
    const config = Object.entries(variables).map(([key, value]) => `$${key}: ${value}`).join(', ');
    const after = sass.compileString(`@use '.generated/bootstrap/bootstrap'${config ? ` with (${config})` : ''};`, {
        loadPaths: [root], style: 'compressed', sourceMap: false,
        fatalDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
        logger: { warn(message) { throw new Error(`Unexpected warning in migrated source: ${message}`); } },
    });
    assert.equal(normalize(after.css), normalize(before.css), `${name}: generated CSS differs from Bootstrap 5.3.8`);
    reports.push({ case: name, css_bytes: Buffer.byteLength(after.css), legacy_warning_count: legacyWarnings.length, migrated_warning_count: 0, equal: true });
    console.log(`PASS ${name}: identical CSS, migrated Sass warnings = 0`);
}
fs.mkdirSync(path.join(root, 'test-results'), { recursive: true });
fs.writeFileSync(path.join(root, 'test-results/bootstrap-equivalence.json'), JSON.stringify(reports, null, 2));
