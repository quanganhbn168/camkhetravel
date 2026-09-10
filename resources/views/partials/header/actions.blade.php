<div class="site-header__actions">
    <button class="site-header__search" type="button" @click="searchOpen = true; $nextTick(() => $refs.headerSearchInput.focus())" aria-label="Tìm dịch vụ và bài viết">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></svg>
    </button>
    @if ($headerPhones->isNotEmpty())
        <div class="site-header__phones" aria-label="Điện thoại tư vấn">
            @foreach ($headerPhones as $phone)
                <a href="{{ $phone['href'] }}">{{ $phone['label'] }}</a>
            @endforeach
        </div>
    @endif
    <a class="pccc-header-cta" href="{{ \App\Support\Localization\LocalizedUrl::route('contact') }}">Nhận tư vấn <span aria-hidden="true">→</span></a>
    <button class="site-header__toggle xl:hidden" type="button" @click="open = true" :aria-expanded="open.toString()" aria-controls="mobile-drawer" aria-label="Mở menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
</div>
