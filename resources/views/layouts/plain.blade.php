@php
    $planBodyClass = trim($__env->yieldContent('body_class', 'site-layouts-plain__element-3'));
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')
    @vite(['resources/scss/frontend.scss', 'resources/js/app.js'])
    @stack('styles')
    <x-site-design-tokens />
</head>
<body class="{{ $planBodyClass }}">
    @yield('before_content')
    <main id="@yield('main_id', 'plan-main')" class="@yield('main_class', 'site-main')">
        @if (session('success'))
            <div class="alert alert-success site-flash" role="status">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    @yield('after_content')
    @stack('scripts')
</body>
</html>
