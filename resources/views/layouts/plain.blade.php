<!doctype html>
<html lang="vi">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')
    @vite(['resources/scss/frontend.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="@yield('body_class', '')">
    @yield('before_content')
    <main id="@yield('main_id', 'plan-main')" class="@yield('main_class', 'main')">
        @if (session('success'))
            <div class="container my-3"><div class="alert alert-success" role="status">{{ session('success') }}</div></div>
        @endif
        @yield('content')
    </main>
    @yield('after_content')
    @stack('scripts')
</body>
</html>
