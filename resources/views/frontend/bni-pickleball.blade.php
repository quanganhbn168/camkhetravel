@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page bni-pickleball-page')
@section('main_id', 'bni-pickleball-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    <div x-data="{ scheduleDay: {{ $scheduleDays->first()['number'] ?? 1 }} }">
        <section class="bni-pickleball-hero">
            @if ($heroImageUrl)<img src="{{ $heroImageUrl }}" alt="" aria-hidden="true">@endif
            <div class="site-shell bni-pickleball-hero__content">
                <a class="bni-back-link" href="{{ LocalizedUrl::route('bni.handover') }}">← Lễ chuyển giao BNI</a>
                <h1>{{ $event?->title ?: 'BNI Pickleball Championship' }}</h1>
                <p>{{ $event?->summary ?: 'Một giải đấu giao hữu để cộng đồng BNI kết nối, thi đấu và lan tỏa tinh thần đồng đội.' }}</p>
                <div class="bni-countdown bni-countdown--light" @if ($event?->starts_at) data-bni-countdown="{{ $event->starts_at->toIso8601String() }}" @endif>
                    <p>Đếm ngược đến giải đấu</p>
                    <div><span data-bni-countdown-days>--<small>Ngày</small></span><span data-bni-countdown-hours>--<small>Giờ</small></span><span data-bni-countdown-minutes>--<small>Phút</small></span><span data-bni-countdown-seconds>--<small>Giây</small></span></div>
                </div>
            </div>
        </section>

        <section class="bni-section">
            <div class="site-shell bni-pickleball-register">
                <div><h2>Đăng ký tham gia</h2><p>Đăng ký để Ban tổ chức sắp xếp bảng đấu, thông tin check-in và hỗ trợ phù hợp.</p>@if ($event?->venue)<p class="bni-meta-line">{{ $event->venue }}</p>@endif</div>
                <form class="bni-form" method="POST" action="{{ LocalizedUrl::route('bni.pickleball.register') }}">
                    @csrf
                    <div><label for="pickleball-full-name">Họ và tên</label><input id="pickleball-full-name" name="full_name" required value="{{ old('full_name') }}"></div>
                    <div><label for="pickleball-phone">Số điện thoại</label><input id="pickleball-phone" name="phone" required value="{{ old('phone') }}"></div>
                    <div><label for="pickleball-email">Email</label><input id="pickleball-email" name="email" type="email" value="{{ old('email') }}"></div>
                    <div><label for="pickleball-team">Tên đội</label><input id="pickleball-team" name="team_name" value="{{ old('team_name') }}"></div>
                    <div class="bni-form__full"><label for="pickleball-note">Ghi chú</label><textarea id="pickleball-note" name="note" rows="3">{{ old('note') }}</textarea></div>
                    <button class="bni-button bni-button--red bni-form__full" type="submit">Gửi đăng ký</button>
                </form>
            </div>
        </section>

        <section class="bni-section bni-schedule" aria-labelledby="pickleball-schedule-title">
            <div class="site-shell">
                <div class="bni-section-heading"><h2 id="pickleball-schedule-title">Lịch thi đấu & kết quả</h2></div>
                <div class="bni-schedule__layout">
                    <div class="bni-schedule__main">
                        <div class="bni-tab-list">
                            @forelse ($scheduleDays as $day)<button type="button" @click="scheduleDay = {{ $day['number'] }}" :class="scheduleDay === {{ $day['number'] }} && 'is-active'">{{ $day['label'] }}</button>@empty<button class="is-active" type="button">Ngày thi đấu</button>@endforelse
                        </div>
                        @forelse ($scheduleDays as $day)
                            <div class="bni-schedule-list" x-show="scheduleDay === {{ $day['number'] }}">
                                @foreach ($day['items'] as $item)
                                    <article class="bni-schedule-item"><time>{{ $item['time'] ?: 'Đang cập nhật' }}</time><div>@if ($item['stage'])<p>{{ $item['stage'] }}</p>@endif<h3>{{ $item['title'] }}</h3>@if ($item['description'])<span>{{ $item['description'] }}</span>@endif @if ($item['result'])<strong>Kết quả: {{ $item['result'] }}</strong>@endif</div></article>
                                @endforeach
                            </div>
                        @empty
                            <div class="bni-schedule-list"><p class="bni-empty-copy">Lịch và kết quả sẽ được cập nhật từ panel BNI.</p></div>
                        @endforelse
                    </div>
                    <aside class="bni-schedule__aside"><img src="{{ asset('bni-logo-red.svg') }}" alt="BNI"><div><h3>Kết quả trực tiếp</h3><p>Ban tổ chức cập nhật bảng đấu và kết quả tại đây.</p></div></aside>
                </div>
            </div>
        </section>

        <section class="bni-section bni-news" aria-labelledby="pickleball-news-title">
            <div class="site-shell"><div class="bni-section-heading"><h2 id="pickleball-news-title">Tin giải đấu</h2><p class="bni-section-heading__description">Cập nhật từ sân Pickleball.</p></div><div class="bni-pickleball-news">@forelse ($articles as $article)<article class="bni-news-card">@if ($article['image_url'])<img src="{{ $article['image_url'] }}" alt="" loading="lazy">@endif<div><p>Pickleball</p><h3><a href="{{ LocalizedUrl::route('bni.articles.show', ['article' => $article['slug']]) }}">{{ $article['title'] }}</a></h3><span>{{ $article['excerpt'] }}</span></div></article>@empty<p class="bni-empty-copy">Tin giải đấu sẽ hiển thị tại đây sau khi được xuất bản.</p>@endforelse</div></div>
        </section>
    </div>
@endsection
