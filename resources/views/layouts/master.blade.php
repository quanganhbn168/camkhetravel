@use(App\Settings\DesignSettings)
@php($design = app(DesignSettings::class))
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style id="site-design-tokens">
        :root {
            --site-color-primary: {{ $design->color_primary }};
            --site-color-primary-hover: {{ $design->color_primary_hover }};
            --site-color-ink: {{ $design->color_ink }};
            --site-color-midnight: {{ $design->color_midnight }};
            --site-color-surface: {{ $design->color_surface }};
            --site-color-muted: {{ $design->color_muted }};
            --site-color-green-light: {{ $design->color_green_light }};
            --site-color-green-dark: {{ $design->color_green_dark }};
            --site-gradient-green-dark: linear-gradient(135deg, {{ $design->gradient_green_dark_start }}, {{ $design->gradient_green_dark_end }});
            --site-gradient-green-light: linear-gradient(135deg, {{ $design->gradient_green_light_start }}, {{ $design->gradient_green_light_end }});
            --site-font-size-base: {{ $design->font_size_base }};
            --site-font-size-body: {{ $design->font_size_body }};
            --site-font-size-small: {{ $design->font_size_small }};
            --site-font-size-h1: {{ $design->font_size_h1 }};
            --site-font-size-h2: {{ $design->font_size_h2 }};
            --site-font-size-h3: {{ $design->font_size_h3 }};
            --site-font-size-stat: {{ $design->font_size_stat }};
        }
    </style>
</head>
<body class="@yield('body_class', 'min-h-screen')">
    @yield('before_header')
    @include('partials.header')

    <main id="@yield('main_id', 'site-main')" class="@yield('main_class', 'overflow-x-clip')">
        @if (session('success'))
            <div class="fixed top-24 right-4 z-50 max-w-md rounded-2xl bg-emerald-700 px-5 py-4 text-sm font-medium text-white shadow-xl" role="status">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

    @yield('before_footer')
    @include('partials.footer')
    @include('partials.floating-actions')
    @yield('after_footer')
    @stack('scripts')
</body>
</html>
