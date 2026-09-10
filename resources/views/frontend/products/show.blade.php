@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <article>
        <header class="relative isolate overflow-hidden bg-ink py-16 text-white md:py-24">
            @if ($product->image_url)<img class="absolute inset-0 -z-20 h-full w-full object-cover opacity-30" src="{{ $product->image_url }}" alt="" aria-hidden="true">@endif
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,color-mix(in_srgb,var(--site-color-ink)_96%,transparent),color-mix(in_srgb,var(--site-color-ink)_62%,transparent))]"></div>
            <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">
                <p class="flex flex-wrap items-center gap-2 text-sm text-slate-300"><a class="hover:text-white" href="{{ LocalizedUrl::route('home') }}">Trang chủ</a><span class="text-slate-500">›</span><a class="hover:text-white" href="{{ LocalizedUrl::route('products.index') }}">Sản phẩm</a>@if ($product->category)<span class="text-slate-500">›</span><span>{{ $product->category->name }}</span>@endif</p>
                <h1 class="mt-7 max-w-4xl font-display text-4xl leading-tight tracking-[-0.045em] md:text-6xl">{{ $product->title }}</h1>
                @if ($product->excerpt)<p class="mt-5 max-w-2xl text-base leading-8 text-white/75 md:text-lg">{{ $product->excerpt }}</p>@endif
                @if ($product->sku)<p class="mt-6 text-sm font-semibold uppercase tracking-[0.16em] text-primary">Mã sản phẩm: {{ $product->sku }}</p>@endif
            </div>
        </header>

        <section class="section-space">
            <div class="site-container mx-auto grid w-full max-w-7xl items-start gap-10 px-4 lg:grid-cols-[minmax(0,1.15fr)_minmax(18rem,.85fr)] lg:px-8">
                <div>
                    @if ($product->image_url)<div class="overflow-hidden rounded-[1.75rem] bg-slate-100"><img class="h-auto w-full object-cover" src="{{ $product->image_url }}" alt="{{ $product->title }}"></div>@endif
                    @if ($galleryImages)<div class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4">@foreach ($galleryImages as $image)<img class="aspect-square w-full rounded-2xl object-cover" src="{{ $image }}" alt="" loading="lazy">@endforeach</div>@endif
                    @if ($product->body)<div class="article-prose mt-10">{!! $product->body !!}</div>@endif
                </div>
                <aside class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-[0_18px_48px_rgba(6,25,37,.08)] md:p-8">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary">Thông tin sản phẩm</p>
                    <dl class="mt-6 divide-y divide-slate-100 text-sm">
                        @if ($product->category)<div class="flex justify-between gap-4 py-4"><dt class="text-slate-500">Danh mục</dt><dd class="text-right font-semibold text-ink">{{ $product->category->name }}</dd></div>@endif
                        @if ($product->sku)<div class="flex justify-between gap-4 py-4"><dt class="text-slate-500">Mã sản phẩm</dt><dd class="text-right font-semibold text-ink">{{ $product->sku }}</dd></div>@endif
                        <div class="flex justify-between gap-4 py-4"><dt class="text-slate-500">Tư vấn</dt><dd class="text-right font-semibold text-primary">Liên hệ DVTEC</dd></div>
                    </dl>
                    @if ($product->tags->isNotEmpty())<div class="mt-6 flex flex-wrap gap-2">@foreach ($product->tags as $tag)<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">#{{ $tag->name }}</span>@endforeach</div>@endif
                    <a class="button-primary mt-7 w-full" href="{{ LocalizedUrl::route('contact') }}">Nhận tư vấn sản phẩm <span aria-hidden="true">→</span></a>
                </aside>
            </div>
        </section>

        @if ($faqItems->isNotEmpty())
            <section class="home-faq section-space border-t border-slate-100">
                <div class="site-container mx-auto grid w-full max-w-7xl gap-10 px-4 lg:grid-cols-[.75fr_1.25fr] lg:px-8">
                    <h2 class="display-title text-3xl leading-tight uppercase md:text-4xl">Câu hỏi thường gặp</h2>
                    <div class="home-faq__list">@foreach ($faqItems as $faq)<details class="home-faq__item" @if ($loop->first) open @endif><summary class="home-faq__question"><span>{{ $faq->question }}</span><span class="home-faq__indicator" aria-hidden="true">+</span></summary><div class="home-faq__answer"><p>{{ $faq->answer }}</p></div></details>@endforeach</div>
                </div>
            </section>
        @endif
    </article>
@endsection
