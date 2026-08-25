@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page')
@section('main_id', 'bni-handover-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    <div x-data="{ scheduleDay: {{ $scheduleDays->first()['number'] ?? 1 }}, newsTab: 'event', galleryTab: 'event' }">
        <section class="bni-experience-hero" aria-labelledby="bni-handover-title">
            @if ($heroImageUrl)
                <img class="bni-experience-hero__image" src="{{ $heroImageUrl }}" alt="" aria-hidden="true">
            @endif
            <div class="bni-experience-hero__shade" aria-hidden="true"></div>
            <div class="site-shell bni-experience-hero__content">
                <img class="bni-experience-hero__logo" src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                <p class="bni-experience-kicker">{{ $event?->kicker ?: 'BNI VIETNAM' }}</p>
                <h1 id="bni-handover-title">{{ $event?->title ?: 'Lễ chuyển giao Ban Điều hành BNI' }}</h1>
                <p class="bni-experience-hero__summary">{{ $event?->summary ?: 'Một dấu mốc kết nối, tri ân hành trình đã qua và mở ra nhiệm kỳ mới.' }}</p>
                <div class="bni-experience-hero__actions">
                    <a class="bni-button bni-button--red" href="#lich-trinh">Xem lịch trình <span aria-hidden="true">↓</span></a>
                    @guest
                        <a class="bni-button bni-button--ghost" href="{{ LocalizedUrl::route('bni.member.login') }}">Đăng nhập hội viên</a>
                    @else
                        <form method="POST" action="{{ LocalizedUrl::route('bni.member.logout') }}">@csrf<button class="bni-button bni-button--ghost" type="submit">Đăng xuất</button></form>
                    @endguest
                </div>
            </div>
        </section>

        <section class="bni-chapters" aria-label="Bốn chapter BNI">
            <div class="site-shell bni-chapters__grid">
                @forelse ($chapters as $chapter)
                    <article class="bni-chapter-card">
                        @if ($chapter['logo_url'])<img src="{{ $chapter['logo_url'] }}" alt="{{ $chapter['name'] }}" loading="lazy">@endif
                        <h2>{{ $chapter['short_name'] }}</h2>
                        @if ($chapter['description'])<p>{{ $chapter['description'] }}</p>@endif
                    </article>
                @empty
                    @foreach (['KINHBAC', 'KBG', 'IMPACT', 'FAMOUS'] as $chapter)
                        <article class="bni-chapter-card"><h2>{{ $chapter }}</h2><p>Thông tin chapter sẽ được cập nhật từ CMS BNI.</p></article>
                    @endforeach
                @endforelse
            </div>
        </section>

        <section class="bni-section" aria-labelledby="bni-handover-overview-title">
            <div class="site-shell bni-overview">
                <div class="bni-overview__content">
                    <h2 id="bni-handover-overview-title">Sự kiện chuyển giao</h2>
                    <div class="bni-rich-copy">{!! $event?->content ?: '<p>Không gian để các chapter cùng nhìn lại hành trình, tri ân Ban Điều hành và khởi động một chu kỳ phát triển mới.</p>' !!}</div>
                    <div class="bni-purpose-grid">
                        @forelse ($purposes as $purpose)
                            <article class="bni-purpose-card"><h3>{{ $purpose['title'] }}</h3><p>{{ $purpose['description'] }}</p></article>
                        @empty
                            @foreach (['Kết nối ban điều hành', 'Lan tỏa văn hóa BNI', 'Ghi nhận hành trình', 'Khởi động nhiệm kỳ mới'] as $purpose)
                                <article class="bni-purpose-card"><h3>{{ $purpose }}</h3><p>Nội dung sẽ được Ban tổ chức cập nhật.</p></article>
                            @endforeach
                        @endforelse
                    </div>
                </div>
                <aside class="bni-overview__video" aria-label="Video chuyển giao">
                    @if ($event?->video_url)
                        <a class="bni-video-card glightbox" href="{{ $event->video_url }}" data-type="video" data-gallery="bni-handover-video" data-title="{{ $event->title }}" target="_blank" rel="noopener" aria-label="Xem video chuyển giao">
                            @if ($heroImageUrl)<img src="{{ $heroImageUrl }}" alt="" aria-hidden="true">@endif
                            <span class="bni-video-card__play" aria-hidden="true">▶</span>
                            <span>Phát video</span>
                        </a>
                    @else
                        <div class="bni-video-card bni-video-card--empty"><img src="{{ asset('bni-logo-red.svg') }}" alt="BNI"><p>Video sẽ được chọn trong phần quản trị sự kiện.</p></div>
                    @endif
                </aside>
            </div>
        </section>

        <section class="bni-section bni-schedule" id="lich-trinh" aria-labelledby="bni-schedule-title">
            <div class="site-shell">
                <div class="bni-section-heading"><h2 id="bni-schedule-title">Lịch trình sự kiện</h2></div>
                <div class="bni-schedule__layout">
                    <div class="bni-schedule__main">
                        <div class="bni-tab-list" role="tablist" aria-label="Ngày sự kiện">
                            @forelse ($scheduleDays as $day)
                                <button type="button" role="tab" @click="scheduleDay = {{ $day['number'] }}" :aria-selected="(scheduleDay === {{ $day['number'] }}).toString()" :class="scheduleDay === {{ $day['number'] }} && 'is-active'">{{ $day['label'] }}</button>
                            @empty
                                <button class="is-active" type="button">Ngày 1</button><button type="button">Ngày 2</button>
                            @endforelse
                        </div>
                        @forelse ($scheduleDays as $day)
                            <div class="bni-schedule-list" x-show="scheduleDay === {{ $day['number'] }}" x-transition.opacity>
                                @foreach ($day['items'] as $item)
                                    <article class="bni-schedule-item"><time>{{ $item['time'] ?: 'Đang cập nhật' }}</time><div><h3>{{ $item['title'] }}</h3>@if ($item['description'])<p>{{ $item['description'] }}</p>@endif @if ($item['location'])<span>{{ $item['location'] }}</span>@endif</div></article>
                                @endforeach
                            </div>
                        @empty
                            <div class="bni-schedule-list"><p class="bni-empty-copy">Lịch trình sẽ được Ban tổ chức cập nhật trên CMS BNI.</p></div>
                        @endforelse
                    </div>
                    <aside class="bni-schedule__aside">
                        @if ($heroImageUrl)<img src="{{ $heroImageUrl }}" alt="Hình ảnh {{ $event?->title ?: 'Lễ chuyển giao BNI' }}">@else<img src="{{ asset('bni-logo-red.svg') }}" alt="BNI">@endif
                        <div class="bni-countdown" @if ($event?->starts_at) data-bni-countdown="{{ $event->starts_at->toIso8601String() }}" @endif>
                            <p>Đếm ngược đến sự kiện</p>
                            <div><span data-bni-countdown-days>--<small>Ngày</small></span><span data-bni-countdown-hours>--<small>Giờ</small></span><span data-bni-countdown-minutes>--<small>Phút</small></span><span data-bni-countdown-seconds>--<small>Giây</small></span></div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <section class="bni-section" aria-labelledby="bni-activities-title">
            <div class="site-shell">
                <div class="bni-section-heading bni-section-heading--center"><h2 id="bni-activities-title">Những hoạt động đặc biệt</h2></div>
                <div class="bni-activities-grid">
                    @forelse ($activities as $activity)
                        <a class="bni-activity-card bni-activity-card--{{ $activity['type'] }}" href="{{ $activity['link_url'] ?: '#' }}" @if (! $activity['link_url']) aria-disabled="true" @endif>
                            @if ($activity['image_url'])<img src="{{ $activity['image_url'] }}" alt="" loading="lazy">@endif
                            <div><p>{{ $activity['type'] === 'pickleball' ? 'Kết nối thể thao' : 'Sự kiện đặc biệt' }}</p><h3>{{ $activity['title'] }}</h3><span>{{ $activity['description'] }}</span>@if ($activity['type'] === 'pickleball')<b>Khám phá giải đấu →</b>@endif</div>
                        </a>
                    @empty
                        <article class="bni-activity-card bni-activity-card--handover"><div><p>Sự kiện đặc biệt</p><h3>Hình ảnh lễ chuyển giao</h3><span>Hình ảnh sẽ được cập nhật từ thư viện BNI.</span></div></article>
                        <article class="bni-activity-card bni-activity-card--gala"><div><p>Kết nối cộng đồng</p><h3>Gala dinner & sinh nhật</h3><span>Không gian giao lưu và vinh danh hội viên.</span></div></article>
                        <a class="bni-activity-card bni-activity-card--pickleball" href="{{ LocalizedUrl::route('bni.pickleball') }}"><div><p>Kết nối thể thao</p><h3>BNI Pickleball</h3><span>Giải đấu giao hữu dành cho cộng đồng BNI.</span><b>Khám phá giải đấu →</b></div></a>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bni-section bni-news" aria-labelledby="bni-news-title">
            <div class="site-shell">
                <div class="bni-news__heading"><div><h2 id="bni-news-title">Tin tức</h2><p class="bni-section-heading__description">Cập nhật từ sự kiện và các chapter.</p></div><div class="bni-tab-list" role="tablist"><button type="button" @click="newsTab = 'event'" :class="newsTab === 'event' && 'is-active'">Tin sự kiện</button><button type="button" @click="newsTab = 'chapter'" :class="newsTab === 'chapter' && 'is-active'">Tin các chapter</button></div></div>
                <div class="bni-news-grid">
                    @forelse ($articles as $article)
                        <article class="bni-news-card {{ $loop->first ? 'bni-news-card--featured' : '' }}" x-show="newsTab === '{{ $article['type'] === 'chapter' ? 'chapter' : 'event' }}'" x-transition.opacity>
                            @if ($article['image_url'])<img src="{{ $article['image_url'] }}" alt="" loading="lazy">@endif
                            <div><p>{{ $article['chapter'] ?: ($article['type'] === 'chapter' ? 'Tin chapter' : 'Tin sự kiện') }}</p><h3><a href="{{ LocalizedUrl::route('bni.articles.show', ['article' => $article['slug']]) }}">{{ $article['title'] }}</a></h3>@if ($article['excerpt'])<span>{{ $article['excerpt'] }}</span>@endif @if ($article['published_at'])<time datetime="{{ $article['published_at']->toDateString() }}">{{ $article['published_at']->translatedFormat('d/m/Y') }}</time>@endif</div>
                        </article>
                    @empty
                        <p class="bni-empty-copy">Tin tức sẽ hiển thị sau khi Ban tổ chức xuất bản từ panel BNI.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bni-section bni-gallery" aria-labelledby="bni-gallery-title">
            <div class="site-shell"><div class="bni-news__heading"><div><h2 id="bni-gallery-title">Thư viện ảnh</h2><p class="bni-section-heading__description">Những khoảnh khắc kết nối từ sự kiện và các chapter.</p></div><div class="bni-tab-list" role="tablist"><button type="button" @click="galleryTab = 'event'" :class="galleryTab === 'event' && 'is-active'">Theo sự kiện</button><button type="button" @click="galleryTab = 'chapter'" :class="galleryTab === 'chapter' && 'is-active'">Theo chapter</button></div></div>
                <div class="bni-gallery-grid">
                    @forelse ($galleries as $gallery)
                        @if ($gallery['image_url'])<a class="bni-gallery-card glightbox" href="{{ $gallery['image_url'] }}" data-gallery="bni-gallery-{{ $gallery['group'] }}" data-title="{{ $gallery['title'] }}" x-show="galleryTab === '{{ $gallery['group'] }}'" x-transition.opacity><img src="{{ $gallery['image_url'] }}" alt="{{ $gallery['title'] ?: 'Hình ảnh BNI' }}" loading="lazy">@if ($gallery['title'])<span>{{ $gallery['title'] }}</span>@endif</a>@endif
                    @empty
                        <p class="bni-empty-copy">Hình ảnh sẽ được chọn từ thư viện ảnh BNI trong panel riêng.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bni-contact-band" aria-labelledby="bni-contact-title"><div class="site-shell"><div><h2 id="bni-contact-title">Kết nối cùng ban tổ chức</h2><p>Anh/chị cần hỗ trợ về sự kiện? {{ $event?->venue ?: 'Thông tin địa điểm sẽ được cập nhật.' }}</p></div><div class="bni-contact-band__actions">@if ($event?->contact_phone)<a class="bni-button bni-button--red" href="tel:{{ preg_replace('/\s+/', '', $event->contact_phone) }}">{{ $event->contact_phone }}</a>@endif <a class="bni-button bni-button--dark" href="{{ LocalizedUrl::route('contact') }}">Liên hệ THT Media</a></div></div></section>
    </div>
@endsection
