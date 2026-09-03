@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page bni-plan-page bni-registration-page')
@section('main_id', 'bni-registration-main')
@section('main_class', 'bni-plan-main overflow-x-clip')

@section('content')
    <div class="bni-plan">
        @include('frontend.partials.bni-navigation')

        @if (session('success'))
            <div class="bni-plan-flash" role="status">{{ session('success') }}</div>
        @endif

        <section class="bni-registration-hero" aria-labelledby="bni-registration-title">
            @if ($heroImageUrl)
                <img class="bni-registration-hero__image" src="{{ $heroImageUrl }}" alt="" aria-hidden="true">
            @endif
            <div class="bni-registration-hero__overlay" aria-hidden="true"></div>
            <div class="site-shell bni-registration-hero__content">
                <img src="{{ asset('bni-logo-red.svg') }}" alt="BNI" class="bni-registration-hero__logo">
                <p class="bni-plan-section__eyebrow">Đăng ký tham dự</p>
                <h1 id="bni-registration-title">{{ $event->title }}</h1>
                @if ($event->summary)<p>{{ $event->summary }}</p>@endif
                <a class="bni-button bni-button--light" href="{{ LocalizedUrl::route('bni.handover') }}">← Về trang sự kiện</a>
            </div>
        </section>

        <section id="form-dang-ky" class="bni-plan-section bni-registration-section" aria-labelledby="bni-registration-form-title">
            <div class="site-shell bni-registration-layout">
                <aside class="bni-registration-facts" aria-label="Thông tin sự kiện">
                    <p class="bni-plan-section__eyebrow">Thông tin chương trình</p>
                    <h2>{{ $event->title }}</h2>
                    <dl>
                        @if ($eventDate)<div><dt>Ngày tổ chức</dt><dd>{{ $eventDate }}</dd></div>@endif
                        @if ($eventTime)<div><dt>Thời gian</dt><dd>{{ $eventTime }}</dd></div>@endif
                        @if ($eventLocation)<div><dt>Địa điểm</dt><dd>{{ $eventLocation }}</dd></div>@endif
                        @if ($eventDirectionsUrl)
                            <div>
                                <dt>Google Maps</dt>
                                <dd><a class="bni-registration-map-link" href="{{ $eventDirectionsUrl }}" target="_blank" rel="noopener noreferrer">Xem đường đi <span aria-hidden="true">↗</span></a></dd>
                            </div>
                        @endif
                    </dl>
                </aside>

                <div class="bni-registration-form-wrap">
                    <p class="bni-plan-section__eyebrow">Thông tin người tham dự</p>
                    <h2 id="bni-registration-form-title">Đăng ký Lễ chuyển giao</h2>
                    <p>Thông tin được chuyển trực tiếp tới Ban tổ chức BNI để xác nhận tham dự.</p>

                    <form class="bni-form bni-registration-form" method="POST" action="{{ LocalizedUrl::route('bni.registrations.store') }}">
                        @csrf
                        <div>
                            <label for="registration-full-name">Họ và tên *</label>
                            <input id="registration-full-name" type="text" name="full_name" value="{{ old('full_name') }}" autocomplete="name" required>
                            @error('full_name')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="registration-phone">Số điện thoại *</label>
                            <input id="registration-phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" required>
                            @error('phone')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="registration-email">Email</label>
                            <input id="registration-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email">
                            @error('email')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="registration-chapter">Chapter</label>
                            <select id="registration-chapter" name="bni_chapter_id">
                                <option value="">Chọn chapter</option>
                                @foreach ($chapters as $chapter)
                                    <option value="{{ $chapter->id }}" @selected((string) old('bni_chapter_id') === (string) $chapter->id)>{{ $chapter->short_name ?: $chapter->name }}</option>
                                @endforeach
                            </select>
                            @error('bni_chapter_id')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="bni-form__full">
                            <label for="registration-note">Lời nhắn với Ban tổ chức</label>
                            <textarea id="registration-note" name="note" rows="4">{{ old('note') }}</textarea>
                            @error('note')<p class="bni-form-error">{{ $message }}</p>@enderror
                        </div>
                        <button class="bni-button bni-button--red bni-form__full" type="submit">Gửi đăng ký</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
