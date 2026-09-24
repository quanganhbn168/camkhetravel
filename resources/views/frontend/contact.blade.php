@extends('layouts.master')

@push('styles')
    @vite('resources/css/pages/contact.css')
@endpush

@section('content')
    <section class="contact-page-hero" data-system-page="contact">
        @if ($pageBannerUrl)
            <img
                class="contact-page-hero__image"
                data-page-banner-image
                src="{{ $pageBannerUrl }}"
                alt="{{ $page['title'] }}"
            >
        @endif

        <div class="contact-page-hero__overlay"></div>

        <div class="container">
            <nav class="contact-page-hero__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>

                <span aria-hidden="true">/</span>

                <span aria-current="page">Liên hệ</span>
            </nav>

            <h1>{{ $page['title'] }}</h1>

            <p class="contact-page-hero__intro">
                {{ $website->site_name }} tiếp nhận nhu cầu di chuyển và tư vấn
                phương án xe phù hợp với lịch trình của anh/chị.
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container contact-layout">

            {{-- CONTACT INFO --}}
            <div>
                <aside class="contact-details">

                    <h2 class="h2 contact-details__title">
                        Liên hệ với chúng tôi
                    </h2>

                    <div class="contact-details__list">

                        {{-- PHONE --}}
                        @if ($contactPhones->isNotEmpty())
                            <div class="contact-info-item">
                                <div class="contact-info-item__icon">
                                    <i class="fa-solid fa-phone"></i>
                                </div>

                                <div class="contact-info-item__content">
                                    <div class="contact-info-item__label">
                                        Điện thoại
                                    </div>

                                    <div class="contact-info-item__phones">
                                        @foreach ($contactPhones as $phone)
                                            @if (! $loop->first)
                                                <span class="contact-info-item__separator" aria-hidden="true">
                                                    -
                                                </span>
                                            @endif

                                            <a href="{{ $phone['href'] }}">
                                                {{ $phone['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif


                        {{-- EMAIL --}}
                        @if ($website->contact_email)
                            <div class="contact-info-item">
                                <div class="contact-info-item__icon">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>

                                <div class="contact-info-item__content">
                                    <div class="contact-info-item__label">
                                        Email
                                    </div>

                                    <a
                                        class="contact-info-item__value text-break"
                                        href="mailto:{{ $website->contact_email }}"
                                    >
                                        {{ $website->contact_email }}
                                    </a>
                                </div>
                            </div>
                        @endif


                        {{-- BRANCHES --}}
                        @if ($contactBranches->isNotEmpty())
                            @foreach ($contactBranches as $branch)
                                <div class="contact-info-item">
                                    <div class="contact-info-item__icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>

                                    <div class="contact-info-item__content">
                                        <div class="contact-info-item__label">
                                            {{ $branch['name'] }}
                                        </div>

                                        <div class="contact-info-item__value">
                                            {{ $branch['address'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @elseif ($website->address)
                            <div class="contact-info-item">
                                <div class="contact-info-item__icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>

                                <div class="contact-info-item__content">
                                    <div class="contact-info-item__label">
                                        Địa chỉ
                                    </div>

                                    <div class="contact-info-item__value">
                                        {{ $website->address }}
                                    </div>
                                </div>
                            </div>
                        @endif


                    </div>
                </aside>


                {{-- MAP --}}
                <div
                    class="contact-page__map"
                    aria-label="Bản đồ vị trí {{ $website->company_name ?: $website->site_name }}"
                >
                    @if ($googleMapsEmbedUrl)
                        <iframe
                            src="{{ $googleMapsEmbedUrl }}"
                            title="Bản đồ vị trí {{ $website->company_name ?: $website->site_name }}"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                        ></iframe>
                    @else
                        <div class="contact-page__map-empty">
                            <i class="fa-solid fa-map-location-dot me-2"></i>
                            Bản đồ chưa được cấu hình trong Cài đặt chung.
                        </div>
                    @endif
                </div>
            </div>


            {{-- CONTACT FORM --}}
            <form
                class="contact-form"
                method="POST"
                action="{{ route('contact.store') }}"
            >
                @csrf

                <div class="row g-3">

                    <label class="fw-semibold col-md-6">
                        Họ và tên

                        <input
                            class="form-control"
                            name="name"
                            value="{{ old('name') }}"
                            required
                        >
                    </label>

                    <label class="fw-semibold col-md-6">
                        Số điện thoại

                        <input
                            class="form-control"
                            name="phone"
                            value="{{ old('phone') }}"
                        >
                    </label>

                    <label class="fw-semibold col-md-6">
                        Email

                        <input
                            class="form-control"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                        >
                    </label>

                    <label class="fw-semibold col-md-6">
                        Công ty

                        <input
                            class="form-control"
                            name="company"
                            value="{{ old('company') }}"
                        >
                    </label>

                    <label class="fw-semibold col-md-6">
                        Dịch vụ quan tâm

                        <select class="form-select" name="service_id">
                            <option value="">Chọn dịch vụ</option>

                            @foreach ($services as $service)
                                <option
                                    value="{{ $service->id }}"
                                    @selected(
                                        old('service_id') == $service->id
                                        || request('service') == $service->id
                                    )
                                >
                                    {{ $service->title }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="fw-semibold col-md-6">
                        Ngân sách dự kiến

                        <input
                            class="form-control"
                            name="budget"
                            value="{{ old('budget') }}"
                        >
                    </label>

                    <label class="fw-semibold col-md-6">
                        Thời gian dự kiến

                        <input
                            class="form-control"
                            name="timeline"
                            value="{{ old('timeline') }}"
                        >
                    </label>

                    <label class="fw-semibold col-12">
                        Nhu cầu của bạn

                        <textarea
                            class="form-control"
                            name="message"
                            rows="7"
                            required
                        >{{ old('message') }}</textarea>
                    </label>

                </div>

                @if ($errors->any())
                    <p class="alert alert-danger mt-3">
                        {{ $errors->first() }}
                    </p>
                @endif

                <button class="btn btn-primary mt-3" type="submit">
                    Gửi yêu cầu
                    <i class="fa-solid fa-arrow-right ms-2"></i>
                </button>
            </form>

        </div>
    </section>
@endsection
