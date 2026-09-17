@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/pricing-index.css')
@endpush

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="pricing-page-hero">
        <div class="pricing-page-hero__glow" aria-hidden="true"></div>
        <div class="site-container pricing-page-hero__inner w-100 mx-auto dv-pricing-index__div-1">
            <p class="pricing-page-hero__eyebrow">BẢNG GIÁ THEO DỊCH VỤ</p>
            <h1>{{ $selectedService ? 'Mức đầu tư cho '.$selectedService->title : 'Chọn đúng dịch vụ, xem đúng bảng giá' }}</h1>
            <p>Thay vì trộn tất cả gói trên một màn hình, mỗi dịch vụ có bảng giá, phạm vi công việc và tài liệu riêng để anh/chị dễ so sánh.</p>
        </div>
    </section>

    <section class="pricing-service-directory" aria-labelledby="pricing-service-directory-title">
        <div class="site-container w-100 mx-auto dv-pricing-index__div-1">
            <header class="pricing-service-directory__heading">
                <div>
                    <p class="pricing-service-directory__eyebrow">01 · CHỌN DỊCH VỤ</p>
                    <h2 id="pricing-service-directory-title">Anh/chị đang cần báo giá dịch vụ nào?</h2>
                </div>
                <p>Chỉ bảng giá của dịch vụ được chọn mới hiển thị bên dưới.</p>
            </header>

            @if ($pricingServices->isNotEmpty())
                <form class="pricing-service-directory__select" action="{{ LocalizedUrl::route('pricing.index') }}" method="GET">
                    <label for="pricing-service-select">Dịch vụ</label>
                    <div>
                        <select id="pricing-service-select" name="dich-vu" onchange="this.form.submit()">
                            @foreach ($pricingServices as $service)
                                <option value="{{ $service->slug }}" @selected($selectedService?->is($service))>{{ $service->title }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-dark button-dark" type="submit">Xem bảng giá</button>
                    </div>
                </form>

                <nav class="pricing-service-directory__grid" aria-label="Danh sách dịch vụ có bảng giá">
                    @foreach ($pricingServices as $service)
                        @php($isSelected = $selectedService?->is($service))
                        <a class="pricing-service-card  {{ $isSelected ? 'is-active' : '' }}"
                           href="{{ LocalizedUrl::route('pricing.index', ['dich-vu' => $service->slug]) }}#chi-tiet-bang-gia"
                           @if ($isSelected) aria-current="page" @endif>
                            <span class="pricing-service-card__number">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="pricing-service-card__content">
                                @if ($service->category?->name)<small>{{ $service->category->name }}</small>@endif
                                <strong>{{ $service->title }}</strong>
                                <em>
                                    @if (($service->pricingCatalog?->active_packages_count ?? 0) > 0)
                                        {{ $service->pricingCatalog->active_packages_count }} gói dịch vụ
                                    @else
                                        Bảng giá tài liệu
                                    @endif
                                </em>
                            </span>
                            <span class="pricing-service-card__arrow" aria-hidden="true">↗</span>
                        </a>
                    @endforeach
                </nav>
            @else
                <div class="pricing-page-empty">
                    <h2>Bảng giá đang được cập nhật</h2>
                    <p>Quản trị viên có thể tạo bảng giá theo từng dịch vụ trong mục “Bảng giá dịch vụ”.</p>
                </div>
            @endif
        </div>
    </section>

    @if ($selectedService)
        <section class="pricing-selected-service" id="chi-tiet-bang-gia" aria-labelledby="pricing-selected-service-title">
            <div class="site-container pricing-selected-service__layout w-100 mx-auto dv-pricing-index__div-1">
                <div>
                    <p class="pricing-service-directory__eyebrow">02 · BẢNG GIÁ ĐANG XEM</p>
                    <h2 id="pricing-selected-service-title">{{ $selectedService->title }}</h2>
                    <p>{{ $servicePricing?->description ?: 'Mức đầu tư dưới đây là dữ liệu đang được quản lý riêng cho dịch vụ này. Phạm vi cuối cùng sẽ được xác nhận theo yêu cầu thực tế.' }}</p>
                </div>
                <div class="pricing-selected-service__actions">
                    <a class="btn btn-dark button-dark" href="{{ LocalizedUrl::service($selectedService) }}">Xem chi tiết dịch vụ <span aria-hidden="true">→</span></a>
                    <a class="pricing-selected-service__contact" href="{{ LocalizedUrl::route('contact', ['service' => $selectedService->id]) }}">Nhận tư vấn</a>
                </div>
            </div>
        </section>

        @if ($servicePricingMatrix['packages'] !== [])
            @include('frontend.services.partials.pricing-plans', ['service' => $selectedService, 'pricingMatrix' => $servicePricingMatrix])
        @endif

        @if ($pricingMediaUrl || $pricingSourceUrl)
            @include('frontend.services.partials.pricing-media', [
                'service' => $selectedService,
                'pricingCatalog' => $servicePricing,
                'pricingMediaUrl' => $pricingMediaUrl,
                'pricingMediaIsImage' => $pricingMediaIsImage,
                'pricingSourceUrl' => $pricingSourceUrl,
            ])
        @endif

        @if ($servicePricingMatrix['packages'] === [] && ! $pricingMediaUrl && ! $pricingSourceUrl)
            <section class="section-space">
                <div class="site-container w-100 mx-auto dv-pricing-index__div-1">
                    <div class="pricing-page-empty">
                        <h2>Bảng giá dịch vụ đang được hoàn thiện</h2>
                        <p>Anh/chị để lại mục tiêu và ngân sách dự kiến, DVTEC sẽ tư vấn phạm vi phù hợp.</p>
                        <a class="btn btn-dark button-dark" href="{{ LocalizedUrl::route('contact', ['service' => $selectedService->id]) }}">Nhận tư vấn <span aria-hidden="true">→</span></a>
                    </div>
                </div>
            </section>
        @endif
    @endif

    <section class="dv-pricing-index__section-2">
        <div class="site-container w-100 mx-auto flex-column justify-content-between dv-pricing-index__div-3">
            <div><h2 class="dv-pricing-index__heading-4">Cần một cấu hình riêng?</h2><p class="dv-pricing-index__copy-5">DVTEC có thể ghép phạm vi theo mục tiêu, kênh triển khai, tiến độ và ngân sách thực tế.</p></div>
            <a class="btn btn-primary button-primary flex-shrink-0" href="{{ LocalizedUrl::route('contact') }}">Yêu cầu báo giá <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
