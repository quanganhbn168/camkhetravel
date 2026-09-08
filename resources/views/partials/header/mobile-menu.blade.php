@use(App\Support\Localization\LocalizedUrl)

<div class="fixed inset-0 z-[70] xl:hidden" x-cloak x-show="open">
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
                        <a class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold {{ $item['is_active'] ? 'text-accent hover:bg-transparent' : 'text-slate-700 hover:bg-slate-50' }}" href="{{ $item['url'] }}" @click="open = false" @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif @if ($item['is_active']) aria-current="page" @endif>
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
                                    <a class="rounded-lg px-3 py-2 text-sm leading-5 {{ $child['is_active'] ? 'font-semibold text-accent hover:bg-transparent' : 'text-slate-600 hover:bg-slate-50 hover:text-ink' }}" href="{{ $child['url'] }}" @click="open = false" @if ($child['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif @if ($child['is_active']) aria-current="page" @endif>{{ $child['label'] }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

            </nav>
        </aside>
    </div>
