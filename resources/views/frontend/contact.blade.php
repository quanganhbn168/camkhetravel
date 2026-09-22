@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/contact.scss')
@endpush

@section('content')
    <section class="contact-page-hero" data-system-page="contact">
        @if ($pageBannerUrl)
            <img class="contact-page-hero__image" data-page-banner-image src="{{ $pageBannerUrl }}" alt="{{ $page['title'] }}">
        @endif
        <div class="contact-page-hero__overlay"></div>
        <div class="container">
            <nav class="contact-page-hero__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Liên hệ</span>
            </nav>
            <h1>{{ $page['title'] }}</h1>
            <p class="contact-page-hero__intro">{{ $website->site_name }} luôn sẵn sàng lắng nghe và tư vấn giải pháp PCCC phù hợp cho công trình của anh/chị.</p>
        </div>
    </section>
    <section class="section-space">
        <div class="container contact-layout">
            <div >
                <aside class="contact-details">
                    <h2 class="h2">Thông tin kết nối</h2>
                    <div class="d-grid gap-3">
                        @if ($contactPhones->isNotEmpty())
                            <div class="d-flex flex-wrap align-items-baseline gap-2">
                                @foreach ($contactPhones as $phone)
                                    @if (! $loop->first)<span class="text-body-secondary" aria-hidden="true">-</span>@endif
                                    <a class="fw-semibold fs-5" href="{{ $phone['href'] }}">{{ $phone['label'] }}</a>
                                @endforeach
                            </div>
                        @endif
                        @if ($website->contact_email)
                            <a class="text-break" href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a>
                        @endif
                        @if ($contactBranches->isNotEmpty())
                            @foreach ($contactBranches as $branch)
                                <p class="{{ $loop->first ? 'border-top pt-3' : '' }} mb-0"><span class="fw-semibold text-body">{{ $branch['name'] }}:</span> {{ $branch['address'] }}</p>
                            @endforeach
                        @elseif ($website->address)
                            <p class="border-top pt-3">{{ $website->address }}</p>
                        @endif
                        @if ($googleMapsUrl)
                            <a class="btn btn-primary mt-3" href="{{ $googleMapsUrl }}" target="_blank" rel="noopener noreferrer">Mở Google Maps <span aria-hidden="true">↗</span></a>
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
            <form class="contact-form" method="POST" action="{{ route('contact.store') }}">
                @csrf
                <div class="row g-3">
                    <label class="fw-semibold col-md-6">Họ và tên<input class="form-control" name="name" value="{{ old('name') }}" required></label>
                    <label class="fw-semibold col-md-6">Số điện thoại<input class="form-control" name="phone" value="{{ old('phone') }}"></label>
                    <label class="fw-semibold col-md-6">Email<input class="form-control" type="email" name="email" value="{{ old('email') }}"></label>
                    <label class="fw-semibold col-md-6">Công ty<input class="form-control" name="company" value="{{ old('company') }}"></label>
                    <label class="fw-semibold col-md-6">Dịch vụ quan tâm<select class="form-select" name="service_id"><option value="">Chọn dịch vụ</option>@foreach ($services as $service)<option value="{{ $service->id }}" @selected(old('service_id') == $service->id || request('service') == $service->id)>{{ $service->title }}</option>@endforeach</select></label>
                    <label class="fw-semibold col-md-6">Ngân sách dự kiến<input class="form-control" name="budget" value="{{ old('budget') }}"></label>
                    <label class="fw-semibold col-md-6">Thời gian dự kiến<input class="form-control" name="timeline" value="{{ old('timeline') }}"></label>
                    <label class="fw-semibold col-12">Nhu cầu của bạn<textarea class="form-control" name="message" rows="7" required>{{ old('message') }}</textarea></label>
                </div>
                @if ($errors->any())<p class="alert alert-danger mt-3">{{ $errors->first() }}</p>@endif
                <button class="btn btn-primary mt-3" type="submit">Gửi yêu cầu <span aria-hidden="true">↗</span></button>
            </form>
        </div>
    </section>
@endsection
