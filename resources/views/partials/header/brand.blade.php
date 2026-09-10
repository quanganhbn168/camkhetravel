@use(App\Support\Localization\LocalizedUrl)

<a class="site-header__brand" href="{{ LocalizedUrl::route('home') }}" aria-label="{{ $website->site_name }}">
                @if ($headerLogoUrl)
                    <img src="{{ $headerLogoUrl }}" alt="{{ $website->site_name }}" class="h-14 max-w-56 object-contain object-left">
                @else
                    <span class="grid size-11 place-items-center rounded-xl bg-ink font-display text-base font-bold text-white">DV</span>
                    <span class="text-sm font-bold tracking-[-0.04em] text-ink sm:text-base">{{ $website->site_name }}</span>
                @endif
            </a>
