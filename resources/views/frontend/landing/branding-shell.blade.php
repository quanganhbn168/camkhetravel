@extends('layouts.landing')

@php
    $page = $landingViewModel;
    $content = $page['content'];
    $contact = $page['contact'];
@endphp

@section('body_class', 'tht-landing tht-landing-branding min-h-screen overflow-x-clip')
@section('main_id', 'top')
@section('main_class', 'branding-main')

@section('head')
    <x-landing-theme-tokens selector=".tht-landing-branding" :theme="$landingTheme" />
    <link rel="preload" as="image" href="{{ $content['hero']['image'] }}" fetchpriority="high">
@endsection

@section('landing_header')
    @include('frontend.landing.parts.branding.header')
@endsection

@section('content')
    <div class="tht-landing-page branding-page" data-landing-page data-landing-id="{{ $landingPage->id }}" data-track-endpoint="{{ $landingTrackingUrl }}" data-campaign-state="{{ $landingCampaignState }}">
        <x-landing.campaign-notice :state="$landingCampaignState" :message="$landingPage->expired_message" />
        @include('frontend.landing.pages.branding')
    </div>
@endsection

@section('landing_footer')
    @include('frontend.landing.parts.branding.footer')
    @include('partials.floating-actions')
@endsection
