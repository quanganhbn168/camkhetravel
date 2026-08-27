<div class="fixed bottom-5 left-4 z-30 grid gap-2 sm:left-5" aria-label="Liên hệ nhanh">
    @if ($website->hotline)
        <a class="floating-action bg-ink" href="tel:{{ preg_replace('/\s+/', '', $website->hotline) }}" aria-label="Gọi {{ $website->hotline }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.64a2 2 0 0 1-.45 2.11L8.01 9.74a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.86.29 1.74.5 2.64.62A2 2 0 0 1 22 16.92Z"/></svg>
        </a>
    @endif
    @if ($website->zalo_url)
        <a class="floating-action bg-primary" href="{{ $website->zalo_url }}" target="_blank" rel="noopener noreferrer" aria-label="Nhắn Zalo">
            <img class="size-6" src="{{ asset('images/zalo.svg') }}" alt="">
        </a>
    @endif
</div>

<button class="scroll-top" type="button" data-scroll-top aria-label="Lên đầu trang">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
</button>
