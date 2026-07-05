@extends('admin.layout')

@section('title', 'Partnership Applications')

@section('extra-css')
<style>
    .stats-mini {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-mini {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-mini::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        transition: all 0.2s;
    }
    .stat-mini.pending::before { background: linear-gradient(90deg, #f59e0b, #fb923c); }
    .stat-mini.approved::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-mini.rejected::before { background: linear-gradient(90deg, #ef4444, #f87171); }
    .stat-mini.registered::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .stat-mini.expired::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    .stat-mini:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .stat-mini-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .stat-mini-label { 
        font-size: 13px; 
        color: #64748b; 
        font-weight: 600;
        letter-spacing: 0.01em;
    }
    .stat-mini-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .stat-mini.pending .stat-mini-icon { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
    .stat-mini.approved .stat-mini-icon { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
    .stat-mini.rejected .stat-mini-icon { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
    .stat-mini.registered .stat-mini-icon { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb; }
    .stat-mini.expired .stat-mini-icon { background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); color: #7c3aed; }
    .stat-mini-value { 
        font-size: 32px; 
        font-weight: 800; 
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 8px;
    }
    .stat-mini-footer {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    @media (max-width: 1200px) {
        .stats-mini {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 768px) {
        .stats-mini {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .action-btns { display: flex; gap: 6px; align-items: center; }
    .btn-icon-view {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 0;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    }
    .btn-icon-view:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
        transform: scale(1.08);
        box-shadow: 0 4px 14px rgba(29, 78, 216, 0.18);
    }
    .status-stack {
        display: flex;
        flex-direction: column;
        gap: 5px;
        align-items: flex-start;
    }
    .business-badge {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(212,168,67,0.12);
        color: #b8860b;
    }
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

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-12px) scale(0.97);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .view-modal-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .view-modal-topbar-inner {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .view-modal-title {
        margin: 0;
        font-size: 11px;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.65);
        letter-spacing: 0.14em;
        text-transform: uppercase;
        background: none;
        -webkit-background-clip: unset;
        -webkit-text-fill-color: unset;
        background-clip: unset;
    }
    .view-modal-title-main {
        font-size: 18px;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.02em;
        line-height: 1.2;
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
        line-height: 1;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }
    .view-modal-close:hover {
        background: rgba(255, 255, 255, 0.22);
        color: #fff;
        border-color: rgba(255, 255, 255, 0.45);
        transform: rotate(90deg) scale(1.05);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }
    .modal textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        resize: vertical;
        min-height: 80px;
    }
    .modal textarea:focus {
        outline: none;
        border-color: var(--green);
    }
    .modal-btns {
        display: flex;
        gap: 10px;
        margin-top: 16px;
        justify-content: flex-end;
    }
    .detail-row {
        display: flex;
        margin-bottom: 12px;
        font-size: 14px;
    }
    .detail-label {
        font-weight: 600;
        color: #374151;
        width: 120px;
        flex-shrink: 0;
    }
    .detail-value {
        color: #4b5563;
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
        color: var(--green);
        font-weight: 600;
        text-decoration: none;
    }
    .file-link:hover {
        text-decoration: underline;
    }
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
    .file-link.file-link-pill:hover {
        background: #dcfce7;
        border-color: #4ade80;
        text-decoration: none;
    }
    .btn-primary {
        background: var(--green);
        color: #fff;
    }
    .btn-primary:hover {
        background: var(--green-light);
    }
    .badge-pending {
        background: rgba(245,158,11,0.1);
        color: #d97706;
    }
    .badge-approved {
        background: #dcfce7;
        color: #166534;
    }
    .badge-rejected {
        background: rgba(239,68,68,0.1);
        color: #dc2626;
    }
    .badge-registered {
        background: rgba(59,130,246,0.1);
        color: #2563eb;
    }
    .badge-expired {
        background: #e5e7eb;
        color: #4b5563;
    }
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
    .wizard-badge.gray {
        background: #f3f4f6;
        color: #374151;
        border-color: #e5e7eb;
    }
    .wizard-badge.step-1 {
        background: #eff6ff;
        color: #1e40af;
        border-color: #bfdbfe;
    }
    .wizard-badge.step-2 {
        background: #fff7ed;
        color: #9a3412;
        border-color: #fed7aa;
    }
    .wizard-badge.step-3 {
        background: #f5f3ff;
        color: #5b21b6;
        border-color: #ddd6fe;
    }
    .wizard-badge.step-4 {
        background: #f0fdfa;
        color: #0f766e;
        border-color: #99f6e4;
    }
    .wizard-badge.amber {
        background: #fffbeb;
        color: #b45309;
        border-color: #fde68a;
    }
    .wizard-badge.violet {
        background: #f5f3ff;
        color: #6d28d9;
        border-color: #ddd6fe;
    }
    .wizard-badge.blue {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }
    .wizard-badge.green {
        background: #ecfdf3;
        color: #166534;
        border-color: #bbf7d0;
    }
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
    .contract-warning {
        margin-top: 8px;
        font-size: 12px;
        color: #b45309;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 8px 10px;
    }
    .admin-upload-box {
        margin-top: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        background: #f8fafc;
    }
    .admin-upload-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 8px;
        align-items: center;
        margin-bottom: 10px;
    }
    .admin-upload-row:last-child {
        margin-bottom: 0;
    }
    .admin-upload-row input[type="file"] {
        width: 100%;
    }
    .admin-upload-tip {
        margin-top: 12px;
        font-size: 12px;
        color: #1f2937;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 10px;
        line-height: 1.5;
    }
    .doc-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 700;
        margin-right: 6px;
        margin-bottom: 6px;
    }
    .doc-pill.ok {
        background: #dcfce7;
        color: #166534;
    }
    .doc-pill.missing {
        background: #fee2e2;
        color: #991b1b;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 700;
    }
    .status-pill.active {
        background: #dcfce7;
        color: #166534;
    }
    .status-pill.inactive {
        background: #fee2e2;
        color: #991b1b;
    }
    .status-pill.pending {
        background: #e5e7eb;
        color: #374151;
    }
    .contract-box {
        margin-top: 10px;
        background: #f8fafc;
        border: 1px solid #dbe3ee;
        border-radius: 12px;
        padding: 12px;
    }
    .contract-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .contract-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .contract-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
    }
    .contract-input {
        width: 100%;
        padding: 10px 11px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        color: #0f172a;
        background: #fff;
    }
    .contract-input:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.16);
    }
    .contract-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 12px;
    }
    .contract-save-btn {
        min-width: 72px;
        padding: 8px 14px;
        font-weight: 700;
        box-shadow: 0 8px 14px rgba(22, 101, 52, 0.22);
    }
    .contract-save-btn:hover {
        box-shadow: 0 10px 18px rgba(22, 101, 52, 0.3);
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
    .view-modal-header-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
    }
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
        box-shadow: 
            0 12px 28px rgba(5, 150, 105, 0.35),
            0 4px 12px rgba(5, 150, 105, 0.2),
            inset 0 2px 4px rgba(255, 255, 255, 0.2);
        border: 3px solid rgba(255, 255, 255, 0.95);
        letter-spacing: 0.5px;
    }
    .view-modal-name {
        margin: 0;
        font-size: 19px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        letter-spacing: -0.01em;
    }
    .view-modal-subtext {
        margin-top: 5px;
        font-size: 13px;
        color: #64748b;
        line-height: 1.55;
        font-weight: 500;
    }
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
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .view-modal-status-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.16);
    }
    .view-modal-section {
        margin-top: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #fff;
        padding: 18px;
        box-shadow: 
            0 4px 16px rgba(15, 23, 42, 0.05),
            0 2px 8px rgba(15, 23, 42, 0.05);
    }
    .view-modal-section.soft-block {
        background: #f9f9f9;
        border-color: #e5e7eb;
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
    .view-modal-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }
    .view-modal-section-head .view-modal-section-title {
        margin-bottom: 0;
    }
    .view-modal-link-btn {
        border: none;
        background: transparent;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        cursor: pointer;
        padding: 0;
    }
    .view-modal-link-btn:hover {
        color: #0f172a;
        text-decoration: underline;
    }
    .view-modal-link-btn.cancel {
        text-transform: none;
        letter-spacing: 0;
        font-size: 12px;
        font-weight: 600;
        margin-left: 8px;
    }
    .view-modal-limit-note {
        margin-top: 4px;
        font-size: 12px;
        color: #94a3b8;
        text-align: right;
    }
    .view-modal-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .view-modal-notice {
        font-size: 13px;
        color: #4b5563;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
    }
    .view-modal-doc-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f8fafc;
        margin-bottom: 8px;
    }
    .view-modal-doc-row:last-child {
        margin-bottom: 0;
    }
    .view-modal-doc-left {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }
    .view-modal-doc-icon {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 900;
        flex-shrink: 0;
    }
    .view-modal-doc-icon.ok {
        color: #166534;
        background: #dcfce7;
    }
    .view-modal-doc-icon.missing {
        color: #991b1b;
        background: #fee2e2;
    }
    .view-modal-doc-name {
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
    }
    .view-modal-doc-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .view-modal-doc-status {
        font-size: 12px;
        font-weight: 700;
        border-radius: 999px;
        padding: 4px 10px;
        border: 1px solid transparent;
    }
    .view-modal-doc-status.ok {
        background: #ecfdf3;
        border-color: #bbf7d0;
        color: #166534;
    }
    .view-modal-doc-status.missing {
        background: #fef2f2;
        border-color: #fecaca;
        color: #991b1b;
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
    .view-modal-actions {
        position: sticky;
        bottom: 0;
        margin: 18px -20px 0;
        padding: 14px 20px 16px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, #ffffff 24%);
        border-top: 1px solid #e2e8f0;
        box-shadow: 0 -10px 22px rgba(15, 23, 42, 0.06);
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    .view-modal-actions .btn {
        margin: 0;
    }
    .wizard-mini-progress {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        max-width: 420px;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 12px;
    }
    .wizard-mini-progress > div {
        justify-content: center;
    }
    .wizard-mini-progress > div:last-child::after {
        content: '';
        width: 72px;
        height: 3px;
        margin-top: 14px;
        visibility: hidden;
        flex: 0 0 auto;
    }
    .wizard-mini-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
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
    .wizard-mini-dot.step-1.active {
        background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
        border-color: #1d4ed8;
        color: #fff;
        box-shadow:
            0 6px 16px rgba(29, 78, 216, 0.35),
            0 0 0 4px rgba(29, 78, 216, 0.15);
        transform: scale(1.08);
    }
    .wizard-mini-dot.step-2.active {
        background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
        border-color: #ea580c;
        color: #fff;
        box-shadow:
            0 6px 16px rgba(234, 88, 12, 0.35),
            0 0 0 4px rgba(234, 88, 12, 0.15);
        transform: scale(1.08);
    }
    .wizard-mini-dot.step-3.active {
        background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
        border-color: #7c3aed;
        color: #fff;
        box-shadow:
            0 6px 16px rgba(124, 58, 237, 0.35),
            0 0 0 4px rgba(124, 58, 237, 0.15);
        transform: scale(1.08);
    }
    .wizard-mini-dot.step-4.active {
        background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
        border-color: #0d9488;
        color: #fff;
        box-shadow:
            0 6px 16px rgba(13, 148, 136, 0.35),
            0 0 0 4px rgba(13, 148, 136, 0.15);
        transform: scale(1.08);
    }
    .wizard-mini-label {
        font-size: 11px;
        color: #64748b;
        font-weight: 700;
        text-align: center;
        letter-spacing: 0.3px;
    }
    .wizard-mini-line {
        height: 3px;
        width: 72px;
        margin-top: 14px;
        background: #e5e7eb;
        border-radius: 999px;
        transition: all 0.3s ease;
    }
    .wizard-mini-line.step-1.done {
        background: linear-gradient(90deg, #16a34a 0%, #22c55e 100%);
        box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);
    }
    .wizard-mini-line.step-2.done {
        background: linear-gradient(90deg, #16a34a 0%, #22c55e 100%);
        box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);
    }
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
        box-shadow: 
            0 4px 14px rgba(15, 23, 42, 0.05),
            0 1px 4px rgba(15, 23, 42, 0.03);
    }
    .wizard-action-title {
        margin: 0 0 6px;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.01em;
    }
    .wizard-action-subtitle {
        margin: 0 0 10px;
        font-size: 12px;
        color: #64748b;
    }
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
    .wizard-doc-check-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        font-size: 13px;
        color: #1f2937;
    }
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
    #rejectModal {
        z-index: 1100;
    }
    #rejectModal .modal {
        width: min(92vw, 420px);
        max-width: 420px;
        padding: 18px;
        animation: rejectModalIn 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes rejectModalIn {
        from {
            opacity: 0;
            transform: translateY(8px) scale(0.96);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .reject-modal-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }
    .reject-modal-icon {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        background: #fee2e2;
        color: #b91c1c;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .reject-modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #991b1b;
    }
    .reject-modal-context {
        margin: 0 0 14px;
        color: #475569;
        font-size: 13px;
        line-height: 1.55;
    }
    .reject-modal-context strong {
        color: #111827;
    }
    .reject-modal-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #991b1b;
        margin-bottom: 6px;
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
    .reject-modal-error {
        margin-top: 8px;
        font-size: 12px;
        color: #b91c1c;
        display: none;
    }
    @media (max-width: 640px) {
        #viewModal .modal {
            width: calc(100vw - 20px);
            padding: 0;
        }
        #viewModal .view-modal-topbar {
            padding: 14px 16px;
        }
        #viewModal #viewContent {
            padding: 14px 14px 14px;
        }
        .view-modal-title-main {
            font-size: 16px;
        }
        .view-modal-header {
            flex-direction: column;
        }
        .contract-grid {
            grid-template-columns: 1fr;
        }
        .view-modal-grid {
            grid-template-columns: 1fr;
        }
        .view-modal-doc-row {
            align-items: flex-start;
            flex-direction: column;
        }
        .view-modal-doc-right {
            justify-content: flex-start;
        }
        .view-modal-actions {
            margin: 14px -14px 0;
            padding: 12px 14px 14px;
        }
    }
    .admin-upload-divider {
        margin: 14px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .admin-upload-divider::before,
    .admin-upload-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #d1d5db;
    }
    .admin-upload-all {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 12px;
        background: #ffffff;
    }
    .admin-upload-all-title {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }
    .admin-upload-all-subtitle {
        margin: 4px 0 10px;
        font-size: 12px;
        color: #6b7280;
    }
    .admin-upload-all-row {
        display: grid;
        grid-template-columns: 130px 1fr;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }
    .admin-upload-all-row:last-of-type {
        margin-bottom: 0;
    }
    .admin-upload-all-label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
    }
    .admin-upload-all-label .required {
        color: #dc2626;
    }
    .admin-upload-feedback {
        margin-top: 10px;
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
        display: none;
    }
    .admin-upload-feedback.error {
        display: block;
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    .admin-upload-feedback.success {
        display: block;
        background: #dcfce7;
        border: 1px solid #86efac;
        color: #166534;
    }
    .admin-upload-all .btn {
        width: 100%;
        justify-content: center;
        margin-top: 12px;
    }
    tbody tr.hidden {
        display: none;
    }

    /* === Consistent button icons === */
    .btn svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }
    .btn-sm svg {
        width: 14px;
        height: 14px;
    }
    .btn:focus-visible {
        outline: 2px solid var(--green);
        outline-offset: 2px;
    }

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
        color: #64748b;
    }
    #partnerships-data-table .business-badge {
        font-size: 11.5px;
        padding: 3px 9px;
    }
    #partnerships-data-table .contact-number {
        font-size: 12.5px;
        color: #1e293b;
        white-space: nowrap;
    }
    #partnerships-data-table .contact-empty {
        color: #94a3b8;
        font-size: 12.5px;
    }
    #partnerships-data-table .submitted-date {
        font-weight: 600;
        font-size: 12.5px;
        white-space: nowrap;
    }
    #partnerships-data-table .submitted-time {
        font-size: 11px;
        color: #94a3b8;
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

    /* Centered actions column */
    #partnerships-data-table th.actions-col,
    #partnerships-data-table td.actions-col {
        text-align: center;
    }
    #partnerships-data-table td.actions-col .action-btns {
        justify-content: center;
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
                                <div class="action-btns">
                                    <button
                                        type="button"
                                        class="btn btn-outline btn-sm"
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px;color:#94a3b8;">
                                No partnership applications found.
                            </td>
                        </tr>
                    @endforelse
                    <tr id="partnerships-no-results-row" style="display:none;">
                        <td colspan="6" style="text-align:center;padding:48px;color:#94a3b8;">
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

    <!-- View Modal -->
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

    <!-- Reject Application Modal -->
    <div class="modal-backdrop" id="rejectModal">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle">
            <div class="reject-modal-header">
                <span class="reject-modal-icon" aria-hidden="true">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-7.18 12.43A2 2 0 004.82 19h14.36a2 2 0 001.71-2.71L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </span>
                <h3 id="rejectModalTitle" class="reject-modal-title">Reject Application</h3>
            </div>
            <p class="reject-modal-context">
                You are rejecting the application of <strong id="rejectModalApplicantName">Applicant</strong>. This action will notify them via email.
            </p>

            <label class="reject-modal-label" for="rejectModalReason">Rejection Reason (required)</label>
            <textarea id="rejectModalReason" class="reject-modal-textarea" maxlength="500" placeholder="Enter rejection reason..."></textarea>
            <div id="rejectModalError" class="reject-modal-error"></div>

            <div class="modal-btns" style="margin-top:14px;">
                <button type="button" class="btn btn-outline" onclick="closeRejectModal()">Cancel</button>
                <button type="button" class="btn btn-red" onclick="submitRejection()">Confirm Reject</button>
            </div>
        </div>
    </div>

    <!-- Approve Partnership Modal -->
    <div class="modal-backdrop" id="approveModal">
        <div class="modal">
            <h3>Approve Application</h3>
            <p style="font-size:13px;color:#64748b;margin-bottom:12px;">Set contract dates before approving. If dates are already set, they can be reused.</p>
            <form id="approveForm" method="POST">
                @csrf
                @method('PATCH')

                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Contract Start Date</label>
                <input type="date" name="contract_period_start" id="approveContractStart" style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">

                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin:12px 0 6px;">Contract End Date</label>
                <input type="date" name="contract_period_end" id="approveContractEnd" style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;">

                <div id="contractWarning" class="contract-warning" style="display:none;">
                    Contract period not set — you will be prompted to set it before approval.
                </div>

                <div class="modal-btns">
                    <button type="button" class="btn btn-outline" onclick="closeApproveModal()">Cancel</button>
                    <button type="submit" class="btn btn-green">Confirm Approval</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script id="applicationsData" type="application/json">@json($applications->items())</script>
