@use(App\Support\Localization\LocalizedUrl)

<a class="site-header__brand" href="{{ LocalizedUrl::route('home') }}" aria-label="{{ $website->site_name }}">
                @if ($headerLogoUrl)
                    <img src="{{ $headerLogoUrl }}" alt="{{ $website->site_name }}" class="h-14 max-w-56 object-contain object-left">
                @else
                    <span class="pccc-brand__symbol" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.9 2.2c.2 3.1-.8 4.7-2.4 6.1-1.2 1-1.8 2-1.8 3.3 0 .8.3 1.5.8 2.1.2-1.7 1.1-2.8 2.6-3.8-.2 2.5 2.7 3.5 2.7 5.8 0 1.1-.4 2-1 2.8 2.1-.8 3.6-2.7 3.6-5 0-2.4-1.3-4.3-3.2-5.8.4 2.3-.1 3.5-.9 4.3.2-2.9-.7-5.9-.4-9.8Z"/></svg>
                    </span>
                    <span class="pccc-brand__words"><strong>DVTEC</strong><small>Trading &amp; Construction</small></span>
                @endif
            </a>
