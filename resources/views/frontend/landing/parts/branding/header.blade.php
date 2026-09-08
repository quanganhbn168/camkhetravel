<a class="branding-skip" href="#branding-hero-title">Đến nội dung chính</a>
<header class="branding-header" id="branding-header">
    <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative flex items-center justify-between gap-4">
        <a href="#top" class="branding-logo" aria-label="THT Media – đầu trang">
            <img src="{{ $page['logo_url'] }}" alt="THT Media" width="180" height="60">
            <strong>THT MEDIA</strong>
        </a>
        <nav class="branding-desktop-nav" aria-label="Điều hướng landing">
            @foreach ($page['navigation'] as $item)
                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <a href="#lien-he" class="branding-button branding-header-cta">Nhận tư vấn ngay <x-landing.branding-icon /></a>
        <div class="branding-hotline">
            <x-landing.branding-icon name="phone" />
            <div>
                <a href="tel:{{ preg_replace('/\D/', '', $website->hotline) }}">{{ $website->hotline }}</a>
                <a href="tel:{{ preg_replace('/\D/', '', $website->contact_phone) }}">{{ $website->contact_phone }}</a>
            </div>
        </div>
        <button class="branding-menu-toggle" type="button" aria-label="Mở menu" aria-expanded="false" aria-controls="branding-mobile-menu"><span></span><span></span><span></span></button>
    </div>
    <nav id="branding-mobile-menu" class="branding-mobile-menu" aria-label="Điều hướng trên điện thoại" hidden>
        @foreach ($page['navigation'] as $item)
            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @endforeach
    </nav>
</header>
