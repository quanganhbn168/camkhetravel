<section class="branding-hero" aria-labelledby="branding-hero-title">
    <div class="branding-hero-art" aria-hidden="true">
        <img src="{{ $content['hero']['image'] }}" alt="" width="1024" height="1536" fetchpriority="high" decoding="async">
        <p class="branding-art-quote">“Thương hiệu hôm nay<br>Giá trị ngày mai”</p>
    </div>
    <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative branding-hero-inner">
        <div class="branding-hero-copy">
            <p class="branding-stage">{{ $content['hero']['eyebrow'] }}</p>
            <h1 id="branding-hero-title" class="branding-multiline">{{ $content['hero']['title'] }}</h1>
            <p class="branding-hero-subtitle">{{ $content['hero']['subtitle'] }}</p>
            <p class="branding-hero-description">{{ $content['hero']['description'] }}</p>
            <div class="branding-hero-pricing">
                <div class="branding-old-price"><span>TỔNG GIÁ TRỊ</span><s>{{ number_format($content['offer']['original_price'], 0, ',', '.') }} VNĐ</s></div>
                <x-landing.branding-icon class="branding-price-arrow" />
                @include('frontend.landing.parts.branding.offer-price', ['offer' => $content['offer']])
            </div>
            <div class="branding-hero-actions flex flex-wrap gap-4">
                <a class="branding-button" href="#lien-he" data-landing-event="cta_click" data-block-id="hero">Nhận tư vấn ngay <x-landing.branding-icon /></a>
                <a class="branding-button branding-button-outline" href="#du-an">Xem dự án thực tế <x-landing.branding-icon name="play" /></a>
            </div>
            <ul class="branding-hero-benefits">
                @foreach ($content['hero']['benefits'] as $benefit)
                    <li><x-landing.branding-icon :name="$benefit['icon']" /><span>{{ $benefit['label'] }}</span></li>
                @endforeach
            </ul>
        </div>
        <div id="gioi-thieu" class="branding-intro">
            <p class="branding-kicker">{{ $content['intro']['eyebrow'] }}</p>
            <h2 class="branding-multiline">{{ $content['intro']['title'] }}</h2>
            <p>{{ $content['intro']['description'] }}</p>
            <a href="#dich-vu" class="branding-button">Khám phá giải pháp <x-landing.branding-icon /></a>
        </div>
    </div>
</section>

<section id="dich-vu" class="branding-section branding-services">
    <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative">
        <div class="branding-section-heading">
            <div><p class="branding-kicker">DỊCH VỤ TRONG GÓI BAO GỒM:</p><h2>4 GIẢI PHÁP TOÀN DIỆN CHO DOANH NGHIỆP</h2></div>
            <p class="branding-heading-note">Đồng bộ – Chuyên nghiệp – Hiệu quả – Tiết kiệm</p>
        </div>
        <div class="branding-solutions-layout">
            <aside class="branding-solutions-offer" aria-label="Ưu đãi trọn gói">
                <p class="branding-kicker">ĐẦU TƯ MỘT LẦN · ĐỒNG BỘ THƯƠNG HIỆU</p>
                <h3>Trọn bộ 4 giải pháp</h3>
                <div class="branding-old-price"><span>TỔNG GIÁ TRỊ</span><s>{{ number_format($content['offer']['original_price'], 0, ',', '.') }} VNĐ</s></div>
                @include('frontend.landing.parts.branding.offer-price', ['offer' => $content['offer']])
                <p>{{ $content['offer']['note'] }}</p>
                <a class="branding-button" href="#lien-he" data-landing-event="cta_click" data-block-id="services-offer">Nhận tư vấn ngay <x-landing.branding-icon /></a>
            </aside>
            <div class="branding-four-grid branding-solutions-grid grid grid-cols-1 sm:grid-cols-2">
                @foreach ($content['services'] as $service)
                    <article class="branding-service-card">
                        <x-landing.branding-icon :name="$service['icon']" class="branding-service-icon" />
                        <h3 class="branding-multiline">{{ $service['title'] }}</h3>
                        <p class="branding-service-price">{{ number_format($service['price'], 0, ',', '.') }}đ</p>
                        <ul>@foreach ($service['features'] as $feature)<li>{{ $feature }}</li>@endforeach</ul>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="branding-combo branding-dark">
    <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative">
        <div class="branding-combo-heading"><h2>{{ $content['offer']['heading'] }}</h2><p>{{ $content['offer']['description'] }}</p></div>
        <div class="branding-combo-grid grid lg:grid-cols-2">
            <div class="branding-price-table">
                @foreach ($content['services'] as $service)
                    <div><x-landing.branding-icon :name="$service['icon']" /><span>{{ $service['name'] }}</span><strong>{{ number_format($service['price'], 0, ',', '.') }}đ</strong></div>
                @endforeach
                <div class="branding-table-total"><span>Tổng giá trị</span><s>{{ number_format($content['offer']['original_price'], 0, ',', '.') }} VNĐ</s></div>
            </div>
            <div class="branding-combo-offer">
                @include('frontend.landing.parts.branding.offer-price', ['offer' => $content['offer']])
                <a class="branding-button branding-button-white" href="#lien-he" data-landing-event="cta_click" data-block-id="combo">Nhận tư vấn ngay <x-landing.branding-icon /></a>
                <p>{{ $content['offer']['note'] }}</p>
            </div>
        </div>
    </div>
