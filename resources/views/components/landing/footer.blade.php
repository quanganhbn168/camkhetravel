@props([
    'brand' => 'THT MEDIA',
    'logo' => null,
    'text' => 'Đồng hành cùng doanh nghiệp từ chiến lược đến triển khai',
    'phone' => null,
    'contact' => [],
    'services' => [],
    'zaloUrl' => null,
    'facebookUrl' => null,
])

<footer class="tht-landing-footer">
    <x-landing.container>
        <div class="tht-landing-footer__grid">
            <div class="tht-landing-footer__brand">
                <a href="#tht-landing-main" class="tht-landing-footer__logo mb-3 inline-block" aria-label="{{ $brand }}">
                    @if ($logo)
                        <img src="{{ $logo }}" alt="{{ $brand }}" width="146" height="52">
                    @else
                        <strong>{{ $brand }}</strong>
                    @endif
                </a>
                <p class="mt-2">{{ $text }}</p>
                <div class="tht-landing-footer__socials">
                    @if ($facebookUrl)
                        <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    @endif
                    @if ($zaloUrl)
                        <a href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Zalo"><span class="tht-landing-zalo-icon tht-landing-zalo-icon--social" aria-hidden="true"></span></a>
                    @endif
                    @if ($phone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $phone) }}" aria-label="Hotline"><i class="fa-solid fa-phone"></i></a>
                    @endif
                </div>
            </div>

            <div class="tht-landing-footer__links tht-landing-footer__links--services">
                <h4>Dịch vụ</h4>
                <nav aria-label="Các dịch vụ">
                    @foreach ((array) $services as $service)
                        <a href="{{ $service['url'] ?? '#' }}">{{ $service['label'] ?? '' }}</a>
                    @endforeach
                    <a href="#lien-he" data-landing-event="cta_click" data-block-id="footer">Đăng ký tư vấn</a>
                </nav>
            </div>

            <div class="tht-landing-footer__contact">
                <h4>Liên hệ</h4>
                <div class="tht-landing-footer__contact-list">
                    @if (! empty($contact['hotline_1']))
                        <div class="tht-landing-footer__contact-item"><i class="fa-solid fa-phone" aria-hidden="true"></i><div><strong>Hotline:</strong><a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $contact['hotline_1']) }}">{{ $contact['hotline_1'] }}</a>@if (! empty($contact['hotline_2']))<span>/</span><a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $contact['hotline_2']) }}">{{ $contact['hotline_2'] }}</a>@endif</div></div>
                    @endif
                    @if (! empty($contact['email']))
                        <div class="tht-landing-footer__contact-item"><i class="fa-solid fa-envelope" aria-hidden="true"></i><div><strong>Email:</strong><a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></div></div>
                    @endif
                    @if (! empty($contact['address_1']))
                        <div class="tht-landing-footer__contact-item"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><div><strong>Địa chỉ:</strong><span>{{ $contact['address_1'] }}</span></div></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="tht-landing-footer__bottom"><span>&copy; {{ now()->year }} {{ $brand }}. Bảo lưu mọi quyền.</span><a href="#tht-landing-main">Về đầu trang <i class="fa-solid fa-arrow-up-long ml-1"></i></a></div>
    </x-landing.container>
</footer>
