@use(App\Support\Localization\LocalizedUrl)

<div class="relative z-40"
    x-data="{
        open: false,
        searchOpen: false,
        syncBodyLock() {
            document.body.classList.toggle('overflow-hidden', this.open || this.searchOpen);
        }
    }"
    x-init="$watch('open', () => syncBodyLock()); $watch('searchOpen', () => syncBodyLock())"
    @keydown.escape.window="open = false; searchOpen = false">
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 shadow-[0_8px_28px_rgba(16,35,62,0.05)] backdrop-blur-xl">
        <div class="site-shell flex min-h-20 items-center justify-between gap-5 py-3">
            <a class="flex shrink-0 items-center gap-3" href="{{ LocalizedUrl::route('home') }}" aria-label="{{ $website->site_name }}">
                @if ($headerLogoUrl)
                    <img src="{{ $headerLogoUrl }}" alt="{{ $website->site_name }}" class="h-14 max-w-56 object-contain object-left">
                @else
                    <span class="grid size-11 place-items-center rounded-xl bg-ink font-display text-base font-bold text-white">THT</span>
                    <span class="text-sm font-bold tracking-[-0.04em] text-ink sm:text-base">{{ $website->site_name }}</span>
                @endif
                @if (request()->routeIs('bni.handover'))
                    <span class="bni-handover-header-brand hidden xl:grid">
                        <img class="bni-handover-header-logo" src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                        <span class="bni-handover-header-chapters" aria-label="Các chapter BNI">
                            @foreach ($bniHeaderChapters as $chapter)
                                <span>{{ $chapter->short_name ?: $chapter->name }}</span>
                            @endforeach
                        </span>
                    </span>
                @endif
            </a>

            <div class="hidden items-center justify-end gap-3 lg:flex">
                <button class="grid size-10 place-items-center rounded-full border border-slate-200 text-ink transition hover:border-primary hover:bg-mist hover:text-primary" type="button" @click="searchOpen = true; $nextTick(() => $refs.headerSearchInput.focus())" aria-label="Tìm dịch vụ và bài viết">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></svg>
                </button>

                @if ($website->hotline || $website->contact_phone)
                    <div class="group flex items-center gap-2 border-l border-slate-200 pl-5 text-right">
                        <span class="grid size-9 place-items-center rounded-full bg-mist text-accent transition group-hover:bg-primary group-hover:text-white">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.77.62 2.61a2 2 0 0 1-.45 2.11L8 9.72a16 16 0 0 0 6 6l1.28-1.28a16 16 0 0 1 2.11-.45c.84.29 1.71.5 2.61.62A2 2 0 0 1 22 16.92Z"/></svg>
                        </span>
                        <span class="flex flex-wrap items-center justify-end gap-x-2 text-sm font-semibold text-ink">
                            @if ($website->hotline)<a class="hover:text-primary" href="tel:{{ preg_replace('/\s+/', '', $website->hotline) }}">{{ $website->hotline }}</a>@endif
                            @if ($website->hotline && $website->contact_phone)<span class="text-slate-300" aria-hidden="true">-</span>@endif
                            @if ($website->contact_phone)<a class="hover:text-primary" href="tel:{{ preg_replace('/\s+/', '', $website->contact_phone) }}">{{ $website->contact_phone }}</a>@endif
                        </span>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2 lg:hidden">
                <button class="grid size-10 place-items-center rounded-xl border border-slate-200 text-ink" type="button" @click="searchOpen = true; $nextTick(() => $refs.headerSearchInput.focus())" aria-label="Tìm dịch vụ và bài viết">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></svg>
                </button>
                <button class="grid size-11 place-items-center rounded-xl bg-ink text-white shadow-sm" type="button" @click="open = true" :aria-expanded="open.toString()" aria-controls="mobile-drawer">
                    <span class="sr-only">Mở menu</span>
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
        </div>

        <div class="hidden border-t border-white/15 bg-midnight lg:block">
            <nav class="mx-auto flex w-[min(100%-2rem,96rem)] flex-wrap items-stretch justify-center" aria-label="Điều hướng chính">
                @foreach ($headerNavigation as $item)
                    <div class="relative shrink-0"
                        @if ($item['has_children'])
                            x-data="{ submenuOpen: false }"
                            @mouseenter="submenuOpen = true"
                            @mouseleave="submenuOpen = false"
                            @focusin="submenuOpen = true"
                            @focusout="submenuOpen = false"
                        @endif>
                        <a class="flex min-h-13 items-center gap-2 px-3 py-3 text-[0.7rem] font-semibold leading-4 tracking-[0.04em] text-white uppercase transition hover:bg-white/10 hover:text-white {{ $item['is_active'] ? 'bg-primary text-white hover:bg-primary' : '' }}" href="{{ $item['url'] }}" @if ($item['has_children']) :aria-expanded="submenuOpen.toString()" aria-haspopup="true" @endif @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif @if ($item['is_active']) aria-current="page" @endif>
                            @if ($item['home'] ?? false)
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z"/></svg>
                                <span class="sr-only">{{ $item['label'] }}</span>
                            @else
                                {{ $item['label'] }}
                            @endif
                            @if ($item['has_children'])
                                <svg class="size-3 transition-transform" :class="submenuOpen && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                            @endif
                        </a>
                        @if ($item['has_children'])
                            <div class="absolute left-0 top-full z-50 grid min-w-64 overflow-hidden rounded-b-xl border border-t-2 border-slate-200 border-t-primary bg-white py-2 shadow-[0_18px_42px_rgba(16,35,62,0.18)]" x-cloak x-show="submenuOpen" x-transition.origin.top.left>
                                @foreach ($item['children'] as $child)
                                    <a class="px-4 py-2.5 text-sm font-medium leading-5 text-slate-700 transition hover:bg-sand hover:text-accent {{ $child['is_active'] ? 'bg-sand text-accent' : '' }}" href="{{ $child['url'] }}" @if ($child['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif @if ($child['is_active']) aria-current="page" @endif>{{ $child['label'] }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
                <a class="bni-handover-menu-link {{ request()->routeIs('bni.handover') ? 'is-active' : '' }}" href="{{ LocalizedUrl::route('bni.handover') }}" @if (request()->routeIs('bni.handover')) aria-current="page" @endif>
                    <img class="bni-handover-menu-link__logo" src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                    <span>LỄ CHUYỂN GIAO</span>
                </a>
            </nav>
        </div>
    </header>

    <div class="fixed inset-0 z-[70] lg:hidden" x-cloak x-show="open">
        <div class="absolute inset-0 bg-midnight/55 backdrop-blur-sm" x-transition.opacity @click="open = false"></div>
        <aside id="mobile-drawer" class="absolute inset-y-0 right-0 flex w-[min(100%-1.25rem,24rem)] flex-col bg-white shadow-[-24px_0_60px_rgba(8,26,49,0.2)]" x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" @click.stop role="dialog" aria-modal="true" aria-label="Menu điều hướng">
            <div class="flex min-h-20 items-center justify-between border-b border-slate-100 px-5">
                <span class="font-display text-xl font-semibold text-ink">Khám phá THT Media</span>
                <button class="grid size-10 place-items-center rounded-xl border border-slate-200 text-ink transition hover:bg-slate-50" type="button" @click="open = false">
                    <span class="sr-only">Đóng menu</span>
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <nav class="grid flex-1 content-start gap-1 overflow-y-auto px-4 py-5" aria-label="Điều hướng di động">
                @foreach ($headerNavigation as $item)
                    <div class="grid gap-1">
                        <a class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold {{ $item['is_active'] ? 'bg-sand text-accent' : 'text-slate-700 hover:bg-slate-50' }}" href="{{ $item['url'] }}" @click="open = false" @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif @if ($item['is_active']) aria-current="page" @endif>
                            @if ($item['home'] ?? false)
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z"/></svg>
                            @else
                                <span class="size-1.5 rounded-full bg-current opacity-40" aria-hidden="true"></span>
                            @endif
                            <span>{{ $item['label'] }}</span>
                            @if ($item['has_children'])
                                <svg class="ml-auto size-4 opacity-50" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.23 4.21a.75.75 0 0 1 1.06.02L12.5 8.7a.75.75 0 0 1 0 1.03l-4.21 4.5a.75.75 0 1 1-1.1-1.03L10.92 9.2 7.19 5.24a.75.75 0 0 1 .04-1.03Z" clip-rule="evenodd"/></svg>
                            @endif
                        </a>
                        @if ($item['has_children'])
                            <div class="ml-6 grid gap-1 border-l border-slate-200 py-1 pl-3">
                                @foreach ($item['children'] as $child)
                                    <a class="rounded-lg px-3 py-2 text-sm leading-5 {{ $child['is_active'] ? 'bg-sand font-semibold text-accent' : 'text-slate-600 hover:bg-slate-50 hover:text-ink' }}" href="{{ $child['url'] }}" @click="open = false" @if ($child['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif @if ($child['is_active']) aria-current="page" @endif>{{ $child['label'] }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
                <a class="bni-handover-menu-link bni-handover-menu-link--mobile {{ request()->routeIs('bni.handover') ? 'is-active' : '' }}" href="{{ LocalizedUrl::route('bni.handover') }}" @click="open = false" @if (request()->routeIs('bni.handover')) aria-current="page" @endif>
                    <img class="bni-handover-menu-link__logo" src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                    <span>LỄ CHUYỂN GIAO</span>
                </a>

            </nav>
        </aside>
    </div>

    <div class="fixed inset-0 z-[80]" x-cloak x-show="searchOpen" @click="searchOpen = false" role="dialog" aria-modal="true" aria-labelledby="header-search-title">
        <div class="absolute inset-0 bg-ink/65 backdrop-blur-sm" x-transition.opacity></div>
        <section class="absolute inset-x-0 top-5 mx-auto w-[min(100%-2rem,42rem)] rounded-[1.5rem] bg-white p-5 shadow-[0_24px_60px_rgba(16,35,62,0.24)] md:top-10 md:p-7" x-transition.scale.origin.top @click.stop>
            <div class="flex items-start justify-between gap-5">
                <div>
                    <h2 class="display-title text-2xl uppercase" id="header-search-title">Tìm dịch vụ & bài viết</h2>
                </div>
                <button class="grid size-10 place-items-center rounded-full border border-slate-200 text-ink transition hover:bg-slate-50" type="button" @click="searchOpen = false" aria-label="Đóng tìm kiếm">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <form class="mt-6" action="{{ LocalizedUrl::route('search') }}" method="GET" role="search">
                <label class="sr-only" for="header-search-query">Từ khóa tìm kiếm</label>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></svg>
                        <input class="form-field mt-0 min-h-12 pl-12" id="header-search-query" name="q" type="search" value="{{ request('q') }}" placeholder="Ví dụ: quay sự kiện, profile, website" maxlength="100" x-ref="headerSearchInput">
                    </div>
                    <button class="button-primary min-h-12 shrink-0" type="submit">Tìm kiếm <span aria-hidden="true">→</span></button>
                </div>
            </form>
            <p class="mt-4 text-sm leading-6 text-slate-500">Tìm trong dịch vụ và bài viết đã xuất bản.</p>
        </section>
    </div>

</div>