</section>

<section class="branding-section branding-ecosystem">
    <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative">
        <div class="branding-section-heading"><div><p class="branding-kicker">HỆ SINH THÁI THƯƠNG HIỆU</p><h2>ĐỒNG BỘ TỪ CHIẾN LƯỢC ĐẾN TRIỂN KHAI</h2></div><p class="branding-heading-note">Một hệ thống – Một thông điệp – Một sức mạnh</p></div>
        <ol class="branding-four-grid grid grid-cols-2 lg:grid-cols-4">
            @foreach ($content['services'] as $service)
                <li class="branding-ecosystem-card">
                    <x-landing.branding-icon :name="$service['icon']" />
                    <h3>{{ $loop->iteration }}. {{ $service['ecosystem'] }}</h3>
                    <p class="branding-multiline">{{ $service['caption'] }}</p>
                    @unless ($loop->last)<x-landing.branding-icon class="branding-connector" />@endunless
                </li>
            @endforeach
        </ol>
    </div>
</section>

<section class="branding-section branding-audiences">
    <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative">
        <div class="branding-section-heading"><div><p class="branding-kicker">PHÙ HỢP VỚI AI?</p><h2>GIẢI PHÁP DÀNH CHO MỌI DOANH NGHIỆP</h2></div></div>
        <div class="branding-four-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($content['audiences'] as $audience)
                <article class="branding-audience-card">
                    <img src="{{ $audience['image'] }}" alt="{{ str_replace("\n", ' ', $audience['title']) }}" width="900" height="560" loading="lazy" decoding="async">
                    <div><x-landing.branding-icon :name="$audience['icon']" /><div><h3 class="branding-multiline">{{ $audience['title'] }}</h3><p>{{ $audience['description'] }}</p></div></div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section id="quy-trinh" class="branding-section branding-process">
    <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative">
        <div class="branding-section-heading"><div><p class="branding-kicker">QUY TRÌNH LÀM VIỆC</p><h2>5 BƯỚC ĐƠN GIẢN – HIỆU QUẢ TỐI ĐA</h2></div></div>
        <ol class="branding-process-grid">
            @foreach ($content['process'] as $step)
                <li><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $step['title'] }}</h3><p class="branding-multiline">{{ $step['description'] }}</p></div></li>
            @endforeach
        </ol>
    </div>
</section>

<section id="du-an" class="branding-section branding-projects">
    <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative">
        <div class="branding-section-heading"><div><p class="branding-kicker">NHỮNG DỰ ÁN THT MEDIA ĐÃ TRIỂN KHAI</p><h2>DỰ ÁN TIÊU BIỂU</h2></div><a class="branding-button branding-button-outline" href="{{ route('projects.index') }}">Xem thêm dự án <x-landing.branding-icon /></a></div>
        <div class="branding-four-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($content['projects'] as $project)
                <a href="{{ $project['url'] }}" class="branding-project-card">
                    <img src="{{ $project['image'] }}" alt="{{ $project['title'] }}" width="900" height="560" loading="lazy" decoding="async">
                    <h3>{{ $project['title'] }}</h3><p>{{ $project['caption'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

@include('frontend.landing.parts.branding.partners')

@include('frontend.landing.parts.branding.contact')
