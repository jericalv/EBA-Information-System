@extends('concessionaire.layout')

@section('title', 'MOAs & Contracts')

@section('extra-css')
<style>
    .moa-container {
        --green: #0a5c2f;
        --green-light: #117a42;
        --gold: #d4a843;
        
        max-width: 800px;
        margin: 32px auto;
        padding: 0 24px;
    }
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .section-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: var(--green);
    }
    .moa-card {
        background: #fff;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .doc-title {
        margin: 0 0 14px;
        font-size: 19px;
        font-weight: 800;
        color: #0f172a;
    }
    .section-gap {
        margin-top: 20px;
    }
    .moa-info {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 16px;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .info-label {
        font-weight: 600;
        color: #6b7280;
        font-size: 14px;
    }
    .info-value {
        font-weight: 700;
        color: #1f2937;
        font-size: 15px;
    }
    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }
    .status-active {
        background: rgba(10,92,47,0.1);
        color: var(--green);
    }
    .status-pending {
        background: rgba(212,168,67,0.2);
        color: #b8860b;
    }
    .status-expired {
        background: rgba(220,38,38,0.1);
        color: #dc2626;
    }
    .btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
    }
    .btn-primary {
        background: var(--green);
        color: #fff;
    }
    .btn-primary:hover {
        background: var(--green-light);
    }
    .btn-outline {
        background: transparent;
        border: 2px solid var(--green);
        color: var(--green);
    }
    .btn-outline:hover {
        background: var(--green);
        color: #fff;
    }
    .moa-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-state svg {
        width: 80px;
        height: 80px;
        color: #d1d5db;
        margin-bottom: 16px;
    }
    .empty-state h3 {
        font-size: 20px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
    }
    .empty-state p {
        color: #9ca3af;
        margin-bottom: 20px;
    }
    .notice-box {
        background: rgba(212,168,67,0.1);
        border-left: 4px solid var(--gold);
        padding: 16px 20px;
        border-radius: 0 10px 10px 0;
        margin-top: 24px;
    }
    .notice-box h4 {
        font-weight: 700;
        color: #92400e;
        margin-bottom: 4px;
        font-size: 14px;
    }
    .notice-box p {
        color: #78350f;
        font-size: 13px;
        line-height: 1.5;
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
                    <a class="btn btn-outline" href="{{ asset('storage/' . ltrim($moaPath, '/')) }}" target="_blank" rel="noopener">
                        View Document
                    </a>
                    <a class="btn btn-outline" href="{{ route('application') }}#my-application">
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
                    <a class="btn btn-outline" href="{{ asset('storage/' . ltrim($contractPath, '/')) }}" target="_blank" rel="noopener">
                        View Document
                    </a>
                    <a class="btn btn-outline" href="{{ route('application') }}#my-application">
                        Replace Document
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection