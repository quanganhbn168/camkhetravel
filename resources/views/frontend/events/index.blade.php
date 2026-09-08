@extends('layouts.master')
@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page')
@section('main_class', 'bni-experience-main')

@section('content')
    @include('frontend.bni.partials.navigation')
    <div class="events-page">
        <section class="events-hero" aria-labelledby="events-title">
            <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">
                <nav class="events-breadcrumb" aria-label="Đường dẫn"><a href="{{ LocalizedUrl::route('bni.handover') }}">Lễ chuyển giao BNI</a><span aria-hidden="true">/</span><span>Sự kiện</span></nav>
                <p class="events-eyebrow">Gặp gỡ · Kết nối · Đồng hành</p>
                <h1 id="events-title">Sự kiện trong cộng đồng BNI</h1>
                <p class="events-hero__description">Cập nhật các chương trình kết nối doanh nghiệp, giao lưu và hoạt động cộng đồng.</p>
            </div>
        </section>

        <section class="events-list" aria-label="Danh sách sự kiện">
            <div class="site-container mx-auto w-full max-w-7xl px-4 lg:px-8">
                <div class="events-toolbar">
                    <nav class="events-filters" aria-label="Lọc sự kiện">
                        @foreach ($filters as $value => $label)
                            <a href="{{ LocalizedUrl::route('bni.events.index', $value === 'tat-ca' ? [] : ['trang-thai' => $value]) }}" @if ($filter === $value) aria-current="page" @endif>{{ $label }}</a>
                        @endforeach
                    </nav>
                    <p>{{ $events->total() }} sự kiện</p>
                </div>
                <div class="events-grid">
                    @forelse ($events as $event)
                        <article class="event-card">
                            <a class="event-card__visual" href="{{ $event['url'] }}" tabindex="-1" aria-hidden="true">
                                @if ($event['image'])
                                    <img src="{{ $event['image'] }}" alt="" width="960" height="540" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                                @else
                                    <span class="event-card__placeholder"><x-heroicon-o-calendar-days aria-hidden="true" /><span>THT MEDIA</span></span>
                                @endif
                                <span class="event-card__status {{ $event['is_past'] ? 'event-card__status--past' : '' }}">{{ $event['status'] }}</span>
                            </a>
                            <div class="event-card__body">
                                <p class="events-eyebrow">{{ $event['category'] }}</p>
                                <h2><a href="{{ $event['url'] }}">{{ $event['title'] }}</a></h2>
                                <div class="event-card__meta">
                                    <p><x-heroicon-o-calendar-days aria-hidden="true" /><span>{{ $event['date'] }}</span></p>
                                    <p><x-heroicon-o-map-pin aria-hidden="true" /><span>{{ $event['venue'] }}</span></p>
                                </div>
                                @if ($event['summary'])<p class="event-card__summary">{{ $event['summary'] }}</p>@endif
                                <a class="event-card__link" href="{{ $event['url'] }}" aria-label="Xem sự kiện: {{ $event['title'] }}">Xem sự kiện <span aria-hidden="true">↗</span></a>
                            </div>
                        </article>
                    @empty
                        <div class="events-empty"><x-heroicon-o-calendar-days aria-hidden="true" /><h2>Chưa có sự kiện trong mục này</h2><p>Anh/chị có thể xem các chương trình khác trong danh sách sự kiện.</p><a href="{{ LocalizedUrl::route('bni.events.index') }}">Xem tất cả sự kiện →</a></div>
                    @endforelse
                </div>
                @if ($events->hasPages())<div class="mt-8">{{ $events->links() }}</div>@endif
            </div>
        </section>
    </div>
@endsection
