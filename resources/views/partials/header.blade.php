<div class="site-header-shell"
    x-data="{
        open: false,
        searchOpen: false,
        hidden: false,
        lastScrollY: window.scrollY,
        syncScroll() {
            const currentY = Math.max(0, window.scrollY);
            if (currentY <= this.$el.offsetHeight || this.open || this.searchOpen) {
                this.hidden = false;
            } else if (Math.abs(currentY - this.lastScrollY) < 6) {
                return;
            } else {
                this.hidden = currentY > this.lastScrollY;
            }
            this.lastScrollY = currentY;
        },
        syncBodyLock() {
            if (this.open || this.searchOpen) this.hidden = false;
            document.body.classList.toggle('overflow-hidden', this.open || this.searchOpen);
        }
    }"
    x-init="$watch('open', () => syncBodyLock()); $watch('searchOpen', () => syncBodyLock())"
    :class="{ 'is-scroll-hidden': hidden }"
    @scroll.window.throttle.50ms="syncScroll()"
    @focusin="hidden = false"
    @resize.window="if (window.innerWidth >= 1280) open = false"
    @keydown.escape.window="open = false; searchOpen = false">
    <header class="site-header" aria-label="Đầu trang DVTEC">
        <div class="pccc-topbar">
            <div class="site-container pccc-topbar__inner">
                <span>Giải pháp PCCC toàn diện - Đồng hành cùng công trình an toàn</span>
                <div class="pccc-topbar__links">
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
        <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8 site-header__inner">
            @include('partials.header.brand')
            <div class="hidden min-w-0 flex-1 xl:block">
                @include('partials.header.navigation')
            </div>
            @include('partials.header.actions')
        </div>
    </header>
    @include('partials.header.mobile-menu')
    @include('partials.header.search')
</div>
