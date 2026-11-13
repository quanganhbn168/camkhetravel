<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', $seo['title'])</title>
<meta name="description" content="@yield('meta_description', $seo['description'])">
@hasSection('meta_keywords')
    <meta name="keywords" content="@yield('meta_keywords')">
@elseif ($seo['keywords'])
    <meta name="keywords" content="{{ $seo['keywords'] }}">
@endif
<meta name="author" content="@yield('author', $website->company_name ?: $website->site_name)">
<meta name="robots" content="{{ $seo['robots'] }}">
<link rel="canonical" href="@yield('canonical', $seo['canonical'])">
@include('partials.head.favicon')
<meta property="og:type" content="@yield('og_type', $seo['type'])">
<meta property="og:site_name" content="{{ $website->site_name }}">
<meta property="og:title" content="@yield('og_title', $seo['title'])">
<meta property="og:description" content="@yield('og_description', $seo['description'])">
<meta property="og:url" content="@yield('og_url', $seo['canonical'])">
<meta property="og:locale" content="{{ $seo['locale'] }}">
@if ($seo['image'])
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta property="og:image:alt" content="{{ $seo['image_alt'] }}">
@endif
<meta name="twitter:card" content="{{ $seo['image'] ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="@yield('twitter_title', $seo['title'])">
<meta name="twitter:description" content="@yield('twitter_description', $seo['description'])">
@if ($seo['image'])<meta name="twitter:image" content="{{ $seo['image'] }}">@endif
@if (($seo['article'] ?? []) !== [])
    @isset($seo['article']['published_time'])<meta property="article:published_time" content="{{ $seo['article']['published_time'] }}">@endisset
    @isset($seo['article']['modified_time'])<meta property="article:modified_time" content="{{ $seo['article']['modified_time'] }}">@endisset
    @isset($seo['article']['section'])<meta property="article:section" content="{{ $seo['article']['section'] }}">@endisset
@endif
{{-- JSON-LD do từng trang tự đẩy vào qua @push('schema') bằng <x-json-ld>. --}}
@yield('schema')
@stack('schema')
