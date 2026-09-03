@use(App\Support\Localization\LocalizedUrl)

@php
    $handoverUrl = LocalizedUrl::route('bni.handover');
@endphp

<nav class="bni-handover-page-nav" aria-label="Điều hướng Lễ chuyển giao">
    <div class="site-shell bni-handover-page-nav__inner">
        <a class="bni-handover-page-nav__brand" href="{{ $handoverUrl }}#tong-quan" aria-label="Về đầu trang Lễ chuyển giao">
            <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
            <span>Lễ chuyển giao</span>
        </a>
        <div class="bni-handover-page-nav__links">
            <a href="{{ $handoverUrl }}#su-kien">Sự kiện</a>
            <a href="{{ $handoverUrl }}#video-gioi-thieu">Video giới thiệu</a>
            <a href="{{ $handoverUrl }}#lich-trinh">Lịch trình</a>
            <a @class(['is-active' => request()->routeIs('bni.articles.*')]) href="{{ LocalizedUrl::route('bni.articles.index') }}">Tin tức</a>
            <a @class(['is-active' => request()->routeIs('bni.gallery.*')]) href="{{ LocalizedUrl::route('bni.gallery.index') }}">Thư viện ảnh</a>
        </div>
        <a @class(['bni-button', 'bni-button--red', 'bni-handover-page-nav__cta', 'is-active' => request()->routeIs('bni.registrations.*')]) href="{{ LocalizedUrl::route('bni.registrations.create') }}">Đăng ký</a>
    </div>
</nav>
