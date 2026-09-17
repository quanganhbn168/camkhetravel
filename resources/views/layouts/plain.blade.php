@php
    $planBodyClass = trim($__env->yieldContent('body_class', 'dv-layouts-plain__element-3'));
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
    @include('partials.tracking.head')
</head>
<body class="{{ $planBodyClass }}">
    @include('partials.tracking.body')
    @yield('before_content')
    <main id="@yield('main_id', 'plan-main')" class="@yield('main_class', 'site-main')">
        @if (session('success'))
            <div class="alert alert-success site-flash" role="status">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    @yield('after_content')
    @stack('scripts')
    @include('partials.tracking.footer')
</body>
</html>
