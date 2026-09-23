@extends('layouts.master')

@push('styles')
    @vite('resources/css/pages/home.css')
@endpush

@section('body_class', 'camkhetravel-home')
@section('main_id', 'camkhe-noi-dung')
@section('main_class', 'camkhe-home-main')

@section('content')
    <div class="camkhe-home" data-camkhe-home style="--camkhe-no-image: url('{{ $noImageUrl }}')">
        <div hidden data-camkhe-config
            data-phone="{{ $frontendConfig['phone'] }}"
            data-zalo-url="{{ $frontendConfig['zaloUrl'] }}"
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
                            {{ $heroSlide?->primary_label ?: 'Nhận báo giá chuyến đi' }} <x-site-icon name="arrow" />
                        </button>
                        <a class="btn btn-zalo" href="{{ $heroSlide?->secondary_url ?: '/#dich-vu' }}">{{ $heroSlide?->secondary_label ?: 'Xem dịch vụ' }} <x-site-icon name="arrow" /></a>
                    </div>
                    <div class="hero-trust">
                        <span><x-site-icon name="shield" /> Trao đổi rõ ràng</span>
                        <span><x-site-icon name="users" /> Theo nhu cầu</span>
                        <span><x-site-icon name="clock" /> Chủ động lịch trình</span>
                        <span><x-site-icon name="pin" /> Điểm đón trao đổi trước</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="container booking-wrap">
            <form class="quick-quote" id="quickQuote" aria-label="Yêu cầu báo giá nhanh">
                <div class="quick-field"><x-site-icon name="pin" /><div><label for="quickPickup">Điểm đón</label><input id="quickPickup" name="pickup" placeholder="Nhập địa chỉ đón" maxlength="180" autocomplete="off"></div></div>
                <div class="quick-field"><x-site-icon name="pin" /><div><label for="quickDestination">Điểm đến</label><input id="quickDestination" name="destination" placeholder="Bạn muốn đi đâu?" maxlength="180" autocomplete="off"></div></div>
                <div class="quick-field"><x-site-icon name="calendar" /><div><label for="quickDate">Ngày đi</label><input id="quickDate" name="departure" type="date" aria-label="Ngày khởi hành"></div></div>
                <div class="quick-field"><x-site-icon name="users" /><div><label for="quickPassengers">Số khách</label><select id="quickPassengers" name="passengers"><option value="">Chọn số khách</option><option value="1–4">1–4 khách</option><option value="5–6">5–6 khách</option><option value="7–15">7–15 khách</option><option value="Cần tư vấn thêm">Cần tư vấn thêm</option></select></div></div>
                <button type="submit" class="btn btn-brand">Yêu cầu báo giá <x-site-icon name="arrow" /></button>
            </form>
        </div>

        <section class="section services-section" id="dich-vu" aria-labelledby="services-title">
            <div class="container">
                <div class="section-heading"><h2 id="services-title">Dịch vụ của CamKheTravel</h2><p>Chọn dịch vụ phù hợp với hành trình <x-site-icon name="arrow" /></p></div>
                <div class="services-grid">
                    @forelse ($services as $service)
                        <article class="service-card">
                            <img src="{{ $service->image_url }}" alt="{{ $service->title }}" width="650" height="390" loading="lazy" decoding="async">
                            <div class="service-card-body">
                                <span class="icon-bubble {{ $service->quote_type === 'wedding' ? 'rose' : ($service->quote_type === 'shared' ? 'sand' : '') }}"><x-site-icon :name="$service->quote_icon" /></span>
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
                <div class="section-heading"><h2 id="fleet-title">Lựa chọn xe cho hành trình</h2><button type="button" class="text-link" data-quote-type="trip">Tư vấn chọn xe <x-site-icon name="arrow" /></button></div>
                <div class="fleet-grid">
                    @foreach ($fleetTypes as $vehicle)
                        <article class="fleet-card">
                            <div class="fleet-image"><img src="{{ $noImageUrl }}" alt="{{ $vehicle['title'] }} – ảnh mặc định" width="700" height="500" loading="lazy" decoding="async"></div>
                            <div class="fleet-content">
                                <h3>{{ $vehicle['title'] }}</h3>
                                @foreach ($vehicle['features'] as $feature)
                                    <p><x-site-icon name="check" />{{ $feature }}</p>
                                @endforeach
                                <button type="button" class="btn btn-outline-brand btn-sm" data-vehicle="{{ $vehicle['code'] }}" data-vehicle-name="{{ $vehicle['title'] }}" data-vehicle-description="{{ $vehicle['features']->implode('. ') }}" data-vehicle-image="{{ $noImageUrl }}">Xem chi tiết <x-site-icon name="arrow" /></button>
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
                        <div class="partner-actions"><button type="button" class="btn btn-brand" data-quote-type="partner">Đăng ký đối tác <x-site-icon name="arrow" /></button><button type="button" class="btn btn-outline-brand" data-quote-type="partner">Nhận báo giá hợp tác <x-site-icon name="arrow" /></button></div>
                    </div>
                    <p class="signature partner-signature">Cùng đối tác<br>tạo nên những hành trình<br>trọn vẹn.</p>
                </div>
            </div>
            <div class="container partner-bottom">
                <div class="partner-benefits">
                    @foreach ($homepage->partner_benefits as $benefit)
                        <div class="partner-benefit"><span class="icon-bubble"><x-site-icon name="handshake" /></span><div><h3>{{ $benefit['title'] }}</h3><p>{{ $benefit['description'] }}</p></div></div>
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
                                <li><span class="step-icon"><x-site-icon :name="$loop->first ? 'file' : ($loop->last ? 'car' : 'chat')" /><b>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</b></span><h4>{{ $step['title'] }}</h4><p>{{ $step['description'] }}</p></li>
                            @endforeach
                        </ol>
                        <button type="button" class="btn btn-brand" data-quote-type="partner">Trở thành đối tác <x-site-icon name="arrow" /></button>
                    </div>
                    <aside class="partner-quote"><p class="signature">Hành trình<br>tốt đẹp hơn<br>khi có người<br>đồng hành.</p><span>CAMKHETRAVEL</span></aside>
                </div>
            </div>
        </section>

        <section class="wedding-section" id="xe-cuoi" aria-labelledby="wedding-title">
            <picture class="wedding-picture"><source media="(max-width: 767px)" srcset="{{ $noImageUrl }}"><img src="{{ $noImageUrl }}" alt="Ảnh mặc định" width="1800" height="567" loading="lazy" decoding="async"></picture>
            <div class="container wedding-inner"><div class="wedding-copy"><h2 id="wedding-title">Xe cưới Phú Thọ</h2><p class="wedding-subtitle">Đồng hành trong ngày trọng đại.</p><p class="wedding-models">Xe dâu <span>•</span> Đưa đón gia đình</p><p class="wedding-description">Lịch trình và yêu cầu xe được trao đổi theo kế hoạch của gia đình.</p><button type="button" class="btn btn-brand" data-quote-type="wedding">Tư vấn dịch vụ xe cưới <x-site-icon name="arrow" /></button></div><p class="signature wedding-signature">Hạnh phúc<br>bắt đầu từ<br>những hành trình đẹp.</p></div>
        </section>

        <section class="section commitments-section" id="cam-ket" aria-labelledby="commitments-title">
            <div class="container"><div class="section-heading"><h2 id="commitments-title">CamKheTravel đồng hành cùng hành trình</h2></div><div class="commitments-grid">
                @foreach ($homepage->commitment_items as $commitment)
                    <article class="commitment"><span class="icon-bubble"><x-site-icon :name="$loop->first ? 'file' : ($loop->last ? 'headset' : ($loop->iteration === 2 ? 'car' : 'clock'))" /></span><div><h3>{{ $commitment['title'] }}</h3><p>{{ $commitment['description'] }}</p></div></article>
                @endforeach
            </div></div>
        </section>

        @if ($testimonials->isNotEmpty())
            <section class="section reviews-section" id="danh-gia" aria-labelledby="reviews-title">
                <div class="container"><div class="section-heading"><h2 id="reviews-title">Khách hàng nói về CamKheTravel</h2>@if ($hasIllustrativeTestimonials)<span class="demo-label">Có phản hồi minh họa</span>@endif</div><div class="reviews-grid">
                    @foreach ($testimonials as $testimonial)
                        <article class="review-card">
                            <div class="review-stars" aria-label="Đánh giá {{ $testimonial->rating ?? 0 }} trên 5 sao">
                                @for ($star = 1; $star <= 5; $star++)<x-site-icon name="star" class="{{ $star <= (int) ($testimonial->rating ?? 0) ? 'is-active' : '' }}" />@endfor
                                @if ($testimonial->is_illustrative)<span>Minh họa</span>@endif
                            </div>
                            <blockquote>“{{ $testimonial->quote }}”</blockquote>
                            <div class="review-person"><span class="avatar">{{ mb_substr($testimonial->client_name, 0, 2) }}</span><div><h3>{{ $testimonial->client_name }}</h3><p>{{ $testimonial->client_role ?: $testimonial->company_name }}</p></div></div>
                        </article>
                    @endforeach
                </div></div>
            </section>
        @endif

        <section class="section journal-section" id="tin-tuc" aria-labelledby="journal-title">
            <div class="container">
                <div class="section-heading">
                    <h2 id="journal-title">Tin tức & kinh nghiệm hành trình</h2>
                    <a class="text-link" href="{{ route('posts.index') }}">Xem tất cả bài viết <x-site-icon name="arrow" /></a>
                </div>
                <div class="journal-grid">
                    @forelse ($latestPosts as $post)
                        @include('frontend.partials.post-card', ['showExcerpt' => true])
                    @empty
                        <p class="empty-state">Bài viết đang được cập nhật. Anh/chị có thể xem dịch vụ và gửi lịch trình để được tư vấn.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="contact-banner" id="lien-he" aria-labelledby="contact-title">
            <div class="container contact-inner"><p class="signature contact-signature">Cẩm Khê, Phú Thọ,<br>hẹn bạn trên hành trình.</p><div class="contact-main"><h2 id="contact-title">{{ $homepage->consultation_title ?: 'Bạn cần phương tiện cho chuyến đi sắp tới?' }}</h2><p>{{ $homepage->consultation_content ?: 'Gửi lịch trình để CamKheTravel tư vấn phương án phù hợp.' }}</p><div class="contact-actions"><button type="button" class="btn btn-brand" data-quote-type="trip">Nhận tư vấn <x-site-icon name="arrow" /></button><button type="button" class="btn btn-white" data-contact="phone"><x-site-icon name="phone" /> Liên hệ</button></div><button type="button" class="text-link mx-auto mt-2" data-bs-toggle="modal" data-bs-target="#privacyModal">Thông tin dữ liệu</button></div><p class="signature contact-signature right">CamKheTravel,<br>đồng hành cùng chuyến đi.</p></div>
        </section>
    </div>
