@extends('layouts.landing')

@php
    $page = (array) ($landingViewModel ?? []);
    $content = (array) ($page['content'] ?? []);
    $contact = (array) ($page['contact'] ?? ($content['contact'] ?? []));
    $pageClass = \App\Support\Landing\LandingView::className((string) ($content['page_class'] ?? 'tht-landing-theme-enterprise'));
    $templateKey = (string) ($page['template_key'] ?? ($landingPage->template_key ?? ''));
    $pageView = (string) ($page['view'] ?? '');
    $zaloUrl = \App\Support\Landing\LandingView::zaloUrl($contact);
    if (isset($processItems) && is_array($processItems) && $processItems !== []) {
        $content['managed_process_items'] = $processItems;
    }
    if (isset($pricingMatrix) && is_array($pricingMatrix) && ! empty($pricingMatrix['packages'])) {
        $content['managed_pricing_plans'] = $pricingMatrix['packages'];
    }
@endphp

@section('body_class', 'tht-landing tht-landing-media '.$pageClass.' min-h-screen overflow-x-clip')
@section('main_id', 'tht-landing-main')
@section('main_class', 'tht-landing-main')

@section('head')
    <x-landing-theme-tokens selector=".tht-landing, .{{ $pageClass }}" :theme="$landingTheme ?? ['primary' => '#5cb811', 'accent' => '#f97316', 'surface' => '#f8fafc', 'ink' => '#0f172a']" />
@endsection

@section('landing_header')
    <x-landing.header
        :brand="$page['brand'] ?? 'THT MEDIA'"
        :logo="$page['logo_url'] ?? null"
        :navigation="$page['navigation'] ?? []"
        cta-label="Nhắn Zalo tư vấn"
        :zalo-url="$zaloUrl"
    />
@endsection

@section('content')
    <div class="tht-landing-page {{ $pageClass }}" data-landing-page @if ($landingPage ?? null) data-landing-id="{{ $landingPage->id }}" data-track-endpoint="{{ $landingTrackingUrl ?? '' }}" data-campaign-state="{{ $landingCampaignState ?? 'active' }}" @endif>
        @if ($landingPage ?? null)
            <x-landing.campaign-notice :state="$landingCampaignState" :message="$landingPage->expired_message" />
        @endif

        @if ($pageView !== '' && view()->exists($pageView))
            @include($pageView, ['landing' => $page, 'landingContent' => $content])
        @else
            <x-landing.container class="py-24 text-center"><p>Landing page chưa có template.</p></x-landing.container>
        @endif

        @unless (in_array($templateKey, ['landing_ads', 'landing_academy', 'landing_academy_v2', 'landing_tiktok'], true))
            <x-landing.contact-modal :contact="$contact" :section="$content['contact_section'] ?? []" :zalo-url="$zaloUrl" />
        @endunless
    </div>
@endsection

@section('landing_footer')
    <x-landing.footer
        :brand="$page['brand'] ?? 'THT MEDIA'"
        :logo="$page['logo_url'] ?? null"
        :text="$content['footer_text'] ?? 'Đồng hành cùng doanh nghiệp từ chiến lược đến triển khai truyền thông.'"
        :phone="$contact['hotline_1'] ?? null"
        :contact="$contact"
        :services="$page['services'] ?? []"
        :zalo-url="$zaloUrl"
        :facebook-url="$contact['facebook_url'] ?? null"
    />
    <x-landing.contact-actions
        :phone="$contact['hotline_1'] ?? null"
        :phone-label="$contact['hotline_display'] ?? null"
        :zalo-url="$zaloUrl"
        :zalo-label="$pageClass === 'tht-landing-theme-wedding' ? 'Liên lạc qua Zalo' : 'Nhắn Zalo'"
    />
@endsection
