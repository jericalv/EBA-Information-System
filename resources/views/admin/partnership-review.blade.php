@extends('admin.layout')

@section('title', 'Review Application #' . $application->id)

@php
    // ---------- Back link / list state ----------
    $listQuery = array_filter(request()->only(['search', 'status', 'page']), fn ($v) => $v !== null && $v !== '');

    // ---------- Contact ----------
    $contactNumber = $application->phone_number ?: $application->phone;

    // ---------- Wizard state ----------
    $wizardStatus = $application->status === 'approved'
        ? 'final_approved'
        : ($application->wizard_status ?: 'loi_pending');
    $wizardStep = $application->wizardStepNumber();
    $isFullyApproved = $wizardStatus === 'final_approved';
    $isWizardRejected = in_array($wizardStatus, ['loi_rejected', 'form_rejected'], true);

    // ---------- Status chip ----------
    $statusChipClass = match ($application->status) {
        'pending', 'under_review' => 'badge-pending',
        'approved' => 'badge-approved',
        'rejected' => 'badge-rejected',
        'registered' => 'badge-registered',
        'expired' => 'badge-expired',
        default => 'badge-expired',
    };
    $statusChipLabel = match ($application->status) {
        'pending', 'under_review' => 'Under Review',
        'approved' => 'Approved',
        'registered' => 'Registered',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        default => ucfirst($application->status),
    };
    if ($isWizardRejected) {
        $statusChipClass = 'badge-rejected';
        $statusChipLabel = 'Rejected';
    }

    // ---------- Status callout ----------
    [$calloutColor, $calloutMessage] = match ($wizardStatus) {
        'loi_pending' => ['gray', 'Waiting for the concessionaire to upload their Letter of Intent'],
        'loi_submitted' => ['amber', 'LOI submitted — your review is required'],
        'loi_rejected' => ['red', 'LOI rejected — awaiting resubmission'],
        'form_pending' => ['gray', 'Waiting for the concessionaire to fill the application form'],
        'form_submitted' => ['amber', 'Application form submitted — your review is required'],
        'form_rejected' => ['red', 'Form rejected — awaiting resubmission'],
        'docs_in_progress' => ['violet', 'Concessionaire is submitting physical documents'],
        'receipt_pending' => ['blue', 'All documents received — awaiting receipt upload'],
        'receipt_submitted' => ['amber', 'Receipt uploaded — final approval required'],
        'final_approved' => ['green', 'Application fully approved'],
        default => ['gray', 'Waiting for the concessionaire to upload their Letter of Intent'],
    };

    // ---------- Physical docs checklist ----------
    $docsState = [
        'recommendation' => (bool) $application->docs_recommendation_checked,
        'notice_occupy' => (bool) $application->docs_notice_occupy_checked,
        'notice_termination' => (bool) $application->docs_notice_termination_checked,
        'moa_contract' => (bool) $application->docs_moa_contract_checked,
    ];
    $docsChecked = count(array_filter($docsState));
    $docLabels = [
        'recommendation' => 'Recommendation for Approval Rental',
        'notice_occupy' => 'Notice to Occupy',
        'notice_termination' => 'Notice of Termination of Rental',
        'moa_contract' => 'MOA / Contract',
    ];

    // ---------- Form summary ----------
    $businessTypeLabel = collect(explode(',', (string) $application->type_of_business))
        ->map(fn ($v) => trim($v))
        ->filter()
        ->map(fn ($v) => match ($v) { 'food' => 'Food', 'non_food' => 'Non-Food', default => $v })
        ->implode(', ');
    $hasFormData = $application->form_submitted_at
        || filled($application->business_proposal)
        || filled($application->proposed_location)
        || filled($application->type_of_business);
    $isPreviousLabel = $application->is_previous_concessionaire ? 'Yes' : 'No';
    $hasPreviousLocationYear = $application->is_previous_concessionaire && filled($application->previous_location_year);

    // ---------- Document viewer tabs ----------
    $fileKind = function (?string $path): string {
        $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
        return match (true) {
            $ext === 'pdf' => 'pdf',
            in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) => 'image',
            default => 'other',
        };
    };
    $docRouteFiles = fn (string $type) => collect($application->documentPaths($type))
        ->map(fn ($path, $i) => [
            'url' => route('admin.partnerships.document', ['application' => $application, 'type' => $type, 'index' => $i]),
            'kind' => $fileKind($path),
        ])->values()->all();
    $storageFiles = fn (string $type) => collect($application->documentPaths($type))
        ->map(fn ($path) => [
            'url' => Storage::url($path),
            'kind' => $fileKind($path),
        ])->values()->all();

    $docTabs = collect([
        ['key' => 'loi', 'label' => 'Letter of Intent', 'files' => $docRouteFiles('letter_of_intent')],
        ['key' => 'valid_id', 'label' => 'Valid ID', 'files' => $storageFiles('valid_id')],
        ['key' => 'business_permit', 'label' => 'Business Permit', 'files' => $storageFiles('business_permit')],
        ['key' => 'receipt', 'label' => 'Payment Receipt', 'files' => $docRouteFiles('receipt')],
        ['key' => 'moa', 'label' => 'MOA', 'files' => $docRouteFiles('moa')],
        ['key' => 'contract', 'label' => 'Contract', 'files' => $docRouteFiles('contract')],
    ])->filter(fn ($tab) => count($tab['files']) > 0)->values();

    // ---------- Timeline ----------
    $timeline = collect([
        ['at' => $application->created_at, 'label' => 'Application started', 'detail' => 'Partnership application created.'],
        ['at' => $application->loi_submitted_at, 'label' => 'Letter of Intent submitted', 'detail' => null],
        ['at' => $application->form_submitted_at, 'label' => 'Application form submitted', 'detail' => null],
        ['at' => $application->receipt_submitted_at, 'label' => 'Payment receipt submitted', 'detail' => null],
    ])->filter(fn ($e) => $e['at'])->sortBy('at')->values()->all();

    $currentTimelineNote = match (true) {
        $wizardStatus === 'loi_rejected' => ['tone' => 'red', 'label' => 'LOI rejected', 'detail' => $application->loi_rejection_reason],
        $wizardStatus === 'form_rejected' => ['tone' => 'red', 'label' => 'Application form rejected', 'detail' => $application->form_rejection_reason],
        $isFullyApproved => ['tone' => 'green', 'label' => 'Final approval granted', 'detail' => 'The concessionaire now has full portal access.'],
        default => null,
    };

    // ---------- Faculty recommendation ----------
    $facultyRec = $application->faculty_recommendation; // recommend_approval | recommend_rejection | null

    // ---------- Administration ----------
    $contractEditable = in_array($application->status, ['pending', 'under_review', 'approved', 'registered'], true);
