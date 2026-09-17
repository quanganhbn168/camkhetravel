<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')
    @vite(['resources/scss/frontend.scss', 'resources/js/app.js'])
    @stack('styles')
    <x-site-design-tokens />
    @include('partials.tracking.head')
</head>
<body class="@yield('body_class', 'dv-layouts-master__element-2')">
    @include('partials.tracking.body')
    @yield('before_header')
    @unless ($hideHeader ?? false)
        <x-site-header />
    @endunless

    <main id="@yield('main_id', 'site-main')" class="@yield('main_class', 'site-main')">
        @if (session('success'))
            <div class="alert alert-success site-flash" role="status">{{ session('success') }}</div>
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
