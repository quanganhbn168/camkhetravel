@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('content')
    <section class="contact-page-hero">
        @if ($contactHeroImageUrl)
            <img class="contact-page-hero__image" src="{{ $contactHeroImageUrl }}" alt="" aria-hidden="true">
        @endif
        <div class="contact-page-hero__overlay"></div>
        <div class="site-shell contact-page-hero__content">
            <h1>LIÊN HỆ</h1>
        </div>
    </section>
    <section class="section-space">
        <div class="site-shell grid gap-8 lg:grid-cols-[0.7fr_1.3fr] lg:gap-12">
            <aside class="p-8 md:p-10">
                <h2 class="font-display text-3xl leading-tight text-ink">Thông tin kết nối</h2>
                <div class="mt-10 grid gap-4">
                    @if ($website->hotline)
                        <a class="text-primary text-lg font-semibold" href="tel:{{ preg_replace('/\s+/', '', $website->hotline) }}">{{ $website->hotline }}</a>
                    @endif
                    @if ($website->contact_email)
                        <a class="text-sm text-slate-500 hover:text-ink" href="mailto:{{ $website->contact_email }}">{{ $website->contact_email }}</a>
                    @endif
                    @if ($website->address)
                        <p class="mt-5 border-t border-slate-200 pt-5 text-sm leading-7 text-slate-600">{{ $website->address }}</p>
                    @endif
                </div>
            </aside>
            <form class="rounded-[2rem] border border-slate-200 p-6 shadow-[0_16px_40px_rgba(16,35,62,0.06)] md:p-10" method="POST" action="{{ LocalizedUrl::route('contact.store') }}">
                @csrf
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="text-sm font-semibold text-ink">Họ và tên<input class="form-field" name="name" value="{{ old('name') }}" required></label>
                    <label class="text-sm font-semibold text-ink">Số điện thoại<input class="form-field" name="phone" value="{{ old('phone') }}"></label>
                    <label class="text-sm font-semibold text-ink">Email<input class="form-field" type="email" name="email" value="{{ old('email') }}"></label>
                    <label class="text-sm font-semibold text-ink">Công ty<input class="form-field" name="company" value="{{ old('company') }}"></label>
                    <label class="text-sm font-semibold text-ink">Dịch vụ quan tâm<select class="form-field" name="landing_id"><option value="">Chọn dịch vụ</option>@foreach ($services as $service)<option value="{{ $service->id }}" @selected(old('landing_id') == $service->id || request('landing') == $service->id)>{{ $service->title }}</option>@endforeach</select></label>
                    <label class="text-sm font-semibold text-ink">Ngân sách dự kiến<input class="form-field" name="budget" value="{{ old('budget') }}"></label>
                    <label class="text-sm font-semibold text-ink">Thời gian dự kiến<input class="form-field" name="timeline" value="{{ old('timeline') }}"></label>
                    <label class="text-sm font-semibold text-ink md:col-span-2">Nhu cầu của bạn<textarea class="form-field" name="message" rows="7" required>{{ old('message') }}</textarea></label>
                </div>
                @if ($errors->any())<p class="mt-5 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</p>@endif
                <button class="button-primary mt-7" type="submit">Gửi yêu cầu <span aria-hidden="true">↗</span></button>
            </form>
        </div>
    </section>
@endsection
