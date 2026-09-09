<section id="lien-he" class="branding-contact branding-dark">
    <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative branding-contact-grid">
        <div class="branding-contact-offer">
            <p>{{ $content['contact_section']['eyebrow'] }}</p>
            <div class="branding-old-price"><span>TỔNG GIÁ TRỊ</span><s>{{ number_format($content['offer']['original_price'], 0, ',', '.') }} VNĐ</s></div>
            @include('frontend.landing.parts.branding.offer-price', ['offer' => $content['offer'], 'isHeading' => true])
            <p>{{ $content['contact_section']['description'] }}</p>
        </div>
        <form id="branding-lead-form" class="branding-lead-form" method="POST" action="{{ route('contact.store') }}">
            @csrf
            <input type="hidden" name="from_landing_page" value="1">
            <input type="hidden" name="landing_page_id" value="{{ $landingPage->id }}">
            <input type="hidden" name="landing_block_id" value="branding-contact">
            <input type="hidden" name="return_to" value="{{ request()->getPathInfo() }}#lien-he">
            @foreach (['visitor_id', 'session_id', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid', 'fbclid', 'first_url', 'referrer'] as $field)
                <input type="hidden" name="{{ $field }}" data-attribution-field="{{ $field }}">
            @endforeach
            <h3>ĐĂNG KÝ TƯ VẤN MIỄN PHÍ</h3>
            @if (session('success'))<p class="branding-form-success" role="status">{{ session('success') }}</p>@endif
            @if ($errors->any())<div class="branding-form-errors" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
            <label><span class="sr-only">Họ tên của bạn</span><input name="name" type="text" placeholder="Họ tên của bạn *" value="{{ old('name') }}" autocomplete="name" maxlength="255" required></label>
            <label><span class="sr-only">Số điện thoại</span><input name="phone" type="tel" placeholder="Số điện thoại *" value="{{ old('phone') }}" autocomplete="tel" inputmode="tel" maxlength="32" required></label>
            <label><span class="sr-only">Nhu cầu của bạn</span><textarea name="message" rows="3" placeholder="Nhu cầu của bạn (tùy chọn)" maxlength="5000">{{ old('message') }}</textarea></label>
            <button class="branding-button" type="submit">Gửi thông tin ngay <x-landing.branding-icon /></button>
        </form>
        <address class="branding-direct-contact">
            <h3>LIÊN HỆ TRỰC TIẾP</h3>
            <div class="branding-contact-phones"><x-landing.branding-icon name="phone" /><div><a href="tel:{{ preg_replace('/\D/', '', $website->hotline) }}">{{ $website->hotline }}</a><a href="tel:{{ preg_replace('/\D/', '', $website->contact_phone) }}">{{ $website->contact_phone }}</a></div></div>
            <a href="mailto:{{ $contact['email'] }}"><x-landing.branding-icon name="email" /><span>{{ $contact['email'] }}</span></a>
            <p><x-landing.branding-icon name="pin" /><span>{{ $contact['address_1'] }}</span></p>
            <div class="branding-socials">
                @if ($website->facebook_url)<a href="{{ $website->facebook_url }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="branding-facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>@endif
                @if ($website->youtube_url)<a href="{{ $website->youtube_url }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="branding-youtube"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>@endif
                @if ($website->zalo_url)<a href="{{ $website->zalo_url }}" target="_blank" rel="noopener noreferrer" aria-label="Zalo" class="branding-zalo">Zalo</a>@endif
            </div>
        </address>
    </div>
</section>
