@if ($paginator->hasPages())
    <nav aria-label="Phân trang">
        <ul class="pagination justify-content-center flex-wrap gap-1 mb-0">
            <li class="page-item @if ($paginator->onFirstPage()) disabled @endif">
                @if ($paginator->onFirstPage())
                    <span class="page-link" aria-disabled="true" aria-label="Trang trước">‹</span>
                @else
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">‹</a>
                @endif
            </li>
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link" aria-disabled="true">{{ $element }}</span></li>
                @elseif (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><span class="page-link" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}" aria-label="Trang {{ $page }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            <li class="page-item @unless ($paginator->hasMorePages()) disabled @endunless">
                @if ($paginator->hasMorePages())
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang tiếp theo">›</a>
                @else
                    <span class="page-link" aria-disabled="true" aria-label="Trang tiếp theo">›</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
