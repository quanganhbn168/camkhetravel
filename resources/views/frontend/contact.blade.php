@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/contact.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)

@php
    $contactPhones = collect($website->phones ?? [])
        ->filter(fn ($phone) => is_array($phone) && filled($phone['number'] ?? null))
        ->values();

    if ($contactPhones->isEmpty()) {
        $contactPhones = collect([
            ['number' => $website->hotline],
            ['number' => $website->contact_phone],
        ])->filter(fn ($phone) => filled($phone['number'] ?? null))->values();
    }

    $contactBranches = collect($website->branches ?? [])
        ->filter(fn ($branch) => is_array($branch) && ($branch['is_active'] ?? true) && filled($branch['address'] ?? null))
        ->values();
@endphp

@section('content')
    <section class="contact-page-hero">
        @if (($contactHeroImageUrl ?: $defaultBannerUrl))
            <img class="contact-page-hero__image" src="{{ ($contactHeroImageUrl ?: $defaultBannerUrl) }}" alt="" aria-hidden="true">
        @endif
        <div class="contact-page-hero__overlay"></div>
        <div class="site-container contact-page-hero__content w-100 mx-auto site-contact__div-1">
            <nav class="contact-page-hero__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ LocalizedUrl::route('home') }}">Trang chủ</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Liên hệ</span>
            </nav>
            <p class="contact-page-hero__eyebrow">Thông tin liên hệ</p>
            <h1>Liên hệ với chúng tôi</h1>
            <p class="contact-page-hero__intro">{{ $website->site_name }} luôn sẵn sàng lắng nghe và tư vấn giải pháp PCCC phù hợp cho công trình của anh/chị.</p>
        </div>
    </section>
    <section class="section-space">
        <div class="site-container w-100 mx-auto site-contact__div-2">
            <div class="contact-page__details">
                <aside class="site-contact__aside-3">
                    <h2 class="site-contact__heading-4">Thông tin kết nối</h2>
                    <div class="d-grid site-contact__div-5">
                        @if ($contactPhones->isNotEmpty())
                            <div class="d-flex flex-wrap align-items-baseline site-contact__div-6">
                                @foreach ($contactPhones as $phone)
                                    @if (! $loop->first)<span class="site-contact__copy-7" aria-hidden="true">-</span>@endif
                                    <a class="fw-semibold site-contact__action-8" href="tel:{{ preg_replace('/\s+/', '', $phone['number']) }}">{{ $phone['number'] }}</a>
                                @endforeach
                            </div>
                        @endif
                        @if ($website->contact_email)
                            <a class="site-contact__action-9" href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a>
                        @endif
                        @if ($contactBranches->isNotEmpty())
                            @foreach ($contactBranches as $branch)
                                <p class="{{ $loop->first ? 'site-contact__element-10' : 'site-contact__element-11' }} site-contact__copy-12"><span class="fw-semibold site-contact__copy-13">{{ $branch['name'] ?? 'Địa chỉ' }}:</span> {{ $branch['address'] }}</p>
                            @endforeach
                        @elseif ($website->address)
                            <p class="site-contact__copy-14">{{ $website->address }}</p>
                        @endif
                        @if ($googleMapsUrl)
                            <a class="btn btn-primary button-primary site-contact__action-15" href="{{ $googleMapsUrl }}" target="_blank" rel="noopener noreferrer">Mở Google Maps <span aria-hidden="true">↗</span></a>
                        @endif
                    </div>
                </aside>

                <div class="contact-page__map" aria-label="Bản đồ vị trí {{ $website->company_name ?: $website->site_name }}">
                    @if ($googleMapsEmbedUrl)
                        <iframe src="{{ $googleMapsEmbedUrl }}" title="Bản đồ vị trí {{ $website->company_name ?: $website->site_name }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    @else
                        <div class="contact-page__map-empty">Bản đồ chưa được cấu hình trong Cài đặt chung.</div>
                    @endif
                </div>
            </div>
            <form class="site-contact__element-16" method="POST" action="{{ LocalizedUrl::route('contact.store') }}">
                @csrf
                <div class="site-contact__div-17">
                    <label class="fw-semibold site-contact__element-18">Họ và tên<input class="form-control form-field" name="name" value="{{ old('name') }}" required></label>
                    <label class="fw-semibold site-contact__element-18">Số điện thoại<input class="form-control form-field" name="phone" value="{{ old('phone') }}"></label>
                    <label class="fw-semibold site-contact__element-18">Email<input class="form-control form-field" type="email" name="email" value="{{ old('email') }}"></label>
                    <label class="fw-semibold site-contact__element-18">Công ty<input class="form-control form-field" name="company" value="{{ old('company') }}"></label>
                    <label class="fw-semibold site-contact__element-18">Dịch vụ quan tâm<select class="form-select form-field" name="service_id"><option value="">Chọn dịch vụ</option>@foreach ($services as $service)<option value="{{ $service->id }}" @selected(old('service_id') == $service->id || request('service') == $service->id)>{{ $service->title }}</option>@endforeach</select></label>
                    <label class="fw-semibold site-contact__element-18">Ngân sách dự kiến<input class="form-control form-field" name="budget" value="{{ old('budget') }}"></label>
                    <label class="fw-semibold site-contact__element-18">Thời gian dự kiến<input class="form-control form-field" name="timeline" value="{{ old('timeline') }}"></label>
                    <label class="fw-semibold site-contact__element-19">Nhu cầu của bạn<textarea class="form-control form-field" name="message" rows="7" required>{{ old('message', $pricingMessage ?? '') }}</textarea></label>
                </div>
                @if ($errors->any())<p class="site-contact__copy-20">{{ $errors->first() }}</p>@endif
                <button class="btn btn-primary button-primary site-contact__action-15" type="submit">Gửi yêu cầu <span aria-hidden="true">↗</span></button>
            </form>
        </div>
    </section>
@endsection
