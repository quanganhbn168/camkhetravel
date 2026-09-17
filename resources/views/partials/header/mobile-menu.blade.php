<aside class="offcanvas offcanvas-end site-mobile-menu" tabindex="-1" id="mobile-drawer" aria-labelledby="mobile-drawer-title">
    <div class="offcanvas-header border-bottom">
        <h2 class="offcanvas-title fs-5" id="mobile-drawer-title">{{ $website->site_name }}</h2>
        <button class="btn-close" type="button" data-bs-dismiss="offcanvas" aria-label="Đóng menu"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="site-mobile-nav" aria-label="Điều hướng di động">
            @foreach ($headerNavigation as $item)
                <div class="site-mobile-nav__item">
                    <div class="d-flex align-items-center">
                        <a class="site-mobile-nav__link flex-grow-1 @if ($item['is_active']) is-active @endif"
                           href="{{ $item['url'] }}" @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif
                           @if ($item['is_active']) aria-current="page" @endif>{{ $item['label'] }}</a>
                        @if ($item['has_children'])
                            <button class="site-mobile-nav__toggle" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#mobile-submenu-{{ $loop->index }}" aria-controls="mobile-submenu-{{ $loop->index }}"
                                    aria-expanded="{{ $item['is_active'] ? 'true' : 'false' }}" aria-label="Mở danh mục {{ $item['label'] }}">⌄</button>
                        @endif
                    </div>
                    @if ($item['has_children'])
                        <div id="mobile-submenu-{{ $loop->index }}" class="collapse @if ($item['is_active']) show @endif">
                            <ul class="site-mobile-nav__children">
                                @foreach ($item['children'] as $child)
                                    <li><a class="@if ($child['is_active']) is-active @endif" href="{{ $child['url'] }}"
                                           @if ($child['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif
                                           @if ($child['is_active']) aria-current="page" @endif>{{ $child['label'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>
    </div>
</aside>
