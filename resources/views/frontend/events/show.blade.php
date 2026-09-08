@extends('layouts.master')
@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page')
@section('main_class', 'bni-experience-main')

@section('content')
    @include('frontend.bni.partials.navigation')
    <article class="events-page event-detail">
        <header class="events-hero">
            <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">
                <nav class="events-breadcrumb" aria-label="Đường dẫn"><a href="{{ LocalizedUrl::route('bni.handover') }}">Lễ chuyển giao BNI</a><span aria-hidden="true">/</span><a href="{{ LocalizedUrl::route('bni.events.index') }}">Sự kiện</a></nav>
                <p class="events-eyebrow">{{ $details['category'] }} · {{ $details['status'] }}</p>
                <h1>{{ $event->title }}</h1>
                <div class="event-card__meta"><p><x-heroicon-o-calendar-days aria-hidden="true" />{{ $details['date'] }}</p><p><x-heroicon-o-map-pin aria-hidden="true" />{{ $details['venue'] }}</p></div>
            </div>
        </header>
        <div class="site-container mx-auto w-full max-w-4xl px-4 py-10 lg:px-8">
            @if ($details['image'])<img class="event-detail__image" src="{{ $details['image'] }}" alt="{{ $event->title }}" fetchpriority="high">@endif
            @if ($event->summary)<p class="event-detail__summary">{{ $event->summary }}</p>@endif
            @if ($event->content)<div class="prose max-w-none">{!! $event->content !!}</div>@endif
            @if ($event->scheduleDays->isNotEmpty())
                <section class="event-detail__schedule" aria-labelledby="event-schedule-title">
                    <h2 id="event-schedule-title">Lịch trình sự kiện</h2>
                    @foreach ($event->scheduleDays as $day)
                        <h3>{{ $day->title ?: $day->event_date?->format('d/m/Y') }}</h3>
                        <ol>@foreach ($day->items as $item)<li><strong>{{ $item->title }}</strong>@if ($item->description)<p>{{ $item->description }}</p>@endif</li>@endforeach</ol>
                    @endforeach
                </section>
            @endif
            <a class="event-card__link" href="{{ LocalizedUrl::route('bni.events.index') }}">← Tất cả sự kiện</a>
        </div>
    </article>
@endsection
