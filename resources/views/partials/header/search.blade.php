@use(App\Support\Localization\LocalizedUrl)

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
