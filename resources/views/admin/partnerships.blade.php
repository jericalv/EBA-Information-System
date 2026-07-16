@extends('admin.layout')

@section('title', 'Partnership Applications')

@section('extra-css')
<style>
    /* ===== Applications Table ===== */
    .business-badge {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(212,168,67,0.12);
        color: #b8860b;
        max-width: 100%;
        white-space: normal;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .badge-pending { background: rgba(245,158,11,0.1); color: #d97706; }
    .badge-approved { background: #E9F6EE; color: #15803D; }
    .badge-rejected { background: rgba(239,68,68,0.1); color: #dc2626; }
    .badge-registered { background: rgba(59,130,246,0.1); color: #2563eb; }
    .badge-expired { background: #e5e7eb; color: var(--muted); }
    html[data-theme="dark"] .badge-pending { background: rgba(227, 164, 72, 0.14); color: #E9C288; }
    html[data-theme="dark"] .badge-approved { background: rgba(30, 149, 96, 0.16); color: #8CD6AF; }
    html[data-theme="dark"] .badge-rejected { background: rgba(227, 106, 106, 0.14); color: #F0A0A0; }
    html[data-theme="dark"] .badge-registered { background: rgba(96, 165, 250, 0.14); color: #9CC4F8; }
    html[data-theme="dark"] .badge-expired { background: rgba(255, 255, 255, 0.07); color: var(--muted); }

    /* Wizard step badge (table) */
    .wizard-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        margin-top: 6px;
        border: 1px solid transparent;
    }
    .wizard-badge.gray { background: var(--hover-2); color: var(--ink); border-color: var(--line-strong); }
    .wizard-badge.step-1 { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
    .wizard-badge.step-2 { background: #fff7ed; color: #9a3412; border-color: #fed7aa; }
    .wizard-badge.step-3 { background: #f5f3ff; color: #5b21b6; border-color: #ddd6fe; }
    .wizard-badge.step-4 { background: #f0fdfa; color: #0f766e; border-color: #99f6e4; }
    .wizard-badge.amber { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .wizard-badge.violet { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .wizard-badge.blue { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .wizard-badge.green { background: var(--hover-2); color: var(--ink); border-color: var(--line-strong); }
    html[data-theme="dark"] .wizard-badge.step-1,
    html[data-theme="dark"] .wizard-badge.blue { background: rgba(96, 165, 250, 0.14); color: #9CC4F8; border-color: rgba(96, 165, 250, 0.35); }
    html[data-theme="dark"] .wizard-badge.step-2 { background: rgba(234, 124, 58, 0.15); color: #F0B08A; border-color: rgba(234, 124, 58, 0.35); }
    html[data-theme="dark"] .wizard-badge.step-3,
    html[data-theme="dark"] .wizard-badge.violet { background: rgba(167, 139, 250, 0.15); color: #C6B4FA; border-color: rgba(167, 139, 250, 0.35); }
    html[data-theme="dark"] .wizard-badge.step-4 { background: rgba(45, 193, 166, 0.15); color: #85DCC9; border-color: rgba(45, 193, 166, 0.35); }
    html[data-theme="dark"] .wizard-badge.amber { background: rgba(227, 164, 72, 0.14); color: #E9C288; border-color: rgba(227, 164, 72, 0.35); }
    .wizard-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #f59e0b;
        box-shadow: 0 0 0 rgba(245, 158, 11, 0.45);
        animation: wizardPulse 1.5s infinite;
        flex-shrink: 0;
    }
    @keyframes wizardPulse {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.45); }
        70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }

    tbody tr.hidden { display: none; }

    .btn svg { width: 15px; height: 15px; flex-shrink: 0; }
    .btn-sm svg { width: 14px; height: 14px; }
    .btn:focus-visible { outline: 2px solid var(--green); outline-offset: 2px; }

    /* === Compact Applications Table === */
    #partnerships-data-table thead th {
        padding: 8px 16px;
        font-size: 10.5px;
        letter-spacing: 0.6px;
        white-space: nowrap;
    }
    #partnerships-data-table td {
        padding: 8px 16px;
        font-size: 12.5px;
        vertical-align: middle;
    }
    #partnerships-data-table tbody tr {
        transition: background 0.15s ease;
    }
    #partnerships-data-table .user-cell {
        gap: 10px;
    }
    #partnerships-data-table .user-avatar {
        width: 32px;
        height: 32px;
        font-size: 11px;
        background: linear-gradient(135deg, var(--green) 0%, var(--green-light) 100%);
        box-shadow: 0 2px 4px rgba(10,92,47,0.18);
    }
    #partnerships-data-table .user-name {
        font-size: 12.5px;
        line-height: 1.3;
    }
    #partnerships-data-table .user-email {
        font-size: 11px;
        margin-top: 1px;
        color: var(--muted);
    }
    #partnerships-data-table .business-badge {
        font-size: 11.5px;
        padding: 3px 9px;
    }
    #partnerships-data-table .contact-number {
        font-size: 12.5px;
        color: var(--ink);
        white-space: nowrap;
    }
    #partnerships-data-table .contact-empty {
        color: var(--faint);
        font-size: 12.5px;
    }
    #partnerships-data-table .submitted-date {
        font-weight: 600;
        font-size: 12.5px;
        white-space: nowrap;
    }
    #partnerships-data-table .submitted-time {
        font-size: 11px;
        color: var(--faint);
        margin-top: 1px;
    }
    #partnerships-data-table .badge {
        font-size: 11.5px;
        padding: 3px 9px;
    }
    #partnerships-data-table .wizard-badge {
        margin-top: 5px;
        padding: 3px 9px;
        font-size: 10.5px;
    }
    #partnerships-data-table th.actions-col,
    #partnerships-data-table td.actions-col {
        text-align: center;
    }
    #partnerships-data-table td.actions-col .btn {
        white-space: nowrap;
    }
</style>
@endsection

@section('content')

    <!-- Filters -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.partnerships') }}" class="toolbar" style="width:100%;">
                <div class="search-box">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input id="partnerships-search-input" type="text" name="search" placeholder="Search by name, email, or business..." value="{{ request('search') }}" oninput="filterRows()">
                </div>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card">
        <div class="card-body">
            @php
                // Carried on the review link so "Back to Partnerships" restores this view.
                $listQuery = array_filter(request()->only(['search', 'status', 'page']), fn ($v) => $v !== null && $v !== '');
            @endphp
            <table id="partnerships-data-table">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Business</th>
                        <th>Contact</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        <tr
                            data-row="partnership"
                            data-name="{{ strtolower(trim(($application->business_name ?? '') . ' ' . ($application->full_name ?? ''))) }}"
                            data-status="{{ strtolower($application->status === 'under_review' ? 'under review' : str_replace('_', ' ', $application->status)) }}"
                        >
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">{{ strtoupper(substr($application->first_name, 0, 1) . substr($application->last_name, 0, 1)) }}</div>
                                    <div>
                                        <div class="user-name">{{ $application->full_name }}</div>
                                        <div class="user-email">{{ $application->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="business-badge">{{ $application->business_name }}</span>
                            </td>
                            <td>
                                @php
                                    $contactNumber = $application->phone_number ?: $application->phone;
                                @endphp
                                @if($contactNumber)
                                    <div class="contact-number">{{ $contactNumber }}</div>
                                @else
                                    <span class="contact-empty">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="submitted-date">{{ $application->created_at->format('M d, Y') }}</div>
                                <div class="submitted-time">{{ $application->created_at->format('g:i A') }}</div>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($application->status) {
                                        'pending' => 'badge-pending',
                                        'under_review' => 'badge-pending',
                                        'approved' => 'badge-approved',
                                        'rejected' => 'badge-rejected',
                                        'registered' => 'badge-registered',
                                        'expired' => 'badge-expired',
                                        default => ''
                                    };
                                    $statusLabel = match($application->status) {
                                        'pending' => 'Under Review',
                                        'under_review' => 'Under Review',
                                        'approved' => 'Approved',
                                        'registered' => 'Registered',
                                        'rejected' => 'Rejected',
                                        'expired' => 'Expired',
                                        default => ucfirst($application->status),
                                    };

                                    $wizardStatus = $application->status === 'approved'
                                        ? 'final_approved'
                                        : ($application->wizard_status ?? 'loi_pending');

                                    if (in_array($wizardStatus, ['loi_rejected', 'form_rejected'])) {
                                        $badgeClass = 'badge-rejected';
                                        $statusLabel = 'Rejected';
                                    }

                                    [$wizardBadgeColor, $wizardBadgeLabel, $wizardNeedsAction] = match ($wizardStatus) {
                                        'loi_submitted' => ['step-1', 'Step 1: Action Needed', true],
                                        'form_submitted' => ['step-2', 'Step 2: Action Needed', true],
                                        'receipt_submitted' => ['step-4', 'Step 4: Action Needed', true],
                                        'loi_pending', 'loi_rejected' => ['step-1', 'Step 1: LOI', false],
                                        'form_pending', 'form_rejected' => ['step-2', 'Step 2: Form', false],
                                        'docs_in_progress' => ['step-3', 'Step 3: Docs', false],
                                        'receipt_pending' => ['step-3', 'Step 3: Receipt Upload', false],
                                        'final_approved' => ['green', '✓ Approved', false],
                                        default => ['step-1', 'Step 1: LOI', false],
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                                @if ($wizardStatus !== 'final_approved')
                                <div>
                                    <span class="wizard-badge {{ $wizardBadgeColor }}">
                                        @if ($wizardNeedsAction)
                                            <span class="wizard-dot"></span>
                                        @endif
                                        {{ $wizardBadgeLabel }}
                                    </span>
                                </div>
                                @endif
                            </td>
                            <td class="actions-col">
                                <a href="{{ route('admin.partnerships.show', array_merge(['application' => $application->id], $listQuery)) }}" class="btn btn-outline btn-sm">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px;color: var(--faint);">
                                No partnership applications found.
                            </td>
                        </tr>
                    @endforelse
                    <tr id="partnerships-no-results-row" style="display:none;">
                        <td colspan="6" style="text-align:center;padding:48px;color: var(--faint);">
                            No results found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @if ($applications->hasPages())
            <div class="pagination-wrap">
                {{ $applications->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

@endsection

@section('scripts')
<script>
    function filterRows() {
        const input = document.getElementById('partnerships-search-input');
        const rows = document.querySelectorAll('tr[data-row="partnership"]');
        const noResultsRow = document.getElementById('partnerships-no-results-row');

        if (!input || rows.length === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
            return;
        }

        const query = input.value.trim().toLowerCase();
        let visibleRows = 0;

        rows.forEach((row) => {
            const name = row.dataset.name || '';
            const status = row.dataset.status || '';
            const matches = !query || name.includes(query) || status.includes(query);

            row.classList.toggle('hidden', !matches);
            if (matches) {
                visibleRows += 1;
            }
        });

        if (noResultsRow) {
            noResultsRow.style.display = visibleRows === 0 ? '' : 'none';
        }
    }

    filterRows();
</script>
@endsection
