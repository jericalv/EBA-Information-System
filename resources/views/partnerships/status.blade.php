<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Partnership Application Status | EBA Information System</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fa; color: #1f2937; }
        .wrap { max-width: 860px; margin: 28px auto; padding: 0 16px 28px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .title { margin: 0 0 8px; font-size: 24px; }
        .muted { color: #6b7280; font-size: 14px; }
        .badge { display: inline-flex; align-items: center; padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-registered { background: #dbeafe; color: #1d4ed8; }
        .row { margin-bottom: 10px; font-size: 14px; }
        .label { color: #6b7280; width: 180px; display: inline-block; }
        .alert { padding: 12px 14px; border-radius: 8px; margin-bottom: 12px; font-size: 14px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .bookmark-bar {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 12px;
            color: #1e3a8a;
            font-size: 13px;
        }
        .bookmark-bar a {
            color: #1d4ed8;
            text-decoration: underline;
            font-weight: 700;
            word-break: break-all;
        }
        .btn { display: inline-flex; text-decoration: none; border: 0; cursor: pointer; border-radius: 8px; padding: 10px 14px; font-size: 14px; font-weight: 700; }
        .btn-primary { background: #0A5C2F; color: #fff; }
        .btn-outline { background: #fff; color: #0A5C2F; border: 1px solid #0A5C2F; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 16px; }
        .file-link { color: #0A5C2F; font-weight: 700; text-decoration: none; }
        .file-link:hover { text-decoration: underline; }
        .note { margin-top: 10px; color: #6b7280; font-size: 13px; }
        .field { margin-bottom: 12px; }
        input[type="file"] { width: 100%; max-width: 480px; }
        .doc-list { margin-top: 14px; display: grid; gap: 12px; }
        .doc-item { border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; }
        .doc-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .doc-name { font-weight: 700; color: #1f2937; }
        .doc-status { font-size: 13px; color: #4b5563; }
        .doc-actions { margin-top: 10px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .dot-ok { color: #166534; font-weight: 700; }
        .dot-missing { color: #991b1b; font-weight: 700; }
        .dot-optional { color: #6b7280; font-weight: 700; }
        .upload-inline { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .upload-inline input[type="file"] { max-width: 290px; }
    </style>
</head>
<body>
@include('partials.pending-application-banner')

<div class="wrap">
    @if (session('success'))
        <div class="alert alert-success">
            <div>{{ session('success') }}</div>
            <div style="margin-top:6px;">
                Track your application status at:
                <a href="{{ route('application') }}" style="color:#166534;text-decoration:underline;font-weight:700;">{{ route('application') }}</a>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="bookmark-bar">
        Bookmark this page to track your application anytime.
        <a href="{{ route('application') }}">{{ route('application') }}</a>
    </div>

    <div class="card">
        <h1 class="title">Partnership Application Status</h1>
        <p class="muted">Your concessionaire account stays locked until faculty/staff approves your application.</p>
        <div class="alert alert-error" style="margin-top:12px;">
            Upload your signed <strong>MOA</strong> and <strong>Contract</strong> below. While pending approval, access to concessionaire tools is disabled.
        </div>
    </div>

    @if (!$application)
        <div class="card">
            <p>Your pending concessionaire application is being prepared. Please refresh this page in a moment.</p>
            <div class="actions">
                <a class="btn btn-primary" href="{{ route('application') }}">Refresh Status</a>
                <a class="btn btn-outline" href="{{ route('home') }}">Back to Home</a>
            </div>
        </div>
    @else
        @php
            $badgeClass = match($application->status) {
                'pending' => 'badge-pending',
                'approved' => 'badge-approved',
                'rejected' => 'badge-rejected',
                'registered' => 'badge-registered',
                default => 'badge-pending',
            };

            $statusText = match($application->status) {
                'pending' => 'Under Review',
                'approved' => 'Approved',
                'rejected' => 'Rejected' . ($application->rejection_reason ? ' - ' . $application->rejection_reason : ''),
                'registered' => 'Active Concessionaire',
                default => ucfirst($application->status),
            };
        @endphp

        <div class="card">
            <div class="row">
                <span class="label">Status</span>
                <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
            </div>
            <div class="row"><span class="label">Business Name</span>{{ $application->business_name }}</div>
            <div class="row"><span class="label">Submitted</span>{{ $application->created_at?->format('M d, Y h:i A') }}</div>

            @php
                $letterPath = $application->letter_of_intent_path ?: $application->letter_of_intent;
            @endphp

            @if ($letterPath)
                <div class="row">
                    <span class="label">Current Document</span>
                    <a class="file-link" target="_blank" href="{{ asset('storage/' . $letterPath) }}">Download Letter of Intent</a>
                </div>
            @endif

            @if ($application->status === 'rejected' && $application->rejection_reason)
                <div class="row"><span class="label">Rejection Reason</span>{{ $application->rejection_reason }}</div>
            @endif

            <hr style="margin:16px 0;border:0;border-top:1px solid #e5e7eb;">
            <h3 style="margin-top:0;">Signed Documents Checklist</h3>
            <p class="note">Track your required documents and upload missing files while your application is under review.</p>

            <div class="doc-list">
                <div class="doc-item">
                    <div class="doc-head">
                        <div class="doc-name">Memorandum of Agreement (MOA)</div>
                        <div class="doc-status">
                            @if ($application->moa_path)
                                <span class="dot-ok">&#x2705; Uploaded</span>
                            @else
                                <span class="dot-missing">&#x274C; Not yet uploaded</span>
                            @endif
                        </div>
                    </div>
                    <div class="doc-actions">
                        @if ($application->moa_path)
                            <a class="file-link" target="_blank" href="{{ asset('storage/' . $application->moa_path) }}">Download MOA</a>
                        @endif

                        @if (in_array($application->status, ['pending', 'rejected'], true))
                            <form method="POST" action="{{ route('partnership.documents.upload') }}" enctype="multipart/form-data" class="upload-inline">
                                @csrf
                                <input type="hidden" name="document_type" value="moa">
                                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                                <button class="btn btn-primary" type="submit">Upload MOA</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="doc-item">
                    <div class="doc-head">
                        <div class="doc-name">Contract</div>
                        <div class="doc-status">
                            @if ($application->contract_path)
                                <span class="dot-ok">&#x2705; Uploaded</span>
                            @else
                                <span class="dot-missing">&#x274C; Not yet uploaded</span>
                            @endif
                        </div>
                    </div>
                    <div class="doc-actions">
                        @if ($application->contract_path)
                            <a class="file-link" target="_blank" href="{{ asset('storage/' . $application->contract_path) }}">Download Contract</a>
                        @endif

                        @if (in_array($application->status, ['pending', 'rejected'], true))
                            <form method="POST" action="{{ route('partnership.documents.upload') }}" enctype="multipart/form-data" class="upload-inline">
                                @csrf
                                <input type="hidden" name="document_type" value="contract">
                                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                                <button class="btn btn-primary" type="submit">Upload Contract</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="doc-item">
                    <div class="doc-head">
                        <div class="doc-name">Letter of Intent (Optional)</div>
                        <div class="doc-status">
                            @if ($letterPath)
                                <span class="dot-ok">&#x2705; Uploaded</span>
                            @else
                                <span class="dot-optional">&#x26AA; Not uploaded (optional)</span>
                            @endif
                        </div>
                    </div>
                    <div class="doc-actions">
                        @if ($letterPath)
                            <a class="file-link" target="_blank" href="{{ asset('storage/' . $letterPath) }}">Download Letter of Intent</a>
                        @endif

                        @if (in_array($application->status, ['pending', 'rejected'], true))
                            <form method="POST" action="{{ route('partnership.documents.upload') }}" enctype="multipart/form-data" class="upload-inline">
                                @csrf
                                <input type="hidden" name="document_type" value="letter_of_intent">
                                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                                <button class="btn btn-primary" type="submit">Upload Letter of Intent</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="actions">
                <a class="btn btn-outline" href="{{ route('home') }}">Back to Home</a>
            </div>
        </div>
    @endif
</div>
</body>
</html>
