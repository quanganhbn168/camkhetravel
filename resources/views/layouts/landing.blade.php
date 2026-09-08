<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('partials.head.seo')
    @yield('head')
    @stack('head')

    @if ($landingPage ?? null)
        <meta name="landing-page-id" content="{{ $landingPage->getKey() }}">
        <meta name="landing-template" content="{{ $landingPage->template_key }}">
    @endif

    @if (filled($landingTracking['head'] ?? null)){!! $landingTracking['head'] !!}@endif
    @vite($landingAssets['vite'])
    <x-site-design-tokens />
</head>
<body
    class="@yield('body_class', 'min-h-screen overflow-x-clip')"
    @if ($landingPage ?? null)
        data-landing-page-id="{{ $landingPage->getKey() }}"
        data-landing-template="{{ $landingPage->template_key }}"
        data-landing-tracking="{{ $landingPage->tracking_enabled ? 'true' : 'false' }}"
        data-landing-track-endpoint="{{ $landingTrackingUrl ?? '' }}"
        data-landing-campaign-state="{{ $landingCampaignState ?? 'active' }}"
    @endif
>
    @if (filled($landingTracking['body'] ?? null)){!! $landingTracking['body'] !!}@endif
    @yield('before_content')

    @hasSection('landing_header')
        @yield('landing_header')
    @elseif (! ($hideHeader ?? false))
        <x-site-header />
    @endif

    <main id="@yield('main_id', 'landing-main')" class="@yield('main_class', 'overflow-x-clip')">
        @if (session('success'))
            <div class="fixed right-4 top-24 z-50 max-w-md rounded-2xl bg-emerald-700 px-5 py-4 text-sm font-medium text-white shadow-xl" role="status">
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>

    @yield('after_content')

    @hasSection('landing_footer')
        @yield('landing_footer')
    @elseif (! ($hideFooter ?? false))
        <x-site-footer />
    @endif

    @include('partials.bni-pwa')
    @if (filled($landingTracking['footer'] ?? null)){!! $landingTracking['footer'] !!}@endif
    @stack('scripts')
</body>
</html>
