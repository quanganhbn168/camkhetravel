@if ($paginator->hasPages())
    <nav class="flex items-center justify-center gap-2" role="navigation" aria-label="Phân trang">
        @if ($paginator->onFirstPage())
            <span class="grid size-10 cursor-not-allowed place-items-center rounded-xl border border-slate-100 text-slate-300" aria-disabled="true" aria-label="Trang trước">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </span>
        @else
            <a class="grid size-10 place-items-center rounded-xl border border-slate-200 text-slate-600 transition hover:border-primary hover:bg-primary hover:text-white" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="grid size-10 place-items-center text-sm font-semibold text-slate-400" aria-disabled="true">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="grid size-10 place-items-center rounded-xl bg-primary text-sm font-bold text-white shadow-[0_8px_18px_color-mix(in_srgb,var(--site-color-primary)_30%,transparent)]" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="grid size-10 place-items-center rounded-xl border border-transparent text-sm font-semibold text-slate-600 transition hover:border-slate-200 hover:bg-white hover:text-ink" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="grid size-10 place-items-center rounded-xl border border-slate-200 text-slate-600 transition hover:border-primary hover:bg-primary hover:text-white" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang tiếp theo">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        @else
            <span class="grid size-10 cursor-not-allowed place-items-center rounded-xl border border-slate-100 text-slate-300" aria-disabled="true" aria-label="Trang tiếp theo">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </span>
        @endif
    </nav>
@endif
