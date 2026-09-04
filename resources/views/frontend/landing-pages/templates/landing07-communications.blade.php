@use(App\Support\Landing\CommunicationsLandingContent)
@use(App\Support\Localization\LocalizedUrl)

@php
    $source = is_array($landingTemplateSettings['communications_source'] ?? null)
        ? $landingTemplateSettings['communications_source']
        : CommunicationsLandingContent::source();
    $hero = (array) ($source['hero'] ?? []);
    $positioning = (array) ($source['positioning'] ?? []);
    $solutions = (array) ($source['solutions'] ?? []);
    $showcase = (array) ($source['showcase'] ?? []);
    $gallery = (array) ($source['gallery'] ?? []);
    $process = (array) ($source['process'] ?? []);
    $strategy = (array) ($source['strategy'] ?? []);
    $clients = (array) ($source['faq'] ?? []);
    $contact = (array) ($source['contact_section'] ?? []);
    $pricing = (array) ($source['pricing'] ?? []);
    $pricingSections = collect($pricing['pricing_sections'] ?? [])
        ->filter(fn (mixed $section): bool => is_array($section) && ($section['status'] ?? true))
        ->values();
    $asset = static function (mixed $path): ?string {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = ltrim($path, '/');
        $prefix = 'giaiphaptruyenthongdoanhnghiep/';
        $relative = str_starts_with($path, $prefix) ? substr($path, strlen($prefix)) : $path;

        return asset('storage/media/landing-07/giaiphaptruyenthongdoanhnghiep/'.str_replace(' ', '%20', $relative));
    };
    $money = static fn (mixed $value): string => number_format((float) $value, 0, ',', '.').'đ';
    $featureValue = static function (mixed $value): string {
        if ($value === true) {
            return 'Đã bao gồm';
        }

        if ($value === false || $value === null || $value === '') {
            return '';
        }

        if (! is_array($value)) {
            return (string) $value;
        }

        $labels = ['total_posts' => 'bài', 'image_posts' => 'bài ảnh', 'videos' => 'video', 'keywords' => 'từ khóa', 'posts' => 'bài'];

        return collect($value)
            ->filter(fn (mixed $item): bool => is_scalar($item))
            ->map(fn (mixed $item, string|int $key): string => trim((string) $item.' '.($labels[$key] ?? '')))
            ->implode(' · ');
    };
    $heroTitle = preg_replace('#<br\s*/?>#i', "\n", (string) ($hero['title'] ?? $landingPage->title));
    $phone = trim((string) ($website->hotline ?: $website->contact_phone));
    $phoneUrl = preg_replace('/\s+/', '', $phone);
    $logoUrl = $websiteMediaUrls[$website->logo_media_id] ?? null;
    $heroImage = $asset($hero['image'] ?? null);
    $firstPricingId = (string) ($pricingSections->first()['id'] ?? 'facebook');
@endphp

<div
    class="landing-page landing-page--landing07-communications"
    data-landing-page
    data-landing-id="{{ $landingPage->id }}"
    data-track-endpoint="{{ $landingTrackingUrl }}"
    data-campaign-state="{{ $landingCampaignState }}"
    data-template-source="landing07-giaiphaptruyenthongdoanhnghiep"
    style="--landing-primary: {{ $landingTheme['primary'] ?? '#12372a' }}; --landing-accent: {{ $landingTheme['accent'] ?? '#d8a84e' }}; --landing-surface: {{ $landingTheme['surface'] ?? '#f4f7ef' }}; --landing-ink: {{ $landingTheme['ink'] ?? '#14251d' }}"
