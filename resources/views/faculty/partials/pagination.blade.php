@if ($paginator->hasPages())
    <nav class="pg" role="navigation" aria-label="Pagination">
        @if (method_exists($paginator, 'total'))
            <span class="pg-info">Showing {{ number_format($paginator->firstItem() ?? 0) }}&ndash;{{ number_format($paginator->lastItem() ?? 0) }} of {{ number_format($paginator->total()) }}</span>
        @endif

        <div class="pg-list">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="pg-btn is-disabled" aria-disabled="true" aria-label="Previous page">&lsaquo;</span>
            @else
                <a class="pg-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">&lsaquo;</a>
            @endif

            {{-- Page numbers (length-aware paginators only) --}}
            @foreach ($elements ?? [] as $element)
                @if (is_string($element))
                    <span class="pg-dots">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pg-btn is-current" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="pg-btn" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a class="pg-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">&rsaquo;</a>
            @else
                <span class="pg-btn is-disabled" aria-disabled="true" aria-label="Next page">&rsaquo;</span>
            @endif
        </div>
    </nav>
@endif
