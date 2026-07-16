@extends('faculty.layout')

@section('title', 'Partnership Applications')
@section('page-title', 'Partnerships')

@section('extra-css')
<style>
    main { max-width: 1560px !important; }
    .table { width: 100%; border-collapse: collapse; min-width: 900px; table-layout: fixed; }
    .table td .cell-muted { font-size: 13px; color: var(--muted); }
    .type-badge {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        background: var(--hover-2);
        color: var(--ink);
    }
    .table th, .table td { padding: 12px 12px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: top; overflow-wrap: break-word; word-break: break-word; }
    .business-badge, .type-badge { max-width: 100%; white-space: normal; overflow-wrap: break-word; word-break: break-word; }
    .table th { background: var(--hover); color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; }

    .business-badge {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(212,168,67,0.12);
        color: #b8860b;
    }

    .badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 4px 10px; font-size: 12px; font-weight: 700; }
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
    html[data-theme="dark"] .business-badge { background: rgba(227, 164, 72, 0.14); color: #E9C288; }

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
        white-space: nowrap;
    }
    .wizard-badge.gray { background: var(--hover-2); color: var(--ink); border-color: var(--line); }
    .wizard-badge.step-1 { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
    .wizard-badge.step-2 { background: #fff7ed; color: #9a3412; border-color: #fed7aa; }
    .wizard-badge.step-3 { background: #f5f3ff; color: #5b21b6; border-color: #ddd6fe; }
    .wizard-badge.step-4 { background: #f0fdfa; color: #0f766e; border-color: #99f6e4; }
    .wizard-badge.amber { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .wizard-badge.violet { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .wizard-badge.blue { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .wizard-badge.green { background: var(--hover-2); color: var(--ink); border-color: #D6DCE3; }
    html[data-theme="dark"] .wizard-badge.step-1,
    html[data-theme="dark"] .wizard-badge.blue { background: rgba(96, 165, 250, 0.14); color: #9CC4F8; border-color: rgba(96, 165, 250, 0.35); }
    html[data-theme="dark"] .wizard-badge.step-2 { background: rgba(234, 124, 58, 0.15); color: #F0B08A; border-color: rgba(234, 124, 58, 0.35); }
    html[data-theme="dark"] .wizard-badge.step-3,
    html[data-theme="dark"] .wizard-badge.violet { background: rgba(167, 139, 250, 0.15); color: #C6B4FA; border-color: rgba(167, 139, 250, 0.35); }
    html[data-theme="dark"] .wizard-badge.step-4 { background: rgba(45, 193, 166, 0.15); color: #85DCC9; border-color: rgba(45, 193, 166, 0.35); }
    html[data-theme="dark"] .wizard-badge.amber { background: rgba(227, 164, 72, 0.14); color: #E9C288; border-color: rgba(227, 164, 72, 0.35); }
    html[data-theme="dark"] .wizard-badge.green { border-color: var(--line-strong); }
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

    .table .btn { white-space: nowrap; padding: 8px 18px; font-size: 13.5px; font-weight: 700; }
    .btn svg { width: 14px; height: 14px; flex-shrink: 0; }
</style>
@endsection

@section('content')
    <div class="card" style="margin-bottom:18px;">
        <div class="card-header">
            <form method="GET" action="{{ route('staff.partnerships.index') }}" class="toolbar" style="width:100%;">
                <div class="search-box">
                    <input type="text" id="partnership-search" placeholder="Search by name, email, or business..." autocomplete="off">
                </div>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="registered" {{ request('status') === 'registered' ? 'selected' : '' }}>Registered</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </form>
        </div>
    </div>

    <div class="card" style="margin-bottom:32px;">
        <div class="card-body" style="width:100%;">
            @php
                // Carried on the review link so "Back to Partnerships" restores this view.
                $listQuery = array_filter(request()->only(['status', 'page']), fn ($v) => $v !== null && $v !== '');
            @endphp
            <table class="table" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width:24%;">Applicant</th>
                        <th style="width:16%;">Business</th>
                        <th style="width:14%;">Contact</th>
                        <th style="width:22%;">Status</th>
                        <th style="width:14%;">Submitted</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        @php
                            $statusClass = match ($application->status) {
                                'pending' => 'badge-pending',
                                'under_review' => 'badge-pending',
                                'approved' => 'badge-approved',
                                'rejected' => 'badge-rejected',
                                'registered' => 'badge-registered',
                                'expired' => 'badge-expired',
                                default => 'badge-expired',
                            };
                            $statusLabel = match ($application->status) {
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
                                $statusClass = 'badge-rejected';
                                $statusLabel = 'Rejected';
                            }

                            $applicantPhone = $application->phone_number ?: $application->phone;

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
                        <tr class="partnership-row" data-search="{{ strtolower(trim(($application->full_name ?? '') . ' ' . ($application->email ?? '') . ' ' . ($application->business_name ?? ''))) }}">
                            <td>
                                <strong>{{ $application->full_name }}</strong>
                                <div style="font-size:13px;color:var(--muted);">{{ $application->email }}</div>
                            </td>
                            <td><span class="business-badge">{{ $application->business_name }}</span></td>
                            <td>
                                @if ($applicantPhone)
                                    {{ $applicantPhone }}
                                @else
                                    <span class="cell-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                @if ($wizardStatus !== 'final_approved')
                                    <div>
                                        <span class="wizard-badge {{ $wizardBadgeColor }}">
                                            @if ($wizardNeedsAction)<span class="wizard-dot"></span>@endif
                                            {{ $wizardBadgeLabel }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $application->created_at->format('M d, Y') }}</div>
                                <div style="font-size:13px;color:var(--muted);">{{ $application->created_at->format('g:i A') }}</div>
                            </td>
                            <td>
                                <a href="{{ route('staff.partnerships.show', array_merge(['id' => $application->id], $listQuery)) }}" class="btn btn-outline btn-sm">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:var(--muted);padding:24px;">No partnership applications found.</td>
                        </tr>
                    @endforelse
                    <tr id="partnership-no-match" style="display:none;">
                        <td colspan="9" style="text-align:center;color:var(--muted);padding:24px;">No matching partnership applications found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if ($applications->hasPages())
            <div class="pagination-wrap">
                {{ $applications->links('faculty.partials.pagination') }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('partnership-search');
        const rows = Array.from(document.querySelectorAll('.partnership-row'));
        const emptyMatchRow = document.getElementById('partnership-no-match');

        if (searchInput && emptyMatchRow) {
            const filterRows = function () {
                const query = searchInput.value.trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach(function (row) {
                    const haystack = row.getAttribute('data-search') || '';
                    const isVisible = haystack.indexOf(query) !== -1;

                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) {
                        visibleCount += 1;
                    }
                });

                emptyMatchRow.style.display = visibleCount === 0 ? '' : 'none';
            };

            searchInput.addEventListener('input', filterRows);
        }
    });
</script>
@endsection
