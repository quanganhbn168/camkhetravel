@extends('layouts.master')

@push('styles')
    @vite('resources/scss/pages/home.scss')
@endpush

@section('body_class', 'camkhetravel-home')
@section('main_id', 'camkhe-noi-dung')
@section('main_class', 'camkhe-home-main')

@section('before_header')
    <a class="skip-link" href="#camkhe-noi-dung">Bỏ qua điều hướng</a>
    <svg class="svg-sprite" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14m-6-6 6 6-6 6" /></symbol>
        <symbol id="i-car" viewBox="0 0 24 24"><path d="m5 9 2-5h10l2 5M3 10l2-1h14l2 1v8H3zM5 18v2m14-2v2M6 13h2m8 0h2M8 16h8" /></symbol>
        <symbol id="i-pin" viewBox="0 0 24 24"><path d="M20 10c0 6-8 11-8 11S4 16 4 10a8 8 0 1 1 16 0Z" /><circle cx="12" cy="10" r="2.5" /></symbol>
        <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M7 3v4m10-4v4M3 11h18m-14 4h2m4 0h2m-8 3h2" /></symbol>
        <symbol id="i-users" viewBox="0 0 24 24"><circle cx="9" cy="7" r="3" /><path d="M2 21v-3a7 7 0 0 1 14 0v3m1-17a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 5v2" /></symbol>
        <symbol id="i-heart" viewBox="0 0 24 24"><path d="M20.8 4.7a5.5 5.5 0 0 0-7.8 0L12 5.8l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.5a5.5 5.5 0 0 0 0-7.8Z" /></symbol>
        <symbol id="i-briefcase" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="14" rx="2" /><path d="M8 7V3h8v4M3 12l9 3 9-3m-9 0v4" /></symbol>
        <symbol id="i-shield" viewBox="0 0 24 24"><path d="m12 2 9 4v6c0 5-9 10-9 10S3 17 3 12V6zM8 12l3 3 5-6" /></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" /><path d="M12 6v6l4 2" /></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6" /></symbol>
        <symbol id="i-phone" viewBox="0 0 24 24"><path d="m7 3 3 5-3 3a18 18 0 0 0 6 6l3-3 5 3-1 4C10 23 1 14 3 4z" /></symbol>
        <symbol id="i-chat" viewBox="0 0 24 24"><path d="M21 11a9 9 0 0 1-9 9 11 11 0 0 1-4-1l-6 3 2-6a9 9 0 1 1 17-5Z" /><path d="M7 9h10m-10 4h7" /></symbol>
        <symbol id="i-handshake" viewBox="0 0 24 24"><path d="m2 8 4-4 5 2 3-2 8 5-4 7-5 5-7-5zM11 6l-4 5 3 2 4-4 5 5M7 17l2-2m1 5 2-3m3 2 1-3" /></symbol>
        <symbol id="i-file" viewBox="0 0 24 24"><path d="M5 2h9l5 5v15H5zM14 2v6h5M8 12h8m-8 4h8" /></symbol>
        <symbol id="i-headset" viewBox="0 0 24 24"><path d="M3 14v-3a9 9 0 0 1 18 0v3M5 12H3v7h4v-7Zm14 0h2v7h-4v-7Zm2 7c0 3-5 3-7 3" /></symbol>
        <symbol id="i-tag" viewBox="0 0 24 24"><path d="M3 3h8l10 10-8 8L3 11z" /><circle cx="7.5" cy="7.5" r="1" /></symbol>
        <symbol id="i-mail" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="m2 5 10 8L22 5" /></symbol>
        <symbol id="i-copy" viewBox="0 0 24 24"><rect x="8" y="8" width="13" height="13" rx="2" /><path d="M16 8V3H3v13h5" /></symbol>
        <symbol id="i-star" viewBox="0 0 24 24"><path d="m12 2 3 6 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z" /></symbol>
        <symbol id="i-menu" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18" /></symbol>
        <symbol id="i-up" viewBox="0 0 24 24"><path d="m6 14 6-6 6 6" /></symbol>
    </svg>

    <header class="site-header" id="siteHeader">
        <nav class="navbar navbar-expand-lg container" aria-label="Điều hướng chính">
            <a class="navbar-brand brand-wordmark" href="/#trang-chu" aria-label="CamKheTravel – Trang chủ">CamKheTravel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Mở hoặc đóng menu">
                <svg class="icon" aria-hidden="true" focusable="false"><use href="#i-menu"></use></svg>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    @foreach ($navigation as $item)
                        <li class="nav-item"><a class="nav-link" href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
                <button type="button" class="btn btn-brand" data-quote-type="trip">
                    Nhận báo giá <svg class="icon" aria-hidden="true" focusable="false"><use href="#i-arrow"></use></svg>
                </button>
            </div>
        </nav>
    </header>
