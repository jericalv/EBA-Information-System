@extends('faculty.layout')

@section('title', 'Review Partnership Application')

@section('extra-css')
<style>
    .grid { display: grid; grid-template-columns: 1.1fr .9fr; gap: 16px; }
    .panel { background: var(--card); border: 1px solid var(--line); border-radius: 14px; padding: 18px; }
    .label { font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; font-weight: 700; }
    .value { font-size: 14px; color: var(--ink); margin-bottom: 12px; }
    .badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 4px 10px; font-size: 12px; font-weight: 700; }
    .badge-pending { background: rgba(245,158,11,0.1); color: #d97706; }
    .badge-approved { background: var(--hover-2); color: var(--ink); }
    .badge-rejected { background: rgba(239,68,68,0.1); color: #dc2626; }
    .badge-registered { background: rgba(59,130,246,0.1); color: #2563eb; }
    .badge-expired { background: var(--hover-2); color: var(--muted); }
    .badge-rec-approve { background: var(--hover-2); color: var(--ink); }
    .badge-rec-reject { background: #fee2e2; color: #991b1b; }
    html[data-theme="dark"] .badge-pending { background: rgba(227, 164, 72, 0.14); color: #E9C288; }
    html[data-theme="dark"] .badge-rejected,
    html[data-theme="dark"] .badge-rec-reject { background: rgba(227, 106, 106, 0.14); color: #F0A0A0; }
    html[data-theme="dark"] .badge-registered { background: rgba(96, 165, 250, 0.14); color: #9CC4F8; }
    .proposal { background: var(--hover); border: 1px solid var(--line); border-radius: 8px; padding: 12px; font-size: 14px; line-height: 1.45; color: var(--ink); }
    .upload-box { border: 1px solid var(--line); border-radius: 10px; padding: 12px; background: var(--hover); margin-bottom: 12px; }
    .upload-row { display: grid; grid-template-columns: 1fr auto; gap: 8px; align-items: center; }
    .upload-row input[type='file'] { width: 100%; }
    .field { display: grid; gap: 8px; margin-bottom: 12px; }
    .field textarea { width: 100%; min-height: 96px; border: 1px solid var(--line-strong); border-radius: 6px; padding: 10px 12px; font: inherit; background: var(--field); color: var(--ink); }
    .field textarea:focus { outline: none; border-color: var(--pine); box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.12); }
    .field textarea[readonly] { background: var(--hover); color: var(--ink); }
    .choice { display: flex; gap: 14px; align-items: center; flex-wrap: wrap; }
    @media (max-width: 900px) { .grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
    <h1 class="page-title">Review Application #{{ $application->id }}</h1>
    <p class="page-subtitle">Review details, upload partnership documents, and submit your recommendation for admin decision.</p>

    <div style="margin-bottom:14px;">
        <a href="{{ route('staff.partnerships.index') }}" class="btn btn-outline">Back to Partnerships</a>
    </div>

    <div class="grid">
        <div class="panel">
            <div class="label">Applicant</div>
            <div class="value"><strong>{{ $application->full_name }}</strong> ({{ $application->email }})</div>

            <div class="label">Business Name</div>
            <div class="value">{{ $application->business_name }}</div>

            <div class="label">Current Status</div>
            <div class="value">
                @php
                    $statusClass = match ($application->status) {
                        'pending' => 'badge-pending',
                        'approved' => 'badge-approved',
                        'rejected' => 'badge-rejected',
                        'registered' => 'badge-registered',
                        'expired' => 'badge-expired',
                        default => 'badge-expired',
                    };
                @endphp
                <span class="badge {{ $statusClass }}">{{ ucfirst($application->status) }}</span>
            </div>

            <div class="label">Proposal</div>
            <div class="proposal">{{ $application->proposal }}</div>
        </div>

        <div class="panel">
            <h3 style="margin:0 0 10px;color:var(--ink);">Upload Documents</h3>

            <form method="POST" action="{{ route('staff.partnerships.upload-document', $application->id) }}" enctype="multipart/form-data" class="upload-box">
                @csrf
                <input type="hidden" name="document_type" value="moa">
                <div class="upload-row">
                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                    <button type="submit" class="btn btn-green">Upload MOA</button>
                </div>
                @if ($application->moa_path)
                    <div style="font-size:12px;color:var(--muted);margin-top:8px;">Current: <a href="{{ asset('storage/' . $application->moa_path) }}" target="_blank">View MOA</a></div>
                @endif
            </form>

            <form method="POST" action="{{ route('staff.partnerships.upload-document', $application->id) }}" enctype="multipart/form-data" class="upload-box">
                @csrf
                <input type="hidden" name="document_type" value="contract">
                <div class="upload-row">
                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                    <button type="submit" class="btn btn-green">Upload Contract</button>
                </div>
                @if ($application->contract_path)
                    <div style="font-size:12px;color:var(--muted);margin-top:8px;">Current: <a href="{{ asset('storage/' . $application->contract_path) }}" target="_blank">View Contract</a></div>
                @endif
            </form>

            <form method="POST" action="{{ route('staff.partnerships.upload-document', $application->id) }}" enctype="multipart/form-data" class="upload-box">
                @csrf
                <input type="hidden" name="document_type" value="letter_of_intent">
                <div class="upload-row">
                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                    <button type="submit" class="btn btn-green">Upload LOI</button>
                </div>
                @php
                    $loiPath = $application->letter_of_intent_path ?: $application->letter_of_intent;
                @endphp
                @if ($loiPath)
                    <div style="font-size:12px;color:var(--muted);margin-top:8px;">Current: <a href="{{ asset('storage/' . $loiPath) }}" target="_blank">View LOI</a></div>
                @endif
            </form>
        </div>
    </div>

    <div class="panel" style="margin-top:16px;">
        <h3 style="margin:0 0 12px;color:var(--ink);">Faculty Recommendation</h3>

        @if ($application->reviewed_by)
            @php
                $recommendationBadgeClass = $application->faculty_recommendation === 'recommend_approval'
                    ? 'badge-rec-approve'
                    : 'badge-rec-reject';
                $recommendationLabel = $application->faculty_recommendation === 'recommend_approval'
                    ? 'Recommend Approval'
                    : 'Recommend Rejection';
            @endphp
            <div style="margin-bottom:10px;">
                <span class="badge {{ $recommendationBadgeClass }}">{{ $recommendationLabel }}</span>
                @if ($application->reviewer)
                    <span style="font-size:13px;color:var(--muted);">by {{ $application->reviewer->name }}</span>
                @endif
            </div>
            <div class="field">
                <label class="label" style="margin:0;text-transform:none;letter-spacing:0;">Faculty Notes</label>
                <textarea readonly>{{ $application->faculty_notes }}</textarea>
            </div>
        @else
            <form method="POST" action="{{ route('staff.partnerships.recommend', $application->id) }}">
                @csrf
                <div class="field">
                    <label class="label" style="margin:0;text-transform:none;letter-spacing:0;">Recommendation</label>
                    <div class="choice">
                        <label><input type="radio" name="faculty_recommendation" value="recommend_approval" required> Recommend Approval</label>
                        <label><input type="radio" name="faculty_recommendation" value="recommend_rejection" required> Recommend Rejection</label>
                    </div>
                </div>

                <div class="field">
                    <label class="label" style="margin:0;text-transform:none;letter-spacing:0;">Notes</label>
                    <textarea name="faculty_notes" placeholder="Add faculty notes for the admin review context..."></textarea>
                </div>

                <button type="submit" class="btn btn-green">Submit Recommendation</button>
            </form>
        @endif
    </div>
@endsection
