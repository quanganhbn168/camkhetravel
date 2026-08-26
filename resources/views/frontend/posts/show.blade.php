@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)
@use(Illuminate\Support\Str)

@section('content')
    <article x-data="{ tocPassed: false, floatingOpen: false, inlineTocOpen: true, init() { this.$nextTick(() => { if (! this.$refs.inlineToc || ! ('IntersectionObserver' in window)) return; new IntersectionObserver(([entry]) => { this.tocPassed = ! entry.isIntersecting && entry.boundingClientRect.top < 0; if (! this.tocPassed) this.floatingOpen = false; }, { threshold: 0 }).observe(this.$refs.inlineToc); }); } }" @keydown.escape.window="floatingOpen = false">
        <header class="relative isolate overflow-hidden bg-ink py-14 text-white md:py-20">
            @if ($post->image_url)
                <img class="absolute inset-0 -z-20 h-full w-full object-cover opacity-30" src="{{ $post->image_url }}" alt="" aria-hidden="true">
            @endif
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,color-mix(in_srgb,var(--site-color-ink)_96%,transparent),color-mix(in_srgb,var(--site-color-ink)_68%,transparent))]"></div>
            <div class="site-shell">
                <p class="flex max-w-3xl items-center gap-2 overflow-hidden text-sm text-slate-300"><a class="shrink-0 hover:text-white" href="{{ LocalizedUrl::route('home') }}">{{ __('site.home') }}</a><span class="text-slate-500">›</span><a class="shrink-0 hover:text-white" href="{{ LocalizedUrl::route('posts.index') }}">{{ __('site.news') }}</a><span class="text-slate-500">›</span><span class="truncate">{{ $post->title }}</span></p>
                <p class="font-display mt-7 text-4xl leading-tight tracking-[-0.045em] md:text-5xl">Tin tức</p>
            </div>
        </header>

        <section class="section-space">
            <div class="site-shell grid items-start gap-10 lg:grid-cols-[minmax(0,1fr)_17.75rem] lg:gap-9">
                <div class="min-w-0">
                    <h1 class="display-title max-w-4xl text-3xl leading-[1.18] md:text-4xl">{{ $post->title }}</h1>
                    <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-medium text-slate-500">
                        @if ($post->published_at)<time class="inline-flex items-center gap-2" datetime="{{ $post->published_at->toDateString() }}"><i class="fa-regular fa-calendar-days text-primary" aria-hidden="true"></i>{{ $post->published_at->translatedFormat('d/m/Y') }}</time>@endif
                        <span class="inline-flex items-center gap-2"><i class="fa-regular fa-user text-primary" aria-hidden="true"></i>{{ $website->company_name ?: $website->site_name }}</span>
                    </div>
                    <div class="mt-5 flex flex-wrap items-center gap-2" x-data="{ copied: false, link: @js($share['url']), async copy() { try { if (navigator.clipboard && window.isSecureContext) { await navigator.clipboard.writeText(this.link); } else { const input = document.createElement('textarea'); input.value = this.link; input.style.position = 'fixed'; input.style.opacity = '0'; document.body.appendChild(input); input.select(); document.execCommand('copy'); input.remove(); } this.copied = true; window.setTimeout(() => this.copied = false, 1800); } catch (error) { console.error('Không thể sao chép liên kết.', error); } } }" aria-label="Chia sẻ bài viết">
                        <span class="mr-1 text-xs font-semibold tracking-[0.08em] text-slate-400 uppercase">Chia sẻ</span>
                        <a class="grid size-9 place-items-center rounded-full border border-slate-200 bg-white text-sm text-slate-600 transition hover:-translate-y-0.5 hover:border-[#1877f2] hover:bg-[#1877f2] hover:text-white" href="{{ $share['facebook'] }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ bài viết lên Facebook">
                            <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                        </a>
                        <a class="grid size-9 place-items-center rounded-full border border-slate-200 bg-white text-sm text-slate-600 transition hover:-translate-y-0.5 hover:border-[#0a66c2] hover:bg-[#0a66c2] hover:text-white" href="{{ $share['linkedin'] }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ bài viết lên LinkedIn">
                            <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
                        </a>
                        <a class="grid size-9 place-items-center rounded-full border border-slate-200 bg-white text-sm text-slate-600 transition hover:-translate-y-0.5 hover:border-ink hover:bg-ink hover:text-white" href="{{ $share['x'] }}" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ bài viết lên X">
                            <span class="text-xs font-bold" aria-hidden="true">X</span>
                        </a>
                        <button class="inline-flex h-9 items-center gap-2 rounded-full border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:-translate-y-0.5 hover:border-primary hover:text-primary" type="button" @click="copy()" :aria-label="copied ? 'Đã sao chép liên kết' : 'Sao chép liên kết'">
                            <i class="fa-regular fa-copy text-sm" aria-hidden="true"></i>
                            <span x-text="copied ? 'Đã sao chép' : 'Sao chép link'"></span>
                        </button>
                    </div>

                    @if ($post->image_url)
                        <div class="mt-7 aspect-[16/8.5] overflow-hidden rounded-[1.75rem] bg-slate-200"><img class="h-full w-full object-cover" src="{{ $post->image_url }}" alt="{{ $post->title }}"></div>
                    @endif

                    <div class="mx-auto max-w-3xl">
                        @if ($post->excerpt)
                            <p class="mt-10 border-l-4 border-primary pl-5 text-lg leading-8 font-medium text-ink md:text-xl">{{ $post->excerpt }}</p>
                        @endif
                        @if ($tableOfContents !== [])
                            <nav class="mt-9 overflow-hidden rounded-2xl border border-slate-200 bg-mist/50" x-ref="inlineToc" aria-labelledby="inline-table-of-contents-title">
                                <button class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left md:px-6 md:py-5" type="button" @click="inlineTocOpen = !inlineTocOpen" :aria-expanded="inlineTocOpen.toString()" aria-controls="inline-table-of-contents">
                                    <span class="font-display block text-xl leading-tight text-ink" id="inline-table-of-contents-title">Mục lục bài viết</span>
                                    <span class="grid size-9 shrink-0 place-items-center rounded-full border border-slate-200 bg-white text-ink transition" :class="{ 'rotate-180': inlineTocOpen }" aria-hidden="true">
                                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                                    </span>
                                </button>
                                <div id="inline-table-of-contents" x-show="inlineTocOpen" x-transition.opacity.duration.200ms>
                                    <ol class="border-t border-slate-200 px-5 py-4 text-sm leading-6 md:px-6">
                                        @foreach ($tableOfContents as $item)
                                            <li @class(['mt-2 first:mt-0', 'pl-4' => $item['level'] === 3])>
                                                <a class="block rounded-xl px-3 py-2 font-medium text-slate-600 transition hover:bg-white hover:text-primary" href="#{{ $item['id'] }}">{{ $item['title'] }}</a>
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            </nav>

                            <aside class="fixed bottom-36 left-5 z-30 sm:left-6" x-cloak x-show="tocPassed" x-transition.opacity>
                                <button class="inline-flex min-h-11 items-center gap-2 rounded-full bg-ink px-4 py-2.5 text-sm font-semibold text-white shadow-[0_14px_32px_rgba(31,43,37,0.22)] transition hover:-translate-y-0.5 hover:bg-primary focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2" type="button" @click="floatingOpen = !floatingOpen" :aria-expanded="floatingOpen.toString()" aria-controls="floating-table-of-contents">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M9 6h11M9 12h11M9 18h11M4 6h.01M4 12h.01M4 18h.01"/></svg>
                                    <span>Mục lục</span>
                                    <svg class="size-3.5 transition-transform" :class="{ 'rotate-180': floatingOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                                </button>

                                <section class="absolute bottom-[calc(100%+0.75rem)] left-0 w-[min(calc(100vw-2.5rem),22rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_24px_60px_rgba(31,43,37,0.18)]" id="floating-table-of-contents" x-cloak x-show="floatingOpen" x-transition.origin.bottom.left @click.outside="floatingOpen = false" aria-labelledby="floating-table-of-contents-title">
                                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 bg-mist/55 px-5 py-4">
                                        <h2 class="font-display text-lg leading-tight text-ink" id="floating-table-of-contents-title">Mục lục bài viết</h2>
                                        <button class="grid size-8 place-items-center rounded-full text-slate-500 transition hover:bg-white hover:text-ink" type="button" @click="floatingOpen = false" aria-label="Đóng mục lục">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
                                        </button>
                                    </div>
                                    <ol class="max-h-[min(28rem,calc(100vh-10rem))] space-y-1 overflow-y-auto p-3 text-sm leading-6">
                                        @foreach ($tableOfContents as $item)
                                            <li @class(['pl-3' => $item['level'] === 3])>
                                                <a class="block rounded-xl px-3 py-2 font-medium text-slate-600 transition hover:bg-mist hover:text-primary" href="#{{ $item['id'] }}" @click="floatingOpen = false">{{ $item['title'] }}</a>
                                            </li>
                                        @endforeach
                                    </ol>
                                </section>
                            </aside>
                        @endif
                        <div class="article-prose pt-10 md:pt-12">{!! $post->body_html !!}</div>
                    </div>

                    @if ($previousPost || $nextPost)
                        <section class="mx-auto mt-14 max-w-3xl border-t border-slate-200 pt-8 md:mt-20">
                            <div class="grid gap-4 sm:grid-cols-2">
                                @if ($previousPost)
                                    <a class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-3 transition hover:border-primary hover:shadow-sm" href="{{ LocalizedUrl::post($previousPost) }}">
                                        @if ($previousPost->image_url)<img class="size-16 shrink-0 rounded-xl object-cover" src="{{ $previousPost->image_url }}" alt="" loading="lazy">@endif
                                        <span class="min-w-0">
                                            <span class="text-[0.65rem] font-bold tracking-[0.12em] text-slate-400 uppercase">Bài viết trước</span>
                                            <span class="mt-1 block line-clamp-2 text-sm font-semibold leading-5 text-ink group-hover:text-primary">{{ $previousPost->title }}</span>
                                            @if ($previousPost->published_at)<time class="mt-1 block text-xs text-slate-400" datetime="{{ $previousPost->published_at->toDateString() }}">{{ $previousPost->published_at->translatedFormat('d/m/Y') }}</time>@endif
                                        </span>
                                    </a>
                                @else
                                    <span></span>
                                @endif

                                @if ($nextPost)
                                    <a class="group flex flex-row-reverse items-center gap-4 rounded-2xl border border-slate-200 bg-white p-3 text-right transition hover:border-primary hover:shadow-sm" href="{{ LocalizedUrl::post($nextPost) }}">
                                        @if ($nextPost->image_url)<img class="size-16 shrink-0 rounded-xl object-cover" src="{{ $nextPost->image_url }}" alt="" loading="lazy">@endif
                                        <span class="min-w-0">
                                            <span class="text-[0.65rem] font-bold tracking-[0.12em] text-slate-400 uppercase">Bài viết tiếp theo</span>
                                            <span class="mt-1 block line-clamp-2 text-sm font-semibold leading-5 text-ink group-hover:text-primary">{{ $nextPost->title }}</span>
                                            @if ($nextPost->published_at)<time class="mt-1 block text-xs text-slate-400" datetime="{{ $nextPost->published_at->toDateString() }}">{{ $nextPost->published_at->translatedFormat('d/m/Y') }}</time>@endif
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </section>
                    @endif

                    <section class="mx-auto mt-14 max-w-3xl border-t border-slate-200 pt-12 md:mt-20" id="binh-luan">
                        <h2 class="display-title text-2xl leading-tight uppercase">Bình luận ({{ $post->approvedComments->count() }})</h2>

                        <div class="mt-6 grid gap-8 border-t border-slate-200 pt-7 lg:grid-cols-[minmax(0,.85fr)_minmax(0,1.15fr)]">
                            <div class="grid content-start gap-4">
                                @forelse ($post->approvedComments as $comment)
                                    <article class="flex gap-4">
                                        <span class="grid size-11 shrink-0 place-items-center rounded-full bg-mist text-sm font-bold text-primary" aria-hidden="true">{{ Str::upper(Str::substr($comment->author_name, 0, 1)) }}</span>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                                                <h3 class="text-sm font-semibold text-ink">{{ $comment->author_name }}</h3>
                                                <time class="text-xs font-medium text-slate-400" datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y H:i') }}</time>
                                            </div>
                                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $comment->body }}</p>
                                        </div>
                                    </article>
                                @empty
                                    <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500">Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ ý kiến.</p>
                                @endforelse
                            </div>

                            <x-comment-form :post="$post" :compact="true" />
                        </div>
                    </section>
                </div>

                @include('frontend.partials.news-sidebar')
            </div>
        </section>
    </article>
@endsection