@endsection

@section('content')
    <div class="camkhe-home" data-camkhe-home style="--camkhe-no-image: url('{{ $noImageUrl }}')">
        <div hidden data-camkhe-config
            data-phone="{{ $frontendConfig['phone'] }}"
            data-zalo-url="{{ $frontendConfig['zaloUrl'] }}"
            data-email="{{ $frontendConfig['email'] }}"
            data-region="{{ $frontendConfig['region'] }}"
            data-lead-endpoint="{{ $frontendConfig['leadEndpoint'] }}"></div>

        <section class="hero" id="trang-chu" aria-labelledby="hero-title">
            <picture class="hero-picture">
                <source media="(max-width: 767px)" srcset="{{ $noImageUrl }}">
                <img src="{{ $noImageUrl }}" alt="Ảnh mặc định" width="1800" height="625" fetchpriority="high">
            </picture>
            <div class="container hero-inner">
                <div class="hero-copy">
                    <h1 id="hero-title">{{ $heroSlide?->title ?: 'CamKheTravel – đồng hành cùng hành trình của bạn' }}@if (filled($heroFleetLabel))<br><span>{{ $heroFleetLabel }}</span>@endif</h1>
                    <p class="hero-services">{{ $heroSlide?->description ?: $homepage->capabilities }}</p>
                    <p class="signature hero-signature">Mỗi hành trình<br><span>là một trải nghiệm đáng nhớ.</span></p>
                    <div class="hero-actions">
                        <button type="button" class="btn btn-brand" data-quote-type="trip">
                            {{ $heroSlide?->primary_label ?: 'Nhận báo giá chuyến đi' }} <svg class="icon" aria-hidden="true" focusable="false"><use href="#i-arrow"></use></svg>
                        </button>
                        <a class="btn btn-zalo" href="{{ $heroSlide?->secondary_url ?: '/#dich-vu' }}">{{ $heroSlide?->secondary_label ?: 'Xem dịch vụ' }} <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></a>
                    </div>
                    <div class="hero-trust">
                        <span><svg class="icon" aria-hidden="true"><use href="#i-shield"></use></svg> Trao đổi rõ ràng</span>
                        <span><svg class="icon" aria-hidden="true"><use href="#i-users"></use></svg> Theo nhu cầu</span>
                        <span><svg class="icon" aria-hidden="true"><use href="#i-clock"></use></svg> Chủ động lịch trình</span>
                        <span><svg class="icon" aria-hidden="true"><use href="#i-pin"></use></svg> Điểm đón trao đổi trước</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="container booking-wrap">
            <form class="quick-quote" id="quickQuote" aria-label="Yêu cầu báo giá nhanh">
                <div class="quick-field"><svg class="icon" aria-hidden="true"><use href="#i-pin"></use></svg><div><label for="quickPickup">Điểm đón</label><input id="quickPickup" name="pickup" placeholder="Nhập địa chỉ đón" maxlength="180" autocomplete="off"></div></div>
                <div class="quick-field"><svg class="icon" aria-hidden="true"><use href="#i-pin"></use></svg><div><label for="quickDestination">Điểm đến</label><input id="quickDestination" name="destination" placeholder="Bạn muốn đi đâu?" maxlength="180" autocomplete="off"></div></div>
                <div class="quick-field"><svg class="icon" aria-hidden="true"><use href="#i-calendar"></use></svg><div><label for="quickDate">Ngày đi</label><input id="quickDate" name="departure" type="date" aria-label="Ngày khởi hành"></div></div>
                <div class="quick-field"><svg class="icon" aria-hidden="true"><use href="#i-users"></use></svg><div><label for="quickPassengers">Số khách</label><select id="quickPassengers" name="passengers"><option value="">Chọn số khách</option><option value="1–4">1–4 khách</option><option value="5–6">5–6 khách</option><option value="7–15">7–15 khách</option><option value="Cần tư vấn thêm">Cần tư vấn thêm</option></select></div></div>
                <button type="submit" class="btn btn-brand">Yêu cầu báo giá <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></button>
            </form>
        </div>

        <section class="section services-section" id="dich-vu" aria-labelledby="services-title">
            <div class="container">
                <div class="section-heading"><h2 id="services-title">Dịch vụ của CamKheTravel</h2><p>Chọn dịch vụ phù hợp với hành trình <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></p></div>
                <div class="services-grid">
                    @forelse ($services as $service)
                        <article class="service-card">
                            <img src="{{ $service->image_url }}" alt="{{ $service->title }}" width="650" height="390" loading="lazy" decoding="async">
                            <div class="service-card-body">
                                <span class="icon-bubble {{ $service->quote_type === 'wedding' ? 'rose' : ($service->quote_type === 'shared' ? 'sand' : '') }}"><svg class="icon" aria-hidden="true"><use href="#i-{{ $service->quote_icon }}"></use></svg></span>
                                <div><h3><button type="button" class="stretched-button" data-quote-type="{{ $service->quote_type }}" data-service="{{ $service->title }}" data-service-id="{{ $service->id }}">{{ $service->title }}</button></h3><p>{{ $service->excerpt }}</p></div>
                            </div>
                        </article>
                    @empty
                        <p class="empty-state">Dịch vụ đang được cập nhật. Anh/chị có thể gửi lịch trình để CamKheTravel tiếp nhận tư vấn.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section fleet-section" id="doi-xe" aria-labelledby="fleet-title">
            <div class="container">
                <div class="section-heading"><h2 id="fleet-title">Lựa chọn xe cho hành trình</h2><button type="button" class="text-link" data-quote-type="trip">Tư vấn chọn xe <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></button></div>
                <div class="fleet-grid">
                    @foreach ($fleetTypes as $vehicle)
                        <article class="fleet-card">
                            <div class="fleet-image"><img src="{{ $noImageUrl }}" alt="{{ $vehicle['title'] }} – ảnh mặc định" width="700" height="500" loading="lazy" decoding="async"></div>
                            <div class="fleet-content">
                                <h3>{{ $vehicle['title'] }}</h3>
                                @foreach ($vehicle['features'] as $feature)
                                    <p><svg class="icon" aria-hidden="true"><use href="#i-check"></use></svg>{{ $feature }}</p>
                                @endforeach
                                <button type="button" class="btn btn-outline-brand btn-sm" data-vehicle="{{ $vehicle['code'] }}" data-vehicle-name="{{ $vehicle['title'] }}" data-vehicle-description="{{ $vehicle['features']->implode('. ') }}" data-vehicle-image="{{ $noImageUrl }}">Xem chi tiết <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></button>
                            </div>
                        </article>
                    @endforeach
                </div>
                <p class="section-note">Ảnh mặc định. Loại xe thực tế, số ghế và hành lý sẽ được xác nhận khi trao đổi lịch trình.</p>
            </div>
        </section>

        <section class="partner-section" id="doi-tac" aria-labelledby="partner-title">
            <div class="partner-intro">
                <picture class="partner-picture"><source media="(max-width: 767px)" srcset="{{ $noImageUrl }}"><img src="{{ $noImageUrl }}" alt="Ảnh mặc định" width="1800" height="588" loading="lazy" decoding="async"></picture>
                <div class="container partner-intro-inner">
                    <div class="partner-copy">
                        <h2 id="partner-title">Đối tác cung cấp xe<br>cho tour du lịch</h2>
                        <p class="partner-lead"><strong>Đối tác lên chương trình.<br>CamKheTravel đồng hành cùng chuyến đi.</strong></p>
                        <p>Cung cấp phương tiện theo nhu cầu của công ty du lịch, đại lý lữ hành và đơn vị tổ chức tour. Lịch trình, quy mô đoàn và phương án xe được trao đổi trước khi xác nhận.</p>
                        <div class="partner-actions"><button type="button" class="btn btn-brand" data-quote-type="partner">Đăng ký đối tác <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></button><button type="button" class="btn btn-outline-brand" data-quote-type="partner">Nhận báo giá hợp tác <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></button></div>
                    </div>
                    <p class="signature partner-signature">Cùng đối tác<br>tạo nên những hành trình<br>trọn vẹn.</p>
                </div>
            </div>
            <div class="container partner-bottom">
                <div class="partner-benefits">
                    @foreach ($homepage->partner_benefits as $benefit)
                        <div class="partner-benefit"><span class="icon-bubble"><svg class="icon" aria-hidden="true"><use href="#i-handshake"></use></svg></span><div><h3>{{ $benefit['title'] }}</h3><p>{{ $benefit['description'] }}</p></div></div>
                    @endforeach
                </div>
                <div class="partner-panels">
                    <div class="tour-panel">
                        <h3>Phù hợp nhiều loại hình tour</h3><p class="panel-intro">Cung cấp phương tiện theo chương trình của đối tác.</p>
                        <div class="tour-grid">
                            @foreach ($homepage->tour_types as $tour)
                                <article class="tour-type"><img src="{{ $noImageUrl }}" alt="{{ $tour['title'] }} – ảnh mặc định" width="360" height="260" loading="lazy"><h4>{{ $tour['title'] }}</h4><p>{{ $tour['description'] }}</p></article>
                            @endforeach
                        </div>
                    </div>
                    <div class="process-panel">
                        <h3>Quy trình hợp tác</h3><p class="panel-intro">Thống nhất nhu cầu, phương án và đầu mối liên hệ.</p>
                        <ol class="process-list">
                            @foreach ($homepage->partner_steps as $step)
                                <li><span class="step-icon"><svg class="icon" aria-hidden="true"><use href="#i-{{ $loop->first ? 'file' : ($loop->last ? 'car' : 'chat') }}"></use></svg><b>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</b></span><h4>{{ $step['title'] }}</h4><p>{{ $step['description'] }}</p></li>
                            @endforeach
                        </ol>
                        <button type="button" class="btn btn-brand" data-quote-type="partner">Trở thành đối tác <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></button>
                    </div>
                    <aside class="partner-quote"><p class="signature">Hành trình<br>tốt đẹp hơn<br>khi có người<br>đồng hành.</p><span>CAMKHETRAVEL</span></aside>
                </div>
            </div>
        </section>

        <section class="wedding-section" id="xe-cuoi" aria-labelledby="wedding-title">
            <picture class="wedding-picture"><source media="(max-width: 767px)" srcset="{{ $noImageUrl }}"><img src="{{ $noImageUrl }}" alt="Ảnh mặc định" width="1800" height="567" loading="lazy" decoding="async"></picture>
            <div class="container wedding-inner"><div class="wedding-copy"><h2 id="wedding-title">Xe cưới Phú Thọ</h2><p class="wedding-subtitle">Đồng hành trong ngày trọng đại.</p><p class="wedding-models">Xe dâu <span>•</span> Đưa đón gia đình</p><p class="wedding-description">Lịch trình và yêu cầu xe được trao đổi theo kế hoạch của gia đình.</p><button type="button" class="btn btn-brand" data-quote-type="wedding">Tư vấn dịch vụ xe cưới <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></button></div><p class="signature wedding-signature">Hạnh phúc<br>bắt đầu từ<br>những hành trình đẹp.</p></div>
        </section>

        <section class="section commitments-section" id="cam-ket" aria-labelledby="commitments-title">
            <div class="container"><div class="section-heading"><h2 id="commitments-title">CamKheTravel đồng hành cùng hành trình</h2></div><div class="commitments-grid">
                @foreach ($homepage->commitment_items as $commitment)
                    <article class="commitment"><span class="icon-bubble"><svg class="icon" aria-hidden="true"><use href="#i-{{ $loop->first ? 'file' : ($loop->last ? 'headset' : ($loop->iteration === 2 ? 'car' : 'clock')) }}"></use></svg></span><div><h3>{{ $commitment['title'] }}</h3><p>{{ $commitment['description'] }}</p></div></article>
                @endforeach
            </div></div>
        </section>

        @if ($testimonials->isNotEmpty())
            <section class="section reviews-section" id="danh-gia" aria-labelledby="reviews-title">
                <div class="container"><div class="section-heading"><h2 id="reviews-title">Khách hàng nói về CamKheTravel</h2>@if ($hasIllustrativeTestimonials)<span class="demo-label">Có phản hồi minh họa</span>@endif</div><div class="reviews-grid">
                    @foreach ($testimonials as $testimonial)
                        <article class="review-card">
                            <div class="review-stars" aria-label="Đánh giá {{ $testimonial->rating ?? 0 }} trên 5 sao">
                                @for ($star = 1; $star <= 5; $star++)<svg class="icon {{ $star <= (int) ($testimonial->rating ?? 0) ? 'is-active' : '' }}" aria-hidden="true"><use href="#i-star"></use></svg>@endfor
                                @if ($testimonial->is_illustrative)<span>Minh họa</span>@endif
                            </div>
                            <blockquote>“{{ $testimonial->quote }}”</blockquote>
                            <div class="review-person"><span class="avatar">{{ mb_substr($testimonial->client_name, 0, 2) }}</span><div><h3>{{ $testimonial->client_name }}</h3><p>{{ $testimonial->client_role ?: $testimonial->company_name }}</p></div></div>
                        </article>
                    @endforeach
                </div></div>
            </section>
        @endif

        <section class="contact-banner" id="lien-he" aria-labelledby="contact-title">
            <div class="container contact-inner"><p class="signature contact-signature">Cẩm Khê, Phú Thọ,<br>hẹn bạn trên hành trình.</p><div class="contact-main"><h2 id="contact-title">{{ $homepage->consultation_title ?: 'Bạn cần phương tiện cho chuyến đi sắp tới?' }}</h2><p>{{ $homepage->consultation_content ?: 'Gửi lịch trình để CamKheTravel tư vấn phương án phù hợp.' }}</p><div class="contact-actions"><button type="button" class="btn btn-brand" data-quote-type="trip">Nhận tư vấn <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></button><button type="button" class="btn btn-white" data-contact="phone"><svg class="icon" aria-hidden="true"><use href="#i-phone"></use></svg> Liên hệ</button></div></div><p class="signature contact-signature right">CamKheTravel,<br>đồng hành cùng chuyến đi.</p></div>
        </section>
    </div>
