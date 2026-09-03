@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)
@use(Illuminate\Support\Facades\Vite)

@section('body_class', 'bni-experience-page')
@section('main_id', 'bni-handover-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    @php
        $eventPosterUrl = $eventVideo['poster_url'];
        $chapterMedia = $chapters;
        $featuredChapter = $chapterMedia->first(fn (array $chapter): bool => filled($chapter['video_media_url']) || filled($chapter['video_external_url']) || filled($chapter['cover_url']));
        $featuredChapterVideoUrl = $featuredChapter ? ($featuredChapter['video_media_url'] ?: $featuredChapter['video_external_url']) : null;
        $featuredChapterPosterUrl = $featuredChapter['cover_url'] ?? null;
        $featuredChapterLightboxUrl = $featuredChapterVideoUrl ?: $featuredChapterPosterUrl;
    @endphp
    <div x-data="{ scheduleDay: {{ $scheduleDays->first()['number'] ?? 1 }}, newsTab: @js($newsInitialCategory), galleryTab: @js($galleryInitialGroup) }">
        @include('frontend.partials.bni-navigation')

        <section class="bni-event-slider" id="tong-quan" aria-label="Trình chiếu Lễ chuyển giao BNI">
            @if ($heroSlides->isNotEmpty())
                <div class="swiper bni-event-slider__swiper" data-bni-hero-swiper>
                    <div class="swiper-wrapper">
                        @foreach ($heroSlides as $slide)
                            <article class="swiper-slide bni-event-slide">
                                <figure class="bni-event-slide__visual">
                                    @php
                                        $slideImageUrl = $slide['image_url'] ?: Vite::asset('resources/images/bni/bni-kv-milk-red.webp');
                                    @endphp
                                    @if ($slide['video_media_url'])
                                        <video class="bni-event-slide__video" autoplay muted loop playsinline preload="metadata" poster="{{ $slideImageUrl }}" aria-label="{{ $slide['alt_text'] }}">
                                            <source src="{{ $slide['video_media_url'] }}">
                                            Trình duyệt của bạn chưa hỗ trợ phát video.
                                        </video>
                                    @elseif ($slide['video_external_url'])
                                        <a class="bni-event-slide__video-link glightbox" href="{{ $slide['video_external_url'] }}" data-type="video" data-gallery="bni-hero-videos" data-title="{{ $slide['alt_text'] }}" target="_blank" rel="noopener" aria-label="Phát video: {{ $slide['alt_text'] }}">
                                            <img src="{{ $slideImageUrl }}" alt="{{ $slide['alt_text'] }}" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                                            <span aria-hidden="true">▶</span>
                                        </a>
                                    @else
                                        <img src="{{ $slideImageUrl }}" alt="{{ $slide['alt_text'] }}" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                                    @endif
                                </figure>
                            </article>
                        @endforeach
                    </div>
                </div>
                @if ($heroSlides->count() > 1)
                    <div class="site-shell bni-event-slider__controls" aria-label="Điều khiển slide">
                        <button type="button" data-bni-hero-swiper-prev aria-label="Slide trước">←</button>
                        <span>{{ $heroSlides->count() }} slide</span>
                        <button type="button" data-bni-hero-swiper-next aria-label="Slide tiếp theo">→</button>
                    </div>
                @endif
            @endif
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
                                <a href="{{ $chapter['detail_url'] }}" aria-label="Xem chi tiết {{ $chapter['name'] }}">Xem chi tiết <b aria-hidden="true">↗</b></a>
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
                    <h1 id="bni-handover-overview-title">{{ $event?->title ?: 'Sự kiện chuyển giao BNI' }}</h1>
                    @if ($event?->summary)
                        <p class="bni-overview__summary">{{ $event->summary }}</p>
                    @endif
                    <div class="bni-rich-copy">{!! $event?->content ?: '<p>Không gian để các chapter cùng nhìn lại hành trình, tri ân Ban Điều hành và khởi động một chu kỳ phát triển mới.</p>' !!}</div>
                    <div class="bni-overview__actions">
                        <a class="bni-button bni-button--red" href="#lich-trinh">Xem lịch trình <span aria-hidden="true">↓</span></a>
                        <a class="bni-button bni-button--dark" href="{{ $registration['url'] }}">{{ $registration['label'] }}</a>
                        @guest
                            <a class="bni-button bni-button--light" href="{{ LocalizedUrl::route('bni.member.login') }}">Đăng nhập hội viên</a>
                        @endguest
                    </div>
                    <div class="bni-purpose-grid">
                        @forelse ($purposes as $purpose)
                            <article class="bni-purpose-card"><h3>{{ $purpose['title'] }}</h3><p>{{ $purpose['description'] }}</p></article>
                        @empty
                            <p class="bni-empty-copy">Mục đích sự kiện sẽ hiển thị sau khi Ban tổ chức cập nhật trong CMS BNI.</p>
                        @endforelse
                    </div>
                </div>
                <aside class="bni-overview__video" aria-label="Video và hình ảnh từ các chapter">
                    <div class="bni-overview__featured-media">
                        @if ($featuredChapterLightboxUrl)
                            <a class="bni-video-card glightbox" href="{{ $featuredChapterLightboxUrl }}" data-type="{{ $featuredChapterVideoUrl ? 'video' : 'image' }}" data-gallery="bni-chapter-videos" data-title="{{ $featuredChapter['name'] }}" target="_blank" rel="noopener" aria-label="{{ $featuredChapterVideoUrl ? 'Xem video' : 'Xem hình ảnh' }} {{ $featuredChapter['name'] }}">
                                @if ($featuredChapterPosterUrl)
                                    <img src="{{ $featuredChapterPosterUrl }}" alt="{{ $featuredChapter['name'] }}">
                                @else
                                    <span class="bni-database-media-placeholder"><strong>{{ $featuredChapter['short_name'] }}</strong><small>Chapter chưa gắn ảnh cover</small></span>
                                @endif
                                @if ($featuredChapterVideoUrl)<span class="bni-video-card__play" aria-hidden="true">▶</span>@endif
                                <span>{{ $featuredChapterVideoUrl ? 'Phát video' : 'Xem hình ảnh' }} {{ $featuredChapter['short_name'] }}</span>
                            </a>
                        @else
                            <div class="bni-video-card bni-video-card--poster bni-database-media-placeholder"><span>Video Chapter</span><small>Chưa gắn video hoặc ảnh cover trong quản trị Chapter</small></div>
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

        <section class="bni-section bni-intro-video" id="video-gioi-thieu" aria-label="Video giới thiệu Lễ chuyển giao">
            <div class="site-shell bni-intro-video__shell">
                <h2 class="sr-only">Video giới thiệu Lễ chuyển giao</h2>
                @if ($eventVideo['media_url'] || $eventVideo['external_url'])
                    <a class="bni-video-card glightbox" href="{{ $eventVideo['media_url'] ?: $eventVideo['external_url'] }}" data-type="video" data-gallery="bni-handover-intro-video" data-title="{{ $event?->title ?: 'Lễ chuyển giao BNI' }}" target="_blank" rel="noopener" aria-label="Xem video giới thiệu Lễ chuyển giao">
                        @if ($eventPosterUrl)<img src="{{ $eventPosterUrl }}" alt="Ảnh cover video giới thiệu {{ $event?->title ?: 'Lễ chuyển giao BNI' }}">@else<span class="bni-database-media-placeholder"><strong>Video giới thiệu</strong><small>Chưa gắn ảnh cover</small></span>@endif
                        <span class="bni-video-card__play" aria-hidden="true">▶</span>
                        <span>Phát video giới thiệu</span>
                    </a>
                @elseif ($eventPosterUrl)
                    <figure class="bni-video-card bni-video-card--poster">
                        <img src="{{ $eventPosterUrl }}" alt="Ảnh cover video giới thiệu {{ $event?->title ?: 'Lễ chuyển giao BNI' }}">
                        <figcaption>Video giới thiệu đang được cập nhật</figcaption>
                    </figure>
                @else
                    <div class="bni-video-card bni-video-card--poster bni-database-media-placeholder"><span>Video giới thiệu</span><small>Chưa gắn video hoặc ảnh cover trong mục Video giới thiệu</small></div>
                @endif
            </div>
        </section>

        <section class="bni-section bni-schedule" id="lich-trinh" aria-labelledby="bni-schedule-title">
            <div class="site-shell">
                <div class="bni-section-heading"><h2 id="bni-schedule-title">Lịch trình sự kiện</h2></div>
                <div class="bni-schedule__layout">
                    <div class="bni-schedule__main">
                        @if ($scheduleDays->isNotEmpty())
                            <div class="bni-tab-list" role="tablist" aria-label="Ngày sự kiện">
                            @foreach ($scheduleDays as $day)
                                <button type="button" role="tab" @click="scheduleDay = {{ $day['number'] }}" :aria-selected="(scheduleDay === {{ $day['number'] }}).toString()" :class="scheduleDay === {{ $day['number'] }} && 'is-active'">{{ $day['label'] }}</button>
                            @endforeach
                            </div>
                        @endif
                        @forelse ($scheduleDays as $day)
                            <div class="bni-schedule-list" x-show="scheduleDay === {{ $day['number'] }}" x-transition.opacity>
                                @forelse ($day['items'] as $item)
                                    <article class="bni-schedule-item"><time>{{ $item['time'] ?: 'Đang cập nhật' }}</time><div><h3>{{ $item['title'] }}</h3>@if ($item['description'])<p>{{ $item['description'] }}</p>@endif</div></article>
                                @empty
                                    <p class="bni-empty-copy">Các mốc giờ của ngày này đang được Ban tổ chức cập nhật.</p>
                                @endforelse
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
                <div class="bni-news__heading">
                    <div>
                        <h2 id="bni-news-title">Tin tức</h2>
                        <p class="bni-section-heading__description">Bài viết mới nhất được cập nhật theo từng chuyên mục.</p>
                        <a class="bni-news__all-link" href="{{ LocalizedUrl::route('bni.articles.index') }}">Xem tất cả tin tức <span aria-hidden="true">→</span></a>
                    </div>
                    @if ($newsCategories->isNotEmpty())
                        <div class="bni-tab-list" role="tablist" aria-label="Danh mục tin BNI">
                            @foreach ($newsCategories as $category)
                                <button
                                    type="button"
                                    id="{{ $category['key'] }}-tab"
                                    role="tab"
                                    aria-controls="{{ $category['key'] }}-panel"
                                    @click="newsTab = @js($category['key'])"
                                    :aria-selected="newsTab === @js($category['key'])"
                                    :class="newsTab === @js($category['key']) && 'is-active'"
                                >{{ $category['label'] }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>
                @forelse ($newsCategories as $category)
                    <div
                        class="bni-news-grid"
                        id="{{ $category['key'] }}-panel"
                        role="tabpanel"
                        aria-labelledby="{{ $category['key'] }}-tab"
                        x-show="newsTab === @js($category['key'])"
                        x-cloak
                        x-transition.opacity
                    >
                        @foreach ($category['articles'] as $article)
                            <article class="bni-news-card {{ $loop->first ? 'bni-news-card--featured' : '' }}">
                                @if ($article['image_url'])<img src="{{ $article['image_url'] }}" alt="{{ $article['title'] }}" loading="lazy">@endif
                                <div>
                                    <p>{{ $category['label'] }}</p>
                                    <h3><a href="{{ $article['url'] }}">{{ $article['title'] }}</a></h3>
                                    @if ($article['excerpt'])<span>{{ $article['excerpt'] }}</span>@endif
                                    @if ($article['published_at'])<time datetime="{{ $article['published_at']->toDateString() }}">{{ $article['published_at']->translatedFormat('d/m/Y') }}</time>@endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @empty
                    <p class="bni-empty-copy">Tin tức sẽ hiển thị sau khi có bài BNI đã xuất bản trong một danh mục đang hoạt động.</p>
                @endforelse
            </div>
        </section>

        <section class="bni-section bni-gallery" id="thu-vien-anh" aria-labelledby="bni-gallery-title">
            <div class="site-shell">
                <div class="bni-news__heading">
                    <div>
                        <h2 id="bni-gallery-title">Thư viện ảnh</h2>
                        <p class="bni-section-heading__description">Khoảnh khắc được sắp xếp theo từng hoạt động trong chương trình; mỗi ảnh có khu vực xem và bình luận riêng.</p>
                    </div>
                    <div class="bni-gallery-heading-actions">
                        @if ($galleryGroups->isNotEmpty())
                            <div class="bni-tab-list" role="tablist" aria-label="Hoạt động trong thư viện ảnh">
                                @foreach ($galleryGroups as $group)
                                    <button type="button" @click="galleryTab = @js($group['key'])" :class="galleryTab === @js($group['key']) && 'is-active'">{{ $group['label'] }}</button>
                                @endforeach
                            </div>
                        @endif
                        <a class="bni-button bni-button--red" href="{{ LocalizedUrl::route('bni.gallery.index') }}">Xem & gửi ảnh</a>
                    </div>
                </div>
                <div class="bni-gallery-grid">
                    @forelse ($galleries as $gallery)
                        @if ($gallery['image_url'])<a class="bni-gallery-card" href="{{ LocalizedUrl::route('bni.gallery.show', ['galleryItem' => $gallery['id']]) }}" x-show="galleryTab === @js($gallery['group_key'])" x-transition.opacity><img src="{{ $gallery['image_url'] }}" alt="{{ $gallery['title'] ?: 'Hình ảnh BNI' }}" loading="lazy"><span>{{ $gallery['title'] ?: $gallery['group_label'] }}</span></a>@endif
                    @empty
                        <p class="bni-empty-copy">Hình ảnh sẽ được chọn từ thư viện ảnh BNI trong panel riêng.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bni-contact-band" id="dang-ky" aria-labelledby="bni-contact-title">
            <div class="site-shell">
                <div><h2 id="bni-contact-title">Đăng ký cùng ban tổ chức</h2><p>Anh/chị đăng ký tham dự {{ $event?->title ?: 'sự kiện BNI' }} trực tiếp trên hệ thống BNI.</p></div>
                <div class="bni-contact-band__actions">
                    @foreach ($generalContacts as $contact)
                        @if ($contact['phone_url'])<a class="bni-button bni-button--red" href="{{ $contact['phone_url'] }}" title="{{ $contact['name'] }}">{{ $contact['phone'] }}</a>@endif
                    @endforeach
                    <a class="bni-button bni-button--dark" href="{{ LocalizedUrl::route('bni.registrations.create') }}">Mở trang đăng ký</a>
                </div>
            </div>
        </section>
    </div>
@endsection
