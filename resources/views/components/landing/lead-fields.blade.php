@props([
    'landingPage' => null,
    'service' => null,
    'blockId' => 'lead-form',
    'returnAnchor' => 'lien-he',
    'successClass' => null,
])

@php
    $anchor = ltrim((string) $returnAnchor, '#');
    $returnTo = request()->getPathInfo().($anchor !== '' ? '#'.$anchor : '');
    $hasLandingContext = $landingPage || $service;
    $successMessage = session('success');
    $standardSuccessClass = \App\Support\Landing\LandingRegistry::successClass(
        $landingPage?->template_key,
        (string) request()->route('slug'),
    );
    $legacySuccessClasses = preg_split('/\s+/', trim((string) $successClass), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $successClassNames = array_merge([
        'landing-form-success',
        $standardSuccessClass,
    ], $legacySuccessClasses);
    $successClasses = implode(' ', array_values(array_unique(array_filter($successClassNames))));
@endphp

@csrf

@if ($hasLandingContext)
    <input type="hidden" name="from_landing_page" value="1">
    @if ($landingPage)
        <input type="hidden" name="landing_page_id" value="{{ $landingPage->id }}">
    @endif
    @if ($service)
        <input type="hidden" name="service_id" value="{{ $service->id }}">
    @endif
    <input type="hidden" name="landing_block_id" value="{{ $blockId }}">
    <input type="hidden" name="return_to" value="{{ $returnTo }}">
@endif

@foreach (['visitor_id', 'session_id', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid', 'fbclid', 'first_url', 'referrer'] as $field)
    <input type="hidden" name="{{ $field }}" data-attribution-field="{{ $field }}">
@endforeach

<p class="{{ $successClasses }}" data-landing-success role="status" aria-live="polite" @if (blank($successMessage)) hidden @endif>{{ $successMessage }}</p>