@endsection

@section('before_footer')
    <footer class="site-footer">
        <div class="container">
            <div class="row gy-4 footer-top">
                <div class="col-lg-4 col-sm-6"><a href="/#trang-chu" class="footer-brand brand-wordmark">CamKheTravel</a><p class="footer-about">{{ $website->tagline ?: 'Dịch vụ xe và tư vấn phương tiện theo lịch trình.' }}<br>Thông tin chuyến đi được trao đổi trước khi xác nhận.</p></div>
                <div class="col-lg-3 col-sm-6"><h2>Thông tin liên hệ</h2><ul class="footer-contact"><li><svg class="icon" aria-hidden="true"><use href="#i-phone"></use></svg><button type="button" data-contact="phone" data-phone-label>Liên hệ tư vấn</button></li><li><svg class="icon" aria-hidden="true"><use href="#i-chat"></use></svg><button type="button" data-contact="zalo">Tư vấn qua Zalo</button></li><li><svg class="icon" aria-hidden="true"><use href="#i-pin"></use></svg><span data-company-region>{{ $frontendConfig['region'] }}</span></li><li data-email-row hidden><svg class="icon" aria-hidden="true"><use href="#i-mail"></use></svg><a data-email-link></a></li></ul></div>
                <div class="col-lg-3 col-sm-6"><h2>Liên kết nhanh</h2><div class="footer-links">@foreach ($navigation as $item)<a href="{{ $item['url'] }}">{{ $item['label'] }}</a>@endforeach</div></div>
                <div class="col-lg-2 col-sm-6"><h2>Kết nối</h2><div class="social-buttons"><button type="button" data-contact="phone" aria-label="Liên hệ qua điện thoại"><svg class="icon" aria-hidden="true"><use href="#i-phone"></use></svg></button><button type="button" data-contact="zalo" class="social-zalo" aria-label="Liên hệ qua Zalo">Zalo</button><button type="button" data-quote-type="partner" aria-label="Đăng ký đối tác"><svg class="icon" aria-hidden="true"><use href="#i-handshake"></use></svg></button></div><p class="signature footer-signature">Kết nối những hành trình.</p></div>
            </div>
            <div class="footer-bottom"><span>© <span data-year>{{ now()->year }}</span> CamKheTravel.</span><span>Ảnh mặc định được dùng khi chưa có ảnh nội dung.</span><button type="button" data-bs-toggle="modal" data-bs-target="#privacyModal">Thông tin dữ liệu</button></div>
        </div>
    </footer>

    <div class="mobile-actions" aria-label="Liên hệ nhanh"><button type="button" data-contact="phone"><svg class="icon" aria-hidden="true"><use href="#i-phone"></use></svg><span>Gọi tư vấn</span></button><button type="button" data-contact="zalo"><span class="zalo-symbol">Z</span><span>Zalo</span></button><button type="button" data-quote-type="trip"><svg class="icon" aria-hidden="true"><use href="#i-file"></use></svg><span>Nhận báo giá</span></button></div>
    <button type="button" class="back-top" id="backTop" aria-label="Về đầu trang" hidden><svg class="icon" aria-hidden="true"><use href="#i-up"></use></svg></button>
    <noscript><div class="noscript-notice">Bật JavaScript để sử dụng form báo giá và các chức năng tương tác. Nội dung trang vẫn xem được.</div></noscript>

    <div class="modal fade" id="quoteModal" tabindex="-1" aria-labelledby="quoteTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><div><p class="modal-brand">CAMKHETRAVEL</p><h2 class="modal-title fs-4" id="quoteTitle">Nhận báo giá chuyến đi</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body">
        <div class="alert demo-notice" id="demoNotice" role="note" hidden>Gửi form để CamKheTravel tiếp nhận yêu cầu tư vấn. Đây chưa phải xác nhận đặt xe.</div>
        <form id="requestForm" novalidate>
            <input type="hidden" name="type" id="requestType" value="trip"><input type="hidden" name="service" id="requestService" value=""><input type="hidden" name="service_id" id="requestServiceId" value="">
            <div class="honeypot" aria-hidden="true"><label for="requestWebsite">Website phụ</label><input id="requestWebsite" name="website" tabindex="-1" autocomplete="off"></div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="requestName">Họ và tên <span aria-hidden="true">*</span></label><input class="form-control" id="requestName" name="name" required minlength="2" maxlength="100" autocomplete="name"><div class="invalid-feedback">Nhập họ tên từ 2 đến 100 ký tự.</div></div>
                <div class="col-md-6"><label class="form-label" for="requestPhone">Số điện thoại <span aria-hidden="true">*</span></label><input class="form-control" id="requestPhone" name="phone" type="tel" inputmode="tel" required maxlength="24" autocomplete="tel"><div class="invalid-feedback">Nhập số di động Việt Nam hợp lệ.</div></div>
                <div class="col-12" id="companyGroup" hidden><label class="form-label" for="requestCompany">Công ty / Đơn vị tổ chức tour <span aria-hidden="true">*</span></label><input class="form-control" id="requestCompany" name="company" maxlength="180" autocomplete="organization"><div class="invalid-feedback">Nhập tên công ty hoặc đơn vị tổ chức.</div></div>
                <div class="col-md-6"><label class="form-label" for="requestPickup">Điểm đón</label><input class="form-control" id="requestPickup" name="pickup" maxlength="180" placeholder="Ví dụ: Cẩm Khê, Phú Thọ"><div class="invalid-feedback">Nhập điểm đón.</div></div>
                <div class="col-md-6"><label class="form-label" for="requestDestination">Điểm đến</label><input class="form-control" id="requestDestination" name="destination" maxlength="180" placeholder="Địa điểm hoặc lịch trình dự kiến"><div class="invalid-feedback">Nhập điểm đến.</div></div>
                <div class="col-md-6"><label class="form-label" for="requestDeparture">Ngày đi dự kiến</label><input class="form-control" id="requestDeparture" name="departure" type="date"><div class="invalid-feedback">Ngày đi không được trước hôm nay.</div></div>
                <div class="col-md-6"><label class="form-label" for="requestReturn">Ngày về (nếu có)</label><input class="form-control" id="requestReturn" name="returnDate" type="date"><div class="invalid-feedback">Ngày về không được trước ngày đi.</div></div>
                <div class="col-md-6"><label class="form-label" for="requestVehicle">Loại xe</label><select class="form-select" id="requestVehicle" name="vehicle"><option value="Cần tư vấn">Cần tư vấn</option>@foreach ($fleetTypes as $vehicle)<option value="{{ $vehicle['title'] }}">{{ $vehicle['title'] }}</option>@endforeach<option value="Xe cưới">Xe cưới</option></select></div>
                <div class="col-md-6"><label class="form-label" for="requestPassengers">Số khách / Quy mô đoàn</label><input class="form-control" id="requestPassengers" name="passengers" maxlength="80" placeholder="Ví dụ: 4 người, 2 vali"></div>
                <div class="col-12"><label class="form-label" for="requestNotes">Yêu cầu thêm</label><textarea class="form-control" id="requestNotes" name="notes" rows="3" maxlength="2000" placeholder="Giờ đón, điểm dừng, số chuyến, nhu cầu hợp tác..."></textarea></div>
                <div class="col-12"><div class="form-check"><input class="form-check-input" id="requestConsent" name="consent" type="checkbox" required><label class="form-check-label" for="requestConsent">Tôi đồng ý cung cấp thông tin để được liên hệ tư vấn về yêu cầu này.</label><div class="invalid-feedback">Cần đồng ý trước khi tiếp tục.</div></div><p class="form-hint">Đây là yêu cầu tư vấn, chưa phải xác nhận đặt xe hoặc giá cuối cùng.</p></div>
            </div>
            <div id="formError" class="alert alert-danger mt-3" role="alert" hidden></div>
            <div class="form-submit"><button type="button" class="btn btn-soft" data-bs-dismiss="modal">Để sau</button><button type="submit" class="btn btn-brand" id="requestSubmit"><span data-submit-label>Gửi yêu cầu tư vấn</span> <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></button></div>
        </form>
        <div id="requestResult" hidden aria-live="polite"><span class="result-icon"><svg class="icon" aria-hidden="true"><use href="#i-check"></use></svg></span><h3 id="resultTitle">Đã gửi yêu cầu tư vấn</h3><p id="resultDescription">Hệ thống đã tiếp nhận yêu cầu. Đây chưa phải xác nhận đặt xe; CamKheTravel cần liên hệ để thống nhất lịch trình và chi phí.</p><label class="form-label" for="requestSummary">Nội dung yêu cầu</label><textarea id="requestSummary" class="form-control request-summary" rows="9" readonly></textarea><div class="result-actions"><button type="button" class="btn btn-brand" id="copySummary"><svg class="icon" aria-hidden="true"><use href="#i-copy"></use></svg> Sao chép nội dung</button><button type="button" class="btn btn-outline-brand" id="editRequest">Chỉnh sửa yêu cầu</button></div><p id="copyStatus" class="form-hint mt-3" role="status"></p></div>
    </div></div></div></div>

    <div class="modal fade" id="vehicleModal" tabindex="-1" aria-labelledby="vehicleTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title fs-4" id="vehicleTitle">Thông tin xe</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body"><img id="vehicleDetailImage" class="vehicle-detail-image" src="{{ $noImageUrl }}" alt="Ảnh xe mặc định" width="700" height="500"><p id="vehicleDetailDescription"></p><p class="form-hint">Ảnh mặc định. Dòng xe thực tế, sức chứa và hành lý sẽ được xác nhận khi trao đổi lịch trình.</p><button type="button" class="btn btn-brand w-100" id="quoteVehicle">Nhận báo giá xe này <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></button></div></div></div></div>

    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 id="privacyTitle" class="modal-title fs-4">Thông tin dữ liệu</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body"><p id="privacyDataText">Thông tin trong biểu mẫu được gửi về website và lưu thành yêu cầu liên hệ để CamKheTravel xử lý. Việc gửi yêu cầu chưa xác nhận chuyến xe, lịch trình hoặc giá.</p><p>Ảnh mặc định được sử dụng tại các vị trí chưa có ảnh nội dung.</p><p>Quản trị viên cần cập nhật thông tin liên hệ và chính sách dữ liệu áp dụng cho hoạt động thực tế.</p></div></div></div></div>
@endsection
