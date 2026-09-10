<!doctype html>
<html lang="vi">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-site-design-tokens />
    @include('partials.tracking.head')
</head>
<body class="@yield('body_class', 'min-h-screen')">
    @include('partials.tracking.body')
    @yield('before_header')
    @unless ($hideHeader ?? false)
        <x-site-header />
    @endunless

    <main id="@yield('main_id', 'site-main')" class="@yield('main_class', 'overflow-x-clip')">
        @if (session('success'))
            <div class="fixed top-24 right-4 z-50 max-w-md rounded-2xl bg-emerald-700 px-5 py-4 text-sm font-medium text-white shadow-xl" role="status">{{ session('success') }}</div>
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
    @include('partials.tracking.footer')
</body>
</html>