<script id="contractPeriodSavedData" type="application/json">@json((bool) session('contract_period_saved', false))</script>
<script>
    const applications = JSON.parse(document.getElementById('applicationsData')?.textContent || '[]');
    const contractPeriodSaved = JSON.parse(document.getElementById('contractPeriodSavedData')?.textContent || 'false');
    let activeRejectApplicationId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    const wizardRoutes = {
        approveLoi: (id) => `{{ route('admin.partnerships.wizard.approve-loi', ['application' => '__ID__']) }}`.replace('__ID__', id),
        rejectLoi: (id) => `{{ route('admin.partnerships.wizard.reject-loi', ['application' => '__ID__']) }}`.replace('__ID__', id),
        approveForm: (id) => `{{ route('admin.partnerships.wizard.approve-form', ['application' => '__ID__']) }}`.replace('__ID__', id),
        rejectForm: (id) => `{{ route('admin.partnerships.wizard.reject-form', ['application' => '__ID__']) }}`.replace('__ID__', id),
        tickDoc: (id) => `{{ route('admin.partnerships.wizard.tick-doc', ['application' => '__ID__']) }}`.replace('__ID__', id),
        finalApprove: (id) => `{{ route('admin.partnerships.wizard.final-approve', ['application' => '__ID__']) }}`.replace('__ID__', id),
    };

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

    (function refreshAfterContractPeriodSave() {
        if (!contractPeriodSaved) {
            return;
        }

        window.location.reload();
    })();

    filterRows();

    function getWizardStatusConfig(wizardStatus, appStatus) {
        if (wizardStatus === 'final_approved' || appStatus === 'approved') {
            return { color: 'green', message: 'Application fully approved' };
        }

        const map = {
            loi_pending: { color: 'gray', message: 'Waiting for concessionaire to upload LOI' },
            loi_submitted: { color: 'amber', message: 'LOI submitted - Action Required' },
            loi_rejected: { color: 'red', message: 'LOI rejected by admin - Awaiting resubmission' },
            form_pending: { color: 'gray', message: 'Waiting for concessionaire to fill application form' },
            form_submitted: { color: 'amber', message: 'Application form submitted - Action Required' },
            form_rejected: { color: 'red', message: 'Form rejected by admin - Awaiting resubmission' },
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
                `<a href="${url}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:#ecfdf5;color:#065f46;border:1px solid #6ee7b7;border-radius:999px;font-size:12px;font-weight:700;text-decoration:none;transition:all 0.15s ease;">📎 ${label}</a>`;
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

    function openRejectModal(id) {
        const app = applications.find((item) => item.id === id);
        if (!app) {
            return;
        }

        const fullName = `${app.first_name || ''} ${app.middle_name || ''} ${app.last_name || ''}`
            .replace(/\s+/g, ' ')
            .trim() || app.business_name || 'this applicant';

        const applicantName = document.getElementById('rejectModalApplicantName');
        const textarea = document.getElementById('rejectModalReason');
        const error = document.getElementById('rejectModalError');

        activeRejectApplicationId = id;

        if (applicantName) {
            applicantName.textContent = fullName;
        }

        if (textarea) {
            textarea.value = '';
            textarea.disabled = false;
        }

        if (error) {
            error.textContent = '';
            error.style.display = 'none';
        }

        document.getElementById('rejectModal').classList.add('active');

        if (textarea) {
            setTimeout(() => textarea.focus(), 80);
        }
    }

    function closeRejectModal() {
        const error = document.getElementById('rejectModalError');
        const textarea = document.getElementById('rejectModalReason');

        document.getElementById('rejectModal').classList.remove('active');
        activeRejectApplicationId = null;

        if (error) {
            error.textContent = '';
            error.style.display = 'none';
        }

        if (textarea) {
            textarea.disabled = false;
        }
    }

    async function submitRejection(id = activeRejectApplicationId) {
        const textarea = document.getElementById('rejectModalReason');
        const error = document.getElementById('rejectModalError');
        const reason = textarea?.value?.trim() ?? '';

        if (!textarea || !id) {
            return;
        }

        if (!reason) {
            if (error) {
                error.textContent = 'Rejection reason is required.';
                error.style.display = 'block';
            }
            return;
        }

        if (error) {
            error.textContent = '';
            error.style.display = 'none';
        }

        textarea.disabled = true;

        const formData = new FormData();
        formData.append('rejection_reason', reason);
        formData.append('_token', '{{ csrf_token() }}');

        try {
            const response = await fetch(`/admin/partnerships/${id}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.success) {
                const message = data.message || 'Rejection failed. Please try again.';
                if (error) {
                    error.textContent = message;
                    error.style.display = 'block';
                }
                return;
            }

            window.location.reload();
        } catch (err) {
            if (error) {
                error.textContent = 'Rejection failed. Please check your connection and try again.';
                error.style.display = 'block';
            }
        } finally {
            textarea.disabled = false;
        }
    }

    function openApproveModal(id) {
        const approveForm = document.getElementById('approveForm');
        const startInput = document.getElementById('approveContractStart');
        const endInput = document.getElementById('approveContractEnd');

        approveForm.action = '/admin/partnerships/' + id + '/approve';
        startInput.value = '';
        endInput.value = '';
        approveForm.submit();
    }

    function closeApproveModal() {
        const approveForm = document.getElementById('approveForm');
        document.getElementById('approveModal').classList.remove('active');
        approveForm.reset();
    }

    document.getElementById('viewModal').addEventListener('click', function(e) {
        if (e.target === this) closeViewModal();
    });
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
    document.getElementById('approveModal').addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });
</script>
@endsection
