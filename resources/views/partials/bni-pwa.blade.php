@use(App\Support\Localization\LocalizedUrl)

@php
    $isBniPage = request()->routeIs('bni.*');
    $isBniHome = request()->routeIs('bni.handover');
    $isBniInvitation = request()->routeIs('bni.invitations.*');
    $isBniPickleball = request()->routeIs('bni.pickleball');
    $isBniGallery = request()->routeIs('bni.gallery.*');
    $bniHomeUrl = LocalizedUrl::route('bni.handover');
    $bniInvitationUrl = LocalizedUrl::route('bni.invitations.template');
    $bniGalleryUrl = LocalizedUrl::route('bni.gallery.index');
    $mobileSectionUrl = $isBniInvitation
        ? '#rsvp'
        : ($isBniPickleball || $isBniHome
            ? '#lich-trinh'
            : $bniHomeUrl.'#lich-trinh');
    $mobileSectionLabel = $isBniInvitation ? 'RSVP' : 'Lịch trình';
@endphp

@if ($isBniPage)
    <div class="bni-pwa-install" data-bni-install-banner hidden>
        <img class="bni-pwa-install__icon" src="{{ asset('bni-icon-192x192.png') }}" alt="">
        <div class="bni-pwa-install__copy">
            <strong>BNI trên điện thoại</strong>
            <span>Thêm lối tắt để mở nhanh không gian BNI.</span>
        </div>
        <button class="bni-pwa-install__action" type="button" data-bni-install-action>Cài app</button>
        <button class="bni-pwa-install__dismiss" type="button" data-bni-install-dismiss aria-label="Đóng thông báo cài ứng dụng">×</button>
    </div>

    <nav class="bni-mobile-bar" aria-label="Điều hướng BNI trên điện thoại">
        <a class="{{ $isBniHome ? 'is-active' : '' }}" href="{{ $bniHomeUrl }}" @if ($isBniHome) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z"/></svg>
            <span>Tổng quan</span>
        </a>
        <a href="{{ $mobileSectionUrl }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16M8 14h3M8 17h5"/></svg>
            <span>{{ $mobileSectionLabel }}</span>
        </a>
        <a class="{{ $isBniInvitation ? 'is-active' : '' }}" href="{{ $bniInvitationUrl }}" @if ($isBniInvitation) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M7 8h10M7 12h7M7 16h5"/></svg>
            <span>Thư mời</span>
        </a>
        <a class="{{ $isBniGallery ? 'is-active' : '' }}" href="{{ $bniGalleryUrl }}" @if ($isBniGallery) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m4 17 4-4 3 3 2-2 7 6"/></svg>
            <span>Hình ảnh</span>
        </a>
        <button type="button" data-bni-install-action>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 17h6M12 7v6m-3-3 3 3 3-3"/></svg>
            <span>Cài app</span>
        </button>
    </nav>
@endif
