@use(App\Support\Localization\LocalizedUrl)

<nav class="flex flex-wrap items-center justify-start" aria-label="Điều hướng chính">
                    @foreach ($headerNavigation as $item)
                        <div class="relative shrink-0"
                            @if ($item['has_children'])
                                x-data="{ submenuOpen: false }"
                                @mouseenter="submenuOpen = true"
                                @mouseleave="submenuOpen = false"
                                @focusin="submenuOpen = true"
                                @focusout="submenuOpen = false"
                            @endif>
                            <a class="flex min-h-13 items-center gap-2 px-3 py-3 text-[0.7rem] font-semibold leading-4 tracking-[0.04em] {{ $item['is_active'] ? 'text-primary hover:bg-transparent hover:text-primary' : 'text-ink hover:bg-slate-50 hover:text-primary' }} uppercase transition" href="{{ $item['url'] }}" @if ($item['has_children']) :aria-expanded="submenuOpen.toString()" aria-haspopup="true" @endif @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif @if ($item['is_active']) aria-current="page" @endif>
                                @if ($item['home'] ?? false)
                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z"/></svg>
                                    <span class="sr-only">{{ $item['label'] }}</span>
                                @else
                                    {{ $item['label'] }}
                                @endif
                                @if ($item['has_children'])
                                    <svg class="size-3 transition-transform" :class="submenuOpen && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 1 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                                @endif
                            </a>
                            @if ($item['has_children'])
                                <div class="absolute left-0 top-full z-50 grid min-w-64 overflow-hidden rounded-b-xl border border-t-2 border-slate-200 border-t-primary bg-white py-2 shadow-[0_18px_42px_rgba(16,35,62,0.18)]" x-cloak x-show="submenuOpen" x-transition.origin.top.left>
                                    @foreach ($item['children'] as $child)
                                <a class="px-4 py-2.5 text-sm leading-5 transition {{ $child['is_active'] ? 'font-semibold text-accent hover:bg-transparent hover:text-accent' : 'font-medium text-slate-700 hover:bg-sand hover:text-accent' }}" href="{{ $child['url'] }}" @if ($child['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif @if ($child['is_active']) aria-current="page" @endif>{{ $child['label'] }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </nav>
