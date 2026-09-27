function channels(hex) {
    if (!/^#[\da-f]{6}$/i.test(hex)) {
        throw new Error(`Brand colors must use a six-digit hex value in resources/css/brand.css; received ${hex}`);
    }
    return hex.slice(1).match(/../g).map(value => parseInt(value, 16));
}

function mix(color, target, amount) {
    const other = channels(target);
    return '#' + channels(color).map((value, index) => Math.round(value * (1 - amount) + other[index] * amount).toString(16).padStart(2, '0')).join('');
}

function luminance(color) {
    const [r, g, b] = channels(color).map(value => value / 255)
        .map(value => value <= 0.04045 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4);
    return 0.2126 * r + 0.7152 * g + 0.0722 * b;
}

function contrast(a, b) {
    const [high, low] = [luminance(a), luminance(b)].sort((x, y) => y - x);
    return (high + 0.05) / (low + 0.05);
}

function readableText(background, ink) {
    if (contrast(background, ink) >= 4.5) return ink;
    return contrast(background, '#ffffff') >= 4.5 ? '#ffffff' : '#000000';
}

function emphasisOnWhite(color) {
    let result = mix(color, '#000000', 0.42);
    while (contrast(result, '#ffffff') < 4.5) result = mix(result, '#000000', 0.08);
    return result;
}

export function createPalette(primary, accent) {
    channels(primary);
    channels(accent);
    const emphasis = emphasisOnWhite(primary);
    const hover = mix(primary, '#000000', 0.06);
    const ink = mix(primary, '#181b1c', 0.8);
    let onDark = primary;
    while (contrast(onDark, ink) < 4.5) onDark = mix(onDark, '#ffffff', 0.08);
    const text = mix(ink, '#ffffff', 0.11);
    const surface = mix(primary, '#ffffff', 0.97);
    const palette = {
        '--site-primary-dark': emphasis,
        '--site-primary-hover': hover,
        '--site-primary-on-dark': onDark,
        '--site-on-primary': readableText(primary, ink),
        '--site-on-primary-hover': readableText(hover, ink),
        '--site-on-accent': readableText(accent, ink),
        '--site-accent-dark': emphasisOnWhite(accent),
        '--site-accent-line': mix(accent, '#ffffff', 0.65),
        '--site-ink': ink,
        '--site-text': text,
        '--site-muted': mix(ink, '#ffffff', 0.28),
        '--site-mint': mix(primary, '#ffffff', 0.92),
        '--site-surface': surface,
        '--site-line': mix(primary, '#ffffff', 0.83),
        '--site-sand': mix(accent, '#ffffff', 0.88),
    };
    for (const [name, color] of Object.entries({
        primary, warning: accent, secondary: text, dark: ink, light: surface,
        'body-color': text, 'emphasis-color': ink, 'link-color': emphasis, 'link-hover-color': emphasis,
    })) palette[`--bs-${name}-rgb`] = channels(color).join(', ');
    return palette;
}

// Expand standard CSS declarations during Vite's existing PostCSS pass.
// Page styles and the admin theme never declare --site-primary, so remain untouched.
export default function frontendBrand() {
    return {
        postcssPlugin: 'frontend-brand',
        Once(root) {
            root.walkRules(rule => {
                const declarations = rule.nodes.filter(node => node.type === 'decl');
                const primary = declarations.find(node => node.prop === '--site-primary');
                if (!primary) return;
                const accent = declarations.find(node => node.prop === '--site-accent');
                if (!accent) throw primary.error('Define --site-accent beside --site-primary in brand.css.');
                for (const [prop, value] of Object.entries(createPalette(primary.value, accent.value))) {
                    rule.append(primary.clone({ prop, value }));
                }
            });
        },
    };
}
