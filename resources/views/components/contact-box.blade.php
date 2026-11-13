@props(['title' => 'Cần tư vấn đặt xe?', 'description' => null])
{{-- Khung thông tin liên hệ: toàn bộ dữ liệu lấy từ Cài đặt website (hotline/điện thoại, Zalo, email, địa chỉ). --}}
@php
    $phones = ($footerContactPhones ?? collect())->values();
    $branches = ($footerContactBranches ?? collect())->values();
    $address = $branches->first()['address'] ?? ($website->address ?: null);
    $email = $website->contact_email ?: null;
    $zalo = $website->zalo_url ?: null;
    $primaryPhone = $phones->first();
    $hasInfo = $phones->isNotEmpty() || $zalo || $email || $address;
@endphp
@if ($hasInfo)
    <aside {{ $attributes->class('contact-box') }} aria-labelledby="contact-box-title">
        <div class="contact-box__intro">
            <h2 class="contact-box__title" id="contact-box-title">{{ $title }}</h2>
            <p class="contact-box__text">{{ $description ?: 'Liên hệ '.($website->site_name ?: 'chúng tôi').' để được tư vấn lịch trình, báo giá và đặt xe nhanh chóng.' }}</p>
            @if ($primaryPhone || $zalo)
                <div class="contact-box__actions">
                    @if ($primaryPhone)
                        <a class="btn btn-primary contact-box__btn" href="{{ $primaryPhone['href'] }}"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i>Gọi {{ $primaryPhone['label'] }}</a>
                    @endif
                    @if ($zalo)
                        <a class="btn contact-box__btn contact-box__btn--zalo" href="{{ $zalo }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/zalo.svg') }}" alt="" width="20" height="20">Nhắn Zalo</a>
                    @endif
                </div>
            @endif
        </div>

        <ul class="contact-box__list">
            @if ($website->company_name)
                <li><span class="contact-box__icon" aria-hidden="true"><i class="fa-regular fa-building"></i></span><div><span class="contact-box__label">Đơn vị</span><strong>{{ $website->company_name }}</strong></div></li>
            @endif
            @if ($phones->isNotEmpty())
                <li><span class="contact-box__icon" aria-hidden="true"><i class="fa-solid fa-phone"></i></span><div><span class="contact-box__label">Điện thoại</span>@foreach ($phones as $phone)<a href="{{ $phone['href'] }}">{{ $phone['label'] }}</a>@endforeach</div></li>
            @endif
            @if ($email)
                <li><span class="contact-box__icon" aria-hidden="true"><i class="fa-regular fa-envelope"></i></span><div><span class="contact-box__label">Email</span><a href="mailto:{{ $email }}">{{ $email }}</a></div></li>
            @endif
            @if ($address)
                <li><span class="contact-box__icon" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span><div><span class="contact-box__label">Địa chỉ</span><span>{{ $address }}</span></div></li>
            @endif
        </ul>
    </aside>
@endif
