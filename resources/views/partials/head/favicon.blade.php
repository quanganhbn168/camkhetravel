@php($isBniPwa = request()->routeIs('bni.*'))

@if ($isBniPwa)
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('bni-icon-192x192.png') }}">
    <link rel="apple-touch-icon" type="image/png" sizes="192x192" href="{{ asset('bni-icon-192x192.png') }}">
    <link rel="manifest" type="application/manifest+json" href="{{ route('bni.manifest') }}">
    <meta name="theme-color" content="#cf2031">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="BNI">
@else
    @foreach ($faviconLinks as $faviconLink)
        <link rel="{{ $faviconLink['rel'] }}" type="{{ $faviconLink['type'] }}" @isset($faviconLink['sizes']) sizes="{{ $faviconLink['sizes'] }}" @endisset @isset($faviconLink['color']) color="{{ $faviconLink['color'] }}" @endisset href="{{ $faviconLink['href'] }}">
    @endforeach
@endif
