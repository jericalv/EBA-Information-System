@extends('faculty.layout')

@section('title', 'Partnership Applications')
@section('page-title', 'Partnerships')

@section('extra-css')
<style>
    .table { width: 100%; border-collapse: collapse; min-width: 820px; }
    .table th, .table td { padding: 14px 18px; border-bottom: 1px solid #eef2f7; text-align: left; vertical-align: top; }
    .table th { background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; }

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
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: rgba(239,68,68,0.1); color: #dc2626; }
    .badge-registered { background: rgba(59,130,246,0.1); color: #2563eb; }
    .badge-expired { background: #e5e7eb; color: #4b5563; }

    /* Buttons not provided by the faculty layout */
    .btn-red { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .btn-red:hover { background: #fee2e2; }
    .btn-sm { padding: 5px 10px; font-size: 12px; }
    .btn svg { width: 15px; height: 15px; flex-shrink: 0; }
    .btn-sm svg { width: 14px; height: 14px; }

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
    .wizard-badge.gray { background: #f3f4f6; color: #374151; border-color: #e5e7eb; }
    .wizard-badge.step-1 { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
    .wizard-badge.step-2 { background: #fff7ed; color: #9a3412; border-color: #fed7aa; }
    .wizard-badge.step-3 { background: #f5f3ff; color: #5b21b6; border-color: #ddd6fe; }
    .wizard-badge.step-4 { background: #f0fdfa; color: #0f766e; border-color: #99f6e4; }
    .wizard-badge.amber { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .wizard-badge.violet { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .wizard-badge.blue { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .wizard-badge.green { background: #ecfdf3; color: #166534; border-color: #bbf7d0; }
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

    /* Modal */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .modal-backdrop.active { display: flex; }
    .modal {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        padding: 24px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.2);
    }
    .modal h3 { font-size: 18px; margin-bottom: 16px; }
    #viewModal .modal {
        max-width: 640px;
        width: min(92vw, 640px);
        padding: 0;
        max-height: 90vh;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    #viewModal .view-modal-topbar {
        margin-bottom: 0;
        padding: 18px 22px;
        background: linear-gradient(135deg, #0A5C2F 0%, #166534 100%);
        border-radius: 16px 16px 0 0;
        border-bottom: none;
    }
    #viewModal #viewContent {
        overflow-y: auto;
        padding: 18px 20px 20px;
    }
    .view-modal-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .view-modal-title {
        margin: 0;
        font-size: 11px;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.65);
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    .view-modal-close {
        border: 1.5px solid rgba(255, 255, 255, 0.25);
        background: rgba(255, 255, 255, 0.12);
        color: rgba(255, 255, 255, 0.85);
        width: 34px;
        height: 34px;
        border-radius: 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        line-height: 1;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }
    .view-modal-close:hover {
        background: rgba(255, 255, 255, 0.22);
        color: #fff;
        border-color: rgba(255, 255, 255, 0.45);
        transform: rotate(90deg) scale(1.05);
    }

    .view-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-left: 4px solid #0A5C2F;
        border-radius: 10px;
        background: #faf9f7;
    }
    .view-modal-header-main { display: flex; align-items: flex-start; gap: 14px; min-width: 0; }
    .view-modal-avatar {
        width: 62px;
        height: 62px;
        border-radius: 18px;
        background: linear-gradient(135deg, #047857 0%, #059669 100%);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        font-weight: 800;
        flex-shrink: 0;
        border: 3px solid rgba(255, 255, 255, 0.95);
        letter-spacing: 0.5px;
    }
    .view-modal-name { margin: 0; font-size: 19px; font-weight: 800; color: #0f172a; line-height: 1.2; letter-spacing: -0.01em; }
    .view-modal-subtext { margin-top: 5px; font-size: 13px; color: #64748b; line-height: 1.55; font-weight: 500; }
    .view-modal-status-chip {
        padding: 7px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        line-height: 1;
        white-space: nowrap;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
    .view-modal-section {
        margin-top: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #fff;
        padding: 18px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05), 0 2px 8px rgba(15, 23, 42, 0.05);
    }
    .view-modal-section-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 12px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #334155;
    }
    .view-modal-section-title::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #16a34a;
        box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.15);
    }
    .view-modal-notice {
        font-size: 13px;
        color: #4b5563;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
    }
    .view-modal-quote {
        margin-top: 8px;
        padding: 10px 12px;
        border-left: 3px solid #94a3b8;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        color: #374151;
        white-space: pre-wrap;
    }
    .proposal-text {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        font-size: 14px;
        color: #374151;
        max-height: 150px;
        overflow-y: auto;
        margin-top: 8px;
    }
    .file-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #0a5c2f;
        font-weight: 600;
        text-decoration: none;
    }
    .file-link:hover { text-decoration: underline; }
    .file-link.file-link-pill {
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid #86efac;
        background: #f0fdf4;
        color: #166534;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }
    .file-link.file-link-pill:hover { background: #dcfce7; border-color: #4ade80; text-decoration: none; }

    /* Wizard progress */
    .wizard-mini-progress {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        max-width: 420px;
        margin: 0 auto 12px;
    }
    .wizard-mini-progress > div { justify-content: center; }
    .wizard-mini-step { display: flex; flex-direction: column; align-items: center; gap: 6px; }
    .wizard-mini-dot {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        border: 3px solid #d1d5db;
        color: #6b7280;
        background: #fff;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    }
    .wizard-mini-dot.completed {
        background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
        border-color: #15803d;
        color: #fff;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        transform: scale(1.05);
    }
    .wizard-mini-dot.step-1.active { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); border-color: #1d4ed8; color: #fff; transform: scale(1.08); }
    .wizard-mini-dot.step-2.active { background: linear-gradient(135deg, #ea580c 0%, #f97316 100%); border-color: #ea580c; color: #fff; transform: scale(1.08); }
    .wizard-mini-dot.step-3.active { background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); border-color: #7c3aed; color: #fff; transform: scale(1.08); }
    .wizard-mini-dot.step-4.active { background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%); border-color: #0d9488; color: #fff; transform: scale(1.08); }
    .wizard-mini-label { font-size: 11px; color: #64748b; font-weight: 700; text-align: center; letter-spacing: 0.3px; }
    .wizard-mini-line {
        height: 3px;
        width: 72px;
        margin-top: 14px;
        background: #e5e7eb;
        border-radius: 999px;
        transition: all 0.3s ease;
    }
    .wizard-mini-line.step-1.done,
    .wizard-mini-line.step-2.done,
    .wizard-mini-line.step-3.done {
        background: linear-gradient(90deg, #16a34a 0%, #22c55e 100%);
        box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);
    }

    .wizard-status-callout {
        border-radius: 12px;
        padding: 12px 14px;
        border: 1px solid transparent;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    .wizard-status-callout.gray { background: #f3f4f6; color: #374151; border-color: #e5e7eb; }
    .wizard-status-callout.amber { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .wizard-status-callout.red { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
    .wizard-status-callout.violet { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .wizard-status-callout.blue { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .wizard-status-callout.green { background: #ecfdf3; color: #166534; border-color: #bbf7d0; }

    .wizard-action-panel {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
        padding: 16px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05), 0 1px 4px rgba(15, 23, 42, 0.03);
    }
    .wizard-action-title { margin: 0 0 6px; font-size: 15px; font-weight: 800; color: #0f172a; letter-spacing: -0.01em; }
    .wizard-action-subtitle { margin: 0 0 10px; font-size: 12px; color: #64748b; }
    .wizard-inline-error {
        margin-top: 10px;
        font-size: 12px;
        color: #b91c1c;
        background: #fee2e2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 8px;
        display: none;
    }
    .wizard-inline-success {
        margin-top: 10px;
        font-size: 12px;
        color: #166534;
        background: #dcfce7;
        border: 1px solid #86efac;
        border-radius: 8px;
        padding: 8px;
        display: none;
    }
    .wizard-doc-check-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; font-size: 13px; color: #1f2937; }
    .wizard-doc-readonly {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 8px;
        background: #ecfdf3;
        color: #166534;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .wizard-docs-done-banner {
        margin-top: 10px;
        display: none;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #86efac;
        background: #dcfce7;
        color: #166534;
        font-size: 12px;
        font-weight: 700;
    }
    .reject-modal-textarea {
        width: 100%;
        min-height: 96px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #fca5a5;
        resize: vertical;
        font-size: 13px;
        font-family: inherit;
        color: #111827;
        background: #fff;
        box-sizing: border-box;
    }
    .reject-modal-textarea:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
    }

    @media (max-width: 640px) {
        #viewModal .modal { width: calc(100vw - 20px); padding: 0; }
        #viewModal .view-modal-topbar { padding: 14px 16px; }
        #viewModal #viewContent { padding: 14px; }
        .view-modal-header { flex-direction: column; }
    }
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

    <div class="card">
        <div class="card-body" style="width:100%;">
            <table class="table" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width:30%;">Applicant</th>
                        <th style="width:22%;">Business</th>
                        <th style="width:18%;">Status</th>
                        <th style="width:18%;">Submitted</th>
                        <th style="width:12%;">Action</th>
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
                                <div style="font-size:13px;color:#64748b;">{{ $application->email }}</div>
                            </td>
                            <td><span class="business-badge">{{ $application->business_name }}</span></td>
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
                                <div style="font-size:13px;color:#64748b;">{{ $application->created_at->format('g:i A') }}</div>
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-green btn-sm"
                                    onclick="openViewModalFromButton(this)"
                                    data-app-id="{{ $application->id }}"
                                    data-app-status="{{ $application->status }}"
                                    data-app-first-name="{{ $application->first_name }}"
                                    data-app-middle-name="{{ $application->middle_name }}"
                                    data-app-last-name="{{ $application->last_name }}"
                                    data-app-email="{{ $application->email }}"
                                    data-app-phone="{{ $application->phone_number ?: $application->phone }}"
                                    data-wizard-status="{{ $application->wizard_status }}"
                                    data-wizard-step="{{ $application->wizardStepNumber() }}"
                                    data-loi-path="{{ ($application->letter_of_intent_path || $application->letter_of_intent) ? route('staff.partnerships.document', ['application' => $application, 'type' => 'letter_of_intent']) : '' }}"
                                    data-loi-paths="{{ json_encode(collect($application->documentPaths('letter_of_intent'))->map(fn($p, $i) => route('staff.partnerships.document', ['application' => $application, 'type' => 'letter_of_intent', 'index' => $i]))->all()) }}"
                                    data-receipt-path="{{ $application->receipt_path ? route('staff.partnerships.document', ['application' => $application, 'type' => 'receipt']) : '' }}"
                                    data-receipt-paths="{{ json_encode(collect($application->documentPaths('receipt'))->map(fn($p, $i) => route('staff.partnerships.document', ['application' => $application, 'type' => 'receipt', 'index' => $i]))->all()) }}"
                                    data-docs-recommendation="{{ $application->docs_recommendation_checked ? '1' : '0' }}"
                                    data-docs-notice-occupy="{{ $application->docs_notice_occupy_checked ? '1' : '0' }}"
                                    data-docs-notice-termination="{{ $application->docs_notice_termination_checked ? '1' : '0' }}"
                                    data-docs-moa-contract="{{ $application->docs_moa_contract_checked ? '1' : '0' }}"
                                    data-loi-rejection-reason="{{ $application->loi_rejection_reason }}"
                                    data-form-rejection-reason="{{ $application->form_rejection_reason }}"
                                    data-form-first-name="{{ $application->first_name }}"
                                    data-form-last-name="{{ $application->last_name }}"
                                    data-form-business-name="{{ $application->business_name }}"
                                    data-form-phone="{{ $application->phone_number }}"
                                    data-form-address="{{ $application->address }}"
                                    data-form-type-of-business="{{ $application->type_of_business }}"
                                    data-form-proposed-location="{{ $application->proposed_location }}"
                                    data-form-proposed-duration="{{ $application->proposed_duration }}"
                                    data-form-is-previous="{{ $application->is_previous_concessionaire ? 'Yes' : 'No' }}"
                                    data-form-previous-location-year="{{ $application->previous_location_year }}"
                                    data-form-proposal="{{ $application->business_proposal }}"
                                    data-valid-id-path="{{ $application->valid_id_path ? Storage::url($application->valid_id_path) : '' }}"
                                    data-valid-id-paths="{{ json_encode(collect($application->documentPaths('valid_id'))->map(fn($p) => Storage::url($p))->all()) }}"
                                    data-business-permit-path="{{ $application->business_permit_path ? Storage::url($application->business_permit_path) : '' }}"
                                    data-business-permit-paths="{{ json_encode(collect($application->documentPaths('business_permit'))->map(fn($p) => Storage::url($p))->all()) }}"
                                >
                                    Review
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;color:#64748b;padding:24px;">No partnership applications found.</td>
                        </tr>
                    @endforelse
                    <tr id="partnership-no-match" style="display:none;">
                        <td colspan="5" style="text-align:center;color:#64748b;padding:24px;">No matching partnership applications found.</td>
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

    <!-- View / Review Application Modal (wizard) -->
    <div class="modal-backdrop" id="viewModal">
        <div class="modal modal-view">
            <div class="view-modal-topbar">
                <h3 class="view-modal-title">Application Details</h3>
                <button type="button" class="view-modal-close" onclick="closeViewModal()" aria-label="Close">&times;</button>
            </div>
            <div id="viewContent">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script id="applicationsData" type="application/json">@json($applications->items())</script>
<script>
    const applicationsDataNode = document.getElementById('applicationsData');
    const applications = applicationsDataNode ? JSON.parse(applicationsDataNode.textContent || '[]') : [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    const wizardRoutes = {
        approveLoi: (id) => `{{ route('staff.partnerships.wizard.approve-loi', ['application' => '__ID__']) }}`.replace('__ID__', id),
        rejectLoi: (id) => `{{ route('staff.partnerships.wizard.reject-loi', ['application' => '__ID__']) }}`.replace('__ID__', id),
        approveForm: (id) => `{{ route('staff.partnerships.wizard.approve-form', ['application' => '__ID__']) }}`.replace('__ID__', id),
        rejectForm: (id) => `{{ route('staff.partnerships.wizard.reject-form', ['application' => '__ID__']) }}`.replace('__ID__', id),
        tickDoc: (id) => `{{ route('staff.partnerships.wizard.tick-doc', ['application' => '__ID__']) }}`.replace('__ID__', id),
        finalApprove: (id) => `{{ route('staff.partnerships.wizard.final-approve', ['application' => '__ID__']) }}`.replace('__ID__', id),
    };

    function getWizardStatusConfig(wizardStatus, appStatus) {
        if (wizardStatus === 'final_approved' || appStatus === 'approved') {
            return { color: 'green', message: 'Application fully approved' };
        }

        const map = {
            loi_pending: { color: 'gray', message: 'Waiting for concessionaire to upload LOI' },
            loi_submitted: { color: 'amber', message: 'LOI submitted - Action Required' },
            loi_rejected: { color: 'red', message: 'LOI rejected - Awaiting resubmission' },
            form_pending: { color: 'gray', message: 'Waiting for concessionaire to fill application form' },
            form_submitted: { color: 'amber', message: 'Application form submitted - Action Required' },
            form_rejected: { color: 'red', message: 'Form rejected - Awaiting resubmission' },
            docs_in_progress: { color: 'violet', message: 'Concessionaire submitting physical documents' },
            receipt_pending: { color: 'blue', message: 'All docs received - Awaiting receipt upload' },
            receipt_submitted: { color: 'amber', message: 'Receipt uploaded - Final approval required' },
            final_approved: { color: 'green', message: 'Application fully approved' },
        };

        return map[wizardStatus] || map.loi_pending;
    }

    function buildWizardProgressHtml(step, wizardStatus, appStatus) {
        const effectiveStep = Math.max(1, Math.min(4, Number(step) || 1));
        const isApproved = wizardStatus === 'final_approved' || appStatus === 'approved';
        const labels = ['1. LOI', '2. Form', '3. Docs', '4. Receipt'];

        return `
            <div class="wizard-mini-progress">
                ${labels.map((label, index) => {
                    const stepNo = index + 1;
                    const completed = isApproved || effectiveStep > stepNo;
                    const active = !isApproved && effectiveStep === stepNo;
                    const lineDone = isApproved || effectiveStep > stepNo;

                    return `
                        <div style="display:flex;align-items:flex-start;gap:8px;">
                            <div class="wizard-mini-step" style="flex:0 0 auto;">
                                <span class="wizard-mini-dot step-${stepNo} ${completed ? 'completed' : ''} ${active ? 'active' : ''}">${stepNo}</span>
                                <span class="wizard-mini-label">${label}</span>
                            </div>
                            ${stepNo < 4 ? `<span class="wizard-mini-line step-${stepNo} ${lineDone ? 'done' : ''}"></span>` : ''}
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }

    async function wizardFetchJson(url, method, payload) {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload || {}),
        });

        const data = await response.json().catch(() => ({ success: false, message: 'Unexpected server response.' }));
        return { response, data };
    }

    function showWizardPanelMessage(type, message) {
        const successEl = document.getElementById('wizardPanelSuccess');
        const errorEl = document.getElementById('wizardPanelError');
        if (!successEl || !errorEl) return;

        successEl.style.display = 'none';
        errorEl.style.display = 'none';

        if (type === 'success') {
            successEl.textContent = message;
            successEl.style.display = 'block';
        }

        if (type === 'error') {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }

    function openViewModalFromButton(buttonEl) {
        const id = Number(buttonEl?.dataset?.appId || 0);
        if (!id) return;
        openViewModal(id, buttonEl);
    }

    function openViewModal(id, triggerEl = null) {
        const rowBtn = triggerEl || document.querySelector(`button[data-app-id="${id}"]`);
        const wizardDataset = rowBtn?.dataset || {};
        const app = applications.find(a => a.id === id) || null;
        if (!rowBtn && !app) return;

        const wizardStatus = wizardDataset.wizardStatus || app?.wizard_status || 'loi_pending';
        const wizardStep = Number(wizardDataset.wizardStep || app?.wizard_step || 1);
        const wizardLoiPath = wizardDataset.loiPath || '';
        const wizardReceiptPath = wizardDataset.receiptPath || '';
        const parseUrlList = (raw, fallback) => {
            try {
                const arr = JSON.parse(raw || '[]');
                if (Array.isArray(arr) && arr.length) return arr;
            } catch (e) { /* ignore */ }
            return fallback ? [fallback] : [];
        };
        const buildFileLinks = (urls, label) => {
            if (!urls.length) return '';
            return urls.map((url, i) =>
                `<a class="file-link file-link-pill" href="${url}" target="_blank" rel="noopener">${label}${urls.length > 1 ? ' ' + (i + 1) : ''}</a>`
            ).join(' ');
        };
        const wizardLoiPaths = parseUrlList(wizardDataset.loiPaths, wizardLoiPath);
        const wizardReceiptPaths = parseUrlList(wizardDataset.receiptPaths, wizardReceiptPath);
        const docsRecommendation = wizardDataset.docsRecommendation === '1';
        const docsNoticeOccupy = wizardDataset.docsNoticeOccupy === '1';
        const docsNoticeTermination = wizardDataset.docsNoticeTermination === '1';
        const docsMoaContract = wizardDataset.docsMoaContract === '1';
        const loiRejectionReason = wizardDataset.loiRejectionReason || '';
        const formRejectionReason = wizardDataset.formRejectionReason || '';
        const formFirstName = wizardDataset.formFirstName || app?.first_name || '';
        const formLastName = wizardDataset.formLastName || app?.last_name || '';
        const formBusinessName = wizardDataset.formBusinessName || app?.business_name || '';
        const formPhone = wizardDataset.formPhone || app?.phone_number || app?.phone || '';
        const formAddress = wizardDataset.formAddress || '—';
        const formTypeBusiness = wizardDataset.formTypeOfBusiness || '—';
        const formProposedLocation = wizardDataset.formProposedLocation || '—';
        const formProposedDuration = wizardDataset.formProposedDuration || '—';
        const formIsPrevious = wizardDataset.formIsPrevious || '—';
        const formPreviousLocationYear = wizardDataset.formPreviousLocationYear || '';
        const formProposal = wizardDataset.formProposal || app?.business_proposal || app?.proposal || '';

        const appStatus = wizardDataset.appStatus || app?.status || 'pending';
        const appFirstName = wizardDataset.appFirstName || app?.first_name || '';
        const appMiddleName = wizardDataset.appMiddleName || app?.middle_name || '';
        const appLastName = wizardDataset.appLastName || app?.last_name || '';
        const appEmail = wizardDataset.appEmail || app?.email || '—';
        const appPhone = wizardDataset.appPhone || app?.phone || '—';
        const applicationId = Number(wizardDataset.appId || id || app?.id || 0);

        const normalizedBusinessType = (formTypeBusiness || '')
            .split(',')
            .map((value) => value.trim())
            .filter(Boolean)
            .map((value) => {
                if (value === 'food') return 'Food';
                if (value === 'non_food') return 'Non-Food';
                return value;
            })
            .join(', ') || '—';

        const hasPreviousLocationYear = String(formIsPrevious).toLowerCase() === 'yes' && String(formPreviousLocationYear).trim() !== '';
        const validIdUrl = wizardDataset.validIdPath || '';
        const businessPermitUrl = wizardDataset.businessPermitPath || '';
        const validIdUrls = parseUrlList(wizardDataset.validIdPaths, validIdUrl);
        const businessPermitUrls = parseUrlList(wizardDataset.businessPermitPaths, businessPermitUrl);

        const statusBadgeClassMap = {
            pending: 'badge-pending',
            under_review: 'badge-pending',
            approved: 'badge-approved',
            rejected: 'badge-rejected',
            registered: 'badge-registered',
            expired: 'badge-expired'
        };
        const statusLabelMap = {
            pending: 'Under Review',
            under_review: 'Under Review',
            approved: 'Approved',
            registered: 'Registered',
            rejected: 'Rejected',
            expired: 'Expired'
        };
        const isWizardRejected = wizardStatus === 'loi_rejected' || wizardStatus === 'form_rejected';
        const statusBadgeClass = isWizardRejected ? 'badge-rejected' : (statusBadgeClassMap[appStatus] || 'badge-expired');
        const statusLabel = isWizardRejected ? 'Rejected' : (statusLabelMap[appStatus] || appStatus || 'Pending');

        const fullName = `${appFirstName} ${appMiddleName} ${appLastName}`.replace(/\s+/g, ' ').trim();
        const initials = `${String(appFirstName).trim().charAt(0)}${String(appLastName).trim().charAt(0)}`.toUpperCase() || 'NA';
        const wizardStatusConfig = getWizardStatusConfig(wizardStatus, appStatus);

        const wizardStatusCallout = `
            <div class="wizard-status-callout ${wizardStatusConfig.color}">
                ${wizardStatusConfig.message}
            </div>
        `;

        let wizardActionInnerHtml = '';

        if (wizardStatus === 'loi_submitted') {
            wizardActionInnerHtml = `
                <h4 class="wizard-action-title">Step 1 Review: Letter of Intent</h4>
                ${wizardLoiPaths.length ? buildFileLinks(wizardLoiPaths, 'View LOI') : '<div class="view-modal-notice">No LOI file found.</div>'}
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
                    <button type="button" class="btn btn-green btn-sm" id="wizardApproveLoiBtn">Approve LOI</button>
                    <button type="button" class="btn btn-red btn-sm" id="wizardShowRejectLoiBtn">Reject LOI</button>
                </div>
                <div id="wizardRejectLoiWrap" style="display:none;margin-top:10px;">
                    <textarea id="loiRejectReason" class="reject-modal-textarea" placeholder="Enter reason for rejection..."></textarea>
                    <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                        <button type="button" class="btn btn-red btn-sm" id="wizardConfirmRejectLoiBtn">Confirm Rejection</button>
                    </div>
                </div>
            `;
        } else if (wizardStatus === 'form_submitted') {
            const attachmentPill = (url, label) =>
                `<a href="${url}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:#ecfdf5;color:#065f46;border:1px solid #6ee7b7;border-radius:999px;font-size:12px;font-weight:700;text-decoration:none;">📎 ${label}</a>`;
            const noAttachmentPill = (label) =>
                `<span style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:#f8fafc;color:#94a3b8;border:1px solid #e2e8f0;border-radius:999px;font-size:12px;font-weight:600;">${label}</span>`;

            const validIdHtml = validIdUrls.length
                ? validIdUrls.map((url, i) => attachmentPill(url, `View Valid ID${validIdUrls.length > 1 ? ' ' + (i + 1) : ''}`)).join(' ')
                : noAttachmentPill('No Valid ID');

            const businessPermitHtml = businessPermitUrls.length
                ? businessPermitUrls.map((url, i) => attachmentPill(url, `View Business Permit${businessPermitUrls.length > 1 ? ' ' + (i + 1) : ''}`)).join(' ')
                : noAttachmentPill('No Business Permit');

            wizardActionInnerHtml = `
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-top:12px;">
                    <div style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;margin-bottom:16px;">Application Form Summary</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px 20px;">
                        <div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:4px;">First Name</div>
                            <div style="font-size:13px;color:#0f172a;font-weight:600;line-height:1.4;">${formFirstName || '—'}</div>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:4px;">Last Name</div>
                            <div style="font-size:13px;color:#0f172a;font-weight:600;line-height:1.4;">${formLastName || '—'}</div>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:4px;">Business Name</div>
                            <div style="font-size:13px;color:#0f172a;font-weight:600;line-height:1.4;">${formBusinessName || '—'}</div>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:4px;">Phone</div>
                            <div style="font-size:13px;color:#0f172a;font-weight:600;line-height:1.4;">${formPhone || '—'}</div>
                        </div>
                        <div style="grid-column:span 2;">
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:4px;">Address</div>
                            <div style="font-size:13px;color:#0f172a;font-weight:600;line-height:1.4;">${formAddress || '—'}</div>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:4px;">Type of Business</div>
                            <div style="font-size:13px;color:#0f172a;font-weight:600;line-height:1.4;">${normalizedBusinessType || '—'}</div>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:4px;">Proposed Location</div>
                            <div style="font-size:13px;color:#0f172a;font-weight:600;line-height:1.4;">${formProposedLocation || '—'}</div>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:4px;">Proposed Duration</div>
                            <div style="font-size:13px;color:#0f172a;font-weight:600;line-height:1.4;">${formProposedDuration || '—'}</div>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:4px;">Previous CvSU Concessionaire?</div>
                            <div style="font-size:13px;color:#0f172a;font-weight:600;line-height:1.4;">${formIsPrevious || '—'}</div>
                        </div>
                        ${hasPreviousLocationYear ? `
                        <div style="grid-column:span 2;">
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:4px;">Location &amp; Year</div>
                            <div style="font-size:13px;color:#0f172a;font-weight:600;line-height:1.4;">${formPreviousLocationYear}</div>
                        </div>` : ''}
                        <div style="grid-column:span 2;">
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:6px;">Business Proposal</div>
                            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:13px;color:#374151;white-space:pre-wrap;max-height:120px;overflow-y:auto;line-height:1.6;">${formProposal || 'No business proposal submitted.'}</div>
                        </div>
                    </div>
                    <div style="margin-top:14px;padding-top:14px;border-top:1px solid #e2e8f0;">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:8px;">Attachments</div>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
                            ${validIdHtml}
                            ${businessPermitHtml}
                        </div>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;">
                    <button type="button" class="btn btn-green btn-sm" id="wizardApproveFormBtn">Approve Form</button>
                    <button type="button" class="btn btn-red btn-sm" id="wizardShowRejectFormBtn">Reject Form</button>
                </div>
                <div id="wizardRejectFormWrap" style="display:none;margin-top:10px;">
                    <textarea id="formRejectReason" class="reject-modal-textarea" placeholder="Enter reason for rejection..."></textarea>
                    <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                        <button type="button" class="btn btn-red btn-sm" id="wizardConfirmRejectFormBtn">Confirm Rejection</button>
                    </div>
                </div>
            `;
        } else if (wizardStatus === 'docs_in_progress' || wizardStatus === 'receipt_pending') {
            wizardActionInnerHtml = `
                <h4 class="wizard-action-title">Step 3: Physical Documents Checklist</h4>
                <p class="wizard-action-subtitle">Check each document as the concessionaire submits it to the EBA Office.</p>
                <label class="wizard-doc-check-row"><input type="checkbox" id="doc-recommendation" data-doc-key="recommendation" ${docsRecommendation ? 'checked' : ''}> Recommendation for Approval Rental</label>
                <label class="wizard-doc-check-row"><input type="checkbox" id="doc-notice-occupy" data-doc-key="notice_occupy" ${docsNoticeOccupy ? 'checked' : ''}> Notice to Occupy</label>
                <label class="wizard-doc-check-row"><input type="checkbox" id="doc-notice-termination" data-doc-key="notice_termination" ${docsNoticeTermination ? 'checked' : ''}> Notice of Termination of Rental</label>
                <label class="wizard-doc-check-row"><input type="checkbox" id="doc-moa-contract" data-doc-key="moa_contract" ${docsMoaContract ? 'checked' : ''}> MOA/Contract</label>
                <div id="wizardDocsDoneBanner" class="wizard-docs-done-banner">All documents received! Awaiting receipt upload.</div>
            `;
        } else if (wizardStatus === 'receipt_submitted') {
            wizardActionInnerHtml = `
                <h4 class="wizard-action-title">Step 4: Final Approval</h4>
                ${wizardReceiptPaths.length ? buildFileLinks(wizardReceiptPaths, 'View Receipt') : '<div class="view-modal-notice">No receipt file found.</div>'}
                <div style="margin-top:10px;">
                    <div class="wizard-doc-readonly">✓ Recommendation for Approval Rental</div>
                    <div class="wizard-doc-readonly">✓ Notice to Occupy</div>
                    <div class="wizard-doc-readonly">✓ Notice of Termination of Rental</div>
                    <div class="wizard-doc-readonly">✓ MOA/Contract</div>
                </div>
                <button type="button" id="wizardFinalApproveBtn" class="btn btn-green" style="width:100%;margin-top:8px;font-weight:800;">Grant Final Approval</button>
            `;
        } else if (wizardStatus === 'final_approved' || appStatus === 'approved') {
            wizardActionInnerHtml = ``;
        } else {
            wizardActionInnerHtml = `
                <div class="view-modal-notice">No action required at this time. The concessionaire is completing this step.</div>
                ${wizardStatus === 'loi_rejected' && loiRejectionReason ? `<div class="view-modal-quote" style="margin-top:8px;">LOI rejection reason: ${loiRejectionReason}</div>` : ''}
                ${wizardStatus === 'form_rejected' && formRejectionReason ? `<div class="view-modal-quote" style="margin-top:8px;">Form rejection reason: ${formRejectionReason}</div>` : ''}
            `;
        }

        const wizardSectionHtml = `
            <div class="view-modal-section">
                ${buildWizardProgressHtml(wizardStep, wizardStatus, appStatus)}
                ${wizardStatusCallout}
                ${wizardActionInnerHtml ? `
                <div class="wizard-action-panel" id="wizardActionPanel">
                    ${wizardActionInnerHtml}
                    <div id="wizardPanelError" class="wizard-inline-error"></div>
                    <div id="wizardPanelSuccess" class="wizard-inline-success"></div>
                </div>
                ` : ''}
            </div>
        `;

        document.getElementById('viewContent').innerHTML = `
            <div class="view-modal-header">
                <div class="view-modal-header-main">
                    <div class="view-modal-avatar">${initials}</div>
                    <div style="min-width:0;">
                        <h4 class="view-modal-name">${fullName}</h4>
                        <div class="view-modal-subtext">
                            <div>${appEmail}</div>
                            <div>${appPhone}</div>
                        </div>
                    </div>
                </div>
                <span class="badge view-modal-status-chip ${statusBadgeClass}">${statusLabel}</span>
            </div>

            ${wizardSectionHtml}

            ${app?.rejection_reason ? `
                <div class="view-modal-section">
                    <div class="view-modal-section-title">Rejection Reason</div>
                    <div class="proposal-text" style="white-space:pre-wrap;">${app.rejection_reason}</div>
                </div>
            ` : ''}
        `;

        const bindWizardActionHandlers = () => {
            const approveLoiBtn = document.getElementById('wizardApproveLoiBtn');
            const showRejectLoiBtn = document.getElementById('wizardShowRejectLoiBtn');
            const confirmRejectLoiBtn = document.getElementById('wizardConfirmRejectLoiBtn');
            const rejectLoiWrap = document.getElementById('wizardRejectLoiWrap');

            const approveFormBtn = document.getElementById('wizardApproveFormBtn');
            const showRejectFormBtn = document.getElementById('wizardShowRejectFormBtn');
            const confirmRejectFormBtn = document.getElementById('wizardConfirmRejectFormBtn');
            const rejectFormWrap = document.getElementById('wizardRejectFormWrap');

            const finalApproveBtn = document.getElementById('wizardFinalApproveBtn');

            const performAction = async (url, method, payload, successMessage, reloadDelay = 1500) => {
                showWizardPanelMessage(null, '');
                try {
                    const { response, data } = await wizardFetchJson(url, method, payload);
                    if (!response.ok || !data.success) {
                        showWizardPanelMessage('error', data.message || 'Action failed.');
                        return;
                    }
                    showWizardPanelMessage('success', successMessage);
                    setTimeout(() => window.location.reload(), reloadDelay);
                } catch (error) {
                    showWizardPanelMessage('error', 'Action failed. Please try again.');
                }
            };

            if (approveLoiBtn) {
                approveLoiBtn.addEventListener('click', () => {
                    performAction(wizardRoutes.approveLoi(applicationId), 'POST', {}, 'Letter of Intent approved.');
                });
            }

            if (showRejectLoiBtn && rejectLoiWrap) {
                showRejectLoiBtn.addEventListener('click', () => {
                    rejectLoiWrap.style.display = rejectLoiWrap.style.display === 'none' ? 'block' : 'none';
                });
            }

            if (confirmRejectLoiBtn) {
                confirmRejectLoiBtn.addEventListener('click', () => {
                    const reason = document.getElementById('loiRejectReason')?.value?.trim() || '';
                    if (!reason) {
                        showWizardPanelMessage('error', 'Please provide a rejection reason.');
                        return;
                    }
                    performAction(wizardRoutes.rejectLoi(applicationId), 'POST', { reason }, 'Letter of Intent rejected.');
                });
            }

            if (approveFormBtn) {
                approveFormBtn.addEventListener('click', () => {
                    performAction(wizardRoutes.approveForm(applicationId), 'POST', {}, 'Application form approved.');
                });
            }

            if (showRejectFormBtn && rejectFormWrap) {
                showRejectFormBtn.addEventListener('click', () => {
                    rejectFormWrap.style.display = rejectFormWrap.style.display === 'none' ? 'block' : 'none';
                });
            }

            if (confirmRejectFormBtn) {
                confirmRejectFormBtn.addEventListener('click', () => {
                    const reason = document.getElementById('formRejectReason')?.value?.trim() || '';
                    if (!reason) {
                        showWizardPanelMessage('error', 'Please provide a rejection reason.');
                        return;
                    }
                    performAction(wizardRoutes.rejectForm(applicationId), 'POST', { reason }, 'Application form rejected.');
                });
            }

            if (finalApproveBtn) {
                finalApproveBtn.addEventListener('click', () => {
                    performAction(wizardRoutes.finalApprove(applicationId), 'POST', {}, 'Concessionaire fully approved!', 2000);
                });
            }

            const docCheckboxes = document.querySelectorAll('#wizardActionPanel input[type="checkbox"][data-doc-key]');
            const docsDoneBanner = document.getElementById('wizardDocsDoneBanner');

            const updateDocsDoneBanner = (docsState) => {
                if (!docsDoneBanner) return;
                const allChecked = docsState.recommendation && docsState.notice_occupy && docsState.notice_termination && docsState.moa_contract;
                docsDoneBanner.style.display = allChecked ? 'block' : 'none';
            };

            if (docCheckboxes.length > 0) {
                updateDocsDoneBanner({
                    recommendation: docsRecommendation,
                    notice_occupy: docsNoticeOccupy,
                    notice_termination: docsNoticeTermination,
                    moa_contract: docsMoaContract,
                });
            }

            docCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', async function () {
                    const docKey = this.dataset.docKey;
                    const checked = this.checked;
                    const previousChecked = !checked;
                    this.disabled = true;

                    try {
                        const { response, data } = await wizardFetchJson(wizardRoutes.tickDoc(applicationId), 'PATCH', {
                            doc: docKey,
                            checked,
                            _method: 'PATCH',
                        });

                        if (!response.ok || !data.success) {
                            this.checked = previousChecked;
                            showWizardPanelMessage('error', data.message || 'Failed to update document status.');
                            return;
                        }

                        const docs = data.docs || {};
                        const map = {
                            recommendation: document.getElementById('doc-recommendation'),
                            notice_occupy: document.getElementById('doc-notice-occupy'),
                            notice_termination: document.getElementById('doc-notice-termination'),
                            moa_contract: document.getElementById('doc-moa-contract'),
                        };

                        Object.keys(map).forEach((key) => {
                            if (map[key]) {
                                map[key].checked = !!docs[key];
                            }
                        });

                        updateDocsDoneBanner({
                            recommendation: !!docs.recommendation,
                            notice_occupy: !!docs.notice_occupy,
                            notice_termination: !!docs.notice_termination,
                            moa_contract: !!docs.moa_contract,
                        });
                    } catch (error) {
                        this.checked = previousChecked;
                        showWizardPanelMessage('error', 'Failed to update document status.');
                    } finally {
                        this.disabled = false;
                    }
                });
            });
        };

        bindWizardActionHandlers();
        document.getElementById('viewModal').classList.add('active');
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.remove('active');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const viewModal = document.getElementById('viewModal');
        if (viewModal) {
            viewModal.addEventListener('click', function (e) {
                if (e.target === this) closeViewModal();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeViewModal();
        });

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
