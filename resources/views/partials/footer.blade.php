@use(App\Support\Localization\LocalizedUrl)

<footer class="bg-ink pt-14 text-slate-300 md:pt-18">
    <div class="site-shell grid gap-10 pb-12 sm:grid-cols-2 lg:grid-cols-[1.35fr_1fr_1fr_1.35fr] lg:gap-8">
        <div class="lg:pr-7">
            <a class="inline-flex items-center" href="{{ LocalizedUrl::route('home') }}" aria-label="{{ $website->site_name }}">
                @if ($websiteMediaUrls->get($website->logo_media_id))
                    <img class="h-11 max-w-48 object-contain object-left brightness-0 invert" src="{{ $websiteMediaUrls->get($website->logo_media_id) }}" alt="{{ $website->site_name }}">
                @else
                    <span class="font-display text-2xl font-semibold text-white">{{ $website->site_name }}</span>
                @endif
            </a>
            <p class="mt-5 max-w-xs text-sm leading-7 text-slate-400">{{ $website->tagline }}</p>
            <div class="mt-7 flex items-center gap-2">
                @if ($website->facebook_url)
                    <a class="footer-social" href="{{ $website->facebook_url }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.75l.41-3.12H13.5V7.9c0-.9.25-1.52 1.55-1.52h1.66V3.6A22.2 22.2 0 0 0 14.3 3c-2.38 0-4.01 1.45-4.01 4.11v2.77H7.6V13h2.69v8h3.21Z"/></svg>
                    </a>
                @endif
                @if ($website->zalo_url)
                    <a class="footer-social" href="{{ $website->zalo_url }}" target="_blank" rel="noopener noreferrer" aria-label="Zalo">
                        <img class="size-5" src="{{ asset('images/zalo.svg') }}" alt="">
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
            <h2 class="text-sm font-bold text-white uppercase">Dịch vụ</h2>
            <div class="mt-5 grid gap-3 text-sm">
                @forelse ($footerServices as $service)
                    <a class="hover:text-white" href="{{ LocalizedUrl::slug($service->slug) }}">{{ $service->title }}</a>
                @empty
                    <span class="text-slate-500">Đang cập nhật</span>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-sm font-bold text-white uppercase">Liên kết nhanh</h2>
            <div class="mt-5 grid gap-3 text-sm">
                @if ($footerNavigation->isNotEmpty())
                    @foreach ($footerNavigation as $item)
                        <div class="grid gap-2">
                            <a class="hover:text-white" href="{{ $item['url'] }}" @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $item['label'] }}</a>
                            @if ($item['has_children'])
                                <div class="grid gap-2 border-l border-white/10 pl-3 text-xs text-slate-400">
                                    @foreach ($item['children'] as $child)
                                        <a class="hover:text-white" href="{{ $child['url'] }}" @if ($child['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $child['label'] }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <a class="hover:text-white" href="{{ LocalizedUrl::route('home') }}">{{ __('site.home') }}</a>
                    <a class="hover:text-white" href="{{ LocalizedUrl::route('about') }}">{{ __('site.about') }}</a>
                    <a class="hover:text-white" href="{{ LocalizedUrl::route('projects.index') }}">{{ __('site.projects') }}</a>
                    <a class="hover:text-white" href="{{ LocalizedUrl::route('pricing.index') }}">{{ __('site.pricing') }}</a>
                    <a class="hover:text-white" href="{{ LocalizedUrl::route('posts.index') }}">{{ __('site.news') }}</a>
                    <a class="hover:text-white" href="{{ LocalizedUrl::route('contact') }}">{{ __('site.contact') }}</a>
                @endif
            </div>
        </div>

        <div>
            <h2 class="text-sm font-bold text-white uppercase">Thông tin liên hệ</h2>
            <div class="mt-5 grid gap-3 text-sm leading-6">
                @if ($website->address)<p class="flex gap-3 text-slate-400"><svg class="mt-1 size-4 shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>{{ $website->address }}</span></p>@endif
                @if ($website->hotline || $website->contact_phone)
                    <p class="flex gap-3 text-slate-400">
                        <svg class="mt-1 size-4 shrink-0 text-primary" data-footer-contact-icon="phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.2 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.64a2 2 0 0 1-.45 2.11L8 9.75a16 16 0 0 0 6 6l1.28-1.28a16 16 0 0 1 2.11-.45c.86.29 1.74.5 2.64.62A2 2 0 0 1 22 16.92Z"/></svg>
                        <span class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            @if ($website->hotline)<a class="hover:text-white" href="tel:{{ preg_replace('/\s+/', '', $website->hotline) }}">{{ $website->hotline }}</a>@endif
                            @if ($website->hotline && $website->contact_phone)<span class="text-slate-500" aria-hidden="true">|</span>@endif
                            @if ($website->contact_phone)<a class="hover:text-white" href="tel:{{ preg_replace('/\s+/', '', $website->contact_phone) }}">{{ $website->contact_phone }}</a>@endif
                        </span>
                    </p>
                @endif
                @if ($website->contact_email)<a class="flex gap-3 hover:text-white" href="mailto:{{ $website->contact_email }}"><svg class="mt-1 size-4 shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><span>{{ $website->contact_email }}</span></a>@endif
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="site-shell flex flex-col gap-3 py-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
        <span>© {{ now()->year }} {{ $website->company_name ?: $website->site_name }}. All rights reserved.</span>
            <div class="flex gap-5"><a class="hover:text-slate-300" href="{{ LocalizedUrl::route('home') }}">Chính sách bảo mật</a><a class="hover:text-slate-300" href="{{ LocalizedUrl::route('contact') }}">Điều khoản sử dụng</a></div>
        </div>
    </div>
</footer>
