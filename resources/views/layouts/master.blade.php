<!doctype html>
<html lang="vi">
<head>
    {!! app(\App\Settings\WebsiteSettings::class)->head_top_scripts !!}
    @include('partials.head.seo')
    @vite(['resources/css/frontend.css', 'resources/js/app.js'])
    @stack('styles')
    {!! app(\App\Settings\WebsiteSettings::class)->head_scripts !!}
</head>
<body class="@yield('body_class', '')">
    {!! app(\App\Settings\WebsiteSettings::class)->body_scripts !!}
    <a class="skip-link" href="#main">Bỏ qua điều hướng</a>
    <x-site-header />

    <main id="main" class="main">
        @if (session('success'))
            <div class="container my-3"><div class="alert alert-success" role="status">{{ session('success') }}</div></div>
        @endif

        @yield('content')
    </main>

    <x-site-footer :hide-cta="$hideFooterCta ?? false" />
    @include('partials.floating-actions')
    @stack('scripts')
    {!! app(\App\Settings\WebsiteSettings::class)->body_end_scripts !!}
</body>
</html>