>
    <header class="communications-nav" data-communications-nav>
        <div class="communications-shell communications-nav__inner">
            <a class="communications-brand" href="{{ LocalizedUrl::route('home') }}" aria-label="Về trang chủ THT Media">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="" aria-hidden="true">
                @endif
                <span>{{ $landingTemplateSettings['landing07_brand_label'] ?? 'THT MEDIA' }}</span>
            </a>
            <nav class="communications-nav__links" aria-label="Điều hướng landing giải pháp truyền thông">
                @foreach ((array) ($source['navigation'] ?? []) as $item)
                    <a href="{{ $item['url'] ?? '#' }}">{{ $item['label'] ?? '' }}</a>
                @endforeach
                <a class="communications-nav__cta" href="#tu-van" data-landing-event="cta_click" data-block-id="nav">{{ $landingTemplateSettings['landing07_nav_cta'] ?? 'Nhận tư vấn' }} <span aria-hidden="true">↗</span></a>
            </nav>
            <button class="communications-nav__toggle" type="button" aria-expanded="false" aria-controls="communications-mobile-nav" data-communications-menu>
                <span class="sr-only">Mở menu</span><i aria-hidden="true"></i><i aria-hidden="true"></i>
            </button>
        </div>
        <nav class="communications-mobile-nav" id="communications-mobile-nav" aria-label="Điều hướng landing mobile" hidden>
            @foreach ((array) ($source['navigation'] ?? []) as $item)
                <a href="{{ $item['url'] ?? '#' }}">{{ $item['label'] ?? '' }}</a>
            @endforeach
            <a class="communications-nav__cta" href="#tu-van">{{ $landingTemplateSettings['landing07_nav_cta'] ?? 'Nhận tư vấn' }} <span aria-hidden="true">↗</span></a>
        </nav>
    </header>

    @include('frontend.landing-pages.partials.campaign-notice')

    <main>
        <section class="communications-hero" id="hero" data-landing-block="hero">
            <div class="communications-shell communications-hero__grid">
                <div class="communications-hero__copy" data-aos="fade-up">
                    <p class="communications-eyebrow">{{ $hero['eyebrow'] ?? 'GIẢI PHÁP TRUYỀN THÔNG DOANH NGHIỆP' }}</p>
                    <h1>{!! nl2br(e($heroTitle ?: $landingPage->title)) !!}</h1>
                    <p class="communications-hero__summary">{{ $hero['summary'] ?? '' }}</p>
                    <div class="communications-actions">
                        <a class="communications-button communications-button--primary" href="#tu-van" data-landing-event="cta_click" data-block-id="hero">{{ $hero['primary'] ?? 'Đăng ký tư vấn miễn phí' }} <span aria-hidden="true">↗</span></a>
                        <a class="communications-button communications-button--ghost" href="#giai-phap">{{ $hero['secondary'] ?? 'Xem giải pháp' }} <span aria-hidden="true">↓</span></a>
                    </div>
                    <div class="communications-hero__tags" aria-label="Các nhóm giải pháp">
                        @foreach ((array) ($hero['cung_cap'] ?? []) as $tag)
                            <span><b aria-hidden="true">✓</b>{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="communications-hero__visual" data-aos="fade-left" data-aos-delay="100">
                    <div class="communications-hero__orb" aria-hidden="true"></div>
                    @if ($heroImage)
                        <img src="{{ $heroImage }}" alt="{{ $hero['image_alt'] ?? $landingPage->title }}" width="800" height="800" loading="eager" fetchpriority="high">
                    @endif
                    @foreach ((array) ($hero['cung_cap'] ?? []) as $index => $tag)
                        <span class="communications-hero__float communications-hero__float--{{ $index }}"><b aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</b>{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            <div class="communications-hero__caption communications-shell"><span>01 / COMMUNICATIONS</span><span>Chiến lược · Nội dung · Media · Quảng cáo</span></div>
        </section>

        <section class="communications-section communications-pain" id="diem-nghen" aria-labelledby="communications-pain-title">
            <div class="communications-shell">
                <div class="communications-section-heading communications-section-heading--center" data-aos="fade-up">
                    <p class="communications-eyebrow">{{ $positioning['eyebrow'] ?? 'Thách thức doanh nghiệp' }}</p>
                    <h2 id="communications-pain-title">{!! $positioning['title'] ?? 'Truyền thông đang là "điểm nghẽn" của doanh nghiệp?' !!}</h2>
                    <p>{{ $positioning['body'] ?? '' }}</p>
                </div>
                <div class="communications-pain__grid">
                    <figure data-aos="fade-up">
                        @if ($image = $asset($positioning['image'] ?? null))
                            <img src="{{ $image }}" alt="Khó khăn trong phân tích marketing doanh nghiệp" width="1200" height="800" loading="lazy">
                        @endif
                        <figcaption>Nhìn rõ điểm nghẽn trước khi tăng ngân sách.</figcaption>
                    </figure>
                    <div class="communications-pain__list" data-aos="fade-up" data-aos-delay="100">
                        @foreach ((array) ($positioning['items'] ?? []) as $index => $item)
                            <div><span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><p>{{ $item }}</p></div>
                        @endforeach
                    </div>
                </div>
                <p class="communications-pain__footer">{{ $positioning['footer'] ?? '' }}</p>
            </div>
        </section>

        <section class="communications-section communications-solutions" id="giai-phap" aria-labelledby="communications-solutions-title">
            <div class="communications-shell">
                <div class="communications-section-heading" data-aos="fade-up">
                    <p class="communications-eyebrow">{{ $solutions['eyebrow'] ?? 'THT MEDIA SOLUTIONS' }}</p>
                    <h2 id="communications-solutions-title">{{ $solutions['title'] ?? 'GIẢI PHÁP TỪ THT MEDIA' }}</h2>
                    <p>{{ $solutions['body'] ?? '' }}</p>
                </div>
                <div class="communications-solutions__layout" data-communications-solutions>
                    <div class="communications-solutions__tabs" role="tablist" aria-label="Các nhóm giải pháp">
                        @foreach ((array) ($solutions['items'] ?? []) as $index => $item)
                            <button type="button" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}" data-solution-tab="{{ $index }}" class="{{ $index === 0 ? 'is-active' : '' }}">
                                <span>{{ $item['title'] ?? '' }}</span><b aria-hidden="true">↗</b>
                            </button>
                        @endforeach
                    </div>
                    <div class="communications-solutions__panels">
                        @foreach ((array) ($solutions['items'] ?? []) as $index => $item)
                            <article class="communications-solution-panel {{ $index === 0 ? 'is-active' : '' }}" role="tabpanel" data-solution-panel="{{ $index }}" @if ($index !== 0) hidden @endif>
                                @if ($image = $asset($item['bg_image'] ?? null))<img src="{{ $image }}" alt="" aria-hidden="true" loading="lazy">@endif
                                <div class="communications-solution-panel__shade"></div>
                                <div class="communications-solution-panel__content"><span>0{{ $index + 1 }}</span><h3>{{ $item['title'] ?? '' }}</h3><ul>@foreach ((array) ($item['details'] ?? []) as $detail)<li><b aria-hidden="true">✓</b>{{ $detail }}</li>@endforeach</ul></div>
                            </article>
                        @endforeach
                    </div>
                </div>
                <a class="communications-button communications-button--light" href="#tu-van" data-landing-event="cta_click" data-block-id="solutions">{{ $solutions['cta'] ?? 'Nhận giải pháp phù hợp cho doanh nghiệp' }} <span aria-hidden="true">↗</span></a>
            </div>
        </section>

        <section class="communications-section communications-pricing" id="bang-gia" aria-labelledby="communications-pricing-title" data-landing-block="pricing" data-communications-pricing data-default-pricing="{{ $firstPricingId }}">
            <div class="communications-shell">
                <div class="communications-section-heading communications-section-heading--split" data-aos="fade-up">
                    <div><p class="communications-eyebrow">{{ $pricing['eyebrow'] ?? 'Chi phí minh bạch · Lộ trình rõ ràng' }}</p><h2 id="communications-pricing-title">{{ $pricing['title'] ?? 'Bảng giá dịch vụ truyền thông' }}</h2></div>
                    <p>{{ $pricing['description'] ?? '' }}</p>
                </div>
                <div class="communications-pricing__shell" data-aos="fade-up">
                    <div class="communications-pricing__tabs" role="tablist" aria-label="Nhóm dịch vụ">
                        @foreach ($pricingSections as $section)
                            <button type="button" role="tab" aria-selected="{{ (string) ($section['id'] ?? '') === $firstPricingId ? 'true' : 'false' }}" class="{{ (string) ($section['id'] ?? '') === $firstPricingId ? 'is-active' : '' }}" data-pricing-tab="{{ $section['id'] ?? '' }}">{{ $section['name'] ?? 'Dịch vụ' }}</button>
                        @endforeach
                    </div>
                    <div class="communications-pricing__panels">
                        @foreach ($pricingSections as $section)
                            @php($sectionId = (string) ($section['id'] ?? ''))
                            <div class="communications-pricing__panel {{ $sectionId === $firstPricingId ? 'is-active' : '' }}" data-pricing-panel="{{ $sectionId }}" @if ($sectionId !== $firstPricingId) hidden @endif>
                                <p class="communications-pricing__description">{{ $section['description'] ?? '' }}</p>
                                @if (! empty($section['packages']))
                                    <div class="communications-price-grid">
                                        @foreach ($section['packages'] as $packageIndex => $package)
                                            <article class="communications-price-card {{ filled($package['badge'] ?? null) ? 'is-featured' : '' }}">
                                                <header><span>Gói {{ str_pad((string) ($packageIndex + 1), 2, '0', STR_PAD_LEFT) }}</span>@if (filled($package['badge'] ?? null))<b>{{ $package['badge'] }}</b>@endif<h3>{{ $package['name'] ?? '' }}</h3></header>
                                                @if (filled($package['description'] ?? null))<p class="communications-price-card__summary">{{ $package['description'] }}</p>@endif
                                                @if (isset($package['price']) || filled($package['price_label'] ?? null))<p class="communications-price-card__amount"><strong>{{ $package['price_label'] ?? $money($package['price'] ?? 0) }}</strong><small>{{ $package['billing_label'] ?? '' }}</small></p>@endif
                                                @if (! empty($package['pricing']))<div class="communications-price-card__tiers">@foreach ($package['pricing'] as $tier)<span>{{ $tier['package_name'] ?? '' }} <strong>{{ $money($tier['price_per_video'] ?? 0) }}<small>/video</small></strong></span>@endforeach</div>@endif
                                                <ul>@foreach (($package['features'] ?? $package['includes'] ?? []) as $feature)<li><b aria-hidden="true">✓</b><span>{{ is_array($feature) ? ($feature['label'] ?? '') : $feature }}@if (is_array($feature) && filled($featureValue($feature['value'] ?? null)))<small>{{ $featureValue($feature['value'] ?? null) }}</small>@endif</span></li>@endforeach</ul>
                                                <a href="#tu-van" data-landing-event="cta_click" data-block-id="pricing">Đăng ký tư vấn <span aria-hidden="true">↗</span></a>
                                            </article>
                                        @endforeach
                                    </div>
                                @elseif (! empty($section['services']))
                                    <div class="communications-price-grid communications-price-grid--services">
                                        @foreach ($section['services'] as $service)
                                            <article class="communications-price-card"><header><span>Dịch vụ</span><h3>{{ $service['name'] ?? '' }}</h3></header><p class="communications-price-card__amount"><strong>{{ $service['price_label'] ?? $money($service['price'] ?? 0) }}</strong><small>{{ $service['unit'] ?? '' }}</small></p><div class="communications-price-card__meta">@if (filled($service['duration'] ?? null))<span>Thời lượng {{ $service['duration'] }}</span>@endif @if (filled($service['deadline_days'] ?? null))<span>{{ $service['deadline_days'] }} ngày</span>@endif @if (filled($service['minimum_quantity'] ?? null))<span>Tối thiểu {{ $service['minimum_quantity'] }} từ khóa</span>@endif</div><ul>@foreach ($service['includes'] ?? [] as $item)<li><b aria-hidden="true">✓</b>{{ $item }}</li>@endforeach</ul>@if (! empty($service['tiered_pricing'][0]))<p class="communications-price-card__note">Từ {{ $service['tiered_pricing'][0]['from_quantity'] }}: <strong>{{ $money($service['tiered_pricing'][0]['price'] ?? 0) }}{{ $service['tiered_pricing'][0]['unit'] ?? '' }}</strong></p>@elseif (filled($service['note'] ?? null) || filled($service['guarantee'] ?? null))<p class="communications-price-card__note">{{ $service['note'] ?? $service['guarantee'] }}</p>@endif<a href="#tu-van">Đăng ký tư vấn <span aria-hidden="true">↗</span></a></article>
                                        @endforeach
                                    </div>
                                @elseif (! empty($section['management_fee_rules']))
                                    <div class="communications-fee-table"><div class="communications-fee-table__head"><span>Ngân sách quảng cáo</span><span>Phí quản lý</span></div>@foreach ($section['management_fee_rules'] as $rule)<div><span>{{ $rule['budget_to'] === null ? 'Từ '.$money($rule['budget_from'] ?? 0) : (($rule['budget_from'] ?? 0) <= 0 ? 'Dưới '.$money($rule['budget_to']) : $money($rule['budget_from']).' – '.$money($rule['budget_to'])) }}</span><strong>{{ ($rule['fee_type'] ?? '') === 'percentage' ? ($rule['fee_value'] ?? 0).'%' : $money($rule['fee_value'] ?? 0) }}</strong></div>@endforeach</div>
                                    <p class="communications-pricing__note">Tối đa 3 chiến dịch mỗi tháng. Từ chiến dịch thứ {{ $section['extra_campaign_fee']['from_campaign_number'] ?? 4 }}, cộng {{ $money($section['extra_campaign_fee']['fee_per_campaign'] ?? 0) }}/chiến dịch. Báo cáo qua Zalo và Google Sheet.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @if (! empty($pricing['general_notes']))<ul class="communications-pricing__notes">@foreach ($pricing['general_notes'] as $note)<li>{{ $note }}</li>@endforeach</ul>@endif
            </div>
        </section>

        <section class="communications-section communications-showcase" id="du-an" aria-labelledby="communications-showcase-title">
            <div class="communications-shell">
                <div class="communications-section-heading communications-section-heading--center" data-aos="fade-up"><p class="communications-eyebrow">{{ $showcase['eyebrow'] ?? 'CASE STUDY' }}</p><h2 id="communications-showcase-title">{{ $showcase['title'] ?? 'Những dự án tiêu biểu' }}</h2></div>
                <div class="communications-case-grid">
                    @foreach ((array) ($showcase['items'] ?? []) as $index => $item)
                        <details class="communications-case-card" data-aos="fade-up" data-aos-delay="{{ min($index * 45, 180) }}">
                            <summary>
                                @if ($image = $asset($item['image'] ?? null))<img src="{{ $image }}" alt="{{ $item['image_alt'] ?? $item['title'] ?? '' }}" width="800" height="600" loading="lazy">@endif
                                <span class="communications-case-card__number">DỰ ÁN {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $item['title'] ?? '' }}</h3><span class="communications-case-card__more">Xem chi tiết <b aria-hidden="true">↗</b></span>
                            </summary>
                            <div class="communications-case-card__body"><dl><div><dt>Bài toán</dt><dd>{{ $item['problem'] ?? '' }}</dd></div><div><dt>Giải pháp</dt><dd>{{ $item['solution'] ?? '' }}</dd></div><div><dt>Kết quả</dt><dd>{{ $item['result'] ?? '' }}</dd></div></dl></div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="communications-section communications-gallery" id="hinh-anh-thuc-te" aria-labelledby="communications-gallery-title">
            <div class="communications-shell">
                <div class="communications-section-heading communications-section-heading--split" data-aos="fade-up"><div><p class="communications-eyebrow">{{ $gallery['eyebrow'] ?? 'HÌNH ẢNH THỰC TẾ' }}</p><h2 id="communications-gallery-title">{{ $gallery['title'] ?? 'THT MEDIA TRONG TỪNG DỰ ÁN' }}</h2></div><p>{{ $gallery['body'] ?? '' }}</p></div>
                <div class="communications-gallery__grid">@foreach ((array) ($gallery['items'] ?? []) as $index => $item) @if ($image = $asset($item['image'] ?? null))<button type="button" data-gallery-image="{{ $image }}" data-gallery-title="{{ $item['title'] ?? '' }}" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 50 }}"><img src="{{ $image }}" alt="{{ $item['alt'] ?? $item['title'] ?? '' }}" width="{{ $item['width'] ?? 1200 }}" height="{{ $item['height'] ?? 800 }}" loading="lazy"><span>{{ $item['title'] ?? '' }}</span></button>@endif @endforeach</div>
            </div>
        </section>

        <section class="communications-section communications-process" id="quy-trinh" aria-labelledby="communications-process-title">
            <div class="communications-shell"><div class="communications-section-heading communications-section-heading--center" data-aos="fade-up"><p class="communications-eyebrow">Quy trình triển khai</p><h2 id="communications-process-title">{{ $process['title'] ?? 'QUY TRÌNH TRIỂN KHAI' }}</h2></div><div class="communications-process__grid">@foreach ((array) ($process['items'] ?? []) as $index => $item)<article data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 60 }}"><span>{{ $item['step'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $item['title'] ?? '' }}</h3><p>{{ $item['text'] ?? '' }}</p></article>@endforeach</div></div>
        </section>

        <section class="communications-section communications-strategy" aria-labelledby="communications-strategy-title">
            <div class="communications-shell"><div class="communications-section-heading communications-section-heading--center" data-aos="fade-up"><p class="communications-eyebrow">Giá trị cốt lõi</p><h2 id="communications-strategy-title">{{ $strategy['title'] ?? 'VÌ SAO DOANH NGHIỆP LỰA CHỌN THT MEDIA?' }}</h2></div><div class="communications-strategy__grid">@foreach ((array) ($strategy['items'] ?? []) as $index => $item)<article data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 60 }}"><span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $item['title'] ?? '' }}</h3><p>{{ $item['text'] ?? '' }}</p></article>@endforeach</div></div>
        </section>

        <section class="communications-section communications-clients" id="khach-hang" aria-labelledby="communications-clients-title">
            <div class="communications-shell"><div class="communications-section-heading communications-section-heading--center" data-aos="fade-up"><p class="communications-eyebrow">Khách hàng đã đồng hành cùng THT Media</p><h2 id="communications-clients-title">{{ $clients['title'] ?? '' }}</h2></div><div class="communications-client-logos">@foreach ((array) ($clients['logos'] ?? []) as $logo) @if ($image = $asset($logo))<img src="{{ $image }}" alt="Đối tác của THT Media" loading="lazy">@endif @endforeach</div><div class="communications-testimonials">@foreach ((array) ($clients['testimonials'] ?? []) as $item)<blockquote data-aos="fade-up"><span class="communications-testimonial__quote">“</span><p>{{ $item['quote'] ?? '' }}</p><footer><strong>{{ $item['name'] ?? '' }}</strong><span>{{ $item['role'] ?? '' }}</span></footer></blockquote>@endforeach</div></div>
        </section>

        <span id="lien-he" class="communications-anchor" aria-hidden="true"></span>
        <div class="communications-contact"><x-service-consultation-form :content="$landingPage" :heading="$contact['title'] ?? 'Đăng ký tư vấn'" :description="$contact['body'] ?? null" :button-label="$contact['cta'] ?? 'Nhận tư vấn miễn phí'" block-id="lien-he" /></div>
    </main>

    <footer class="communications-footer"><div class="communications-shell"><strong>{{ $landingTemplateSettings['landing07_brand_label'] ?? 'THT MEDIA' }}</strong><span>{{ $landingTemplateSettings['landing07_footer_text'] ?? 'THT Media · Đồng hành từ chiến lược đến triển khai' }}</span><div>@if ($phone)<a href="tel:{{ $phoneUrl }}">{{ $phone }}</a>@endif @if ($website->contact_email)<a href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a>@endif</div></div></footer>

    <dialog class="communications-lightbox" data-gallery-dialog aria-label="Xem ảnh gallery"><button type="button" data-gallery-close aria-label="Đóng">×</button><img data-gallery-dialog-image src="" alt=""><p data-gallery-dialog-title></p></dialog>
</div>
