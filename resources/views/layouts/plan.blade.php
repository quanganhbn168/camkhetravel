@php
    $planBodyClass = trim($__env->yieldContent('body_class', 'min-h-screen'));
    $isBniExperience = request()->routeIs('bni.*');
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ $planBodyClass }}{{ $isBniExperience ? ' bni-app-shell' : '' }}" data-bni-page="{{ $isBniExperience ? 'true' : 'false' }}">
    @yield('before_content')
    <main id="@yield('main_id', 'plan-main')" class="@yield('main_class', 'overflow-x-clip')">
        @if (session('success'))
            <div class="bni-plan-flash" data-bni-flash role="status">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    @yield('after_content')
    @include('partials.bni-pwa')
    @stack('scripts')
</body>
</html>
