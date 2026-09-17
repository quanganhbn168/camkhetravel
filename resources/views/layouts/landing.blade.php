<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')

    @vite($landingAssets['vite'])
    @stack('styles')
    <x-site-design-tokens />
    @include('partials.tracking.head')
</head>
<body
    class="@yield('body_class', 'dv-layouts-landing__element-1')"
>
    @include('partials.tracking.body')
    @yield('before_content')

    @hasSection('landing_header')
        @yield('landing_header')
    @elseif (! ($hideHeader ?? false))
        <x-site-header />
    @endif

    <main id="@yield('main_id', 'landing-main')" class="@yield('main_class', 'site-main')">
        @yield('content')
    </main>

    @yield('after_content')

    @hasSection('landing_footer')
        @yield('landing_footer')
    @elseif (! ($hideFooter ?? false))
        <x-site-footer />
    @endif

    @stack('scripts')
    @include('partials.tracking.footer')
</body>
</html>
