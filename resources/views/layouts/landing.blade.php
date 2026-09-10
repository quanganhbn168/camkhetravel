<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')

    @vite($landingAssets['vite'])
    <x-site-design-tokens />
    @include('partials.tracking.head')
</head>
<body
    class="@yield('body_class', 'min-h-screen overflow-x-clip')"
>
    @include('partials.tracking.body')
    @yield('before_content')

    @hasSection('landing_header')
        @yield('landing_header')
    @elseif (! ($hideHeader ?? false))
        <x-site-header />
    @endif

    <main id="@yield('main_id', 'landing-main')" class="@yield('main_class', 'overflow-x-clip')">
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
