@props(['seo'])
@php($json = \App\Support\Seo\FrontendSeoBuilder::jsonLd($seo))
@if ($json)
<script type="application/ld+json">{!! $json !!}</script>
@endif
