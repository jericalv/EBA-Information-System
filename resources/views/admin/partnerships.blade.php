@extends('admin.layout')

@section('title', 'Partnership Applications')

@section('extra-css')
<style>
    /* ===== Applications Table ===== */
    .action-btns { display: flex; gap: 6px; align-items: center; }

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
    #partnerships-data-table td.actions-col .action-btns {
        justify-content: center;
    }

    /* ===== Row action kebab menu ===== */
    .row-menu-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--muted);
        cursor: pointer;
        transition: background-color 0.12s ease, color 0.12s ease, border-color 0.12s ease;
    }
    .row-menu-btn svg { width: 17px; height: 17px; }
    .row-menu-btn:hover,
    .row-menu-btn.is-open {
        background: var(--pine-soft);
        color: var(--pine);
        border-color: var(--line-strong);
    }
    .row-menu-btn:focus-visible { outline: 2px solid rgba(10, 92, 47, 0.45); outline-offset: 2px; }
    .actions-dropdown-container { position: relative; display: inline-block; text-align: left; }
    .actions-dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 4px);
        width: 180px;
        z-index: 60;
        display: none;
    }
    .actions-dropdown-menu.active { display: block; }

    /* ===== Review Modal (premium ledger) ===== */
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
    html[data-theme="dark"] .wizard-inline-error { background: rgba(227, 106, 106, 0.12); border-color: rgba(227, 106, 106, 0.35); color: #F0A0A0; }
    html[data-theme="dark"] .wizard-inline-success,
    html[data-theme="dark"] .wizard-doc-readonly,
    html[data-theme="dark"] .wizard-docs-done-banner { color: #8CD6AF; border-color: rgba(30, 149, 96, 0.35); }
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
        background: var(--card);
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
        background: var(--card);
        border-bottom: 1px solid var(--line);
        border-radius: 14px 14px 0 0;
    }
    #viewModal #viewContent {
        overflow-y: auto;
        padding: 20px;
        background: var(--hover);
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
        background: var(--card);
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
        background: var(--card);
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
        background: var(--hover);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 500;
        color: var(--ink);
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
        background: var(--hover);
    }
    .rv-attachments .rv-field-label { margin: 0 4px 0 0; }
    .rv-attachment-empty {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        background: var(--card);
        color: var(--faint);
        border: 1px dashed var(--line-strong);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    .view-modal-notice {
        font-size: 13px;
        color: var(--muted);
        background: var(--hover);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 10px;
    }
    .view-modal-quote {
        margin-top: 8px;
        padding: 10px 12px;
        border-left: 3px solid #94a3b8;
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 8px;
        font-size: 13px;
        color: var(--ink);
        white-space: pre-wrap;
    }
    .proposal-text {
        background: var(--hover);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 12px;
        font-size: 14px;
        color: var(--ink);
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
        background: var(--hover-2);
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
        background: var(--card);
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
        background: var(--card);
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
    html[data-theme="dark"] .rv-callout.green { background: rgba(30, 149, 96, 0.12); color: #8CD6AF; border-color: rgba(30, 149, 96, 0.35); }

    .wizard-action-panel {
        margin-top: 14px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: var(--card);
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
        color: var(--ink);
        background: var(--card);
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
                                <div class="actions-dropdown-container">
                                    <button type="button" class="row-menu-btn" aria-label="More actions" aria-haspopup="menu" onclick="toggleActionsMenu(this)">
                                        <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/>
                                        </svg>
                                    </button>
                                    <div class="actions-dropdown-menu pop" role="menu">
                                    <button
                                        type="button"
                                        class="pop-item"
                                        role="menuitem"
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
                                        data-loi-path="{{ ($application->letter_of_intent_path || $application->letter_of_intent) ? route('admin.partnerships.document', ['application' => $application, 'type' => 'letter_of_intent']) : '' }}"
                                        data-loi-paths="{{ json_encode(collect($application->documentPaths('letter_of_intent'))->map(fn($p, $i) => route('admin.partnerships.document', ['application' => $application, 'type' => 'letter_of_intent', 'index' => $i]))->all()) }}"
                                        data-receipt-path="{{ $application->receipt_path ? route('admin.partnerships.document', ['application' => $application, 'type' => 'receipt']) : '' }}"
                                        data-receipt-paths="{{ json_encode(collect($application->documentPaths('receipt'))->map(fn($p, $i) => route('admin.partnerships.document', ['application' => $application, 'type' => 'receipt', 'index' => $i]))->all()) }}"
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
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        View
                                    </button>
                                    </div>
                                </div>
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
        approveLoi: (id) => `{{ route('admin.partnerships.wizard.approve-loi', ['application' => '__ID__']) }}`.replace('__ID__', id),
        rejectLoi: (id) => `{{ route('admin.partnerships.wizard.reject-loi', ['application' => '__ID__']) }}`.replace('__ID__', id),
        approveForm: (id) => `{{ route('admin.partnerships.wizard.approve-form', ['application' => '__ID__']) }}`.replace('__ID__', id),
        rejectForm: (id) => `{{ route('admin.partnerships.wizard.reject-form', ['application' => '__ID__']) }}`.replace('__ID__', id),
        tickDoc: (id) => `{{ route('admin.partnerships.wizard.tick-doc', ['application' => '__ID__']) }}`.replace('__ID__', id),
        finalApprove: (id) => `{{ route('admin.partnerships.wizard.final-approve', ['application' => '__ID__']) }}`.replace('__ID__', id),
    };

    function toggleActionsMenu(button) {
        const menu = button.closest('.actions-dropdown-container')?.querySelector('.actions-dropdown-menu');
        if (!menu) return;
        const willOpen = !menu.classList.contains('active');
        document.querySelectorAll('.actions-dropdown-menu.active').forEach((openMenu) => {
            openMenu.classList.remove('active');
            openMenu.closest('.actions-dropdown-container')?.querySelector('.row-menu-btn')?.classList.remove('is-open');
        });
        menu.classList.toggle('active', willOpen);
        button.classList.toggle('is-open', willOpen);
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.actions-dropdown-container')) {
            document.querySelectorAll('.actions-dropdown-menu.active').forEach((menu) => {
                menu.classList.remove('active');
                menu.closest('.actions-dropdown-container')?.querySelector('.row-menu-btn')?.classList.remove('is-open');
            });
        }
    });

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
        document.querySelectorAll('.actions-dropdown-menu.active').forEach((menu) => {
            menu.classList.remove('active');
            menu.closest('.actions-dropdown-container')?.querySelector('.row-menu-btn')?.classList.remove('is-open');
        });
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
    });
</script>
@endsection
