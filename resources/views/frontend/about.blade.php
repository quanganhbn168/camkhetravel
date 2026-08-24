@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="brand-gradient-light py-16 md:py-24">
        <div class="site-shell grid items-center gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-18">
            <div><p class="eyebrow mb-5">{{ __('site.about') }}</p><h1 class="display-title text-4xl leading-tight md:text-6xl">{{ $page->title }}</h1>@if ($page->excerpt)<p class="mt-6 max-w-xl text-base leading-8 text-slate-500 md:text-lg">{{ $page->excerpt }}</p>@endif<a class="button-dark mt-8" href="{{ LocalizedUrl::route('contact') }}">{{ __('site.discuss_project') }}</a></div>
            <div class="aspect-[1.15] overflow-hidden rounded-[2rem] bg-slate-200 shadow-[0_24px_60px_rgba(16,35,62,0.12)]">@if ($page->image_url)<img class="h-full w-full object-cover" src="{{ $page->image_url }}" alt="{{ $page->title }}">@else<span class="image-placeholder">THT</span>@endif</div>
        </div>
    </section>
    <article class="section-space"><div class="narrow-shell article-prose">{!! $page->body_html !!}</div></article>
@endsection
