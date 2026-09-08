<style id="landing-theme-tokens">
    {{ $selector }} {
        --landing-primary: {{ $theme['primary'] ?? '#12372a' }};
        --landing-accent: {{ $theme['accent'] ?? '#d8a84e' }};
        --landing-surface: {{ $theme['surface'] ?? '#f4f7ef' }};
        --landing-ink: {{ $theme['ink'] ?? '#14251d' }};
    }
</style>
