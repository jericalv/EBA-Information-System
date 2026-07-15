@extends('concessionaire.layout')

@section('title', 'MOAs & Contracts')

@section('extra-css')
<style>
    .moa-container {
        max-width: 780px;
        margin: 0 auto;
    }
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .section-header h2 {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--ink);
    }
    .moa-card {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 12px;
        box-shadow: var(--shadow-card);
        padding: 24px 26px;
    }
    .doc-title {
        margin: 0 0 16px;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--ink);
    }
    .section-gap {
        margin-top: 20px;
    }
    .moa-info {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--line);
    }
    .info-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .info-label {
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--muted);
    }
    .info-value {
        font-weight: 600;
        color: var(--ink);
        font-size: 13.5px;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 11px;
        border-radius: 6px;
        border: 1px solid;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 500;
    }
    .status-active {
        background: #F0F7F2;
        border-color: #CDE3D4;
        color: #14532D;
    }
    .status-pending {
        background: #FDF8EC;
        border-color: #F0E1BC;
        color: #92400E;
    }
    .status-expired {
        background: #FDF3F3;
        border-color: #F2D8D8;
        color: var(--danger);
    }
    html[data-theme="dark"] .status-active {
        background: rgba(123, 211, 160, 0.10);
        border-color: rgba(123, 211, 160, 0.30);
        color: #A9E4C2;
    }
    html[data-theme="dark"] .status-pending {
        background: rgba(227, 164, 72, 0.10);
        border-color: rgba(227, 164, 72, 0.32);
        color: #EEC084;
    }
    html[data-theme="dark"] .status-expired {
        background: rgba(227, 106, 106, 0.12);
        border-color: rgba(227, 106, 106, 0.35);
        color: #F0A0A0;
    }
    .moa-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    .empty-state {
        text-align: center;
        padding: 48px 20px;
    }
    .empty-state svg {
        width: 56px;
        height: 56px;
        color: var(--line-strong);
        margin: 0 auto 14px;
    }
    .empty-state h3 {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--ink);
        margin-bottom: 6px;
    }
    .empty-state p {
        color: var(--muted);
        font-size: 13.5px;
        margin-bottom: 18px;
    }
    .notice-box {
        background: #FDF8EC;
        border: 1px solid #F0E1BC;
        padding: 14px 18px;
        border-radius: 8px;
        margin-top: 20px;
    }
    html[data-theme="dark"] .notice-box {
        background: rgba(227, 164, 72, 0.10);
        border-color: rgba(227, 164, 72, 0.32);
    }
    html[data-theme="dark"] .notice-box h4,
    html[data-theme="dark"] .notice-box p {
        color: #EEC084;
    }
    .notice-box h4 {
        font-weight: 700;
        color: #92400E;
        margin-bottom: 4px;
        font-size: 13.5px;
    }
    .notice-box p {
        color: #92400E;
        font-size: 13px;
        line-height: 1.5;
        opacity: 0.85;
    }
</style>
@endsection

@section('content')
    <div class="moa-container">
        <div class="section-header">
            <h2>MOAs &amp; Contracts</h2>
        </div>

        <div class="moa-card">
            <h3 class="doc-title">Memorandum of Agreement (MOA)</h3>

            @if (! $moaPath)
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <h3>No MOA Uploaded</h3>
                    <p>Upload your Memorandum of Agreement document from your application page.</p>
                    <a class="btn btn-primary" href="{{ route('application') }}#my-application">Go to My Application</a>
                </div>
            @else
                <div class="moa-info">
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge status-active">Uploaded</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Document Name</span>
                        <span class="info-value">{{ basename($moaPath) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Uploaded Date</span>
                        <span class="info-value">{{ $application?->updated_at?->format('F d, Y h:i A') ?? '-' }}</span>
                    </div>
                </div>

                <div class="moa-actions">
                    <a class="btn btn-secondary" href="{{ asset('storage/' . ltrim($moaPath, '/')) }}" target="_blank" rel="noopener">
                        View Document
                    </a>
                    <a class="btn btn-secondary" href="{{ route('application') }}#my-application">
                        Replace Document
                    </a>
                </div>
            @endif
        </div>

        <div class="moa-card section-gap">
            <h3 class="doc-title">Contract</h3>

            @if (! $contractPath)
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <h3>No Contract Uploaded</h3>
                    <p>Upload your contract document from your application page.</p>
                    <a class="btn btn-primary" href="{{ route('application') }}#my-application">Go to My Application</a>
                </div>
            @else
                <div class="moa-info">
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge status-active">Uploaded</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Document Name</span>
                        <span class="info-value">{{ basename($contractPath) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Uploaded Date</span>
                        <span class="info-value">{{ $application?->updated_at?->format('F d, Y h:i A') ?? '-' }}</span>
                    </div>
                </div>

                <div class="moa-actions">
                    <a class="btn btn-secondary" href="{{ asset('storage/' . ltrim($contractPath, '/')) }}" target="_blank" rel="noopener">
                        View Document
                    </a>
                    <a class="btn btn-secondary" href="{{ route('application') }}#my-application">
                        Replace Document
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection