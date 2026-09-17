<nav class="site-nav d-flex flex-wrap align-items-center" aria-label="Điều hướng chính">
    @foreach ($headerNavigation as $item)
        <div class="site-nav__item @if ($item['has_children']) dropdown @endif">
            <a class="site-nav__link @if ($item['is_active']) is-active @endif"
               href="{{ $item['url'] }}"
               @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif
               @if ($item['is_active']) aria-current="page" @endif>{{ $item['label'] }}</a>
            @if ($item['has_children'])
                <button type="button" class="site-nav__toggle dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        aria-controls="desktop-menu-{{ $loop->index }}"
                        aria-label="Mở danh mục {{ $item['label'] }}"></button>
                <ul class="dropdown-menu" id="desktop-menu-{{ $loop->index }}">
                    @foreach ($item['children'] as $child)
                        <li><a class="dropdown-item @if ($child['is_active']) active @endif" href="{{ $child['url'] }}"
                               @if ($child['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif
                               @if ($child['is_active']) aria-current="page" @endif>{{ $child['label'] }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach
</nav>
