<div class="topbar">
    <div class="container topbar__inner">
        <span>CamKheTravel · Đồng hành cùng những hành trình đáng nhớ</span>
        <div class="topbar__links">
            <span>Thứ 2 - Thứ 7: 8:00 - 17:00</span>
            @if ($website->contact_email)
                <a href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a>
            @endif
        </div>
    </div>
</div>
<header class="header-sticky" data-site-header aria-label="Đầu trang {{ $website->site_name }}">
    <div class="header">
        <div class="container header__inner">
            @include('partials.header.brand')
            <div class="d-none d-xl-block flex-grow-1 header__navigation">
                @include('partials.header.navigation')
            </div>
            @include('partials.header.actions')
        </div>
    </div>
</header>
{{-- Overlays stay outside the sticky/transformed header to preserve stacking and focus. --}}
@include('partials.header.mobile-menu')
@include('partials.header.search')