@endsection

@section('after_footer')
    <div class="camkhe-home-dialogs">
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
            <div class="form-submit"><button type="button" class="btn btn-soft" data-bs-dismiss="modal">Để sau</button><button type="submit" class="btn btn-brand" id="requestSubmit"><span data-submit-label>Gửi yêu cầu tư vấn</span> <x-site-icon name="arrow" /></button></div>
        </form>
        <div id="requestResult" hidden aria-live="polite"><span class="result-icon"><x-site-icon name="check" /></span><h3 id="resultTitle">Đã gửi yêu cầu tư vấn</h3><p id="resultDescription">Hệ thống đã tiếp nhận yêu cầu. Đây chưa phải xác nhận đặt xe; CamKheTravel cần liên hệ để thống nhất lịch trình và chi phí.</p><label class="form-label" for="requestSummary">Nội dung yêu cầu</label><textarea id="requestSummary" class="form-control request-summary" rows="9" readonly></textarea><div class="result-actions"><button type="button" class="btn btn-brand" id="copySummary"><x-site-icon name="copy" /> Sao chép nội dung</button><button type="button" class="btn btn-outline-brand" id="editRequest">Chỉnh sửa yêu cầu</button></div><p id="copyStatus" class="form-hint mt-3" role="status"></p></div>
    </div></div></div></div>

    <div class="modal fade" id="vehicleModal" tabindex="-1" aria-labelledby="vehicleTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title fs-4" id="vehicleTitle">Thông tin xe</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body"><img id="vehicleDetailImage" class="vehicle-detail-image" src="{{ $noImageUrl }}" alt="Ảnh xe mặc định" width="700" height="500"><p id="vehicleDetailDescription"></p><p class="form-hint">Ảnh mặc định. Dòng xe thực tế, sức chứa và hành lý sẽ được xác nhận khi trao đổi lịch trình.</p><button type="button" class="btn btn-brand w-100" id="quoteVehicle">Nhận báo giá xe này <x-site-icon name="arrow" /></button></div></div></div></div>

    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 id="privacyTitle" class="modal-title fs-4">Thông tin dữ liệu</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body"><p id="privacyDataText">Thông tin trong biểu mẫu được gửi về website và lưu thành yêu cầu liên hệ để CamKheTravel xử lý. Việc gửi yêu cầu chưa xác nhận chuyến xe, lịch trình hoặc giá.</p><p>Ảnh mặc định được sử dụng tại các vị trí chưa có ảnh nội dung.</p><p>Quản trị viên cần cập nhật thông tin liên hệ và chính sách dữ liệu áp dụng cho hoạt động thực tế.</p></div></div></div></div>
    </div>
@endsection
