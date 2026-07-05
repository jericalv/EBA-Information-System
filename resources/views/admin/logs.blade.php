@extends('admin.layout')
@section('title', 'System Logs')

@section('content')
<style>
    .logs-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
    .logs-title h1 { font-size: 22px; font-weight: 800; color: var(--green); margin: 0; }
    .logs-title p { font-size: 13px; color: #64748b; margin: 4px 0 0; }
    .logs-meta { display: flex; gap: 16px; align-items: center; }
    .meta-badge { background: #f1f5f9; border-radius: 8px; padding: 8px 14px; font-size: 13px; color: #475569; }
    .meta-badge strong { color: #1e293b; }

    .logs-toolbar { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 20px; }
    .logs-toolbar input[type="text"] {
        flex: 1; min-width: 200px; padding: 9px 14px; border: 1px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; outline: none; transition: border .2s;
    }
    .logs-toolbar input[type="text"]:focus { border-color: var(--green); }
    .logs-toolbar select {
        padding: 9px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px;
        background: #fff; outline: none; cursor: pointer;
    }
    .btn-filter { padding: 9px 18px; background: var(--green); color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
    .btn-filter:hover { opacity: 0.9; }
    .btn-clear { padding: 9px 18px; background: #dc2626; color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
    .btn-clear:hover { opacity: 0.9; }

    .log-entry { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 10px; transition: box-shadow .2s; }
    .log-entry:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .log-meta { display: flex; gap: 10px; align-items: center; margin-bottom: 8px; flex-wrap: wrap; }
    .log-time { font-size: 12px; color: #94a3b8; font-family: monospace; }
    .log-level {
        display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 11px;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .level-emergency, .level-alert, .level-critical { background: #fef2f2; color: #dc2626; }
    .level-error { background: #fff7ed; color: #ea580c; }
    .level-warning { background: #fffbeb; color: #d97706; }
    .level-notice, .level-info { background: #eff6ff; color: #2563eb; }
    .level-debug { background: #f0fdf4; color: #16a34a; }
    .log-message {
        font-size: 13px; color: #334155; font-family: 'Courier New', monospace;
        white-space: pre-wrap; word-break: break-all; max-height: 200px; overflow-y: auto;
        background: #f8fafc; padding: 10px 12px; border-radius: 6px; line-height: 1.5;
    }
    .log-toggle { font-size: 12px; color: var(--green); cursor: pointer; font-weight: 600; margin-top: 6px; display: inline-block; }
    .log-toggle:hover { text-decoration: underline; }

    .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .empty-state svg { width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.4; }
    .empty-state p { font-size: 15px; }

    .pagination-wrap { display: flex; justify-content: center; margin-top: 24px; }
</style>

<div class="logs-header">
    <div class="logs-title">
        <h1>System Logs</h1>
        <p>Application log entries from <code>storage/logs/laravel.log</code></p>
    </div>
    <div class="logs-meta">
        <span class="meta-badge"><strong>{{ $total }}</strong> entries</span>
        <span class="meta-badge"><strong>{{ $fileSize > 1048576 ? number_format($fileSize / 1048576, 1) . ' MB' : number_format($fileSize / 1024, 1) . ' KB' }}</strong> file size</span>
        <form action="{{ route('admin.logs.clear') }}" method="POST" onsubmit="return confirm('Clear all logs? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-clear">Clear Logs</button>
        </form>
    </div>
</div>

<form class="logs-toolbar" method="GET" action="{{ route('admin.logs') }}">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs...">
    <select name="level">
        <option value="">All Levels</option>
        @foreach(['emergency','alert','critical','error','warning','notice','info','debug'] as $lvl)
            <option value="{{ $lvl }}" {{ request('level') === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-filter">Filter</button>
    @if(request('search') || request('level'))
        <a href="{{ route('admin.logs') }}" style="font-size:13px;color:#64748b;text-decoration:none;">Reset</a>
    @endif
</form>

@if($logs->count() > 0)
    @foreach($logs as $log)
        <div class="log-entry">
            <div class="log-meta">
                <span class="log-level level-{{ $log['level'] }}">{{ $log['level'] }}</span>
                <span class="log-time">{{ $log['timestamp'] }}</span>
            </div>
            @php
                $msg = $log['message'];
                $isLong = strlen($msg) > 300;
            @endphp
            <div class="log-message" @if($isLong) style="max-height:80px;" data-collapsed="true" @endif>{{ $isLong ? substr($msg, 0, 300) . '...' : $msg }}</div>
            @if($isLong)
                <span class="log-toggle" onclick="toggleLog(this, '{{ addslashes(htmlspecialchars(base64_encode($msg), ENT_QUOTES)) }}')">Show more</span>
            @endif
        </div>
    @endforeach

    <div class="pagination-wrap">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
@else
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
        <p>No log entries found.</p>
    </div>
@endif

<script>
function toggleLog(el, encoded) {
    const container = el.previousElementSibling;
    if (container.dataset.collapsed === 'true') {
        try { container.textContent = atob(encoded); } catch(e) {}
        container.style.maxHeight = '400px';
        container.dataset.collapsed = 'false';
        el.textContent = 'Show less';
    } else {
        container.style.maxHeight = '80px';
        container.dataset.collapsed = 'true';
        el.textContent = 'Show more';
    }
}
</script>
@endsection
