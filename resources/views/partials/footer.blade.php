<footer class="footer">
    <section class="footer__cta">
        @if ($websiteMediaUrls->get($website->footer_background_media_id))
            <img class="footer__cta-background" src="{{ $websiteMediaUrls->get($website->footer_background_media_id) }}" alt="" aria-hidden="true" loading="lazy">
        @endif
        <div class="container footer__cta-content">
            <div>
                <h2 class="footer__cta-title">Giải pháp PCCC an toàn cho công trình</h2>
                <p class="footer__cta-description">{{ $website->site_name }} đồng hành từ khảo sát, thiết kế đến thi công và bảo trì hệ thống.</p>
            </div>
            <a class="btn btn-light" href="{{ route('contact') }}">Nhận tư vấn ngay <span aria-hidden="true">→</span></a>
        </div>
    </section>

    <div class="container footer__main">
        <section >
            <a class="footer__logo" href="{{ route('home') }}" aria-label="{{ $website->site_name }}">
                @if ($websiteMediaUrls->get($website->logo_media_id))
                    <img src="{{ $websiteMediaUrls->get($website->logo_media_id) }}" alt="{{ $website->site_name }}">
                @else
                    <span>{{ $website->site_name }}</span>
                @endif
            </a>
            <p class="footer__tagline">{{ $website->tagline }}</p>
            <div class="footer__socials">
                @if ($website->facebook_url)<a class="footer__social" href="{{ $website->facebook_url }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.75l.41-3.12H13.5V7.9c0-.9.25-1.52 1.55-1.52h1.66V3.6A22.2 22.2 0 0 0 14.3 3c-2.38 0-4.01 1.45-4.01 4.11v2.77H7.6V13h2.69v8h3.21Z"/></svg></a>@endif
                @if ($website->zalo_url)<a class="footer__social" href="{{ $website->zalo_url }}" target="_blank" rel="noopener noreferrer" aria-label="Zalo"><img src="{{ asset('images/zalo.svg') }}" alt=""></a>@endif
                @if ($website->youtube_url)<a class="footer__social" href="{{ $website->youtube_url }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.12C19.55 3.5 12 3.5 12 3.5s-7.55 0-9.4.58A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.12c1.85.58 9.4.58 9.4.58s7.55 0 9.4-.58a3 3 0 0 0 2.1-2.12A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8ZM9.6 15.57V8.43L15.87 12 9.6 15.57Z"/></svg></a>@endif
            </div>
        </section>

        <section >
            <h2 class="footer__heading">Dịch vụ</h2>
            <div class="footer__links">
                @forelse ($footerServices as $service)<a href="{{ route('slug.show', ['slug' => $service->slug]) }}">{{ $service->title }}</a>@empty<span>Đang cập nhật</span>@endforelse
            </div>
        </section>

        <section >
            <h2 class="footer__heading">Liên kết nhanh</h2>
            <nav class="footer__links" aria-label="Liên kết chân trang">
                @if ($footerNavigation->isNotEmpty())
                    @foreach ($footerNavigation as $item)
                        <div >
                            <a href="{{ $item['url'] }}" @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $item['label'] }}</a>
                            @if ($item['has_children'])<div class="footer__sub-links">@foreach ($item['children'] as $child)<a href="{{ $child['url'] }}" @if ($child['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $child['label'] }}</a>@endforeach</div>@endif
                        </div>
                    @endforeach
                @else
                    <a href="{{ route('home') }}">Trang chủ</a><a href="{{ route('about') }}">Giới thiệu</a><a href="{{ route('projects.index') }}">Dự án</a><a href="{{ route('posts.index') }}">Tin tức</a><a href="{{ route('contact') }}">Liên hệ</a>
                @endif
            </nav>
        </section>

        <section >
            <h2 class="footer__heading">Thông tin liên hệ</h2>
            <div class="footer__contacts">
                @if ($footerContactBranches->isNotEmpty())
                    @foreach ($footerContactBranches as $branch)<p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>{{ $branch['name'] }}: {{ $branch['address'] }}</span></p>@endforeach
                @elseif ($website->address)
                    <p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>{{ $website->address }}</span></p>
                @endif
                @if ($footerContactPhones->isNotEmpty())<p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.2 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.64a2 2 0 0 1-.45 2.11L8 9.75a16 16 0 0 0 6 6l1.28-1.28a16 16 0 0 1 2.11-.45c.86.29 1.74.5 2.64.62A2 2 0 0 1 22 16.92Z"/></svg><span class="footer__phone-list">@foreach ($footerContactPhones as $phone)<a href="{{ $phone['href'] }}">{{ $phone['label'] }}</a>@if (! $loop->last)<span aria-hidden="true">|</span>@endif @endforeach</span></p>@endif
                @if ($website->contact_email)<a class="footer__email" href="mailto:{{ $website->contact_email }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><span>{{ $website->contact_email }}</span></a>@endif
            </div>
        </section>
    </div>

    <div class="footer__bottom"><div class="container footer__bottom-content"><span>© {{ $currentYear }} {{ $website->company_name ?: $website->site_name }}. All rights reserved.</span><nav class="footer__legal" aria-label="Pháp lý"><a href="{{ route('home') }}">Chính sách bảo mật</a><a href="{{ route('contact') }}">Điều khoản sử dụng</a></nav></div></div>
</footer>
