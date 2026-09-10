@php
    $planBodyClass = trim($__env->yieldContent('body_class', 'min-h-screen'));
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.tracking.head')
</head>
<body class="{{ $planBodyClass }}">
    @include('partials.tracking.body')
    @yield('before_content')
    <main id="@yield('main_id', 'plan-main')" class="@yield('main_class', 'overflow-x-clip')">
        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    @yield('after_content')
    @stack('scripts')
    @include('partials.tracking.footer')
</body>
</html>
