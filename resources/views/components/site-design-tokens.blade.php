@php($palette = \App\Support\Design\BrandPalette::make($design->color_primary, $design->color_primary_hover))
<style id="site-design-tokens">
    :root {
        --primary-color: {{ $palette['primary'] }};
        --primary-color-rgb: {{ $palette['rgb'] }};
        --primary-hover-color: {{ $palette['hover'] }};
        --primary-hover-color-rgb: {{ $palette['hover_rgb'] }};
        --bs-primary: var(--primary-color);
        --bs-primary-rgb: var(--primary-color-rgb);
        --bs-link-color: var(--primary-color);
        --bs-link-color-rgb: var(--primary-color-rgb);
        --bs-link-hover-color: var(--primary-hover-color);
        --bs-link-hover-color-rgb: var(--primary-hover-color-rgb);
        --bs-primary-bg-subtle: color-mix(in srgb, var(--primary-color) 10%, white);
        --bs-primary-border-subtle: color-mix(in srgb, var(--primary-color) 25%, white);
        --color-primary-contrast: {{ $palette['contrast'] }};
        --color-primary-hover-contrast: {{ $palette['hover_contrast'] }};
        --site-color-primary: var(--primary-color);
        --site-color-primary-hover: var(--primary-hover-color);
        --site-color-ink: {{ $design->color_ink }};
        --site-color-surface: {{ $design->color_surface }};
        --site-color-muted: {{ $design->color_muted }};
        --site-font-size-base: {{ $design->font_size_base }};
        --site-font-size-body: {{ $design->font_size_body }};
        --site-font-size-small: {{ $design->font_size_small }};
        --site-font-size-navigation: 0.9375rem;
        --site-font-size-navigation-submenu: 0.875rem;
        --site-home-section-space: clamp(2rem, 4vw, 3.5rem);
        --site-font-size-footer-heading: var(--site-font-size-small);
        --site-font-size-lead: clamp(1rem, 1.35vw, 1.125rem);
        --site-font-size-h1: {{ $design->font_size_h1 }};
        --site-font-size-h2: {{ $design->font_size_h2 }};
        --site-font-size-h3: {{ $design->font_size_h3 }};
        --site-font-size-price: clamp(1.5rem, 2.2vw, 2rem);
        --site-font-size-stat: {{ $design->font_size_stat }};
        --site-font-body: 'Be Vietnam Pro', ui-sans-serif, system-ui, sans-serif;
        --site-font-display: 'Roboto Condensed Variable', 'Be Vietnam Pro', ui-sans-serif, system-ui, sans-serif;
    }
</style>
