@extends('layouts.plan')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page bni-plan-page bni-invitation-page')
@section('main_id', 'bni-invitation-main')
@section('main_class', 'bni-plan-main overflow-x-clip')

@section('content')
    @php
        $contacts = $invitationContent['contacts'] ?? collect();
        $location = collect([$event->venue, $event->address])->filter()->implode(', ');
    @endphp

    <div class="bni-plan">
        <section class="bni-invitation-hero" aria-labelledby="bni-invitation-title">
            @if ($heroImageUrl)
                <img class="bni-invitation-hero__image" src="{{ $heroImageUrl }}" alt="" aria-hidden="true">
            @endif
            <div class="bni-invitation-hero__overlay" aria-hidden="true"></div>
            <div class="site-shell bni-invitation-hero__shell">
                <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI" class="bni-invitation-hero__logo">
                <p class="bni-invitation-hero__label">{{ $invitationContent['label'] }}</p>
                <p class="bni-invitation-hero__event">{{ $invitationContent['event_label'] }}</p>
                <p class="bni-invitation-hero__greeting">{{ $invitationContent['greeting'] }}</p>
                <h1 id="bni-invitation-title">{{ $guestName }}</h1>
                <p class="bni-invitation-hero__event-title">{{ $event->title }}</p>
            </div>
        </section>

        <section class="bni-plan-section bni-invitation-facts" aria-label="Thông tin sự kiện">
            <div class="site-shell">
                <dl class="bni-invitation-facts__grid{{ $invitation ? '' : ' bni-invitation-facts__grid--two' }}">
                    <div>
                        <dt>Thời gian</dt>
                        <dd>{{ $event->starts_at?->translatedFormat('H:i · d/m/Y') ?: 'Sẽ được cập nhật' }}</dd>
                        @if ($event->ends_at)<small>Đến {{ $event->ends_at->translatedFormat('H:i · d/m/Y') }}</small>@endif
                    </div>
                    <div>
                        <dt>Địa điểm</dt>
                        <dd>{{ $event->venue ?: 'Sẽ được cập nhật' }}</dd>
                        @if ($event->address)<small>{{ $event->address }}</small>@endif
                        @if ($directionsUrl)
                            <a class="bni-invitation-directions" href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer">Chỉ đường <span aria-hidden="true">↗</span></a>
                        @endif
                    </div>
                    @if ($invitation)
                        <div>
                            <dt>Mã thư mời</dt>
                            <dd>{{ $invitation->invitation_code ?: 'BNI-'.$invitation->id }}</dd>
                            @if ($chapter)<small>{{ $chapter->name }}</small>@endif
                        </div>
                    @endif
                </dl>
            </div>
        </section>

        @if (filled($invitationContent['content'] ?? null))
            <section class="bni-plan-section bni-invitation-content" id="noi-dung" aria-labelledby="bni-invitation-content-title">
                <div class="narrow-shell">
                    <h2 id="bni-invitation-content-title">{{ $invitationContent['content_title'] }}</h2>
                    <div class="bni-invitation-rich-copy">{!! $invitationContent['content'] !!}</div>
                </div>
            </section>
        @endif

        <section class="bni-plan-section bni-invitation-schedule" id="lich-trinh" aria-labelledby="bni-invitation-schedule-title">
            <div class="site-shell">
                <div class="bni-plan-section__heading">
                    <p class="bni-plan-section__eyebrow">{{ $invitationContent['event_label'] }}</p>
                    <h2 id="bni-invitation-schedule-title">{{ $invitationContent['schedule_title'] }}</h2>
                </div>
                <div class="bni-invitation-schedule__list">
                    @forelse ($scheduleDays as $day)
                        <section class="bni-invitation-schedule__day" aria-labelledby="bni-invitation-day-{{ $day['number'] }}">
                            <h3 id="bni-invitation-day-{{ $day['number'] }}">{{ $day['label'] }}</h3>
                            @foreach ($day['items'] as $item)
                                <article class="bni-invitation-schedule__item">
                                    <span class="bni-invitation-schedule__index" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <time>{{ $item['time'] ?: 'Đang cập nhật' }}</time>
                                    <div>
                                        <h4>{{ $item['title'] }}</h4>
                                        @if ($item['description'])<p>{{ $item['description'] }}</p>@endif
                                    </div>
                                </article>
                            @endforeach
                        </section>
                    @empty
                        <p class="bni-plan-empty">Lịch trình sự kiện sẽ được Ban tổ chức cập nhật.</p>
                    @endforelse
                </div>
            </div>
        </section>

        @if ($featuredEvents->isNotEmpty())
            <section class="bni-plan-section bni-invitation-featured-events" id="su-kien-noi-bat" aria-labelledby="bni-invitation-featured-events-title">
                <div class="site-shell">
                    <div class="bni-plan-section__heading">
                        <p class="bni-plan-section__eyebrow">BNI EVENTS</p>
                        <h2 id="bni-invitation-featured-events-title">Sự kiện nổi bật</h2>
                    </div>
                    <div class="bni-invitation-featured-events__grid">
                        @foreach ($featuredEvents as $featuredEvent)
                            <article class="bni-invitation-featured-event">
                                <div class="bni-invitation-featured-event__visual">
                                    @if ($featuredEvent['image_url'])
                                        <img src="{{ $featuredEvent['image_url'] }}" alt="{{ $featuredEvent['title'] }}" loading="lazy">
                                    @else
                                        <span aria-hidden="true">BNI</span>
                                    @endif
                                    <span class="bni-invitation-featured-event__label">{{ $featuredEvent['label'] }}</span>
                                </div>
                                <div class="bni-invitation-featured-event__body">
                                    <h3>{{ $featuredEvent['title'] }}</h3>
                                    <div class="bni-invitation-featured-event__meta">
                                        @if ($featuredEvent['date'])<span>{{ $featuredEvent['date'] }}</span>@endif
                                        @if ($featuredEvent['venue'])<span>{{ $featuredEvent['venue'] }}</span>@endif
                                    </div>
                                    @if ($featuredEvent['url'])
                                        <a href="{{ $featuredEvent['url'] }}">Xem sự kiện <span aria-hidden="true">↗</span></a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if (filled($invitationContent['note_content'] ?? null))
            <section class="bni-plan-section bni-invitation-note" id="luu-y" aria-labelledby="bni-invitation-note-title">
                <div class="narrow-shell">
                    <div class="bni-invitation-note__card">
                        <p class="bni-plan-section__eyebrow">Thông tin cần biết</p>
                        <h2 id="bni-invitation-note-title">{{ $invitationContent['note_title'] }}</h2>
                        <div class="bni-invitation-rich-copy">{!! $invitationContent['note_content'] !!}</div>
                    </div>
                </div>
            </section>
        @endif

        <section class="bni-plan-section bni-invitation-rsvp" id="rsvp" aria-labelledby="bni-invitation-rsvp-title">
            <div class="site-shell bni-invitation-rsvp__grid">
                <div class="bni-invitation-rsvp__intro">
                    <p class="bni-plan-section__eyebrow">RSVP</p>
                    <h2 id="bni-invitation-rsvp-title">{{ $invitationContent['rsvp_title'] }}</h2>
                    <p>{{ $invitationContent['rsvp_description'] }}</p>
                </div>
                @if ($invitation)
                    <form class="bni-rsvp-form" method="POST" action="{{ LocalizedUrl::route('bni.invitations.rsvp', ['invitation' => $invitation]) }}">
                        @csrf
                        <fieldset>
                            <legend>Trạng thái tham dự</legend>
                            <div class="bni-rsvp-form__options">
                                <label><input type="radio" name="rsvp_status" value="attending" @checked(old('rsvp_status', $invitation->rsvp_status) === 'attending')> Tôi sẽ tham dự</label>
                                <label><input type="radio" name="rsvp_status" value="declined" @checked(old('rsvp_status', $invitation->rsvp_status) === 'declined')> Tôi chưa thể tham dự</label>
                            </div>
                            @error('rsvp_status')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </fieldset>
                        <div>
                            <label for="guest-count">Số người tham dự</label>
                            <input id="guest-count" type="number" name="guest_count" min="1" max="10" value="{{ old('guest_count', $invitation->guest_count) }}" required>
                            @error('guest_count')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="rsvp-note">Lời nhắn với Ban tổ chức</label>
                            <textarea id="rsvp-note" name="rsvp_note" rows="3">{{ old('rsvp_note', $invitation->rsvp_note) }}</textarea>
                            @error('rsvp_note')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <button class="bni-button bni-button--red" type="submit">Gửi phản hồi</button>
                    </form>
                @else
                    <form class="bni-rsvp-form" method="POST" action="{{ LocalizedUrl::route('bni.invitations.template.rsvp') }}">
                        @csrf
                        <div>
                            <label for="template-full-name">Họ và tên</label>
                            <input id="template-full-name" type="text" name="full_name" value="{{ old('full_name') }}" required>
                            @error('full_name')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="template-phone">Số điện thoại</label>
                            <input id="template-phone" type="tel" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="template-email">Email</label>
                            <input id="template-email" type="email" name="email" value="{{ old('email') }}">
                            @error('email')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="template-note">Lời nhắn với Ban tổ chức</label>
                            <textarea id="template-note" name="note" rows="3">{{ old('note') }}</textarea>
                            @error('note')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <button class="bni-button bni-button--red" type="submit">Gửi xác nhận</button>
                    </form>
                @endif
            </div>
        </section>

        <section class="bni-plan-section bni-invitation-contact" id="lien-he" aria-labelledby="bni-invitation-contact-title">
            <div class="narrow-shell">
                <div class="bni-invitation-contact__card">
                    <p class="bni-plan-section__eyebrow">{{ $chapter?->short_name ?: 'BNI' }}</p>
                    <h2 id="bni-invitation-contact-title">{{ $invitationContent['contact_title'] }}</h2>
                    <p>{{ $invitationContent['contact_description'] }}</p>
                    @if ($contacts->isNotEmpty())
                        <div class="bni-invitation-contact__people">
                            @foreach ($contacts as $contact)
                                <article>
                                    @if ($contact['name'])<strong>{{ $contact['name'] }}</strong>@endif
                                    @if ($contact['position'])<span>{{ $contact['position'] }}</span>@endif
                                    <div class="bni-invitation-contact__links">
                                        @if ($contact['phone_url'])<a href="{{ $contact['phone_url'] }}">{{ $contact['phone'] }}</a>@endif
                                        @if ($contact['email_url'])<a href="{{ $contact['email_url'] }}">{{ $contact['email'] }}</a>@endif
                                        @if ($contact['zalo_url'])<a href="{{ $contact['zalo_url'] }}" target="_blank" rel="noopener">Zalo</a>@endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        @if ($location)<div class="bni-invitation-contact__links"><span>{{ $location }}</span></div>@endif
                    @elseif ($location)
                        <div class="bni-invitation-contact__links"><span>{{ $location }}</span></div>
                    @elseif ($isInvitationTemplate)
                        <p class="bni-invitation-contact__template-note">Đầu mối liên hệ sẽ hiển thị theo từng chapter khi phát hành thư mời.</p>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
