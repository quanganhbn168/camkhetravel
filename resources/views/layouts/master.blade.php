<!doctype html>
<html lang="vi">
<head>
    @include('partials.head.seo')
    @vite(['resources/css/frontend.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="@yield('body_class', '')">
    <a class="skip-link" href="#main">Bỏ qua điều hướng</a>
    <x-site-header />

    <main id="main" class="main">
        @if (session('success'))
            <div class="container my-3"><div class="alert alert-success" role="status">{{ session('success') }}</div></div>
        @endif

        @yield('content')
    </main>

    <x-site-footer />
    @include('partials.floating-actions')
    @stack('scripts')
</body>
</html>
