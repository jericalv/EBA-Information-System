@extends('admin.layout')
@section('title', 'Activity Logs')

@section('content')
<style>
    .logs-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
    .logs-title h1 { font-size: 22px; font-weight: 800; color: var(--green); margin: 0; }
    .logs-title p { font-size: 13px; color: #64748b; margin: 4px 0 0; }

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

    .activity-table { width: 100%; border-collapse: separate; border-spacing: 0; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
    .activity-table th {
        background: #f8fafc; padding: 12px 16px; text-align: left; font-size: 12px;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; border-bottom: 1px solid #e2e8f0;
    }
    .activity-table td {
        padding: 14px 16px; font-size: 13px; border-bottom: 1px solid #f1f5f9; vertical-align: top;
    }
    .activity-table tr:last-child td { border-bottom: none; }
    .activity-table tr:hover td { background: #fafbfc; }

    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-avatar {
        width: 32px; height: 32px; border-radius: 50%; background: var(--green); color: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0;
    }
    .user-name { font-weight: 600; color: #1e293b; }
    .user-ip { font-size: 11px; color: #94a3b8; }

    .action-badge {
        display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;
    }
    .type-badge {
        display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px;
        font-weight: 600; background: #f1f5f9; color: #475569;
    }

    .description-text { color: #334155; line-height: 1.5; }
    .subject-id { font-family: monospace; font-size: 12px; color: var(--green); font-weight: 600; }
    .timestamp { font-size: 12px; color: #94a3b8; white-space: nowrap; }
    .details-toggle {
        font-size: 11px; color: var(--green); cursor: pointer; font-weight: 600;
        margin-top: 4px; display: inline-block;
    }
    .details-toggle:hover { text-decoration: underline; }
    .details-json {
        display: none; margin-top: 6px; padding: 8px 10px; background: #f8fafc; border-radius: 6px;
        font-family: monospace; font-size: 11px; color: #475569; white-space: pre-wrap; word-break: break-all;
    }

    .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .empty-state svg { width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.4; }
    .empty-state p { font-size: 15px; }

    .pagination-wrap { display: flex; justify-content: center; margin-top: 24px; }

    .stats-row { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
    .stat-chip { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 16px; font-size: 13px; color: #475569; }
    .stat-chip strong { color: #1e293b; }
</style>

<div class="logs-header">
    <div class="logs-title">
        <h1>Activity Logs</h1>
        <p>Track all actions performed in the system</p>
    </div>
</div>

<div class="stats-row">
    <span class="stat-chip"><strong>{{ $logs->total() }}</strong> total activities</span>
</div>

<form class="logs-toolbar" method="GET" action="{{ route('admin.activity-logs') }}">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user, description, or code...">
    <select name="action">
        <option value="">All Actions</option>
        @foreach($actions as $act)
            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $act)) }}</option>
        @endforeach
    </select>
    <select name="type">
        <option value="">All Types</option>
        @foreach($types as $t)
            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-filter">Filter</button>
    @if(request('search') || request('action') || request('type'))
        <a href="{{ route('admin.activity-logs') }}" style="font-size:13px;color:#64748b;text-decoration:none;">Reset</a>
    @endif
</form>

@if($logs->count() > 0)
<table class="activity-table">
    <thead>
        <tr>
            <th>User</th>
            <th>Action</th>
            <th>Type</th>
            <th>Description</th>
            <th>Date & Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
        <tr>
            <td>
                <div class="user-cell">
                    <div class="user-avatar">{{ strtoupper(substr($log->user_name, 0, 1)) }}</div>
                    <div>
                        <div class="user-name">{{ $log->user_name }}</div>
                        <div class="user-ip">{{ $log->ip_address }}</div>
                    </div>
                </div>
            </td>
            <td>
                <span class="action-badge" style="background: {{ $log->action_color }}15; color: {{ $log->action_color }};">
                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                </span>
            </td>
            <td>
                <span class="type-badge">{{ ucfirst($log->subject_type) }}</span>
                @if($log->subject_id)
                    <div class="subject-id" style="margin-top:4px;">{{ $log->subject_id }}</div>
                @endif
            </td>
            <td>
                <div class="description-text">{{ $log->description }}</div>
                @if($log->details)
                    <span class="details-toggle" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block'; this.textContent = this.nextElementSibling.style.display === 'block' ? 'Hide details' : 'View details';">View details</span>
                    <div class="details-json">{{ json_encode($log->details, JSON_PRETTY_PRINT) }}</div>
                @endif
            </td>
            <td>
                <div class="timestamp">{{ $log->created_at->format('M d, Y') }}</div>
                <div class="timestamp">{{ $log->created_at->format('g:i:s A') }}</div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="pagination-wrap">
    {{ $logs->links('pagination::bootstrap-5') }}
</div>
@else
<div class="empty-state">
    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
    <p>No activity recorded yet. Recent admin and system actions will appear here.</p>
</div>
@endif
@endsection