@endphp

@section('extra-css')
<style>
    /* ===== Shared badges (same palette as the index) ===== */
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

    :root {
        --wiz-green: #16A34A;
        --wiz-green-dark: #15803D;
        --wiz-green-soft: #E9F6EE;
    }
    html[data-theme="dark"] {
        --wiz-green: #1E9560;
        --wiz-green-dark: #8CD6AF;
        --wiz-green-soft: rgba(30, 149, 96, 0.12);
    }

    /* ===== Back link ===== */
    .pr-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 700;
        color: var(--muted);
        text-decoration: none;
        margin-bottom: 14px;
        transition: color 0.12s ease;
    }
    .pr-back:hover { color: var(--pine); }
    .pr-back svg { width: 14px; height: 14px; }

    /* ===== Page header ===== */
    .pr-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        padding: 18px 22px;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: var(--card);
        box-shadow: var(--shadow-card);
        margin-bottom: 20px;
    }
    .pr-header-main { display: flex; align-items: center; gap: 14px; min-width: 0; }
    .pr-avatar {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        color: #fff;
        background: linear-gradient(135deg, var(--green) 0%, var(--green-light) 100%);
        box-shadow: 0 2px 4px rgba(10,92,47,0.18);
    }
    .pr-title-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .pr-title { margin: 0; font-size: 19px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; }
    .pr-id-tag {
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.1em;
        color: var(--muted);
        border: 1px solid var(--line-strong);
        background: var(--hover);
        border-radius: 6px;
        padding: 3px 8px;
    }
    .pr-header-meta {
        display: flex;
        align-items: center;
        gap: 8px 16px;
        flex-wrap: wrap;
        margin-top: 6px;
        font-size: 12.5px;
        color: var(--muted);
    }
    .pr-header-meta svg { width: 13px; height: 13px; flex-shrink: 0; }
    .pr-header-meta span { display: inline-flex; align-items: center; gap: 5px; min-width: 0; overflow-wrap: anywhere; }
    .pr-business-badge {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(212,168,67,0.12);
        color: #b8860b;
    }
    .pr-header-side { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
    .pr-submitted { text-align: right; }
    .pr-submitted-label {
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--faint);
        margin-bottom: 3px;
    }
    .pr-submitted-value { font-size: 13px; font-weight: 700; color: var(--ink); }

    /* ===== Grid ===== */
    .pr-grid {
        display: grid;
        grid-template-columns: minmax(0, 7fr) minmax(0, 5fr);
        gap: 20px;
        align-items: start;
    }
    .pr-rail { position: sticky; top: 20px; display: flex; flex-direction: column; gap: 20px; }
    .pr-col { display: flex; flex-direction: column; gap: 20px; min-width: 0; }
    @media (max-width: 1100px) {
        .pr-grid { grid-template-columns: 1fr; }
        .pr-rail { position: static; }
    }

    /* ===== Ledger section shells ===== */
    .pr-card {
        border: 1px solid var(--line);
        border-radius: 14px;
        background: var(--card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .pr-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 18px;
        border-bottom: 1px solid var(--line);
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .pr-card-body { padding: 16px 18px; }

    /* ===== Document viewer ===== */
    .pr-doc-tabs { display: flex; gap: 6px; flex-wrap: wrap; padding: 12px 18px 0; }
    .pr-doc-tab {
        border: 1px solid var(--line-strong);
        background: var(--hover);
        color: var(--muted);
        border-radius: 999px;
        padding: 6px 13px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.12s ease, color 0.12s ease, border-color 0.12s ease;
    }
    .pr-doc-tab:hover { color: var(--ink); border-color: var(--pine); }
    .pr-doc-tab.is-active { background: var(--pine); border-color: var(--pine); color: #fff; }
    html[data-theme="dark"] .pr-doc-tab.is-active { color: var(--card); }
    .pr-doc-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        padding: 12px 18px;
    }
    .pr-doc-files { display: flex; gap: 6px; flex-wrap: wrap; }
    .pr-doc-file-pill {
        border: 1px solid var(--line-strong);
        background: var(--card);
        color: var(--muted);
        border-radius: 6px;
        padding: 4px 10px;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
    }
    .pr-doc-file-pill.is-active { background: var(--pine-soft); border-color: var(--pine); color: var(--pine); }
    .pr-doc-open {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: var(--pine);
        text-decoration: none;
        white-space: nowrap;
    }
    .pr-doc-open:hover { text-decoration: underline; }
    .pr-doc-open svg { width: 13px; height: 13px; }
    .pr-doc-stage {
        border-top: 1px solid var(--line);
        background: var(--hover);
        min-height: 200px;
    }
    .pr-doc-frame { display: none; width: 100%; }
    .pr-doc-frame.is-active { display: block; }
    .pr-doc-frame iframe {
        display: block;
        width: 100%;
        height: 560px;
        border: 0;
        background: var(--hover);
    }
    .pr-doc-frame .pr-doc-img-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
        max-height: 560px;
        overflow: auto;
    }
    .pr-doc-frame img { max-width: 100%; max-height: 520px; border-radius: 8px; border: 1px solid var(--line); background: var(--card); }
    .pr-doc-frame .pr-doc-no-preview {
        padding: 48px 18px;
        text-align: center;
        font-size: 13px;
        color: var(--muted);
    }
    .pr-doc-empty { padding: 42px 18px; text-align: center; font-size: 13px; color: var(--faint); }

    /* ===== Form summary (premium ledger grid) ===== */
    .rv-grid { display: grid; grid-template-columns: 1fr 1fr; }
    .rv-field { padding: 11px 18px 12px; border-top: 1px solid var(--line); min-width: 0; }
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
        background: var(--hover);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 500;
        color: var(--ink);
        white-space: pre-wrap;
        line-height: 1.6;
    }
    .pr-notice {
        font-size: 13px;
        color: var(--muted);
        background: var(--hover);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 10px 12px;
    }

    /* ===== Timeline ===== */
    .pr-timeline { list-style: none; margin: 0; padding: 0; }
    .pr-timeline li {
        position: relative;
        padding: 0 0 18px 26px;
    }
    .pr-timeline li:last-child { padding-bottom: 2px; }
    .pr-timeline li::before {
        content: '';
        position: absolute;
        left: 5px;
        top: 14px;
        bottom: -2px;
        width: 2px;
        background: var(--line);
    }
    .pr-timeline li:last-child::before { display: none; }
    .pr-timeline li::after {
        content: '';
        position: absolute;
        left: 0;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: var(--card);
        border: 2.5px solid var(--pine);
        box-sizing: border-box;
    }
    .pr-timeline li.tone-red::after { border-color: #dc2626; }
    .pr-timeline li.tone-green::after { border-color: var(--wiz-green); background: var(--wiz-green); }
    .pr-timeline-label { font-size: 13px; font-weight: 700; color: var(--ink); }
    .pr-timeline-time {
        font-family: var(--font-mono);
        font-size: 11px;
        color: var(--faint);
        margin-top: 2px;
    }
    .pr-timeline-detail {
        margin-top: 6px;
        font-size: 12.5px;
        color: var(--muted);
        background: var(--hover);
        border: 1px solid var(--line);
        border-left: 3px solid var(--line-strong);
        border-radius: 6px;
        padding: 8px 10px;
        white-space: pre-wrap;
    }
    .pr-timeline li.tone-red .pr-timeline-detail { border-left-color: #dc2626; }

    /* ===== Stepper ===== */
    .rv-steps { display: flex; align-items: flex-start; }
    .rv-step { display: flex; flex-direction: column; align-items: center; gap: 7px; flex: 0 0 auto; min-width: 56px; }
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
        background: var(--card);
        border: 1.5px solid var(--line-strong);
        color: var(--faint);
    }
    .rv-step-dot.done { background: var(--wiz-green); border-color: var(--wiz-green); color: #fff; }
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
    .rv-step-line { flex: 1 1 auto; height: 2px; border-radius: 999px; background: var(--line); margin-top: 14px; }
    .rv-step-line.done { background: var(--wiz-green); }

    /* ===== Callout ===== */
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
    .rv-callout.gray { background: var(--hover-2); color: var(--muted); border-color: var(--line-strong); }
    .rv-callout.amber { background: #FFFBEB; color: #B45309; border-color: #FDE68A; }
    .rv-callout.red { background: #FEF2F2; color: #B91C1C; border-color: #FECACA; }
    .rv-callout.violet { background: #F5F3FF; color: #6D28D9; border-color: #DDD6FE; }
    .rv-callout.blue { background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE; }
    .rv-callout.green { background: var(--wiz-green-soft); color: #166534; border-color: #BBE5C8; }
    html[data-theme="dark"] .rv-callout.amber { background: rgba(227, 164, 72, 0.12); color: #E9C288; border-color: rgba(227, 164, 72, 0.35); }
    html[data-theme="dark"] .rv-callout.red { background: rgba(227, 106, 106, 0.12); color: #F0A0A0; border-color: rgba(227, 106, 106, 0.35); }
    html[data-theme="dark"] .rv-callout.violet { background: rgba(167, 139, 250, 0.12); color: #C6B4FA; border-color: rgba(167, 139, 250, 0.35); }
    html[data-theme="dark"] .rv-callout.blue { background: rgba(96, 165, 250, 0.12); color: #9CC4F8; border-color: rgba(96, 165, 250, 0.35); }
    html[data-theme="dark"] .rv-callout.green { color: #8CD6AF; border-color: rgba(30, 149, 96, 0.35); }

    /* ===== Action panel ===== */
    .pr-action-title { margin: 0 0 10px; font-size: 14px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; }
    .pr-action-subtitle { margin: -4px 0 12px; font-size: 12px; color: var(--muted); }
    .pr-inline-error {
        margin-top: 10px;
        font-size: 12px;
        font-weight: 600;
        color: #B91C1C;
        background: #FEF2F2;
        border: 1px solid #FECACA;
        border-radius: 8px;
        padding: 8px 10px;
    }
    html[data-theme="dark"] .pr-inline-error { background: rgba(227, 106, 106, 0.12); border-color: rgba(227, 106, 106, 0.35); color: #F0A0A0; }
    .reject-textarea {
        width: 100%;
        min-height: 96px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #FCA5A5;
        resize: vertical;
        font-size: 13px;
        font-family: inherit;
        color: var(--ink);
        background: var(--field, var(--card));
        box-sizing: border-box;
    }
    .reject-textarea:focus {
        outline: none;
        border-color: #EF4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
    }
    .pr-doc-check-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 0;
        font-size: 13px;
        font-weight: 600;
        color: var(--ink);
        cursor: pointer;
    }
    .pr-doc-check-row + .pr-doc-check-row { border-top: 1px solid var(--line); }
    .pr-doc-check-row input[type="checkbox"] {
        width: 15px;
        height: 15px;
        accent-color: var(--wiz-green);
        flex-shrink: 0;
    }
    .pr-doc-readonly {
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
    html[data-theme="dark"] .pr-doc-readonly { color: #8CD6AF; }
    .pr-docs-progress {
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.08em;
        color: var(--muted);
        border: 1px solid var(--line-strong);
        background: var(--hover);
        border-radius: 999px;
        padding: 3px 10px;
    }
    .pr-docs-progress.is-done { background: var(--wiz-green-soft); border-color: var(--wiz-green); color: var(--wiz-green-dark); }

    /* ===== Faculty recommendation ===== */
    .pr-rec-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 4px 11px;
        font-size: 12px;
        font-weight: 700;
    }
    .pr-rec-badge.approve { background: var(--wiz-green-soft); color: var(--wiz-green-dark); }
    .pr-rec-badge.reject { background: rgba(239,68,68,0.1); color: #dc2626; }
    html[data-theme="dark"] .pr-rec-badge.reject { background: rgba(227, 106, 106, 0.14); color: #F0A0A0; }
    .pr-rec-notes {
        margin-top: 10px;
        background: var(--hover);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        color: var(--ink);
        white-space: pre-wrap;
        line-height: 1.55;
    }
    .pr-rec-meta { margin-top: 8px; font-size: 12px; color: var(--faint); }

    /* ===== Administration row ===== */
    .pr-admin-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-top: 20px;
        align-items: start;
    }
    @media (max-width: 1100px) { .pr-admin-grid { grid-template-columns: 1fr; } }
    .pr-field { margin-bottom: 14px; }
    .pr-field:last-child { margin-bottom: 0; }
    .pr-field > label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 6px;
    }
    .pr-field input[type="date"],
    .pr-field select,
    .pr-field input[type="file"] {
        width: 100%;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        font-family: inherit;
        font-size: 13.5px;
        color: var(--ink);
        background: var(--field, var(--card));
        height: 40px;
        padding: 0 12px;
        box-sizing: border-box;
    }
    .pr-field input[type="file"] { padding: 8px 12px; height: auto; }
    .pr-field input:focus, .pr-field select:focus {
        outline: none;
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.10);
    }
    .pr-field-error { margin-top: 6px; font-size: 12.5px; font-weight: 600; color: #b91c1c; }
    html[data-theme="dark"] .pr-field-error { color: #F0A0A0; }
    .pr-help { margin-top: 6px; font-size: 12px; color: var(--muted); line-height: 1.45; }

    .pr-danger-card { border-color: rgba(220, 38, 38, 0.35); }
    .pr-danger-card .pr-card-head { color: #b91c1c; border-bottom-color: rgba(220, 38, 38, 0.25); }
    html[data-theme="dark"] .pr-danger-card { border-color: rgba(227, 106, 106, 0.4); }
    html[data-theme="dark"] .pr-danger-card .pr-card-head { color: #F0A0A0; }

    .btn svg { width: 15px; height: 15px; flex-shrink: 0; }
    .btn:focus-visible { outline: 2px solid var(--green); outline-offset: 2px; }

    @media (max-width: 640px) {
        .rv-grid { grid-template-columns: 1fr; }
        .rv-grid .rv-field:nth-child(-n+2) { border-top: 1px solid var(--line); }
        .rv-grid .rv-field:first-child { border-top: none; }
        .pr-doc-frame iframe { height: 420px; }
        .pr-submitted { text-align: left; }
    }
</style>
@endsection

@section('content')

    <a href="{{ route('admin.partnerships', $listQuery) }}" class="pr-back">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
        </svg>
        Back to Partnerships
    </a>

    {{-- ===== Page header ===== --}}
    <div class="pr-header">
        <div class="pr-header-main">
            <div class="pr-avatar">{{ strtoupper(substr($application->first_name, 0, 1) . substr($application->last_name, 0, 1)) }}</div>
            <div style="min-width:0;">
                <div class="pr-title-row">
                    <h1 class="pr-title">{{ $application->full_name }}</h1>
                    <span class="pr-id-tag">APP-{{ str_pad($application->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge {{ $statusChipClass }}">{{ $statusChipLabel }}</span>
                </div>
                <div class="pr-header-meta">
                    @if ($application->business_name)
                        <span class="pr-business-badge">{{ $application->business_name }}</span>
                    @endif
                    <span>
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
                        {{ $application->email }}
                    </span>
                    @if ($contactNumber)
                        <span>
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $contactNumber }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="pr-header-side">
            <div class="pr-submitted">
                <div class="pr-submitted-label">Submitted</div>
                <div class="pr-submitted-value">{{ $application->created_at->format('M d, Y') }} · {{ $application->created_at->format('g:i A') }}</div>
            </div>
        </div>
    </div>

    <div class="pr-grid">
        {{-- ================= Left column ================= --}}
        <div class="pr-col">

            {{-- Document viewer --}}
            <section class="pr-card">
                <div class="pr-card-head">Submitted Documents</div>
                @if ($docTabs->isEmpty())
                    <div class="pr-doc-empty">No documents have been uploaded yet.</div>
                @else
                    <div class="pr-doc-tabs" role="tablist" aria-label="Document tabs">
                        @foreach ($docTabs as $tab)
                            <button type="button" class="pr-doc-tab {{ $loop->first ? 'is-active' : '' }}" data-doc-tab="{{ $tab['key'] }}" role="tab">
                                {{ $tab['label'] }}{{ count($tab['files']) > 1 ? ' (' . count($tab['files']) . ')' : '' }}
                            </button>
                        @endforeach
                    </div>
                    @foreach ($docTabs as $tab)
                        <div class="pr-doc-panel" data-doc-panel="{{ $tab['key'] }}" @if(!$loop->first) hidden @endif>
                            <div class="pr-doc-toolbar">
                                <div class="pr-doc-files" @if(count($tab['files']) < 2) style="display:none;" @endif>
                                    @foreach ($tab['files'] as $i => $file)
                                        <button type="button" class="pr-doc-file-pill {{ $i === 0 ? 'is-active' : '' }}" data-doc-file="{{ $tab['key'] }}-{{ $i }}">File {{ $i + 1 }}</button>
                                    @endforeach
                                </div>
                                <a class="pr-doc-open" data-doc-open="{{ $tab['key'] }}" href="{{ $tab['files'][0]['url'] }}" target="_blank" rel="noopener">
                                    Open in new tab
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></svg>
                                </a>
                            </div>
                            <div class="pr-doc-stage">
                                @foreach ($tab['files'] as $i => $file)
                                    <div class="pr-doc-frame {{ $i === 0 ? 'is-active' : '' }}" data-doc-frame="{{ $tab['key'] }}-{{ $i }}" data-doc-url="{{ $file['url'] }}">
                                        @if ($file['kind'] === 'pdf')
                                            <iframe data-src="{{ $file['url'] }}" title="{{ $tab['label'] }} preview"></iframe>
                                        @elseif ($file['kind'] === 'image')
                                            <div class="pr-doc-img-wrap">
                                                <img data-src="{{ $file['url'] }}" alt="{{ $tab['label'] }} preview" loading="lazy">
                                            </div>
                                        @else
                                            <div class="pr-doc-no-preview">
                                                This file type can't be previewed inline.
                                                <a href="{{ $file['url'] }}" target="_blank" rel="noopener" style="color: var(--pine); font-weight:700;">Open it in a new tab</a> instead.
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </section>

            {{-- Application form summary --}}
            <section class="pr-card">
                <div class="pr-card-head">Application Form Summary</div>
                @if ($hasFormData)
                    <div class="rv-grid">
                        <div class="rv-field"><div class="rv-field-label">First Name</div><div class="rv-field-value">{{ $application->first_name ?: '—' }}</div></div>
                        <div class="rv-field"><div class="rv-field-label">Last Name</div><div class="rv-field-value">{{ $application->last_name ?: '—' }}</div></div>
                        <div class="rv-field"><div class="rv-field-label">Business Name</div><div class="rv-field-value">{{ $application->business_name ?: '—' }}</div></div>
                        <div class="rv-field"><div class="rv-field-label">Type of Business</div><div class="rv-field-value">{{ $businessTypeLabel ?: '—' }}</div></div>
                        <div class="rv-field"><div class="rv-field-label">Email</div><div class="rv-field-value">{{ $application->email ?: '—' }}</div></div>
                        <div class="rv-field"><div class="rv-field-label">Phone</div><div class="rv-field-value">{{ $contactNumber ?: '—' }}</div></div>
                        <div class="rv-field rv-field-span"><div class="rv-field-label">Address</div><div class="rv-field-value">{{ $application->address ?: '—' }}</div></div>
                        <div class="rv-field"><div class="rv-field-label">Proposed Location</div><div class="rv-field-value">{{ $application->proposed_location ?: '—' }}</div></div>
                        <div class="rv-field"><div class="rv-field-label">Proposed Duration</div><div class="rv-field-value">{{ $application->proposed_duration ?: '—' }}</div></div>
                        <div class="rv-field {{ $hasPreviousLocationYear ? '' : 'rv-field-span' }}"><div class="rv-field-label">Previous CvSU Concessionaire?</div><div class="rv-field-value">{{ $isPreviousLabel }}</div></div>
                        @if ($hasPreviousLocationYear)
                            <div class="rv-field"><div class="rv-field-label">Location &amp; Year</div><div class="rv-field-value">{{ $application->previous_location_year }}</div></div>
                        @endif
                        <div class="rv-field rv-field-span">
                            <div class="rv-field-label">Business Proposal</div>
                            <div class="rv-proposal">{{ $application->business_proposal ?: 'No business proposal submitted.' }}</div>
                        </div>
                    </div>
                @else
                    <div class="pr-card-body">
                        <div class="pr-notice">The applicant hasn't submitted the application form yet, so there are no details to summarize.</div>
                    </div>
                @endif
            </section>

            {{-- Timeline --}}
            <section class="pr-card">
                <div class="pr-card-head">Review Timeline</div>
                <div class="pr-card-body">
                    <ul class="pr-timeline">
                        @foreach ($timeline as $event)
                            <li>
                                <div class="pr-timeline-label">{{ $event['label'] }}</div>
                                <div class="pr-timeline-time">{{ $event['at']->format('M d, Y · g:i A') }}</div>
                                @if ($event['detail'])
                                    <div class="pr-timeline-detail">{{ $event['detail'] }}</div>
                                @endif
                            </li>
                        @endforeach
                        @if ($currentTimelineNote)
                            <li class="tone-{{ $currentTimelineNote['tone'] }}">
                                <div class="pr-timeline-label">{{ $currentTimelineNote['label'] }}</div>
                                <div class="pr-timeline-time">Current status</div>
                                @if ($currentTimelineNote['detail'])
                                    <div class="pr-timeline-detail">{{ $currentTimelineNote['detail'] }}</div>
                                @endif
                            </li>
                        @endif
                    </ul>
                </div>
            </section>

            {{-- Application-level rejection reason (legacy full reject) --}}
            @if ($application->rejection_reason)
                <section class="pr-card">
                    <div class="pr-card-head">Rejection Reason</div>
                    <div class="pr-card-body">
                        <div class="rv-proposal" style="margin-top:0;">{{ $application->rejection_reason }}</div>
                    </div>
                </section>
            @endif
        </div>

        {{-- ================= Right rail ================= --}}
        <div class="pr-rail">

            {{-- Progress --}}
            <section class="pr-card">
                <div class="pr-card-head">
                    Review Progress
                    @if (in_array($wizardStatus, ['docs_in_progress', 'receipt_pending'], true))
                        <span class="pr-docs-progress {{ $docsChecked === 4 ? 'is-done' : '' }}" id="docsProgressChip">{{ $docsChecked }} / 4 DOCS</span>
                    @endif
                </div>
                <div class="pr-card-body">
                    <div class="rv-steps">
                        @foreach (['LOI', 'Form', 'Docs', 'Receipt'] as $index => $stepLabel)
                            @php
                                $stepNo = $index + 1;
                                $done = $isFullyApproved || $wizardStep > $stepNo;
                                $active = ! $isFullyApproved && $wizardStep === $stepNo;
                                $stateClass = $done ? 'done' : ($active ? 'active' : '');
                            @endphp
                            <div class="rv-step">
                                <span class="rv-step-dot {{ $stateClass }}">{!! $done ? '&#10003;' : $stepNo !!}</span>
                                <span class="rv-step-label {{ $stateClass }}">{{ $stepLabel }}</span>
                            </div>
                            @if ($stepNo < 4)
                                <span class="rv-step-line {{ $done ? 'done' : '' }}"></span>
                            @endif
                        @endforeach
                    </div>
                    <div class="rv-callout {{ $calloutColor }}">{{ $calloutMessage }}</div>
                </div>
            </section>

            {{-- Action panel (state-dependent) --}}
            @if ($wizardStatus === 'loi_submitted')
                <section class="pr-card">
                    <div class="pr-card-head">Step 1 Review · Letter of Intent</div>
                    <div class="pr-card-body">
                        <p class="pr-action-subtitle" style="margin-top:0;">The LOI is previewed under Submitted Documents. Approve to unlock the application form, or reject with a reason.</p>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <form method="POST" action="{{ route('admin.partnerships.wizard.approve-loi', $application) }}">
                                @csrf
                                <button type="submit" class="btn btn-green btn-sm">Approve LOI</button>
                            </form>
                            <button type="button" class="btn btn-red btn-sm" id="toggleRejectBtn">Reject LOI</button>
                        </div>
                        <form method="POST" action="{{ route('admin.partnerships.wizard.reject-loi', $application) }}" id="rejectForm" style="{{ $errors->has('reason') ? '' : 'display:none;' }}margin-top:12px;">
                            @csrf
                            <textarea name="reason" class="reject-textarea" placeholder="Enter reason for rejection (min. 10 characters)...">{{ old('reason') }}</textarea>
                            @error('reason')<div class="pr-inline-error">{{ $message }}</div>@enderror
                            <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                                <button type="submit" class="btn btn-red btn-sm">Confirm Rejection</button>
                            </div>
                        </form>
                    </div>
                </section>
            @elseif ($wizardStatus === 'form_submitted')
                <section class="pr-card">
                    <div class="pr-card-head">Step 2 Review · Application Form</div>
                    <div class="pr-card-body">
                        <p class="pr-action-subtitle" style="margin-top:0;">Review the form summary and attachments, then approve or reject.</p>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <form method="POST" action="{{ route('admin.partnerships.wizard.approve-form', $application) }}">
                                @csrf
                                <button type="submit" class="btn btn-green btn-sm">Approve Form</button>
                            </form>
                            <button type="button" class="btn btn-red btn-sm" id="toggleRejectBtn">Reject Form</button>
                        </div>
                        <form method="POST" action="{{ route('admin.partnerships.wizard.reject-form', $application) }}" id="rejectForm" style="{{ $errors->has('reason') ? '' : 'display:none;' }}margin-top:12px;">
                            @csrf
                            <textarea name="reason" class="reject-textarea" placeholder="Enter reason for rejection (min. 10 characters)...">{{ old('reason') }}</textarea>
                            @error('reason')<div class="pr-inline-error">{{ $message }}</div>@enderror
                            <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                                <button type="submit" class="btn btn-red btn-sm">Confirm Rejection</button>
                            </div>
                        </form>
                    </div>
                </section>
            @elseif (in_array($wizardStatus, ['docs_in_progress', 'receipt_pending'], true))
                <section class="pr-card">
                    <div class="pr-card-head">Step 3 · Physical Documents</div>
                    <div class="pr-card-body">
                        <p class="pr-action-subtitle" style="margin-top:0;">Check each document as the concessionaire submits it to the EBA Office.</p>
                        @foreach ($docLabels as $docKey => $docLabel)
                            <label class="pr-doc-check-row">
                                <input type="checkbox" data-doc-key="{{ $docKey }}" {{ $docsState[$docKey] ? 'checked' : '' }}>
                                {{ $docLabel }}
                            </label>
                        @endforeach
                        <div id="docsPanelError" class="pr-inline-error" style="display:none;"></div>
                        <div class="rv-callout green" id="docsDoneBanner" style="{{ $docsChecked === 4 ? '' : 'display:none;' }}">All documents received! Awaiting receipt upload.</div>
                    </div>
                </section>
            @elseif ($wizardStatus === 'receipt_submitted')
                <section class="pr-card">
                    <div class="pr-card-head">Step 4 · Final Approval</div>
                    <div class="pr-card-body">
                        <p class="pr-action-subtitle" style="margin-top:0;">The payment receipt is previewed under Submitted Documents. All office requirements are complete:</p>
                        @foreach ($docLabels as $docLabel)
                            <div class="pr-doc-readonly">&#10003; {{ $docLabel }}</div>
                        @endforeach
                        <form method="POST" action="{{ route('admin.partnerships.wizard.final-approve', $application) }}"
                              onsubmit="return confirm('Grant final approval? This activates the concessionaire account and sends the approval email.');">
                            @csrf
                            <button type="submit" class="btn btn-green" style="width:100%;margin-top:10px;font-weight:800;">Grant Final Approval</button>
                        </form>
                    </div>
                </section>
            @elseif (! $isFullyApproved)
                <section class="pr-card">
                    <div class="pr-card-body">
                        <div class="pr-notice">No action required at this time. The concessionaire is completing this step.</div>
                        @if ($wizardStatus === 'loi_rejected' && $application->loi_rejection_reason)
                            <div class="pr-inline-error">LOI rejection reason: {{ $application->loi_rejection_reason }}</div>
                        @endif
                        @if ($wizardStatus === 'form_rejected' && $application->form_rejection_reason)
                            <div class="pr-inline-error">Form rejection reason: {{ $application->form_rejection_reason }}</div>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Faculty recommendation --}}
            <section class="pr-card">
                <div class="pr-card-head">Faculty Recommendation</div>
                <div class="pr-card-body">
                    @if ($facultyRec)
                        <span class="pr-rec-badge {{ $facultyRec === 'recommend_approval' ? 'approve' : 'reject' }}">
                            {{ $facultyRec === 'recommend_approval' ? 'Recommends Approval' : 'Recommends Rejection' }}
                        </span>
                        @if ($application->faculty_notes)
                            <div class="pr-rec-notes">{{ $application->faculty_notes }}</div>
                        @endif
                        @if ($application->reviewer)
                            <div class="pr-rec-meta">Reviewed by {{ $application->reviewer->name }}</div>
                        @endif
                    @else
                        <div class="pr-notice">No faculty recommendation has been submitted for this application yet.</div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    {{-- ================= Administration ================= --}}
    <div class="pr-admin-grid">
        {{-- Contract period --}}
        <section class="pr-card">
            <div class="pr-card-head">Contract Period</div>
            <div class="pr-card-body">
                @if ($contractEditable)
                    <form method="POST" action="{{ route('admin.partnerships.contract-period', $application) }}">
                        @csrf
                        @method('PATCH')
                        <div class="pr-field">
                            <label for="contract_period_start">Start Date</label>
                            <input type="date" id="contract_period_start" name="contract_period_start"
                                   value="{{ old('contract_period_start', $application->contract_period_start?->format('Y-m-d')) }}" required>
                            @error('contract_period_start')<div class="pr-field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="pr-field">
                            <label for="contract_period_end">End Date</label>
                            <input type="date" id="contract_period_end" name="contract_period_end"
                                   value="{{ old('contract_period_end', $application->contract_period_end?->format('Y-m-d')) }}" required>
                            @error('contract_period_end')<div class="pr-field-error">{{ $message }}</div>@enderror
                        </div>
                        @error('contract_period')<div class="pr-field-error" style="margin-bottom:10px;">{{ $message }}</div>@enderror
                        <button type="submit" class="btn btn-outline btn-sm">Save Contract Period</button>
                    </form>
                @else
                    <div class="pr-notice">
                        @if ($application->contract_period_start && $application->contract_period_end)
                            {{ $application->contract_period_start->format('M d, Y') }} — {{ $application->contract_period_end->format('M d, Y') }}
                        @else
                            The contract period can only be edited for pending, under-review, approved, or registered applications.
                        @endif
                    </div>
                @endif
            </div>
        </section>

        {{-- Upload on behalf --}}
        <section class="pr-card">
            <div class="pr-card-head">Upload Document</div>
            <div class="pr-card-body">
                <form method="POST" action="{{ route('admin.partnerships.upload-document', $application) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="pr-field">
                        <label for="document_type">Document Type</label>
                        <select id="document_type" name="document_type" required>
                            <option value="letter_of_intent">Letter of Intent</option>
                            <option value="moa">MOA</option>
                            <option value="contract">Contract</option>
                        </select>
                        @error('document_type')<div class="pr-field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="pr-field">
                        <label for="document">File</label>
                        <input type="file" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                        <p class="pr-help">PDF, JPG, or PNG — max 10 MB. Replaces the current file of the same type.</p>
                        @error('document')<div class="pr-field-error">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm">Upload on Behalf</button>
                </form>
            </div>
        </section>

        {{-- Danger zone --}}
        <section class="pr-card pr-danger-card">
            <div class="pr-card-head">Danger Zone</div>
            <div class="pr-card-body">
                <p class="pr-help" style="margin:0 0 12px;">Permanently deletes this application and its uploaded documents. This cannot be undone.</p>
                <form method="POST" action="{{ route('admin.partnerships.destroy', $application) }}"
                      onsubmit="return confirm('Delete this partnership application permanently? Uploaded documents will also be removed.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-red btn-sm">Delete Application</button>
                </form>
            </div>
        </section>
    </div>

@endsection

@section('scripts')
<script>
    // ---------- Document viewer: tab / file switching with lazy loading ----------
    (function () {
        const loadFrame = (frame) => {
            if (!frame || frame.dataset.loaded === '1') return;
            const lazy = frame.querySelector('[data-src]');
            if (lazy) {
                lazy.src = lazy.dataset.src;
                lazy.removeAttribute('data-src');
            }
            frame.dataset.loaded = '1';
        };

        // Load the initially visible frame.
        loadFrame(document.querySelector('.pr-doc-frame.is-active'));

        document.querySelectorAll('[data-doc-tab]').forEach((tabBtn) => {
            tabBtn.addEventListener('click', () => {
                document.querySelectorAll('[data-doc-tab]').forEach((b) => b.classList.toggle('is-active', b === tabBtn));
                document.querySelectorAll('[data-doc-panel]').forEach((panel) => {
                    panel.hidden = panel.dataset.docPanel !== tabBtn.dataset.docTab;
                    if (!panel.hidden) loadFrame(panel.querySelector('.pr-doc-frame.is-active'));
                });
            });
        });

        document.querySelectorAll('[data-doc-file]').forEach((pill) => {
            pill.addEventListener('click', () => {
                const panel = pill.closest('[data-doc-panel]');
                if (!panel) return;
                panel.querySelectorAll('[data-doc-file]').forEach((p) => p.classList.toggle('is-active', p === pill));
                panel.querySelectorAll('.pr-doc-frame').forEach((frame) => {
                    const active = frame.dataset.docFrame === pill.dataset.docFile;
                    frame.classList.toggle('is-active', active);
                    if (active) {
                        loadFrame(frame);
                        const openLink = panel.querySelector('[data-doc-open]');
                        if (openLink) openLink.href = frame.dataset.docUrl;
                    }
                });
            });
        });
    })();

    // ---------- Reject panel toggle ----------
    (function () {
        const toggleBtn = document.getElementById('toggleRejectBtn');
        const rejectForm = document.getElementById('rejectForm');
        if (!toggleBtn || !rejectForm) return;
        toggleBtn.addEventListener('click', () => {
            const isHidden = rejectForm.style.display === 'none';
            rejectForm.style.display = isHidden ? 'block' : 'none';
            if (isHidden) rejectForm.querySelector('textarea')?.focus();
        });
    })();

    // ---------- Physical docs checklist (stays async: 4 ticks shouldn't be 4 page loads) ----------
    (function () {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][data-doc-key]');
        if (!checkboxes.length) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const tickUrl = @json(route('admin.partnerships.wizard.tick-doc', $application));
        const initialWizardStatus = @json($wizardStatus);
        const errorEl = document.getElementById('docsPanelError');
        const banner = document.getElementById('docsDoneBanner');
        const chip = document.getElementById('docsProgressChip');

        const showError = (message) => {
            if (!errorEl) return;
            errorEl.textContent = message;
            errorEl.style.display = message ? 'block' : 'none';
        };

        const syncUi = (docs) => {
            let checked = 0;
            checkboxes.forEach((box) => {
                box.checked = !!docs[box.dataset.docKey];
                if (box.checked) checked += 1;
            });
            if (chip) {
                chip.textContent = checked + ' / 4 DOCS';
                chip.classList.toggle('is-done', checked === 4);
            }
            if (banner) banner.style.display = checked === 4 ? 'flex' : 'none';
        };

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', async function () {
                const previousChecked = !this.checked;
                this.disabled = true;
                showError('');

                try {
                    const response = await fetch(tickUrl, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ doc: this.dataset.docKey, checked: this.checked }),
                    });
                    const data = await response.json().catch(() => ({ success: false, message: 'Unexpected server response.' }));

                    if (!response.ok || !data.success) {
                        this.checked = previousChecked;
                        showError(data.message || 'Failed to update document status.');
                        return;
                    }

                    syncUi(data.docs || {});

                    // Crossing the docs_in_progress <-> receipt_pending boundary changes
                    // the stepper/callout — re-render the page to reflect it.
                    if (data.wizard_status && data.wizard_status !== initialWizardStatus) {
                        window.location.reload();
                    }
                } catch (error) {
                    this.checked = previousChecked;
                    showError('Failed to update document status. Please try again.');
                } finally {
                    this.disabled = false;
                }
            });
        });
    })();
</script>
@endsection
