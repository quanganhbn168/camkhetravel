@extends('layouts.plan')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page bni-pickleball-redesign')
@section('main_id', 'bni-pickleball-main')
@section('main_class', 'pickleball-landing')

@section('content')
    <div x-data="{ scheduleDay: @js($scheduleDays->first()['number'] ?? 1) }">
        <nav class="pickleball-nav" aria-label="Điều hướng giải Pickleball">
            <div class="pickleball-shell pickleball-nav__inner">
                <a class="pickleball-nav__brand" href="#pickleball-top" aria-label="Về đầu trang Pickleball">
                    <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                    <span>Pickleball</span>
                </a>
                <div class="pickleball-nav__links">
                    <a href="#pickleball-intro">Giới thiệu</a>
                    <a href="#lich-trinh">Lịch trình</a>
                    <a href="#pickleball-rules">Thể lệ</a>
                    <a href="#pickleball-prizes">Giải thưởng</a>
                    <a href="#pickleball-news">Tin Pickleball</a>
                </div>
                <a class="pickleball-button pickleball-button--red pickleball-nav__cta" href="#pickleball-register">
                    Đăng ký ngay
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </nav>

        <header class="pickleball-hero" id="pickleball-top">
            <img class="pickleball-hero__image" src="{{ $heroImageUrl ?: asset('images/pickleball/hero-pickleball.jpg') }}" alt="Sân Pickleball trong không gian nhận diện BNI">
            <div class="pickleball-shell pickleball-hero__inner">
                <div class="pickleball-hero__copy">
                    <a class="pickleball-back-link" href="{{ LocalizedUrl::route('bni.handover') }}">
                        <span aria-hidden="true">←</span>
                        Lễ chuyển giao BNI
                    </a>
                    <p class="pickleball-eyebrow">{{ $event?->kicker ?: 'BNI · CONNECT · COMPETE' }}</p>
                    <h1>
                        <span>Giải đấu</span>
                        <strong>Pickleball</strong>
                        <em>{{ $event?->title ?: 'Lễ chuyển giao BNI' }}</em>
                    </h1>
                    <p class="pickleball-hero__lead">{{ $event?->summary ?: 'Một giải đấu giao hữu để cộng đồng BNI kết nối, thi đấu và lan tỏa tinh thần đồng đội.' }}</p>

                    <div class="pickleball-hero__actions">
                        <a class="pickleball-button pickleball-button--red" href="#pickleball-register">
                            Đăng ký tham gia
                            <span aria-hidden="true">→</span>
                        </a>
                        <a class="pickleball-button pickleball-button--outline" href="#pickleball-intro">Tìm hiểu thêm</a>
                    </div>

                    <div class="pickleball-hero__meta" aria-label="Thông tin nhanh">
                        @if ($eventMeta['date'])
                            <span>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M8 3v4M16 3v4M3 10h18"></path></svg>
                                {{ $eventMeta['date'] }}
                            </span>
                        @endif
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="8" cy="8" r="3"></circle><circle cx="17" cy="9" r="2.5"></circle><path d="M2.5 20c.7-4 2.7-6 5.5-6s4.8 2 5.5 6M14 15c3.4 0 5.7 1.7 6.5 5"></path></svg>
                            Kết nối cộng đồng BNI
                        </span>
                    </div>
                </div>

                <div class="pickleball-countdown" @if ($event?->starts_at) data-bni-countdown="{{ $event->starts_at->toIso8601String() }}" @endif>
                    <p>{{ $pickleballContent['countdown_label'] }}</p>
                    <div>
                        <span data-bni-countdown-days>--<small>Ngày</small></span>
                        <span data-bni-countdown-hours>--<small>Giờ</small></span>
                        <span data-bni-countdown-minutes>--<small>Phút</small></span>
                        <span data-bni-countdown-seconds>--<small>Giây</small></span>
                    </div>
                </div>
            </div>
        </header>

        <section class="pickleball-facts" aria-label="Thông tin giải đấu">
            <div class="pickleball-shell pickleball-facts__grid">
                <article class="pickleball-fact">
                    <span class="pickleball-fact__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                    </span>
                    <h2>Thời gian</h2>
                    <p>{{ $eventMeta['weekday'] ?: 'Đang cập nhật' }}</p>
                    <strong>{{ $eventMeta['date_range'] ?: 'Đang cập nhật' }}</strong>
                    @if ($eventMeta['time'])<small>{{ $eventMeta['time'] }}</small>@endif
                </article>

                <article class="pickleball-fact">
                    <span class="pickleball-fact__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>
                    </span>
                    <h2>Địa điểm</h2>
                    <p>{{ $eventMeta['venue'] ?: 'Đang cập nhật' }}</p>
                    @if ($eventMeta['address'])<small>{{ $eventMeta['address'] }}</small>@endif
                    @if ($eventMeta['directions_url'])
                        <a href="{{ $eventMeta['directions_url'] }}" target="_blank" rel="noopener noreferrer">Xem Google Maps ↗</a>
                    @endif
                </article>

                <article class="pickleball-fact">
                    <span class="pickleball-fact__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 4h8v3a4 4 0 0 1-8 0V4Z"></path><path d="M8 6H4c0 4 2 6 5 6M16 6h4c0 4-2 6-5 6M12 11v5M8 20h8M9 16h6"></path></svg>
                    </span>
                    <h2>Giải thưởng</h2>
                    <p>{{ $pickleballContent['prizes']->count() > 0 ? $pickleballContent['prizes']->count().' hạng mục' : 'Đang cập nhật' }}</p>
                    <small>{{ $pickleballContent['prizes_title'] }}</small>
                </article>

                <article class="pickleball-fact">
                    <span class="pickleball-fact__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18H6z"></path><path d="M9 8h6M9 12h6M9 16h3"></path></svg>
                    </span>
                    <h2>Đăng ký</h2>
                    <p>Trực tuyến</p>
                    <small>Thông tin chuyển thẳng tới Ban tổ chức</small>
                    <a href="#pickleball-register">Mở form đăng ký ↓</a>
                </article>
            </div>
        </section>

        <section class="pickleball-section pickleball-intro" id="pickleball-intro" aria-labelledby="pickleball-intro-title">
            <div class="pickleball-shell pickleball-intro__card">
                <figure class="pickleball-intro__media">
                    <img src="{{ asset('images/pickleball/gioi-thieu-pickleball.jpg') }}" alt="Vợt và bóng Pickleball mang nhận diện BNI" loading="lazy">
                </figure>
                <div class="pickleball-intro__copy">
                    <p class="pickleball-section-kicker">Giới thiệu</p>
                    <h2 id="pickleball-intro-title">Kết nối trên sân đấu<br><span>Dẫn lối thành công</span></h2>
                    @if ($pickleballContent['introduction'])
                        <div class="pickleball-rich-copy">{!! $pickleballContent['introduction'] !!}</div>
                    @elseif ($event?->summary)
                        <p>{{ $event->summary }}</p>
                    @else
                        <p>Giải đấu Pickleball tạo nên không gian giao lưu thể thao, kết nối thành viên và lan tỏa tinh thần đồng đội trong cộng đồng BNI.</p>
                    @endif
                    <div class="pickleball-intro__values">
                        <div><strong>Kết nối</strong><span>Mở rộng mối quan hệ</span></div>
                        <div><strong>Thi đấu</strong><span>Lan tỏa tinh thần fair play</span></div>
                        <div><strong>Phát triển</strong><span>Cùng tạo giá trị bền vững</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="pickleball-section pickleball-schedule" id="lich-trinh" aria-labelledby="pickleball-schedule-title">
            <div class="pickleball-shell">
                <div class="pickleball-heading pickleball-heading--center">
                    <p class="pickleball-section-kicker">Chương trình sự kiện</p>
                    <h2 id="pickleball-schedule-title">Lịch trình sự kiện</h2>
                    <span aria-hidden="true"></span>
                </div>

                @if ($scheduleDays->isNotEmpty())
                    <div class="pickleball-schedule__tabs" role="tablist" aria-label="Chọn ngày sự kiện">
                        @foreach ($scheduleDays as $day)
                            <button type="button" role="tab" @click="scheduleDay = {{ $day['number'] }}" :class="scheduleDay === {{ $day['number'] }} && 'is-active'" :aria-selected="scheduleDay === {{ $day['number'] }}">
                                {{ $day['label'] }}
                            </button>
                        @endforeach
                    </div>
                @endif

                @forelse ($scheduleDays as $day)
                    <div class="pickleball-timeline" x-show="scheduleDay === {{ $day['number'] }}" role="tabpanel">
                        @forelse ($day['items'] as $item)
                            <article class="pickleball-timeline__item">
                                <time>{{ $item['time'] ?: 'Đang cập nhật' }}</time>
                                <div>
                                    <h3>{{ $item['title'] }}</h3>
                                    @if ($item['description'])<p>{{ $item['description'] }}</p>@endif
                                </div>
                            </article>
                        @empty
                            <p class="pickleball-empty">Các mốc trong ngày này đang được Ban tổ chức cập nhật.</p>
                        @endforelse
                    </div>
                @empty
                    <p class="pickleball-empty">Lịch trình sự kiện sẽ được cập nhật từ hệ thống quản trị BNI.</p>
                @endforelse
            </div>
        </section>

        <section class="pickleball-section pickleball-rules" id="pickleball-rules" aria-labelledby="pickleball-rules-title">
            <div class="pickleball-shell pickleball-rules__layout">
                <div class="pickleball-rules__heading">
                    <p class="pickleball-section-kicker">Thông tin thi đấu</p>
                    <h2 id="pickleball-rules-title">{{ $pickleballContent['rules_title'] }}</h2>
                    <p>Thông tin chính thức về đối tượng, nội dung, thể thức và các lưu ý trước ngày thi đấu được Ban tổ chức cập nhật tại đây.</p>
                    <a class="pickleball-button pickleball-button--light" href="#pickleball-register">Đăng ký tham gia</a>
                </div>
                <div class="pickleball-rules__content pickleball-rich-copy">
                    {!! $pickleballContent['rules'] ?: '<p>Thể lệ chính thức đang được Ban tổ chức hoàn thiện và sẽ được công bố tại đây.</p>' !!}
                </div>
            </div>
        </section>

        <section class="pickleball-section pickleball-prizes" id="pickleball-prizes" aria-labelledby="pickleball-prizes-title">
            <div class="pickleball-shell">
                <div class="pickleball-heading pickleball-heading--center">
                    <p class="pickleball-section-kicker">Vinh danh & kết nối</p>
                    <h2 id="pickleball-prizes-title">{{ $pickleballContent['prizes_title'] }}</h2>
                    @if ($pickleballContent['prizes_description'])<p>{{ $pickleballContent['prizes_description'] }}</p>@endif
                    <span aria-hidden="true"></span>
                </div>
                <div class="pickleball-prizes__grid">
                    @forelse ($pickleballContent['prizes'] as $index => $prize)
                        <article @class(['pickleball-prize', 'is-highlighted' => ! empty($prize['highlight'])])>
                            <span class="pickleball-prize__number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 4h8v3a4 4 0 0 1-8 0V4Z"></path><path d="M8 6H4c0 4 2 6 5 6M16 6h4c0 4-2 6-5 6M12 11v5M8 20h8M9 16h6"></path></svg>
                            <h3>{{ $prize['title'] }}</h3>
                            @if (filled($prize['value'] ?? null))<strong>{{ $prize['value'] }}</strong>@endif
                            @if (filled($prize['description'] ?? null))<p>{{ $prize['description'] }}</p>@endif
                        </article>
                    @empty
                        <article class="pickleball-prize pickleball-prize--empty">
                            <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                            <h3>Cơ cấu giải thưởng đang được cập nhật</h3>
                            <p>Ban tổ chức sẽ công bố các hạng mục và giá trị giải thưởng chính thức tại khu vực này.</p>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="pickleball-register" id="pickleball-register" aria-labelledby="pickleball-register-title">
            <img class="pickleball-register__background" src="{{ asset('images/pickleball/dang-ky.jpg') }}" alt="" aria-hidden="true" loading="lazy">
            <div class="pickleball-register__overlay" aria-hidden="true"></div>
            <div class="pickleball-shell pickleball-register__inner">
                <div class="pickleball-register__intro">
                    <p class="pickleball-section-kicker">RSVP</p>
                    <h2 id="pickleball-register-title">{{ $pickleballContent['registration_title'] }}</h2>
                    <p>{{ $pickleballContent['registration_description'] }}</p>
                    <dl>
                        @if ($eventMeta['date_range'])<div><dt>Ngày thi đấu</dt><dd>{{ $eventMeta['date_range'] }}</dd></div>@endif
                        @if ($eventMeta['time'])<div><dt>Thời gian</dt><dd>{{ $eventMeta['time'] }}</dd></div>@endif
                        @if ($eventMeta['location'])<div><dt>Địa điểm</dt><dd>{{ $eventMeta['location'] }}</dd></div>@endif
                    </dl>
                    @if ($eventMeta['directions_url'])
                        <a class="pickleball-map-link" href="{{ $eventMeta['directions_url'] }}" target="_blank" rel="noopener noreferrer">Mở Google Maps ↗</a>
                    @endif
                </div>

                <form class="pickleball-form" method="POST" action="{{ LocalizedUrl::route('bni.pickleball.register') }}" data-bni-ajax-form data-reset-on-success="true">
                    @csrf
                    <div>
                        <label for="pickleball-full-name">Họ và tên *</label>
                        <input id="pickleball-full-name" name="full_name" required value="{{ old('full_name') }}" autocomplete="name">
                        @error('full_name')<p class="pickleball-form__error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pickleball-phone">Số điện thoại *</label>
                        <input id="pickleball-phone" type="tel" name="phone" required value="{{ old('phone') }}" autocomplete="tel">
                        @error('phone')<p class="pickleball-form__error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pickleball-email">Email</label>
                        <input id="pickleball-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email">
                        @error('email')<p class="pickleball-form__error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pickleball-chapter">Chapter (nếu có)</label>
                        <select id="pickleball-chapter" name="bni_chapter_id">
                            <option value="">Khách mời / chưa chọn</option>
                            @foreach ($chapters as $chapter)
                                <option value="{{ $chapter->id }}" @selected((int) old('bni_chapter_id') === $chapter->id)>{{ $chapter->short_name ?: $chapter->name }}</option>
                            @endforeach
                        </select>
                        @error('bni_chapter_id')<p class="pickleball-form__error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pickleball-team">Tên đội</label>
                        <input id="pickleball-team" name="team_name" value="{{ old('team_name') }}">
                        @error('team_name')<p class="pickleball-form__error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pickleball-skill">Trình độ</label>
                        <select id="pickleball-skill" name="skill_level">
                            <option value="">Chọn trình độ</option>
                            <option value="beginner" @selected(old('skill_level') === 'beginner')>Mới chơi</option>
                            <option value="intermediate" @selected(old('skill_level') === 'intermediate')>Trung bình</option>
                            <option value="advanced" @selected(old('skill_level') === 'advanced')>Nâng cao</option>
                        </select>
                        @error('skill_level')<p class="pickleball-form__error">{{ $message }}</p>@enderror
                    </div>
                    <div class="pickleball-form__full">
                        <label for="pickleball-note">Ghi chú</label>
                        <textarea id="pickleball-note" name="note" rows="3">{{ old('note') }}</textarea>
                        @error('note')<p class="pickleball-form__error">{{ $message }}</p>@enderror
                    </div>
                    <button class="pickleball-button pickleball-button--red pickleball-form__full" type="submit">
                        Gửi đăng ký
                        <span aria-hidden="true">→</span>
                    </button>
                    <p class="bni-form-status pickleball-form__full" data-bni-form-status role="status" aria-live="polite" hidden></p>
                </form>
            </div>
        </section>

        <section class="pickleball-section pickleball-news" id="pickleball-news" aria-labelledby="pickleball-news-title">
            <div class="pickleball-shell">
                <div class="pickleball-heading">
                    <p class="pickleball-section-kicker">Cập nhật từ sân đấu</p>
                    <h2 id="pickleball-news-title">Tin Pickleball</h2>
                    <span aria-hidden="true"></span>
                </div>
                <div class="pickleball-news__grid">
                    @forelse ($articles as $article)
                        <article class="pickleball-news-card">
                            @if ($article['image_url'])
                                <img src="{{ $article['image_url'] }}" alt="" loading="lazy">
                            @else
                                <div class="pickleball-news-card__placeholder"><img src="{{ asset('bni-logo-red.svg') }}" alt=""></div>
                            @endif
                            <div>
                                <p>Pickleball</p>
                                <h3><a href="{{ LocalizedUrl::route('bni.articles.show', ['article' => $article['slug']]) }}">{{ $article['title'] }}</a></h3>
                                @if ($article['excerpt'])<span>{{ $article['excerpt'] }}</span>@endif
                                <a href="{{ LocalizedUrl::route('bni.articles.show', ['article' => $article['slug']]) }}">Xem bài viết →</a>
                            </div>
                        </article>
                    @empty
                        <p class="pickleball-empty">Tin tức thuộc danh mục “Tin Pickleball” sẽ được hiển thị tại đây.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="pickleball-final-cta" aria-labelledby="pickleball-final-title">
            <img src="{{ asset('images/pickleball/dang-ky.jpg') }}" alt="" aria-hidden="true" loading="lazy">
            <div aria-hidden="true"></div>
            <div class="pickleball-shell pickleball-final-cta__inner">
                <h2 id="pickleball-final-title">Đăng ký ngay – Sẵn sàng bứt phá!</h2>
                <p>Cùng nhau thi đấu · Kết nối · Dẫn lối thành công mới</p>
                <a class="pickleball-button pickleball-button--light" href="#pickleball-register">Đăng ký tham gia ngay →</a>
            </div>
        </section>

        <footer class="pickleball-footer">
            <div class="pickleball-shell">
                <p>BNI Pickleball · Lễ chuyển giao BNI</p>
                <a href="#pickleball-top">Lên đầu trang ↑</a>
            </div>
        </footer>
    </div>
@endsection
