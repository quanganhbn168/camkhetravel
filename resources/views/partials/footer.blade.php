@use(App\Support\Localization\LocalizedUrl)

@php
    $contactPhones = collect($website->phones ?? [])
        ->filter(fn ($phone) => is_array($phone) && filled($phone['number'] ?? null))
        ->values();

    if ($contactPhones->isEmpty()) {
        $contactPhones = collect([
            ['number' => $website->hotline],
            ['number' => $website->contact_phone],
        ])->filter(fn ($phone) => filled($phone['number'] ?? null))->values();
    }

    $contactBranches = collect($website->branches ?? [])
        ->filter(fn ($branch) => is_array($branch) && ($branch['is_active'] ?? true) && filled($branch['address'] ?? null))
        ->values();
@endphp

<footer class="site-footer dv-footer__element-1">
    <section class="pccc-footer-cta">
        @if ($websiteMediaUrls->get($website->footer_background_media_id))
            <img class="site-footer__background" src="{{ $websiteMediaUrls->get($website->footer_background_media_id) }}" alt="" aria-hidden="true" loading="lazy">
            <div class="site-footer__overlay" aria-hidden="true"></div>
        @endif
        <div class="site-container pccc-footer-cta__inner">
            <div>
                <h2>Giải pháp PCCC an toàn cho công trình của anh/chị</h2>
                <p>DVTEC đồng hành từ khảo sát, thiết kế đến thi công và bảo trì hệ thống.</p>
            </div>
            <a class="btn btn-primary button-primary" href="{{ LocalizedUrl::route('contact') }}">Nhận tư vấn ngay <span aria-hidden="true">→</span></a>
        </div>
    </section>
    <div class="site-container w-100 mx-auto dv-footer__div-2">
        <div class="dv-footer__div-3">
            <a class="d-inline-flex align-items-center" href="{{ LocalizedUrl::route('home') }}" aria-label="{{ $website->site_name }}">
                @if ($websiteMediaUrls->get($website->logo_media_id))
                    <img class="object-fit-contain dv-footer__media-5" src="{{ $websiteMediaUrls->get($website->logo_media_id) }}" alt="{{ $website->site_name }}">
                @else
                    <span class="fw-semibold dv-footer__copy-6">{{ $website->site_name }}</span>
                @endif
            </a>
            <p class="dv-footer__copy-7">{{ $website->tagline }}</p>
            <div class="d-flex align-items-center dv-footer__div-8">
                @if ($website->facebook_url)
                    <a class="footer-social" href="{{ $website->facebook_url }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.75l.41-3.12H13.5V7.9c0-.9.25-1.52 1.55-1.52h1.66V3.6A22.2 22.2 0 0 0 14.3 3c-2.38 0-4.01 1.45-4.01 4.11v2.77H7.6V13h2.69v8h3.21Z"/></svg>
                    </a>
                @endif
                @if ($website->zalo_url)
                    <a class="footer-social footer-social--zalo" href="{{ $website->zalo_url }}" target="_blank" rel="noopener noreferrer" aria-label="Zalo">
                        <img class="dv-footer__media-9" src="{{ asset('images/zalo.svg') }}" alt="">
                    </a>
                @endif
                @if ($website->youtube_url)
                    <a class="footer-social" href="{{ $website->youtube_url }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.12C19.55 3.5 12 3.5 12 3.5s-7.55 0-9.4.58A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.12c1.85.58 9.4.58 9.4.58s7.55 0 9.4-.58a3 3 0 0 0 2.1-2.12A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8ZM9.6 15.57V8.43L15.87 12 9.6 15.57Z"/></svg>
                    </a>
                @endif
            </div>
        </div>

        <div>
            <h2 class="fw-bold text-uppercase dv-footer__heading-10">Dịch vụ</h2>
            <div class="d-grid dv-footer__div-11">
                @forelse ($footerServices as $service)
                    <a class="dv-footer__action-12" href="{{ LocalizedUrl::slug($service->slug) }}">{{ $service->title }}</a>
                @empty
                    <span class="dv-footer__copy-13">Đang cập nhật</span>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="fw-bold text-uppercase dv-footer__heading-10">Liên kết nhanh</h2>
            <div class="d-grid dv-footer__div-11">
                @if ($footerNavigation->isNotEmpty())
                    @foreach ($footerNavigation as $item)
                        <div class="d-grid dv-footer__div-14">
                            <a class="dv-footer__action-12" href="{{ $item['url'] }}" @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $item['label'] }}</a>
                            @if ($item['has_children'])
                                <div class="d-grid dv-footer__div-15">
                                    @foreach ($item['children'] as $child)
                                        <a class="dv-footer__action-12" href="{{ $child['url'] }}" @if ($child['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $child['label'] }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <a class="dv-footer__action-12" href="{{ LocalizedUrl::route('home') }}">{{ __('site.home') }}</a>
                    <a class="dv-footer__action-12" href="{{ LocalizedUrl::route('about') }}">{{ __('site.about') }}</a>
                    <a class="dv-footer__action-12" href="{{ LocalizedUrl::route('projects.index') }}">{{ __('site.projects') }}</a>
                    <a class="dv-footer__action-12" href="{{ LocalizedUrl::route('pricing.index') }}">{{ __('site.pricing') }}</a>
                    <a class="dv-footer__action-12" href="{{ LocalizedUrl::route('posts.index') }}">{{ __('site.news') }}</a>
                    <a class="dv-footer__action-12" href="{{ LocalizedUrl::route('contact') }}">{{ __('site.contact') }}</a>
                @endif
            </div>
        </div>

        <div>
            <h2 class="fw-bold text-uppercase dv-footer__heading-10">Thông tin liên hệ</h2>
            <div class="d-grid dv-footer__div-16">
                @if ($contactBranches->isNotEmpty())
                    @foreach ($contactBranches as $branch)
                        <p class="d-flex dv-footer__copy-17"><svg class="flex-shrink-0 dv-footer__media-18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>{{ $branch['name'] ?? 'Địa chỉ' }}: {{ $branch['address'] }}</span></p>
                    @endforeach
                @elseif ($website->address)<p class="d-flex dv-footer__copy-17"><svg class="flex-shrink-0 dv-footer__media-18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>{{ $website->address }}</span></p>@endif
                @if ($contactPhones->isNotEmpty())
                    <p class="d-flex dv-footer__copy-17">
                        <svg class="flex-shrink-0 dv-footer__media-18" data-footer-contact-icon="phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.2 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.64a2 2 0 0 1-.45 2.11L8 9.75a16 16 0 0 0 6 6l1.28-1.28a16 16 0 0 1 2.11-.45c.86.29 1.74.5 2.64.62A2 2 0 0 1 22 16.92Z"/></svg>
                        <span class="d-flex flex-wrap align-items-center dv-footer__copy-19">
                            @foreach ($contactPhones as $phone)
                                @if (! $loop->first)<span class="dv-footer__copy-13" aria-hidden="true">|</span>@endif
                                <a class="dv-footer__action-12" href="tel:{{ preg_replace('/\s+/', '', $phone['number']) }}">{{ $phone['number'] }}</a>
                            @endforeach
                        </span>
                    </p>
                @endif
                @if ($website->contact_email)<a class="d-flex dv-footer__action-20" href="mailto:{{ $website->contact_email }}"><svg class="flex-shrink-0 dv-footer__media-18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><span>{{ $website->contact_email }}</span></a>@endif
            </div>
        </div>
    </div>
    <div class="dv-footer__div-21">
        <div class="site-container w-100 mx-auto flex-column dv-footer__div-22">
        <span>© {{ now()->year }} {{ $website->company_name ?: $website->site_name }}. All rights reserved.</span>
            <div class="d-flex dv-footer__div-23"><a class="dv-footer__action-24" href="{{ LocalizedUrl::route('home') }}">Chính sách bảo mật</a><a class="dv-footer__action-24" href="{{ LocalizedUrl::route('contact') }}">Điều khoản sử dụng</a></div>
        </div>
    </div>
</footer>
