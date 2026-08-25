@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page bni-invitation-page')
@section('main_id', 'bni-invitation-main')
@section('main_class', 'bni-experience-main overflow-x-clip')

@section('content')
    <section class="bni-invitation">
        @if ($heroImageUrl)<img class="bni-invitation__image" src="{{ $heroImageUrl }}" alt="" aria-hidden="true">@endif
        <div class="site-shell bni-invitation__shell">
            <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI" class="bni-invitation__logo">
            <p class="bni-experience-kicker">Thư mời dành riêng</p>
            <h1>Kính mời {{ $invitation->guest_name }}</h1>
            <p class="bni-invitation__lead">Ban tổ chức trân trọng chào đón anh/chị tham dự <strong>{{ $invitation->event->title }}</strong>@if ($invitation->chapter), cùng {{ $invitation->chapter->name }}@endif.</p>
            <dl class="bni-invitation__details"><div><dt>Thời gian</dt><dd>{{ $invitation->event->starts_at?->translatedFormat('H:i · d/m/Y') ?: 'Sẽ được cập nhật' }}</dd></div><div><dt>Địa điểm</dt><dd>{{ $invitation->event->venue ?: 'Sẽ được cập nhật' }}</dd></div><div><dt>Mã thư mời</dt><dd>{{ $invitation->invitation_code ?: 'BNI-'.$invitation->id }}</dd></div></dl>
            <form class="bni-rsvp-form" method="POST" action="{{ LocalizedUrl::route('bni.invitations.rsvp', ['invitation' => $invitation]) }}">
                @csrf
                <h2>Xác nhận tham dự</h2>
                <div class="bni-rsvp-form__options"><label><input type="radio" name="rsvp_status" value="attending" @checked(old('rsvp_status', $invitation->rsvp_status) === 'attending')> Tôi sẽ tham dự</label><label><input type="radio" name="rsvp_status" value="declined" @checked(old('rsvp_status', $invitation->rsvp_status) === 'declined')> Tôi chưa thể tham dự</label></div>
                <div><label for="guest-count">Số người tham dự</label><input id="guest-count" type="number" name="guest_count" min="1" max="10" value="{{ old('guest_count', $invitation->guest_count) }}"></div>
                <div><label for="rsvp-note">Lời nhắn với Ban tổ chức</label><textarea id="rsvp-note" name="rsvp_note" rows="3">{{ old('rsvp_note', $invitation->rsvp_note) }}</textarea></div>
                <button class="bni-button bni-button--red" type="submit">Gửi phản hồi</button>
            </form>
        </div>
    </section>
@endsection
