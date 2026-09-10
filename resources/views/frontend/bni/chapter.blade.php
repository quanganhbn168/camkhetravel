@extends('layouts.master')

@section('body_class', 'bni-experience-page bni-chapter-detail-page')
@section('main_id', 'bni-chapter-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    <article class="bni-chapter-detail">
        <header class="bni-chapter-detail__hero">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 bni-chapter-detail__hero-grid">
                <div class="bni-chapter-detail__hero-copy">
                    <a class="bni-back-link" href="{{ $eventContext['url'] ?? route('bni.handover') }}">← Lễ chuyển giao BNI</a>
                    @if ($chapter['logo_url'])
                        <img class="bni-chapter-detail__hero-logo" src="{{ $chapter['logo_url'] }}" alt="Logo {{ $chapter['name'] }}">
                    @endif
                    <p class="bni-experience-kicker">BNI Chapter</p>
                    <h1>{{ $chapter['short_name'] }}</h1>
                    @if ($chapter['name'] !== $chapter['short_name'])
                        <p class="bni-chapter-detail__full-name">{{ $chapter['name'] }}</p>
                    @endif
                    @if ($eventContext)
                        <p class="bni-chapter-detail__event-name">{{ $eventContext['title'] }}</p>
                    @endif
                </div>
                <figure class="bni-chapter-detail__hero-visual">
                    @if ($chapter['cover_url'])
                        <img src="{{ $chapter['cover_url'] }}" alt="Hình ảnh {{ $chapter['name'] }}">
                    @else
                        <span class="bni-database-media-placeholder"><span>{{ $chapter['short_name'] }}</span><small>Chưa gắn ảnh cover trong CMS BNI</small></span>
                    @endif
                </figure>
            </div>
        </header>

        <section class="bni-section bni-chapter-detail__overview" aria-labelledby="bni-chapter-about-title">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 bni-chapter-detail__overview-grid">
                <div class="bni-chapter-detail__about">
                    <p class="bni-experience-kicker">Giới thiệu chapter</p>
                    <h2 id="bni-chapter-about-title">Về {{ $chapter['short_name'] }}</h2>
                    @if ($chapter['description'])
                        <p>{{ $chapter['description'] }}</p>
                    @else
                        <p class="bni-empty-copy">Nội dung giới thiệu sẽ hiển thị sau khi chapter cập nhật trong CMS BNI.</p>
                    @endif
                </div>

                @if ($eventContext)
                    <aside class="bni-chapter-detail__event-card" aria-label="Sự kiện của chapter">
                        <p>Sự kiện</p>
                        <h2>{{ $eventContext['title'] }}</h2>
                        @if ($eventContext['date'])
                            <div><span>Thời gian</span><strong>{{ $eventContext['date'] }}</strong></div>
                        @endif
                        @if ($eventContext['location'])
                            <div><span>Địa điểm</span><strong>{{ $eventContext['location'] }}</strong></div>
                        @endif
                        <a href="{{ $eventContext['url'] }}">Xem toàn bộ chương trình <span aria-hidden="true">→</span></a>
                    </aside>
                @endif
            </div>
        </section>

        <section class="bni-section bni-chapter-detail__media" aria-labelledby="bni-chapter-video-title">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8 bni-chapter-detail__media-grid">
                <div class="bni-section-heading">
                    <p class="bni-experience-kicker">Video chapter</p>
                    <h2 id="bni-chapter-video-title">Câu chuyện của {{ $chapter['short_name'] }}</h2>
                    <p class="bni-section-heading__description">Video và hình ảnh được chapter cập nhật trực tiếp trong hệ thống BNI.</p>
                </div>
                <div class="bni-chapter-detail__video">
                    @if ($chapterVideo['media_url'])
                        <div class="bni-video-frame">
                            <video controls preload="metadata" @if ($chapterVideo['poster_url']) poster="{{ $chapterVideo['poster_url'] }}" @endif>
                                <source src="{{ $chapterVideo['media_url'] }}">
                                Trình duyệt của bạn chưa hỗ trợ phát video.
                            </video>
                        </div>
                    @elseif ($chapterVideo['external_url'])
                        <a class="bni-video-card glightbox" href="{{ $chapterVideo['external_url'] }}" data-type="video" data-gallery="bni-chapter-detail-video" data-title="{{ $chapter['name'] }}" target="_blank" rel="noopener" aria-label="Xem video {{ $chapter['name'] }}">
                            @if ($chapterVideo['poster_url'])<img src="{{ $chapterVideo['poster_url'] }}" alt="">@endif
                            <span class="bni-video-card__play" aria-hidden="true">▶</span>
                            <span class="bni-video-card__label">Phát video</span>
                        </a>
                    @elseif ($chapterVideo['poster_url'])
                        <figure class="bni-video-card bni-video-card--poster">
                            <img src="{{ $chapterVideo['poster_url'] }}" alt="Hình ảnh {{ $chapter['name'] }}">
                            <figcaption>Hình ảnh chapter</figcaption>
                        </figure>
                    @else
                        <div class="bni-video-card bni-video-card--empty">
                            @if ($chapter['logo_url'])<img src="{{ $chapter['logo_url'] }}" alt="Logo {{ $chapter['name'] }}">@endif
                            <p>Video sẽ hiển thị sau khi chapter cập nhật trong CMS BNI.</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @if ($chapterContacts->isNotEmpty())
            <section class="bni-chapter-detail__contact" aria-labelledby="bni-chapter-contact-title">
                <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
                    <div class="bni-chapter-detail__contact-heading">
                        <p>Đầu mối chapter</p>
                        <h2 id="bni-chapter-contact-title">Kết nối với {{ $chapter['short_name'] }}</h2>
                    </div>
                    <div class="bni-chapter-detail__contact-list">
                        @foreach ($chapterContacts as $contact)
                            <article class="bni-chapter-detail__contact-person">
                                <div>
                                    @if ($contact['name'])<h3>{{ $contact['name'] }}</h3>@endif
                                    @if ($contact['position'])<p>{{ $contact['position'] }}</p>@endif
                                    @if ($contact['note'])<span>{{ $contact['note'] }}</span>@endif
                                </div>
                                <div class="bni-chapter-detail__contact-actions">
                                    @if ($contact['phone_url'])<a class="bni-button bni-button--white" href="{{ $contact['phone_url'] }}">{{ $contact['phone'] }}</a>@endif
                                    @if ($contact['email_url'])<a class="bni-button bni-button--ghost" href="{{ $contact['email_url'] }}">{{ $contact['email'] }}</a>@endif
                                    @if ($contact['zalo_url'])<a class="bni-button bni-button--ghost" href="{{ $contact['zalo_url'] }}" target="_blank" rel="noopener">Zalo</a>@endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="bni-section bni-chapter-detail__news" aria-labelledby="bni-chapter-news-title">
            <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
                <div class="bni-section-heading">
                    <p class="bni-experience-kicker">Tin tức chapter</p>
                    <h2 id="bni-chapter-news-title">Cập nhật từ {{ $chapter['short_name'] }}</h2>
                </div>
                <div class="bni-chapter-detail__news-grid">
                    @forelse ($chapterArticles as $article)
                        <article class="bni-news-card">
                            @if ($article['image_url'])<img src="{{ $article['image_url'] }}" alt="" loading="lazy">@endif
                            <div>
                                <p>{{ $chapter['short_name'] }}</p>
                                <h3><a href="{{ $article['url'] }}">{{ $article['title'] }}</a></h3>
                                @if ($article['excerpt'])<span>{{ $article['excerpt'] }}</span>@endif
                                @if ($article['published_at'])<time datetime="{{ $article['published_at']->toDateString() }}">{{ $article['published_at']->translatedFormat('d/m/Y') }}</time>@endif
                            </div>
                        </article>
                    @empty
                        <p class="bni-empty-copy">Tin tức sẽ hiển thị sau khi chapter xuất bản bài viết đầu tiên.</p>
                    @endforelse
                </div>
            </div>
        </section>

        @if ($siblingChapters->isNotEmpty())
            <section class="bni-section bni-chapter-detail__siblings" aria-labelledby="bni-chapter-siblings-title">
                <div class="site-container w-full max-w-7xl mx-auto px-4 lg:px-8">
                    <div class="bni-section-heading">
                        <p class="bni-experience-kicker">Cùng sự kiện</p>
                        <h2 id="bni-chapter-siblings-title">Các chapter đồng hành</h2>
                    </div>
                    <div class="bni-chapter-detail__siblings-grid">
                        @foreach ($siblingChapters as $sibling)
                            <article class="bni-chapter-detail__sibling-card">
                                <a class="bni-chapter-detail__sibling-visual" href="{{ $sibling['url'] }}" aria-label="Xem chi tiết {{ $sibling['name'] }}">
                                    @if ($sibling['cover_url'])
                                        <img src="{{ $sibling['cover_url'] }}" alt="Hình ảnh {{ $sibling['name'] }}" loading="lazy">
                                    @else
                                        <span class="bni-database-media-placeholder"><span>{{ $sibling['short_name'] }}</span></span>
                                    @endif
                                </a>
                                <div>
                                    @if ($sibling['logo_url'])<img src="{{ $sibling['logo_url'] }}" alt="Logo {{ $sibling['name'] }}" loading="lazy">@endif
                                    <h3><a href="{{ $sibling['url'] }}">{{ $sibling['short_name'] }}</a></h3>
                                    @if ($sibling['description'])<p>{{ $sibling['description'] }}</p>@endif
                                    <a href="{{ $sibling['url'] }}">Xem chi tiết <span aria-hidden="true">→</span></a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </article>
@endsection
