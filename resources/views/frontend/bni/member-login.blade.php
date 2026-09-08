@extends('layouts.master')

@use(App\Support\Localization\LocalizedUrl)

@section('body_class', 'bni-experience-page')
@section('main_id', 'bni-member-login-main')
@section('main_class', 'bni-experience-main')

@section('content')
    <section class="bni-member-login"><div class="bni-member-login__card"><a href="{{ LocalizedUrl::route('bni.handover') }}"><img src="{{ asset('bni-logo-red.svg') }}" alt="BNI"></a><p class="bni-experience-kicker">Cổng hội viên</p><h1>Đăng nhập hội viên BNI</h1><p>Dành cho hội viên được cấp tài khoản để tham gia bình luận và gửi cảm xúc cho tin BNI.</p><form class="bni-form" method="POST" action="{{ LocalizedUrl::route('bni.member.login.store') }}">@csrf<div><label for="member-email">Email</label><input id="member-email" name="email" type="email" required value="{{ old('email') }}"></div><div><label for="member-password">Mật khẩu</label><input id="member-password" name="password" type="password" required></div><label class="bni-check"><input type="checkbox" name="remember" value="1"> Ghi nhớ đăng nhập</label><button class="bni-button bni-button--red" type="submit">Đăng nhập</button></form></div></section>
@endsection
