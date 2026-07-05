@if ($paginator->hasPages())
    <div style="display:flex;justify-content:center;align-items:center;gap:6px;margin-top:24px;flex-wrap:wrap;">
        
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span style="padding:8px 14px;border-radius:8px;border:1px solid #e2e8f0;color:#cbd5e1;font-size:14px;font-weight:600;background:#f8fafc;">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding:8px 14px;border-radius:8px;border:1px solid #e2e8f0;color:#374151;font-size:14px;font-weight:600;background:#fff;text-decoration:none;">‹</a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding:8px 14px;color:#94a3b8;font-size:14px;">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding:8px 14px;border-radius:8px;border:1px solid #0A5C2F;background:#0A5C2F;color:#fff;font-size:14px;font-weight:600;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding:8px 14px;border-radius:8px;border:1px solid #e2e8f0;color:#374151;font-size:14px;font-weight:600;background:#fff;text-decoration:none;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding:8px 14px;border-radius:8px;border:1px solid #e2e8f0;color:#374151;font-size:14px;font-weight:600;background:#fff;text-decoration:none;">›</a>
        @else
            <span style="padding:8px 14px;border-radius:8px;border:1px solid #e2e8f0;color:#cbd5e1;font-size:14px;font-weight:600;background:#f8fafc;">›</span>
        @endif

    </div>
@endif