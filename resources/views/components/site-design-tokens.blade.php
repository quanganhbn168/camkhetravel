@php($palette = \App\Support\Design\BrandPalette::make($design->color_primary, $design->color_primary_hover))
<style id="site-design-tokens">
    :root {
        --bs-primary: {{ $palette['primary'] }};
        --bs-primary-rgb: {{ $palette['rgb'] }};
        --bs-link-color: {{ $palette['primary'] }};
        --bs-link-color-rgb: {{ $palette['rgb'] }};
        --bs-link-hover-color: {{ $palette['hover'] }};
        --bs-link-hover-color-rgb: {{ $palette['hover_rgb'] }};
        --bs-primary-bg-subtle: color-mix(in srgb, {{ $palette['primary'] }} 10%, white);
        --bs-primary-border-subtle: color-mix(in srgb, {{ $palette['primary'] }} 25%, white);
        --dv-primary-contrast: {{ $palette['contrast'] }};
        --dv-primary-hover-contrast: {{ $palette['hover_contrast'] }};
        --site-color-primary: {{ $palette['primary'] }};
        --site-color-primary-hover: {{ $palette['hover'] }};
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
