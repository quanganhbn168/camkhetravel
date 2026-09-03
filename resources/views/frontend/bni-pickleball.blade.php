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
                <p class="bni-experience-kicker">{{ $event?->kicker ?: 'KẾT NỐI BẰNG NĂNG LƯỢNG' }}</p>
                <h1>{{ $event?->title ?: 'BNI Pickleball Championship' }}</h1>
                <p>{{ $event?->summary ?: 'Một giải đấu giao hữu để cộng đồng BNI kết nối, thi đấu và lan tỏa tinh thần đồng đội.' }}</p>
                <div class="bni-countdown bni-countdown--light" @if ($event?->starts_at) data-bni-countdown="{{ $event->starts_at->toIso8601String() }}" @endif>
                    <p>{{ $pickleballContent['countdown_label'] }}</p>
                    <div><span data-bni-countdown-days>--<small>Ngày</small></span><span data-bni-countdown-hours>--<small>Giờ</small></span><span data-bni-countdown-minutes>--<small>Phút</small></span><span data-bni-countdown-seconds>--<small>Giây</small></span></div>
                </div>
                <div class="bni-pickleball-hero__actions">
                    <a class="bni-button bni-button--white" href="#dang-ky">Đăng ký thi đấu</a>
                    <a class="bni-button bni-button--ghost" href="#the-le">Xem thể lệ</a>
                </div>
            </div>
        </section>

        <section class="bni-section bni-pickleball-prizes" id="giai-thuong" aria-labelledby="bni-pickleball-prizes-title">
            <div class="site-shell">
                <div class="bni-section-heading bni-section-heading--center">
                    <p class="bni-experience-kicker">VINH DANH & KẾT NỐI</p>
                    <h2 id="bni-pickleball-prizes-title">{{ $pickleballContent['prizes_title'] }}</h2>
                    @if ($pickleballContent['prizes_description'])<p class="bni-section-heading__description">{{ $pickleballContent['prizes_description'] }}</p>@endif
                </div>
                <div class="bni-prize-grid">
                    @forelse ($pickleballContent['prizes'] as $index => $prize)
                        <article class="bni-prize-card {{ ! empty($prize['highlight']) ? 'is-highlighted' : '' }}">
                            <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $prize['title'] }}</h3>
                            @if (filled($prize['value'] ?? null))<strong>{{ $prize['value'] }}</strong>@endif
                            @if (filled($prize['description'] ?? null))<p>{{ $prize['description'] }}</p>@endif
                        </article>
                    @empty
                        <article class="bni-prize-card bni-prize-card--empty">
                            <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI">
                            <h3>Cơ cấu giải thưởng đang được cập nhật</h3>
                            <p>Ban tổ chức sẽ công bố các hạng mục và giá trị giải thưởng chính thức tại khu vực này.</p>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bni-section bni-pickleball-rules" id="the-le" aria-labelledby="bni-pickleball-rules-title">
            <div class="site-shell bni-pickleball-rules__layout">
                <div>
                    <p class="bni-experience-kicker">THÔNG TIN THI ĐẤU</p>
                    <h2 id="bni-pickleball-rules-title">{{ $pickleballContent['rules_title'] }}</h2>
                    <p>Phần quản trị cho phép Ban tổ chức cập nhật đầy đủ đối tượng tham dự, thể thức, luật áp dụng và các lưu ý trước ngày thi đấu.</p>
                </div>
                <div class="bni-rich-copy bni-pickleball-rules__content">
                    {!! $pickleballContent['rules'] ?: '<p>Thể lệ chính thức đang được Ban tổ chức hoàn thiện và sẽ được công bố tại đây. Anh/chị vui lòng theo dõi trước ngày thi đấu.</p>' !!}
                </div>
            </div>
        </section>

        <section class="bni-section" id="dang-ky">
            <div class="site-shell bni-pickleball-register">
                <div><p class="bni-experience-kicker">RSVP</p><h2>{{ $pickleballContent['registration_title'] }}</h2><p>{{ $pickleballContent['registration_description'] }}</p>@if ($event?->venue)<p class="bni-meta-line">{{ $event->venue }}</p>@endif @if ($event?->starts_at)<p class="bni-meta-line">{{ $event->starts_at->translatedFormat('H:i · d/m/Y') }}</p>@endif</div>
                <form class="bni-form" method="POST" action="{{ LocalizedUrl::route('bni.pickleball.register') }}">
                    @csrf
                    <div><label for="pickleball-full-name">Họ và tên</label><input id="pickleball-full-name" name="full_name" required value="{{ old('full_name') }}">@error('full_name')<p class="bni-form-error">{{ $message }}</p>@enderror</div>
                    <div><label for="pickleball-phone">Số điện thoại</label><input id="pickleball-phone" type="tel" name="phone" required value="{{ old('phone') }}">@error('phone')<p class="bni-form-error">{{ $message }}</p>@enderror</div>
                    <div><label for="pickleball-email">Email</label><input id="pickleball-email" name="email" type="email" value="{{ old('email') }}">@error('email')<p class="bni-form-error">{{ $message }}</p>@enderror</div>
                    <div><label for="pickleball-chapter">Chapter (nếu có)</label><select id="pickleball-chapter" name="bni_chapter_id"><option value="">Khách mời / chưa chọn</option>@foreach ($chapters as $chapter)<option value="{{ $chapter->id }}" @selected((int) old('bni_chapter_id') === $chapter->id)>{{ $chapter->short_name ?: $chapter->name }}</option>@endforeach</select>@error('bni_chapter_id')<p class="bni-form-error">{{ $message }}</p>@enderror</div>
                    <div><label for="pickleball-team">Tên đội</label><input id="pickleball-team" name="team_name" value="{{ old('team_name') }}">@error('team_name')<p class="bni-form-error">{{ $message }}</p>@enderror</div>
                    <div><label for="pickleball-skill">Trình độ</label><select id="pickleball-skill" name="skill_level"><option value="">Chọn trình độ</option><option value="beginner" @selected(old('skill_level') === 'beginner')>Mới chơi</option><option value="intermediate" @selected(old('skill_level') === 'intermediate')>Trung bình</option><option value="advanced" @selected(old('skill_level') === 'advanced')>Nâng cao</option></select>@error('skill_level')<p class="bni-form-error">{{ $message }}</p>@enderror</div>
                    <div class="bni-form__full"><label for="pickleball-note">Ghi chú</label><textarea id="pickleball-note" name="note" rows="3">{{ old('note') }}</textarea>@error('note')<p class="bni-form-error">{{ $message }}</p>@enderror</div>
                    <button class="bni-button bni-button--red bni-form__full" type="submit">Gửi đăng ký</button>
                </form>
            </div>
        </section>

        <section class="bni-section bni-schedule" id="lich-trinh" aria-labelledby="pickleball-schedule-title">
            <div class="site-shell">
                <div class="bni-section-heading"><h2 id="pickleball-schedule-title">Lịch thi đấu</h2></div>
                <div class="bni-schedule__layout">
                    <div class="bni-schedule__main">
                        <div class="bni-tab-list">
                            @forelse ($scheduleDays as $day)<button type="button" @click="scheduleDay = {{ $day['number'] }}" :class="scheduleDay === {{ $day['number'] }} && 'is-active'">{{ $day['label'] }}</button>@empty<button class="is-active" type="button">Ngày thi đấu</button>@endforelse
                        </div>
                        @forelse ($scheduleDays as $day)
                            <div class="bni-schedule-list" x-show="scheduleDay === {{ $day['number'] }}">
                                @foreach ($day['items'] as $item)
                                    <article class="bni-schedule-item"><time>{{ $item['time'] ?: 'Đang cập nhật' }}</time><div><h3>{{ $item['title'] }}</h3>@if ($item['description'])<span>{{ $item['description'] }}</span>@endif</div></article>
                                @endforeach
                            </div>
                        @empty
                            <div class="bni-schedule-list"><p class="bni-empty-copy">Lịch thi đấu sẽ được cập nhật từ panel BNI.</p></div>
                        @endforelse
                    </div>
                    <aside class="bni-schedule__aside"><img src="{{ asset('bni-logo-red.svg') }}" alt="BNI"><div><h3>Lịch theo từng ngày</h3><p>Chọn ngày để xem đầy đủ các khung giờ và nội dung thi đấu.</p></div></aside>
                </div>
            </div>
        </section>

        <section class="bni-section bni-news" aria-labelledby="pickleball-news-title">
            <div class="site-shell"><div class="bni-section-heading"><h2 id="pickleball-news-title">Tin giải đấu</h2><p class="bni-section-heading__description">Cập nhật từ sân Pickleball.</p></div><div class="bni-pickleball-news">@forelse ($articles as $article)<article class="bni-news-card">@if ($article['image_url'])<img src="{{ $article['image_url'] }}" alt="" loading="lazy">@endif<div><p>Pickleball</p><h3><a href="{{ LocalizedUrl::route('bni.articles.show', ['article' => $article['slug']]) }}">{{ $article['title'] }}</a></h3><span>{{ $article['excerpt'] }}</span></div></article>@empty<p class="bni-empty-copy">Tin giải đấu sẽ hiển thị tại đây sau khi được xuất bản.</p>@endforelse</div></div>
        </section>
    </div>
@endsection
