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
    .badge-approved { background: #E9F6EE; color: #15803D; }
    .badge-rejected { background: rgba(239,68,68,0.1); color: #dc2626; }
    .badge-registered { background: rgba(59,130,246,0.1); color: #2563eb; }
    .badge-expired { background: #e5e7eb; color: #4b5563; }

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
    .wizard-badge.green { background: #EEF0F3; color: #1F2937; border-color: #D6DCE3; }
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
    :root {
        --wiz-green: #16A34A;
        --wiz-green-dark: #15803D;
        --wiz-green-soft: #E9F6EE;
    }
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.45);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .modal-backdrop.active { display: flex; }
    .modal {
        background: #fff;
        border-radius: 14px;
        border: 1px solid var(--line);
        padding: 24px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: var(--shadow-pop);
    }
    .modal h3 { font-size: 18px; margin-bottom: 16px; }
    #viewModal .modal {
        max-width: 660px;
        width: min(94vw, 660px);
        padding: 0;
        max-height: 90vh;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    #viewModal .view-modal-topbar {
        margin-bottom: 0;
        padding: 14px 20px;
        background: #fff;
        border-bottom: 1px solid var(--line);
        border-radius: 14px 14px 0 0;
    }
    #viewModal #viewContent {
        overflow-y: auto;
        padding: 20px;
        background: #FAFBFC;
    }
    .view-modal-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .view-modal-title {
        margin: 0;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        color: var(--muted);
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    .view-modal-topbar-right { display: inline-flex; align-items: center; gap: 10px; }
    .view-modal-close {
        border: 1px solid var(--line-strong);
        background: #fff;
        color: var(--muted);
        width: 30px;
        height: 30px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        line-height: 1;
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        flex-shrink: 0;
    }
    .view-modal-close:hover {
        background: var(--pine-soft);
        color: var(--ink);
        border-color: var(--line-strong);
    }

    /* Application form summary */
    .rv-section {
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #fff;
        margin-top: 14px;
        overflow: hidden;
        box-shadow: var(--shadow-card);
    }
    .rv-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 16px;
        border-bottom: 1px solid var(--line);
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .rv-grid { display: grid; grid-template-columns: 1fr 1fr; }
    .rv-field { padding: 11px 16px 12px; border-top: 1px solid var(--line); min-width: 0; }
    .rv-grid .rv-field:nth-child(-n+2) { border-top: none; }
    .rv-field-span { grid-column: 1 / -1; }
    .rv-field-label {
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--faint);
        margin-bottom: 4px;
    }
    .rv-field-value {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--ink);
        line-height: 1.5;
        overflow-wrap: break-word;
    }
    .rv-proposal {
        margin-top: 2px;
        background: #FAFBFC;
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        white-space: pre-wrap;
        max-height: 140px;
        overflow-y: auto;
        line-height: 1.6;
    }
    .rv-attachments {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        padding: 12px 16px;
        border-top: 1px solid var(--line);
        background: #FAFBFC;
    }
    .rv-attachments .rv-field-label { margin: 0 4px 0 0; }
    .rv-attachment-empty {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        background: #fff;
        color: var(--faint);
        border: 1px dashed var(--line-strong);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
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
        color: var(--pine);
        font-weight: 600;
        text-decoration: none;
    }
    .file-link:hover { text-decoration: underline; }
    .file-link.file-link-pill {
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid var(--line-strong);
        background: #F2F4F6;
        color: var(--ink);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }
    .file-link.file-link-pill:hover { background: #E9EDF1; border-color: #AEB6C0; text-decoration: none; }

    /* Completion wizard */
    .rv-wizard {
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #fff;
        padding: 18px 18px 14px;
        box-shadow: var(--shadow-card);
    }
    .rv-steps { display: flex; align-items: flex-start; max-width: 460px; margin: 0 auto; }
    .rv-step { display: flex; flex-direction: column; align-items: center; gap: 7px; flex: 0 0 auto; min-width: 58px; }
    .rv-step-dot {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-mono);
        font-size: 12px;
        font-weight: 600;
        background: #fff;
        border: 1.5px solid var(--line-strong);
        color: var(--faint);
        transition: all 0.2s ease;
    }
    .rv-step-dot.done {
        background: var(--wiz-green);
        border-color: var(--wiz-green);
        color: #fff;
    }
    .rv-step-dot.active {
        border-color: var(--wiz-green);
        color: var(--wiz-green-dark);
        font-weight: 700;
        box-shadow: 0 0 0 4px var(--wiz-green-soft);
    }
    .rv-step-label {
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--faint);
        text-align: center;
    }
    .rv-step-label.done, .rv-step-label.active { color: var(--wiz-green-dark); }
    .rv-step-line {
        flex: 1 1 auto;
        height: 2px;
        border-radius: 999px;
        background: var(--line);
        margin-top: 14px;
        transition: background-color 0.2s ease;
    }
    .rv-step-line.done { background: var(--wiz-green); }

    .rv-callout {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-top: 16px;
        padding: 9px 12px;
        border-radius: 8px;
        border: 1px solid transparent;
        font-size: 12.5px;
        font-weight: 600;
    }
    .rv-callout::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: currentColor;
        flex-shrink: 0;
    }
    .rv-callout.gray { background: #F3F4F6; color: #4B5563; border-color: #E5E7EB; }
    .rv-callout.amber { background: #FFFBEB; color: #B45309; border-color: #FDE68A; }
    .rv-callout.red { background: #FEF2F2; color: #B91C1C; border-color: #FECACA; }
    .rv-callout.violet { background: #F5F3FF; color: #6D28D9; border-color: #DDD6FE; }
    .rv-callout.blue { background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE; }
    .rv-callout.green { background: var(--wiz-green-soft); color: #166534; border-color: #BBE5C8; }

    .wizard-action-panel {
        margin-top: 14px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #fff;
        padding: 16px;
        box-shadow: var(--shadow-card);
    }
    .wizard-action-title { margin: 0 0 10px; font-size: 14px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; }
    .wizard-action-subtitle { margin: -4px 0 10px; font-size: 12px; color: var(--muted); }
    .wizard-inline-error {
        margin-top: 10px;
        font-size: 12px;
        font-weight: 600;
        color: #B91C1C;
        background: #FEF2F2;
        border: 1px solid #FECACA;
        border-radius: 8px;
        padding: 8px 10px;
        display: none;
    }
    .wizard-inline-success {
        margin-top: 10px;
        font-size: 12px;
        font-weight: 600;
        color: #166534;
        background: var(--wiz-green-soft);
        border: 1px solid #BBE5C8;
        border-radius: 8px;
        padding: 8px 10px;
        display: none;
    }
    .wizard-doc-check-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 0;
        font-size: 13px;
        font-weight: 600;
        color: var(--ink);
        cursor: pointer;
    }
    .wizard-doc-check-row + .wizard-doc-check-row { border-top: 1px solid var(--line); }
    .wizard-doc-check-row input[type="checkbox"] {
        width: 15px;
        height: 15px;
        accent-color: var(--wiz-green);
        flex-shrink: 0;
    }
    .wizard-doc-readonly {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 8px;
        background: var(--wiz-green-soft);
        color: #166534;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .wizard-docs-done-banner {
        margin-top: 10px;
        display: none;
        padding: 9px 12px;
        border-radius: 8px;
        border: 1px solid #BBE5C8;
        background: var(--wiz-green-soft);
        color: #166534;
        font-size: 12px;
        font-weight: 700;
    }
    .reject-modal-textarea {
        width: 100%;
        min-height: 96px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #FCA5A5;
        resize: vertical;
        font-size: 13px;
        font-family: inherit;
        color: #111827;
        background: #fff;
        box-sizing: border-box;
    }
    .reject-modal-textarea:focus {
        outline: none;
        border-color: #EF4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
    }

    @media (max-width: 640px) {
        #viewModal .modal { width: calc(100vw - 20px); padding: 0; }
        #viewModal .view-modal-topbar { padding: 12px 14px; }
        #viewModal #viewContent { padding: 14px; }
        .rv-grid { grid-template-columns: 1fr; }
        .rv-grid .rv-field:nth-child(-n+2) { border-top: 1px solid var(--line); }
        .rv-grid .rv-field:first-child { border-top: none; }
        .rv-step { min-width: 48px; }
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

    <div class="card" style="margin-bottom:32px;">
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
                                    class="btn btn-green"
                                    style="padding:10px 20px;font-size:14px;"
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
                                    data-form-submitted="{{ ($application->form_submitted_at || $application->business_proposal || $application->proposed_location || $application->type_of_business) ? '1' : '0' }}"
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
                {{ $applications->links('faculty.partials.pagination') }}
            </div>
        @endif
    </div>

    <!-- View / Review Application Modal (wizard) -->
    <div class="modal-backdrop" id="viewModal">
        <div class="modal modal-view">
            <div class="view-modal-topbar">
                <h3 class="view-modal-title">Application Review</h3>
                <div class="view-modal-topbar-right">
                    <span class="badge" id="viewModalStatusChip" style="display:none;"></span>
                    <button type="button" class="view-modal-close" onclick="closeViewModal()" aria-label="Close">&times;</button>
                </div>
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
        const labels = ['LOI', 'Form', 'Docs', 'Receipt'];

        return `
            <div class="rv-steps">
                ${labels.map((label, index) => {
                    const stepNo = index + 1;
                    const done = isApproved || effectiveStep > stepNo;
                    const active = !isApproved && effectiveStep === stepNo;
                    const stateClass = done ? 'done' : (active ? 'active' : '');

                    return `
                        <div class="rv-step">
                            <span class="rv-step-dot ${stateClass}">${done ? '&#10003;' : stepNo}</span>
                            <span class="rv-step-label ${stateClass}">${label}</span>
                        </div>
                        ${stepNo < 4 ? `<span class="rv-step-line ${done ? 'done' : ''}"></span>` : ''}
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

        const wizardStatusConfig = getWizardStatusConfig(wizardStatus, appStatus);

        const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));

        const wizardStatusCallout = `
            <div class="rv-callout ${wizardStatusConfig.color}">
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
            wizardActionInnerHtml = `
                <h4 class="wizard-action-title">Step 2 Review: Application Form</h4>
                <p class="wizard-action-subtitle">Review the form summary above, then approve or reject.</p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
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
            <div class="rv-wizard">
                ${buildWizardProgressHtml(wizardStep, wizardStatus, appStatus)}
                ${wizardStatusCallout}
            </div>
        `;

        const field = (label, value, span = false) => `
            <div class="rv-field${span ? ' rv-field-span' : ''}">
                <div class="rv-field-label">${label}</div>
                <div class="rv-field-value">${esc(value) || '—'}</div>
            </div>
        `;

        const attachmentPill = (url, label) =>
            `<a href="${url}" target="_blank" rel="noopener" class="file-link file-link-pill">${label}</a>`;
        const noAttachmentPill = (label) =>
            `<span class="rv-attachment-empty">${label}</span>`;

        const validIdHtml = validIdUrls.length
            ? validIdUrls.map((url, i) => attachmentPill(url, `Valid ID${validIdUrls.length > 1 ? ' ' + (i + 1) : ''}`)).join(' ')
            : noAttachmentPill('No Valid ID');

        const businessPermitHtml = businessPermitUrls.length
            ? businessPermitUrls.map((url, i) => attachmentPill(url, `Business Permit${businessPermitUrls.length > 1 ? ' ' + (i + 1) : ''}`)).join(' ')
            : noAttachmentPill('No Business Permit');

        const hasFormData = wizardDataset.formSubmitted === '1'
            || (formProposal && formProposal.trim() !== '')
            || (normalizedBusinessType && normalizedBusinessType !== '—')
            || (formProposedLocation && formProposedLocation !== '—')
            || (formProposedDuration && formProposedDuration !== '—');

        const formSummaryHtml = hasFormData ? `
            <div class="rv-section">
                <div class="rv-section-head">Application Form Summary</div>
                <div class="rv-grid">
                    ${field('First Name', formFirstName)}
                    ${field('Last Name', formLastName)}
                    ${field('Business Name', formBusinessName)}
                    ${field('Type of Business', normalizedBusinessType)}
                    ${field('Email', appEmail)}
                    ${field('Phone', formPhone || appPhone)}
                    ${field('Address', formAddress, true)}
                    ${field('Proposed Location', formProposedLocation)}
                    ${field('Proposed Duration', formProposedDuration)}
                    ${field('Previous CvSU Concessionaire?', formIsPrevious, !hasPreviousLocationYear)}
                    ${hasPreviousLocationYear ? field('Location & Year', formPreviousLocationYear) : ''}
                    <div class="rv-field rv-field-span">
                        <div class="rv-field-label">Business Proposal</div>
                        <div class="rv-proposal">${esc(formProposal) || 'No business proposal submitted.'}</div>
                    </div>
                </div>
                <div class="rv-attachments">
                    <span class="rv-field-label">Attachments</span>
                    ${validIdHtml}
                    ${businessPermitHtml}
                </div>
            </div>
        ` : `
            <div class="rv-section">
                <div class="rv-section-head">Application Form Summary</div>
                <div style="padding:16px;">
                    <div class="view-modal-notice">The applicant hasn't submitted the application form yet, so there are no details to summarize.</div>
                </div>
            </div>
        `;

        const statusChip = document.getElementById('viewModalStatusChip');
        if (statusChip) {
            statusChip.className = `badge ${statusBadgeClass}`;
            statusChip.textContent = statusLabel;
            statusChip.style.display = 'inline-flex';
        }

        document.getElementById('viewContent').innerHTML = `
            ${wizardSectionHtml}

            ${formSummaryHtml}

            ${wizardActionInnerHtml ? `
                <div class="wizard-action-panel" id="wizardActionPanel">
                    ${wizardActionInnerHtml}
                    <div id="wizardPanelError" class="wizard-inline-error"></div>
                    <div id="wizardPanelSuccess" class="wizard-inline-success"></div>
                </div>
            ` : ''}

            ${app?.rejection_reason ? `
                <div class="rv-section">
                    <div class="rv-section-head">Rejection Reason</div>
                    <div style="padding:12px 16px;">
                        <div class="rv-proposal" style="margin-top:0;">${esc(app.rejection_reason)}</div>
                    </div>
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
