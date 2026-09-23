<!doctype html>
<html lang="vi">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')
    @vite(['resources/css/frontend.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="@yield('body_class', '')">
    <a class="skip-link" href="#@yield('main_id', 'main')">Bỏ qua điều hướng</a>
    @yield('before_header')
    @unless ($hideHeader ?? false)
        <x-site-header />
    @endunless

    <main id="@yield('main_id', 'main')" class="@yield('main_class', 'main')">
        @if (session('success'))
            <div class="container my-3"><div class="alert alert-success" role="status">{{ session('success') }}</div></div>
        @endif

        @yield('content')
    </main>

    @yield('before_footer')
    @unless ($hideFooter ?? false)
        <x-site-footer />
        @include('partials.floating-actions')
    @endunless
    @yield('after_footer')
    @stack('scripts')
</body>
</html>
