@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page')
@section('main_id', 'bni-handover-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    @php
        $eventPosterUrl = $eventVideo['poster_url'];
        $chapterMedia = $chapters;
    @endphp
    <div x-data="{ scheduleDay: {{ $scheduleDays->first()['number'] ?? 1 }}, newsTab: 'event', galleryTab: 'event' }">
        <nav class="bni-handover-page-nav" aria-label="Điều hướng Lễ chuyển giao">
            <div class="site-shell bni-handover-page-nav__inner">
                <a class="bni-handover-page-nav__brand" href="#tong-quan" aria-label="Về đầu trang Lễ chuyển giao">
                    <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                    <span>Lễ chuyển giao</span>
                </a>
                <div class="bni-handover-page-nav__links">
                    <a href="#su-kien">Sự kiện</a>
                    <a href="#video-su-kien">Video</a>
                    @foreach ($chapterMedia as $chapter)
                        <a href="#chapter-{{ $chapter['slug'] }}">{{ $chapter['short_name'] }}</a>
                    @endforeach
                    <a href="#lich-trinh">Lịch trình</a>
                    <a href="#tin-tuc-bni">Tin tức</a>
                    <a href="#thu-vien-anh">Thư viện</a>
                </div>
                <a class="bni-button bni-button--red bni-handover-page-nav__cta" href="{{ $registration['url'] }}">{{ $registration['label'] }}</a>
            </div>
        </nav>

        <section class="bni-experience-hero" id="tong-quan" aria-labelledby="bni-handover-title">
            @if ($heroImageUrl)
                <img class="bni-experience-hero__image" src="{{ $heroImageUrl }}" alt="" aria-hidden="true">
            @endif
            <div class="bni-experience-hero__shade" aria-hidden="true"></div>
            <div class="site-shell bni-experience-hero__layout">
                <div class="bni-experience-hero__content">
                    <img class="bni-experience-hero__logo" src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                    <p class="bni-experience-kicker">{{ $event?->kicker ?: 'BNI VIETNAM' }}</p>
                    <h1 id="bni-handover-title">{{ $event?->title ?: 'Lễ chuyển giao Ban Điều hành BNI' }}</h1>
                    <p class="bni-experience-hero__summary">{{ $event?->summary ?: 'Một dấu mốc kết nối, tri ân hành trình đã qua và mở ra nhiệm kỳ mới.' }}</p>
                </div>
                <div class="bni-experience-hero__actions">
                    <a class="bni-button bni-button--red" href="#lich-trinh">Xem lịch trình <span aria-hidden="true">↓</span></a>
                    <a class="bni-button bni-button--ghost" href="{{ $registration['url'] }}">{{ $registration['label'] }}</a>
                    @guest
                        <a class="bni-button bni-button--ghost" href="{{ LocalizedUrl::route('bni.member.login') }}">Đăng nhập hội viên</a>
                    @else
                        <form method="POST" action="{{ LocalizedUrl::route('bni.member.logout') }}">@csrf<button class="bni-button bni-button--ghost" type="submit">Đăng xuất</button></form>
                    @endguest
                </div>
            </div>
        </section>

        <section class="bni-section bni-chapter-widgets" id="chapter-widgets" aria-labelledby="bni-chapter-widgets-title">
            <div class="site-shell">
                <div class="bni-section-heading bni-section-heading--center">
                    <h2 id="bni-chapter-widgets-title">Các chapter đồng hành</h2>
                    <p class="bni-section-heading__description">Thông tin bốn chapter tham gia sự kiện chuyển giao.</p>
                </div>
                <div class="bni-chapter-widgets__grid">
                    @forelse ($chapterMedia as $chapter)
                        <article class="bni-chapter-widget">
                            <div class="bni-chapter-widget__visual">
                                @if ($chapter['cover_url'])
                                    <img src="{{ $chapter['cover_url'] }}" alt="Ảnh chapter {{ $chapter['name'] }}" loading="lazy">
                                @else
                                    <div class="bni-database-media-placeholder"><span>{{ $chapter['short_name'] }}</span><small>Chưa gắn ảnh trong CMS BNI</small></div>
                                @endif
                                @if ($chapter['logo_url'])
                                    <span class="bni-chapter-widget__logo"><img src="{{ $chapter['logo_url'] }}" alt="Logo {{ $chapter['name'] }}" loading="lazy"></span>
                                @endif
                            </div>
                            <div class="bni-chapter-widget__content">
                                <p>BNI Chapter</p>
                                <h3>{{ $chapter['short_name'] }}</h3>
                                @if ($chapter['description'])<span>{{ $chapter['description'] }}</span>@endif
                                <a href="#chapter-{{ $chapter['slug'] }}" aria-label="Xem chi tiết {{ $chapter['name'] }}">Xem chi tiết <b aria-hidden="true">↗</b></a>
                            </div>
                        </article>
                    @empty
                        <p class="bni-empty-copy">Chapter sẽ hiển thị sau khi được bật trong cơ sở dữ liệu BNI.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bni-section bni-handover-overview-section" id="su-kien" aria-labelledby="bni-handover-overview-title">
            <div class="site-shell bni-overview">
                <div class="bni-overview__content">
                    <h2 id="bni-handover-overview-title">Sự kiện chuyển giao</h2>
                    <div class="bni-rich-copy">{!! $event?->content ?: '<p>Không gian để các chapter cùng nhìn lại hành trình, tri ân Ban Điều hành và khởi động một chu kỳ phát triển mới.</p>' !!}</div>
                    <div class="bni-purpose-grid">
                        @forelse ($purposes as $purpose)
                            <article class="bni-purpose-card"><h3>{{ $purpose['title'] }}</h3><p>{{ $purpose['description'] }}</p></article>
                        @empty
                            <p class="bni-empty-copy">Mục đích sự kiện sẽ hiển thị sau khi Ban tổ chức cập nhật trong CMS BNI.</p>
                        @endforelse
                    </div>
                </div>
                <aside class="bni-overview__video" id="video-su-kien" aria-label="Video chuyển giao">
                    <div class="bni-overview__featured-media">
                        @if ($eventVideo['media_url'])
                            <div class="bni-video-frame">
                                <video controls preload="metadata" poster="{{ $eventPosterUrl }}">
                                    <source src="{{ $eventVideo['media_url'] }}">
                                    Trình duyệt của bạn chưa hỗ trợ phát video.
                                </video>
                            </div>
                        @elseif ($eventVideo['external_url'])
                            <a class="bni-video-card glightbox" href="{{ $eventVideo['external_url'] }}" data-type="video" data-gallery="bni-handover-video" data-title="{{ $event?->title ?: 'Lễ chuyển giao BNI' }}" target="_blank" rel="noopener" aria-label="Xem video chuyển giao">
                                <img src="{{ $eventPosterUrl }}" alt="" aria-hidden="true">
                                <span class="bni-video-card__play" aria-hidden="true">▶</span>
                                <span>Phát video</span>
                            </a>
                        @elseif ($eventPosterUrl)
                            <figure class="bni-video-card bni-video-card--poster">
                                <img src="{{ $eventPosterUrl }}" alt="Hình ảnh {{ $event?->title ?: 'Lễ chuyển giao BNI' }}">
                                <figcaption>Hình ảnh sự kiện</figcaption>
                            </figure>
                        @else
                            <div class="bni-video-card bni-video-card--poster bni-database-media-placeholder"><span>Video sự kiện</span><small>Chưa gắn video hoặc ảnh poster trong CMS BNI</small></div>
                        @endif
                    </div>

                    <div class="bni-chapter-video-list" id="chapters" aria-label="Video của bốn chapter">
                        @foreach ($chapterMedia as $chapter)
                            @php
                                $chapterPosterUrl = $chapter['cover_url'];
                                $chapterVideoUrl = $chapter['video_media_url'] ?: $chapter['video_external_url'];
                                $chapterLightboxUrl = $chapterVideoUrl ?: $chapterPosterUrl;
                            @endphp
                            <article class="bni-chapter-video-item" id="chapter-{{ $chapter['slug'] }}">
                                @if ($chapterLightboxUrl)
                                    <a class="bni-chapter-video-item__link glightbox"
                                       href="{{ $chapterLightboxUrl }}"
                                       data-type="{{ $chapterVideoUrl ? 'video' : 'image' }}"
                                       data-gallery="bni-chapter-videos"
                                       data-title="{{ $chapter['name'] }}"
                                       target="_blank"
                                       rel="noopener"
                                       aria-label="{{ $chapterVideoUrl ? 'Xem video' : 'Xem hình ảnh' }} {{ $chapter['name'] }}">
                                        @if ($chapterPosterUrl)<img src="{{ $chapterPosterUrl }}" alt="{{ $chapter['name'] }}" loading="lazy">@else<span class="bni-database-media-placeholder"><small>Chưa có ảnh poster</small></span>@endif
                                        <span class="bni-chapter-video-item__overlay" aria-hidden="true"><strong>{{ $chapter['short_name'] }}</strong></span>
                                    </a>
                                @else
                                    <div class="bni-chapter-video-item__link bni-database-media-placeholder"><span>{{ $chapter['short_name'] }}</span><small>Chưa gắn ảnh hoặc video trong CMS BNI</small></div>
                                @endif
                            </article>
                        @endforeach
                    </div>
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
                            <a class="bni-button bni-button--light bni-countdown__cta" href="{{ $registration['url'] }}">{{ $registration['label'] }} <span aria-hidden="true">→</span></a>
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
                        <p class="bni-empty-copy">Hoạt động sẽ hiển thị sau khi Ban tổ chức cập nhật trong cơ sở dữ liệu BNI.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bni-section bni-news" id="tin-tuc-bni" aria-labelledby="bni-news-title">
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

        <section class="bni-section bni-gallery" id="thu-vien-anh" aria-labelledby="bni-gallery-title">
            <div class="site-shell"><div class="bni-news__heading"><div><h2 id="bni-gallery-title">Thư viện ảnh</h2><p class="bni-section-heading__description">Những khoảnh khắc kết nối từ sự kiện và các chapter; mỗi ảnh có khu vực xem và bình luận riêng.</p></div><div class="bni-gallery-heading-actions"><div class="bni-tab-list" role="tablist"><button type="button" @click="galleryTab = 'event'" :class="galleryTab === 'event' && 'is-active'">Theo sự kiện</button><button type="button" @click="galleryTab = 'chapter'" :class="galleryTab === 'chapter' && 'is-active'">Theo chapter</button></div><a class="bni-button bni-button--red" href="{{ LocalizedUrl::route('bni.gallery.index') }}">Xem & gửi ảnh</a></div></div>
                <div class="bni-gallery-grid">
                    @forelse ($galleries as $gallery)
                        @if ($gallery['image_url'])<a class="bni-gallery-card" href="{{ LocalizedUrl::route('bni.gallery.show', ['galleryItem' => $gallery['id']]) }}" x-show="galleryTab === '{{ $gallery['group'] }}'" x-transition.opacity><img src="{{ $gallery['image_url'] }}" alt="{{ $gallery['title'] ?: 'Hình ảnh BNI' }}" loading="lazy"><span>{{ $gallery['title'] ?: 'Xem ảnh & bình luận' }}</span></a>@endif
                    @empty
                        <p class="bni-empty-copy">Hình ảnh sẽ được chọn từ thư viện ảnh BNI trong panel riêng.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bni-contact-band" id="dang-ky" aria-labelledby="bni-contact-title"><div class="site-shell"><div><h2 id="bni-contact-title">Kết nối cùng ban tổ chức</h2><p>Anh/chị cần hỗ trợ hoặc đăng ký tham dự sự kiện? {{ $event?->venue ?: 'Thông tin địa điểm sẽ được cập nhật.' }}</p></div><div class="bni-contact-band__actions">@if ($event?->contact_phone)<a class="bni-button bni-button--red" href="tel:{{ preg_replace('/\s+/', '', $event->contact_phone) }}">{{ $event->contact_phone }}</a>@endif <a class="bni-button bni-button--dark" href="{{ LocalizedUrl::route('contact') }}">Gửi thông tin đăng ký</a></div></div></section>
    </div>
@endsection
