<div class="site-header-shell" data-site-header>
    <header class="site-header" aria-label="Đầu trang {{ $website->site_name }}">
        <div class="site-topbar">
            <div class="container site-topbar__inner">
                <span>Giải pháp PCCC toàn diện · Đồng hành cùng công trình an toàn</span>
                <div class="site-topbar__links">
                    <span>Thứ 2 - Thứ 7: 8:00 - 17:00</span>
                    @if ($headerPhones->isNotEmpty())
                        <a href="{{ $headerPhones->first()['href'] }}">Hotline: {{ $headerPhones->first()['label'] }}</a>
                    @endif
                    @if ($website->contact_email)
                        <a href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="container site-header__inner">
            @include('partials.header.brand')
            <div class="d-none d-xl-block flex-grow-1 site-header__navigation">
                @include('partials.header.navigation')
            </div>
            @include('partials.header.actions')
        </div>
    </header>
</div>
{{-- Overlays stay outside the sticky/transformed header to preserve stacking and focus. --}}
@include('partials.header.mobile-menu')
@include('partials.header.search')
