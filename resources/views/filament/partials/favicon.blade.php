@if ($faviconUrl)
    <link rel="shortcut icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}" />
    <link rel="apple-touch-icon" sizes="{{ $faviconSizes }}" href="{{ $faviconUrl }}" />
@endif
