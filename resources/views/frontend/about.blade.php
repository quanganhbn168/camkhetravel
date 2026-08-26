@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="brand-gradient-light py-16 md:py-24">
        <div class="site-shell grid items-center gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-18">
            <div><h1 class="display-title text-4xl leading-tight md:text-6xl">{{ $about['title'] }}</h1>@if ($about['intro'])<p class="mt-6 max-w-xl text-base leading-8 text-slate-500 md:text-lg">{{ $about['intro'] }}</p>@endif<a class="button-dark mt-8" href="{{ LocalizedUrl::route('contact') }}">{{ __('site.discuss_project') }}</a></div>
            <div class="aspect-[1.15] overflow-hidden rounded-[2rem] bg-slate-200 shadow-[0_24px_60px_rgba(16,35,62,0.12)]">@if ($about['image_url'])<img class="h-full w-full object-cover" src="{{ $about['image_url'] }}" alt="{{ $about['title'] }}">@else<span class="image-placeholder">THT</span>@endif</div>
        </div>
    </section>
    @if ($about['story'])
        <article class="section-space"><div class="narrow-shell article-prose">{!! $about['story'] !!}</div></article>
    @endif
    @if ($about['history'] || $about['mission'] || $about['vision'] || $about['core_values'])
        <section class="border-t border-slate-200 bg-mist section-space">
            <div class="site-shell grid gap-5 md:grid-cols-2">
                @if ($about['history'])<article class="rounded-2xl bg-white p-6 md:p-8"><h2 class="display-title text-2xl">Hành trình phát triển</h2><p class="mt-5 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $about['history'] }}</p></article>@endif
                @if ($about['mission'])<article class="rounded-2xl bg-white p-6 md:p-8"><h2 class="display-title text-2xl">Sứ mệnh</h2><p class="mt-5 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $about['mission'] }}</p></article>@endif
                @if ($about['vision'])<article class="rounded-2xl bg-white p-6 md:p-8"><h2 class="display-title text-2xl">Tầm nhìn</h2><p class="mt-5 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $about['vision'] }}</p></article>@endif
                @if ($about['core_values'])<article class="rounded-2xl bg-white p-6 md:p-8"><h2 class="display-title text-2xl">Giá trị cốt lõi</h2><div class="article-prose mt-5">{!! $about['core_values'] !!}</div></article>@endif
            </div>
        </section>
    @endif
@endsection
