import assert from 'node:assert/strict';
import test from 'node:test';
import { createPalette } from '../../build/frontend-brand.mjs';

function contrast(a, b) {
    const luminance = (hex) => {
        const channels = hex.match(/[a-f\d]{2}/gi).map(value => parseInt(value, 16) / 255)
            .map(value => value <= 0.04045 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4);
        return channels[0] * 0.2126 + channels[1] * 0.7152 + channels[2] * 0.0722;
    };
    const values = [luminance(a), luminance(b)].sort((x, y) => y - x);
    return (values[0] + 0.05) / (values[1] + 0.05);
}

test('one primary input updates shades, Bootstrap channels and link colors', () => {
    const teal = createPalette('#28bdbf', '#ffca00');
    const red = createPalette('#c62038', '#ffca00');
    assert.equal(teal['--bs-primary-rgb'], '40, 189, 191');
    assert.equal(red['--bs-primary-rgb'], '198, 32, 56');
    for (const token of ['--site-primary-hover', '--site-primary-dark', '--site-ink', '--site-mint', '--site-line', '--bs-link-color-rgb', '--bs-dark-rgb']) {
        assert.notEqual(teal[token], red[token], token);
    }
    assert.equal(teal['--bs-warning-rgb'], red['--bs-warning-rgb']);
    assert.equal(createPalette('#28bdbf', '#aabbcc')['--bs-warning-rgb'], '170, 187, 204');
});

test('button text and emphasis remain readable across light and dark brands', () => {
    for (const primary of ['#28bdbf', '#c62038', '#123456', '#ffffff', '#000000', '#ffff00', '#ff0000', '#777777']) {
        const palette = createPalette(primary, '#ffca00');
        assert.ok(contrast(primary, palette['--site-on-primary']) >= 4.5, primary);
        assert.ok(contrast(palette['--site-primary-hover'], palette['--site-on-primary-hover']) >= 4.5, `${primary} hover`);
        assert.ok(contrast(palette['--site-primary-dark'], '#ffffff') >= 4.5, `${primary} emphasis`);
    }
});

test('invalid brand colors stop the build with an actionable error', () => {
    assert.throws(() => createPalette('var(--unknown)', '#ffca00'), /six-digit hex/i);
    assert.throws(() => createPalette('#28bdbf', 'yellow'), /six-digit hex/i);
});

test('dark surfaces and a replacement accent receive readable brand colors', () => {
    for (const primary of ['#28bdbf', '#123456', '#c62038', '#ffffff', '#000000']) {
        const palette = createPalette(primary, '#6222aa');
        assert.match(palette['--site-primary-on-dark'] ?? '', /^#[a-f\d]{6}$/);
        assert.ok(contrast(palette['--site-primary-on-dark'], palette['--site-ink']) >= 4.5);
        assert.ok(contrast(palette['--site-accent-dark'], '#ffffff') >= 4.5);
        assert.ok(contrast('#6222aa', palette['--site-on-accent']) >= 4.5);
    }
    assert.notEqual(createPalette('#28bdbf', '#6222aa')['--site-accent-dark'], createPalette('#28bdbf', '#ffca00')['--site-accent-dark']);
});
