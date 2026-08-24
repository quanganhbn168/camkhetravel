<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@yield('body_class', 'min-h-screen')">
    @yield('before_header')
    @include('partials.header')

    <main class="@yield('main_class', 'overflow-x-clip')">
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
