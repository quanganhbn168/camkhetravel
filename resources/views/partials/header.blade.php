@use(App\Support\Localization\LocalizedUrl)

@php($logo = $websiteMedia->get($website->logo_media_id))
@php($logoUrl = $websiteMediaUrls->get($logo?->getKey()))
@php($currentLanguage = $languages->get(app()->getLocale()))
<div class="relative z-40"
    x-data="{
        open: false,
        languageOpen: false,
        servicesOpen: false,
        servicesCloseTimer: null,
        openServices() {
            clearTimeout(this.servicesCloseTimer);
            this.servicesOpen = true;
        },
        closeServices() {
            this.servicesCloseTimer = setTimeout(() => { this.servicesOpen = false }, 140);
        }
    }"
    x-init="$watch('open', (value) => document.body.classList.toggle('overflow-hidden', value))"
    @keydown.escape.window="open = false; languageOpen = false; servicesOpen = false">
    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
        <div class="site-shell flex min-h-18 items-center justify-between gap-5">
        <a class="flex shrink-0 items-center gap-3" href="{{ LocalizedUrl::route('home') }}" aria-label="{{ $website->site_name }}">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $website->site_name }}" class="h-10 max-w-40 object-contain">
            @else
                <span class="grid size-10 place-items-center rounded-xl bg-ink font-display text-base font-bold text-white">THT</span>
                <span class="text-sm font-bold tracking-[-0.04em] text-ink sm:text-base">{{ $website->site_name }}</span>
            @endif
        </a>

        <nav class="hidden items-center gap-6 lg:flex" aria-label="Điều hướng chính">
            <a class="text-sm font-medium {{ request()->routeIs('home', 'localized.home') ? 'text-accent' : 'text-slate-600 hover:text-ink' }}" href="{{ LocalizedUrl::route('home') }}">{{ __('site.home') }}</a>
            <a class="text-sm font-medium {{ request()->routeIs('about', 'localized.about') ? 'text-accent' : 'text-slate-600 hover:text-ink' }}" href="{{ LocalizedUrl::route('about') }}">{{ __('site.about') }}</a>
            <div class="relative"
                @mouseenter="openServices()"
                @mouseleave="closeServices()"
                @focusin="openServices()"
                @focusout="closeServices()">
                <a class="inline-flex items-center gap-1 text-sm font-medium {{ request()->routeIs('services.*', 'localized.services.*') ? 'text-accent' : 'text-slate-600 hover:text-ink' }}" href="{{ LocalizedUrl::route('services.index') }}" :aria-expanded="servicesOpen.toString()" aria-haspopup="true">
                    {{ __('site.services') }}
                    <svg class="size-3 transition-transform" :class="servicesOpen && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                </a>
            </div>
            <a class="text-sm font-medium {{ request()->routeIs('projects.index', 'localized.projects.index') ? 'text-accent' : 'text-slate-600 hover:text-ink' }}" href="{{ LocalizedUrl::route('projects.index') }}">{{ __('site.projects') }}</a>
            <a class="text-sm font-medium {{ request()->routeIs('pricing.index', 'localized.pricing.index') ? 'text-accent' : 'text-slate-600 hover:text-ink' }}" href="{{ LocalizedUrl::route('pricing.index') }}">{{ __('site.pricing') }}</a>
            <a class="text-sm font-medium {{ request()->routeIs('posts.index', 'localized.posts.index') ? 'text-accent' : 'text-slate-600 hover:text-ink' }}" href="{{ LocalizedUrl::route('posts.index') }}">{{ __('site.news') }}</a>
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            <div class="relative">
                <button class="flex items-center gap-1 rounded-full border border-slate-200 px-3 py-2 text-xs font-bold text-ink hover:border-slate-300" type="button" @click="languageOpen = !languageOpen" :aria-expanded="languageOpen.toString()">
                    {{ $currentLanguage?->native_name }}
                    <svg class="size-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                </button>
                <div class="absolute right-0 mt-2 w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl" x-cloak x-show="languageOpen" x-transition.origin.top.right @click.outside="languageOpen = false">
                    @foreach ($languages as $language)
                        <a class="flex items-center justify-between rounded-xl px-3 py-2 text-sm {{ app()->getLocale() === $language->code ? 'bg-sand font-semibold text-accent' : 'text-slate-600 hover:bg-slate-50 hover:text-ink' }}" href="{{ LocalizedUrl::switchUrl($language->code) }}">
                            <span>{{ $language->name }}</span><span class="text-xs font-bold">{{ $language->native_name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <a class="button-primary min-h-10 px-4 py-2" href="{{ LocalizedUrl::route('contact') }}">{{ __('site.consult') }}</a>
        </div>

            <button class="grid size-10 place-items-center rounded-xl border border-slate-200 text-ink lg:hidden" type="button" @click="open = true" :aria-expanded="open.toString()" aria-controls="mobile-drawer">
                <span class="sr-only">Mở menu</span>
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </header>

    <div class="absolute inset-x-0 top-full hidden border-b border-slate-200 bg-white shadow-[0_24px_48px_rgba(16,35,62,0.12)] lg:block"
        x-cloak
        x-show="servicesOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        @mouseenter="openServices()"
        @mouseleave="closeServices()"
        @focusin="openServices()"
        @focusout="closeServices()"
        role="region"
        aria-label="{{ __('site.services') }}">
        <div class="site-shell py-6">
            <div class="flex items-end justify-between gap-5 border-b border-slate-100 pb-5">
                <div>
                    <p class="eyebrow">Giải pháp THT Media</p>
                    <p class="mt-2 text-sm text-slate-600">Chọn dịch vụ phù hợp với mục tiêu truyền thông của doanh nghiệp.</p>
                </div>
                <a class="section-link shrink-0" href="{{ LocalizedUrl::route('services.index') }}">Xem tất cả <span aria-hidden="true">→</span></a>
            </div>

            <div class="mt-5 grid gap-x-10 gap-y-7 {{ $megaServiceCategories->count() > 1 ? 'lg:grid-cols-2' : '' }}">
                @foreach ($megaServiceCategories as $category)
                    <section>
                        <a class="inline-flex items-center gap-2 text-sm font-bold text-ink hover:text-accent" href="{{ LocalizedUrl::slug($category->slug) }}">
                            {{ $category->name }} <span class="text-accent" aria-hidden="true">→</span>
                        </a>
                        <div class="mt-3 grid gap-x-5 gap-y-1 sm:grid-cols-2 {{ $megaServiceCategories->count() === 1 ? 'xl:grid-cols-3' : '' }}">
                            @foreach ($category->landings as $service)
                                <a class="group flex items-start gap-2 rounded-lg px-2 py-2 text-sm leading-5 text-slate-600 hover:bg-sand hover:text-ink" href="{{ LocalizedUrl::slug($service->slug) }}">
                                    <span class="mt-1 text-accent" aria-hidden="true">↗</span>
                                    <span>{{ $service->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-[70] lg:hidden" x-cloak x-show="open">
        <div class="absolute inset-0 bg-midnight/55 backdrop-blur-sm" x-transition.opacity @click="open = false"></div>
        <aside id="mobile-drawer" class="absolute inset-y-0 right-0 flex w-[min(100%-1.25rem,24rem)] flex-col bg-white shadow-[-24px_0_60px_rgba(8,26,49,0.2)]" x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" @click.stop role="dialog" aria-modal="true" aria-label="Menu điều hướng">
            <div class="flex min-h-18 items-center justify-between border-b border-slate-100 px-5">
                <span class="font-display text-lg font-semibold text-ink">Menu</span>
                <button class="grid size-10 place-items-center rounded-xl border border-slate-200 text-ink transition hover:bg-slate-50" type="button" @click="open = false">
                    <span class="sr-only">Đóng menu</span>
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <nav class="grid flex-1 content-start gap-1 overflow-y-auto px-4 py-5" aria-label="Điều hướng di động">
                <a class="rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50" href="{{ LocalizedUrl::route('home') }}" @click="open = false">{{ __('site.home') }}</a>
                <a class="rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50" href="{{ LocalizedUrl::route('about') }}" @click="open = false">{{ __('site.about') }}</a>
                <a class="rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50" href="{{ LocalizedUrl::route('services.index') }}" @click="open = false">{{ __('site.services') }}</a>
                <a class="rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50" href="{{ LocalizedUrl::route('projects.index') }}" @click="open = false">{{ __('site.projects') }}</a>
                <a class="rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50" href="{{ LocalizedUrl::route('pricing.index') }}" @click="open = false">{{ __('site.pricing') }}</a>
                <a class="rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50" href="{{ LocalizedUrl::route('posts.index') }}" @click="open = false">{{ __('site.news') }}</a>
                <div class="mt-4 border-t border-slate-100 pt-5">
                    <p class="px-3 text-xs font-bold tracking-[0.15em] text-slate-400 uppercase">Ngôn ngữ</p>
                    <div class="mt-3 flex flex-wrap gap-2 px-3">
                        @foreach ($languages as $language)
                            <a class="rounded-full border px-3 py-1.5 text-xs font-bold {{ app()->getLocale() === $language->code ? 'border-accent bg-sand text-accent' : 'border-slate-200 text-slate-500' }}" href="{{ LocalizedUrl::switchUrl($language->code) }}" @click="open = false">{{ $language->native_name }}</a>
                        @endforeach
                    </div>
                </div>
            </nav>
            <div class="border-t border-slate-100 p-5">
                <a class="button-primary w-full" href="{{ LocalizedUrl::route('contact') }}" @click="open = false">{{ __('site.consult') }}</a>
            </div>
        </aside>
    </div>
</div>
