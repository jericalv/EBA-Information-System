<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Wizard | EBA Information System</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --g:     #0A5C2F;
            --g1:    #0d6b37;
            --cream: #f0ece3;
            --cream2:#e8e3d8;
            --paper: #faf9f6;
            --white: #ffffff;
            --ink:   #171717;
            --ink2:  #3a3a3a;
            --muted: #888070;
            --rule:  #ddd8ce;
            --inp-border: #ccc7b8;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--cream);
            color: var(--ink2);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }

        /* ─── Navbar ─────────────────────────────────────── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        .navbar.scrolled { box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08); }
        .nav-container {
            max-width: 1280px; margin: 0 auto; padding: 0 24px;
            display: flex; align-items: center; justify-content: space-between;
            height: 72px;
        }
        .nav-brand { display: flex; align-items: center; gap: 14px; text-decoration: none; color: var(--ink); }
        .nav-brand img { width: 44px; height: 44px; border-radius: 10px; object-fit: contain; }
        .nav-brand-text { display: flex; flex-direction: column; }
        .nav-brand-title { font-size: 16px; font-weight: 800; letter-spacing: -0.2px; color: var(--g); line-height: 1.2; }
        .nav-brand-subtitle { font-size: 11px; font-weight: 500; color: var(--muted); letter-spacing: 0.3px; }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-links a {
            text-decoration: none; font-size: 14px; font-weight: 500; color: var(--muted);
            padding: 8px 16px; border-radius: 8px; transition: all 0.2s ease;
        }
        .nav-links a:hover { color: var(--g); background: rgba(10,92,47,.06); }
        .nav-links a.active { color: var(--g); background: rgba(10,92,47,.1); font-weight: 700; }
        .nav-links .btn-login { color: var(--g); font-weight: 600; }
        .nav-links .btn-register {
            background: var(--g); color: var(--white); font-weight: 600;
            padding: 8px 20px; border-radius: 8px;
        }
        .nav-links .btn-register:hover { background: var(--g1); color: var(--white); }
        .nav-links .btn-logout {
            background: transparent; color: #dc2626; font-weight: 600;
            padding: 8px 20px; border: 2px solid #dc2626; border-radius: 8px;
        }
        .nav-links .btn-logout:hover { background: #dc2626; color: var(--white); }
        .mobile-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; }
        .mobile-toggle span { display: block; width: 24px; height: 2px; background: var(--ink2); margin: 5px 0; border-radius: 2px; transition: all 0.3s ease; }
        .nav-mobile-actions { display: flex; align-items: center; gap: 10px; }
        .nav-profile-mobile { display: none; }

        /* ─── Page shell ─────────────────────────────────── */
        .shell { padding-top: 72px; }

        /* ─── Top chapter band ───────────────────────────── */
        .chapter-band {
            background: var(--g);
            padding: 44px 24px 40px;
            text-align: center;
        }
        .chapter-step-label {
            display: inline-block;
            font-size: 10px; font-weight: 800; letter-spacing: 2px;
            text-transform: uppercase; color: rgba(255,255,255,.55);
            margin-bottom: 10px;
        }
        .chapter-title {
            font-size: clamp(24px, 4vw, 34px);
            font-weight: 800; color: #fff; letter-spacing: -.5px; line-height: 1.1;
        }
        .chapter-sub {
            font-size: 14px; color: rgba(255,255,255,.65); margin-top: 8px;
        }

        /* ─── Progress bar ───────────────────────────────── */
        .progress-wrap {
            background: var(--g);
            padding-bottom: 0;
        }
        .progress-track {
            max-width: 800px; margin: 0 auto;
            display: flex; align-items: flex-end;
            padding: 0 24px;
        }
        .ps { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0; }
        .ps-top {
            display: flex; align-items: center; width: 100%; margin-bottom: 10px;
        }
        .ps-line { flex: 1; height: 2px; background: rgba(255,255,255,.2); }
        .ps-line.done { background: rgba(255,255,255,.7); }
        .ps-line.none { background: transparent; }
        .ps-dot {
            width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800;
            border: 2px solid rgba(255,255,255,.25);
            background: transparent; color: rgba(255,255,255,.4);
            letter-spacing: 0;
        }
        .ps.done .ps-dot {
            background: rgba(255,255,255,.95); border-color: rgba(255,255,255,.95);
            color: var(--g);
        }
        .ps.active .ps-dot {
            background: #fff; border-color: #fff; color: var(--g);
            box-shadow: 0 0 0 4px rgba(255,255,255,.25);
        }
        .ps-label {
            font-size: 10px; font-weight: 700; letter-spacing: .4px;
            color: rgba(255,255,255,.45); text-transform: uppercase;
            padding: 0 4px; padding-bottom: 12px; text-align: center;
            white-space: nowrap;
        }
        .ps.done .ps-label { color: rgba(255,255,255,.75); }
        .ps.active .ps-label { color: rgba(255,255,255,.95); }

        /* ─── Main content area ──────────────────────────── */
        .main {
            max-width: 800px; margin: 0 auto;
            padding: 36px 24px 72px;
        }

        /* ─── Flash messages ─────────────────────────────── */
        .flash {
            padding: 14px 18px; border-radius: 8px; margin-bottom: 24px;
            font-size: 13px; font-weight: 600;
            border-left: 3px solid;
        }
        .flash-ok { background: #f0fdf4; border-color: #16a34a; color: #166534; }
        .flash-err { background: var(--white); border-color: #dc2626; color: #991b1b; }

        /* ─── Step card ──────────────────────────────────── */
        .step-card {
            background: var(--white);
            border-radius: 14px;
            border: 1px solid var(--rule);
            box-shadow: 0 2px 24px rgba(0,0,0,.06);
            overflow: hidden;
        }
        .step-card + .step-card { margin-top: 20px; }

        /* step card inner header (secondary – within white card) */
        .card-head {
            padding: 28px 36px 22px;
            border-bottom: 1px solid #f0ece3;
            display: flex; align-items: baseline; gap: 16px;
        }
        .card-head-num {
            font-size: 52px; font-weight: 900; line-height: 1;
            color: var(--g); opacity: .08; letter-spacing: -2px;
            flex-shrink: 0; margin-top: -4px;
        }
        .card-head-info h2 { font-size: 17px; font-weight: 800; color: var(--ink); }
        .card-head-info p { font-size: 13px; color: var(--muted); margin-top: 3px; line-height: 1.5; }

        .card-body { padding: 28px 36px 36px; }

        /* ─── Status banners (no icons) ──────────────────── */
        .banner {
            border-left: 3px solid;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 28px;
        }
        .banner-title { font-size: 12px; font-weight: 800; letter-spacing: .5px; text-transform: uppercase; margin-bottom: 4px; }
        .banner-msg { font-size: 13px; line-height: 1.6; }
        .b-amber { border-color: #d97706; background: #fffbeb; color: #92400e; }
        .b-red   { border-color: #dc2626; background: #fef2f2; color: #991b1b; }
        .b-green { border-color: #16a34a; background: #f0fdf4; color: #166534; }
        .b-blue  { border-color: #2563eb; background: #eff6ff; color: #1e40af; }
        .b-violet{ border-color: #7c3aed; background: #f5f3ff; color: #5b21b6; }

        /* ─── Form sections ──────────────────────────────── */
        .fsec { margin-top: 32px; padding-top: 30px; border-top: 1px solid #ede9df; }
        .fsec:first-child { margin-top: 0; padding-top: 0; border-top: none; }
        .fsec-label {
            font-size: 9px; font-weight: 800; letter-spacing: 2px;
            text-transform: uppercase; color: var(--g);
            margin-bottom: 18px;
        }

        /* ─── Form grid & fields ─────────────────────────── */
        .fgrid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .fgrid .full { grid-column: 1 / -1; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field-label {
            font-size: 11px; font-weight: 700; color: var(--ink);
            letter-spacing: .3px; text-transform: uppercase;
        }
        .field-label em { color: #dc2626; font-style: normal; margin-left: 2px; }
        .field-note { font-size: 11px; color: var(--muted); }
        .field-err  { font-size: 12px; color: #dc2626; }

        input[type="text"], input[type="email"], input[type="tel"],
        input[type="number"], select, textarea {
            width: 100%;
            border: 1.5px solid var(--inp-border);
            border-radius: 8px;
            padding: 12px 14px;
            background: var(--paper);
            font-size: 14px; color: var(--ink);
            transition: border-color .15s, box-shadow .15s;
            font-family: inherit;
            appearance: none;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--g);
            box-shadow: 0 0 0 3px rgba(10,92,47,.1);
            background: var(--white);
        }
        input[readonly], input[disabled] {
            background: #f5f3ed; color: var(--muted); cursor: not-allowed;
        }
        textarea { resize: vertical; min-height: 120px; line-height: 1.7; }

        /* ─── Toggle chips (type of business) ───────────── */
        .chip-group { display: flex; gap: 8px; flex-wrap: wrap; }
        .chip { position: relative; }
        .chip input { position: absolute; opacity: 0; width: 0; height: 0; }
        .chip label {
            display: inline-block; padding: 10px 22px;
            border: 1.5px solid var(--inp-border); border-radius: 8px;
            font-size: 13px; font-weight: 700;
            color: var(--muted); background: var(--paper);
            cursor: pointer; transition: .15s; user-select: none;
            letter-spacing: .2px;
        }
        .chip input:checked + label {
            border-color: var(--g); background: #f0f7f2; color: var(--g);
        }

        .hidden { display: none !important; }

        /* ─── Radio pills (yes/no) ───────────────────────── */
        .rpill-group { display: flex; gap: 8px; }
        .rpill { flex: 1; position: relative; }
        .rpill input { position: absolute; opacity: 0; width: 0; height: 0; }
        .rpill label {
            display: block; text-align: center;
            padding: 12px; border: 1.5px solid var(--inp-border);
            border-radius: 8px; font-size: 14px; font-weight: 700;
            color: var(--muted); background: var(--paper);
            cursor: pointer; transition: .15s; user-select: none;
        }
        .rpill input:checked + label {
            border-color: var(--g); background: #f0f7f2; color: var(--g);
        }

        /* ─── File upload ────────────────────────────────── */
        .upload-wrap { position: relative; }
        .upload-input {
            position: absolute; inset: 0; opacity: 0; cursor: pointer;
            width: 100%; height: 100%; z-index: 2;
        }
        .upload-label {
            display: block; padding: 22px 18px; text-align: center;
            border: 2px dashed var(--inp-border); border-radius: 8px;
            background: var(--paper); cursor: pointer; transition: .15s;
        }
        .upload-wrap:hover .upload-label {
            border-color: var(--g); background: #f0f7f2;
        }
        .upload-label-cta {
            font-size: 13px; font-weight: 700; color: var(--g);
            text-decoration: underline; text-underline-offset: 3px;
        }
        .upload-label-sub {
            font-size: 11px; color: var(--muted); margin-top: 4px;
            display: block;
        }
        .upload-label-file {
            font-size: 12px; font-weight: 600; color: var(--ink2);
            margin-top: 8px; display: block;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* ─── Char counter ───────────────────────────────── */
        .char-track {
            height: 2px; background: var(--cream2);
            border-radius: 2px; margin-top: 8px; overflow: hidden;
        }
        .char-fill {
            height: 100%; background: var(--g);
            border-radius: 2px; transition: width .15s;
        }
        .char-label {
            font-size: 11px; color: var(--muted); text-align: right;
            margin-top: 4px;
        }

        /* ─── Certification ──────────────────────────────── */
        .cert {
            display: flex; gap: 12px; align-items: flex-start;
            padding: 16px 18px;
            border: 1.5px solid var(--inp-border); border-radius: 8px;
            background: var(--paper); cursor: pointer;
        }
        .cert input[type="checkbox"] {
            width: 17px; height: 17px; flex-shrink: 0; margin-top: 2px;
            accent-color: var(--g); cursor: pointer;
        }
        .cert-text { font-size: 13px; color: var(--ink2); line-height: 1.6; }

        /* ─── Submit button ──────────────────────────────── */
        .btn-submit {
            display: block; width: 100%; margin-top: 24px;
            padding: 16px; border: none; border-radius: 8px;
            background: var(--g); color: #fff;
            font-size: 13px; font-weight: 800; letter-spacing: 1.5px;
            text-transform: uppercase; cursor: pointer; transition: .15s;
            font-family: inherit;
        }
        .btn-submit:hover {
            background: var(--g1);
            box-shadow: 0 8px 24px rgba(10,92,47,.22);
            transform: translateY(-1px);
        }

        /* ─── Read-only summary ──────────────────────────── */
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table tr { border-bottom: 1px solid #ede9df; }
        .summary-table tr:last-child { border-bottom: none; }
        .summary-table td { padding: 14px 0; vertical-align: top; }
        .s-label {
            font-size: 9px; font-weight: 800; letter-spacing: 1.5px;
            text-transform: uppercase; color: var(--muted);
            width: 40%; padding-right: 20px;
        }
        .s-value { font-size: 14px; color: var(--ink); font-weight: 500; }
        .s-value.empty { color: var(--muted); }
        .s-badge {
            display: inline-block; padding: 3px 10px; border-radius: 4px;
            font-size: 11px; font-weight: 700; letter-spacing: .3px;
            background: #f0f7f2; color: var(--g); border: 1px solid #c3e0cc;
        }

        /* ─── Docs checklist ─────────────────────────────── */
        .doc-progress-bar { height: 4px; background: var(--cream2); border-radius: 4px; margin-bottom: 20px; overflow: hidden; }
        .doc-progress-fill { height: 100%; background: var(--g); border-radius: 4px; transition: width .4s; }
        .doc-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 18px; border-radius: 8px; margin-bottom: 8px;
            border: 1.5px solid;
        }
        .doc-row.done { background: #f0f7f2; border-color: #b6d8c2; }
        .doc-row.wait { background: var(--paper); border-color: var(--inp-border); }
        .doc-row-name { font-size: 13px; font-weight: 600; }
        .doc-row.done .doc-row-name { color: #166534; }
        .doc-row.wait .doc-row-name { color: var(--ink2); }
        .doc-badge {
            font-size: 10px; font-weight: 800; letter-spacing: .5px;
            text-transform: uppercase; padding: 4px 10px; border-radius: 4px;
        }
        .doc-row.done .doc-badge { background: #dcfce7; color: #166534; }
        .doc-row.wait .doc-badge { background: #f5f3ed; color: var(--muted); }
        .doc-tick {
            width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800; margin-right: 12px;
        }
        .doc-row.done .doc-tick { background: var(--g); color: #fff; }
        .doc-row.wait .doc-tick { background: var(--cream2); color: transparent; }
        .doc-left { display: flex; align-items: center; }

        /* ─── Refresh link ───────────────────────────────── */
        .refresh-btn {
            display: inline-block; margin-top: 16px;
            padding: 9px 18px; border-radius: 7px; text-decoration: none;
            border: 1.5px solid var(--inp-border); background: var(--paper);
            font-size: 12px; font-weight: 700; color: var(--ink2);
            letter-spacing: .3px; transition: .15s;
        }
        .refresh-btn:hover { border-color: var(--g); color: var(--g); background: #f0f7f2; }

        /* ─── Receipt waiting ────────────────────────────── */
        .receipt-wait {
            text-align: center; padding: 40px 24px;
        }
        .receipt-wait-num {
            font-size: 48px; font-weight: 900; color: var(--g); opacity: .1;
            margin-bottom: 12px; letter-spacing: -3px;
        }
        .receipt-wait h3 { font-size: 17px; font-weight: 800; color: var(--ink); }
        .receipt-wait p { font-size: 13px; color: var(--muted); margin-top: 8px; line-height: 1.6; max-width: 380px; margin-inline: auto; }

        /* ─── Approved ───────────────────────────────────── */
        .approved-wrap { text-align: center; padding: 52px 32px; }
        .approved-word {
            font-size: 72px; font-weight: 900; color: var(--g); opacity: .07;
            letter-spacing: -4px; line-height: 1; margin-bottom: -8px;
        }
        .approved-title { font-size: 28px; font-weight: 800; color: var(--g); }
        .approved-sub { font-size: 14px; color: var(--muted); margin-top: 8px; line-height: 1.6; max-width: 400px; margin-inline: auto; }
        .btn-dashboard {
            display: inline-block; margin-top: 28px;
            padding: 14px 32px; border-radius: 8px; text-decoration: none;
            background: var(--g); color: #fff;
            font-size: 13px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
            transition: .15s;
        }
        .btn-dashboard:hover { background: var(--g1); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(10,92,47,.2); }

        /* ─── Rejected ───────────────────────────────────── */
        .rejected-card {
            background: var(--white); border-radius: 14px;
            border: 1.5px solid #fecaca;
            box-shadow: 0 2px 20px rgba(0,0,0,.05);
            padding: 36px;
        }
        .rejected-label {
            font-size: 9px; font-weight: 800; letter-spacing: 2px;
            text-transform: uppercase; color: #dc2626; margin-bottom: 12px;
        }
        .rejected-title { font-size: 20px; font-weight: 800; color: var(--ink); }
        .rejected-reason {
            font-size: 14px; color: var(--ink2); line-height: 1.7;
            margin-top: 12px; padding: 16px;
            background: #fef2f2; border-radius: 8px; border-left: 3px solid #dc2626;
        }
        .btn-resubmit {
            display: inline-block; margin-top: 24px;
            padding: 12px 26px; border: none; border-radius: 8px;
            background: var(--ink); color: #fff; font-family: inherit;
            font-size: 13px; font-weight: 800; letter-spacing: 1px;
            text-transform: uppercase; cursor: pointer; transition: .15s;
        }
        .btn-resubmit:hover { background: #333; }

        /* ─── Mobile ─────────────────────────────────────── */
        @media (max-width: 720px) {
            .nav-links { display: none; }
            .mobile-toggle { display: block; }
            .nav-mobile-actions { margin-left: auto; }
            .nav-profile-mobile { display: inline-flex; }
            .nav-links.active {
                display: flex; flex-direction: column; position: absolute;
                top: 66px; left: 0; right: 0; background: var(--paper);
                padding: 16px 24px; border-bottom: 1px solid var(--rule);
                box-shadow: 0 10px 32px rgba(0,0,0,.08);
            }
            .chapter-band { padding: 32px 20px 28px; }
            .ps-label { font-size: 8px; }
            .card-head, .card-body { padding-inline: 20px; }
            .card-head-num { font-size: 36px; }
            .fgrid { grid-template-columns: 1fr; }
            .summary-table { display: block; }
            .summary-table tr { display: flex; flex-direction: column; padding: 12px 0; }
            .s-label { width: 100%; }
        }
    </style>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="/" class="nav-brand">
                <img src="{{ asset('images/eba-logo.png') }}" alt="EBA Logo">
                <div class="nav-brand-text">
                    <span class="nav-brand-title">EBA Information System</span>
                    <span class="nav-brand-subtitle">CvSU &mdash; Trece Martires City Campus</span>
                </div>
            </a>
            <div class="nav-mobile-actions">
                @auth
                    <div class="nav-profile-mobile">
                        @include('partials.public-profile-dropdown', ['compactTrigger' => true])
                    </div>
                @endauth
                <button class="mobile-toggle" onclick="toggleMenu()" aria-label="Toggle navigation">
                    <span></span><span></span><span></span>
                </button>
            </div>
            @if (Route::has('login'))
                <div class="nav-links" id="navLinks">
                    @include('partials.public-nav-links', ['loginButtonClass' => 'btn-login'])
                </div>
            @endif
        </div>
    </nav>

    <div class="shell">

        @if (! $application)

            {{-- ── No application ── --}}
            <div class="chapter-band">
                <span class="chapter-step-label">Application Portal</span>
                <div class="chapter-title">No Application Found</div>
                <div class="chapter-sub">Please contact the EBA Office for assistance.</div>
            </div>

        @elseif ($application->status === 'rejected')

            {{-- ── Rejected ── --}}
            <div class="chapter-band">
                <span class="chapter-step-label">Application Status</span>
                <div class="chapter-title">Application Not Approved</div>
                <div class="chapter-sub">Please review the feedback below and resubmit.</div>
            </div>
            <div class="main">
                @if (session('success'))<div class="flash flash-ok" data-flash>{{ session('success') }}</div>@endif
                @if (session('error'))<div class="flash flash-err" data-flash>{{ session('error') }}</div>@endif
                <div class="rejected-card">
                    <p class="rejected-label">Step 1 of 4 &mdash; Application Rejected</p>
                    <h2 class="rejected-title">Your application needs revision</h2>
                    <div class="rejected-reason">
                        {{ $application->rejection_reason ?? 'No specific reason was provided. Please contact the EBA Office for details.' }}
                    </div>
                    <form action="{{ route('application.resubmit') }}" method="POST" onsubmit="return confirm('This will restart your application from Step 1. Are you sure?');">
                        @csrf
                        <button type="submit" class="btn-resubmit">Resubmit Application</button>
                    </form>
                </div>
            </div>

        @else
            @php
                $status      = $application->wizard_status;
                $currentStep = $application->wizardStepNumber();
                $isApproved  = $status === 'final_approved' || $application->status === 'approved';

                $stepMeta = [
                    1 => ['label' => 'Letter of Intent',   'sub' => 'Upload your signed LOI to get started.'],
                    2 => ['label' => 'Application Form',   'sub' => 'Tell us about you and your business.'],
                    3 => ['label' => 'Office Requirements','sub' => 'Submit required physical documents.'],
                    4 => ['label' => 'Payment Receipt',    'sub' => 'Upload your official payment receipt.'],
                ];

                $activeStep = $isApproved ? 4 : $currentStep;
                $meta       = $stepMeta[$activeStep];

                $oldTypeOfBusiness    = old('type_of_business');
                $selectedBusinessTypes = is_array($oldTypeOfBusiness)
                    ? $oldTypeOfBusiness
                    : array_filter(array_map('trim', explode(',', (string) ($application->type_of_business ?? ''))));
                $oldIsPrevious        = old('is_previous_concessionaire');
                $isPreviousCon        = $oldIsPrevious !== null
                    ? $oldIsPrevious === 'yes'
                    : (bool) ($application->is_previous_concessionaire ?? false);
            @endphp

            {{-- Chapter band --}}
            <div class="chapter-band">
                <span class="chapter-step-label">
                    @if ($isApproved) Application Approved @else Step {{ $activeStep }} of 4 &mdash; {{ $meta['label'] }} @endif
                </span>
                <div class="chapter-title">Apply for a Campus Concession Space</div>
                <div class="chapter-sub">{{ $isApproved ? 'Your concessionaire application has been fully approved.' : $meta['sub'] }}</div>

                {{-- Progress --}}
                <div class="progress-wrap" style="background:transparent; padding-top: 28px;">
                    <div class="progress-track">
                        @foreach ($stepMeta as $num => $sm)
                            @php
                                $sDone   = $isApproved || $currentStep > $num;
                                $sActive = !$isApproved && $currentStep === $num;
                            @endphp
                            <div class="ps {{ $sDone ? 'done' : '' }} {{ $sActive ? 'active' : '' }}">
                                <div class="ps-top">
                                    @if ($num > 1)<div class="ps-line {{ ($isApproved || $currentStep >= $num) ? 'done' : '' }}"></div>@else<div class="ps-line none"></div>@endif
                                    <div class="ps-dot">{{ $sDone ? '✓' : $num }}</div>
                                    @if ($num < 4)<div class="ps-line {{ ($isApproved || $currentStep > $num) ? 'done' : '' }}"></div>@else<div class="ps-line none"></div>@endif
                                </div>
                                <div class="ps-label">{{ $sm['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="main">
                @if (session('success'))<div class="flash flash-ok" data-flash>{{ session('success') }}</div>@endif
                @if (session('error'))<div class="flash flash-err" data-flash>{{ session('error') }}</div>@endif

                {{-- ═══════════ STEP 1 — Letter of Intent ═══════════ --}}
                @if (in_array($status, ['loi_pending', 'loi_submitted', 'loi_rejected'], true))
                    <div class="step-card">
                        <div class="card-head">
                            <div class="card-head-num">01</div>
                            <div class="card-head-info">
                                <h2>Letter of Intent</h2>
                                <p>Upload a signed Letter of Intent addressed to the EBA Office expressing your intent to operate a concession at CvSU Trece Martires City Campus.</p>
                            </div>
                        </div>
                        <div class="card-body">

                            @if ($status === 'loi_rejected')
                                <div class="banner b-red">
                                    <div class="banner-title">Letter of Intent Not Accepted</div>
                                    <div class="banner-msg">{{ $application->loi_rejection_reason }}</div>
                                </div>
                            @endif

                            @if ($status === 'loi_submitted')
                                <div class="banner b-amber">
                                    <div class="banner-title">Under Review</div>
                                    <div class="banner-msg">Your Letter of Intent has been submitted and is being reviewed by the EBA admin. You will be notified once it has been evaluated.</div>
                                </div>
                                <div style="padding:14px 18px;border-radius:8px;background:#f0f7f2;border:1.5px solid #b6d8c2;font-size:13px;font-weight:600;color:#166534;">
                                    File submitted successfully
                                </div>
                            @endif

                            @if (in_array($status, ['loi_pending', 'loi_rejected'], true))
                                <form action="{{ route('application.loi') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="field" style="margin-bottom: 16px;">
                                        <div class="field-label">Letter of Intent File <em>*</em></div>
                                        <div class="upload-wrap">
                                            <input type="file" class="upload-input" name="loi_files[]" id="loiFile" accept=".pdf,.jpg,.jpeg,.png,.webp" multiple data-max-files="5">
                                            <label class="upload-label" for="loiFile">
                                                <span class="upload-label-cta">Click to upload your Letter of Intent</span>
                                                <span class="upload-label-sub">Up to 5 files &mdash; PDF, JPG, PNG, WebP &mdash; max 5 MB each</span>
                                                <span class="upload-label-file" id="loiFileName">No file selected</span>
                                            </label>
                                        </div>
                                        @error('loi_files')<div class="field-err">{{ $message }}</div>@enderror
                                        @error('loi_files.*')<div class="field-err">{{ $message }}</div>@enderror
                                    </div>

                                    <label class="cert" style="margin-bottom: 20px; display: flex;">
                                        <input type="checkbox" name="loi_certification_agreed" required>
                                        <span class="cert-text">I hereby certify that the above information is true and correct and that I agree to abide by the University's rules and regulations on rentals and concessions.</span>
                                    </label>

                                    <button type="submit" class="btn-submit">Submit Letter of Intent</button>
                                </form>
                            @endif

                        </div>
                    </div>
                @endif

                {{-- ═══════════ STEP 2 — Application Form ═══════════ --}}
                @if (in_array($status, ['form_pending', 'form_submitted', 'form_rejected'], true))
                    <div class="step-card">
                        <div class="card-head">
                            <div class="card-head-num">02</div>
                            <div class="card-head-info">
                                <h2>Application Form</h2>
                                <p>Provide your personal details, business information, and supporting documents. All fields marked with an asterisk are required.</p>
                            </div>
                        </div>
                        <div class="card-body">

                            @if ($status === 'form_rejected')
                                <div class="banner b-red">
                                    <div class="banner-title">Form Needs Revision</div>
                                    <div class="banner-msg">{{ $application->form_rejection_reason }}</div>
                                </div>
                            @endif

                            @if ($status === 'form_submitted')
                                <div class="banner b-amber">
                                    <div class="banner-title">Under Review</div>
                                    <div class="banner-msg">Your application form has been submitted and is currently under review. The EBA admin will evaluate it and notify you of any updates.</div>
                                </div>

                                @php
                                    $summaryTypes = array_filter(array_map('trim', explode(',', (string) $application->type_of_business)));
                                    $typeMap      = ['food' => 'Food', 'non_food' => 'Non-Food'];
                                    $typeLabels   = collect($summaryTypes)->map(fn($t) => $typeMap[$t] ?? $t)->values()->all();
                                @endphp

                                <table class="summary-table">
                                    <tr><td class="s-label">First Name</td><td class="s-value">{{ $application->first_name ?: '—' }}</td></tr>
                                    <tr><td class="s-label">Last Name</td><td class="s-value">{{ $application->last_name ?: '—' }}</td></tr>
                                    <tr><td class="s-label">Business Name</td><td class="s-value">{{ $application->business_name ?: '—' }}</td></tr>
                                    <tr><td class="s-label">Contact Number</td><td class="s-value">{{ $application->phone_number ?: '—' }}</td></tr>
                                    <tr><td class="s-label">Address</td><td class="s-value {{ $application->address ? '' : 'empty' }}">{{ $application->address ?: 'Not provided' }}</td></tr>
                                    <tr><td class="s-label">Type of Business</td><td class="s-value">{{ implode(', ', $typeLabels) ?: 'Not provided' }}</td></tr>
                                    <tr><td class="s-label">Proposed Location</td><td class="s-value {{ $application->proposed_location ? '' : 'empty' }}">{{ $application->proposed_location ?: 'Not provided' }}</td></tr>
                                    <tr><td class="s-label">Proposed Duration</td><td class="s-value {{ $application->proposed_duration ? '' : 'empty' }}">{{ $application->proposed_duration ?: 'Not provided' }}</td></tr>
                                    <tr><td class="s-label">Previous CvSU Concessionaire</td><td class="s-value">{{ $application->is_previous_concessionaire ? 'Yes' : 'No' }}</td></tr>
                                    @if ($application->is_previous_concessionaire)
                                        <tr><td class="s-label">Previous Location &amp; Year</td><td class="s-value">{{ $application->previous_location_year ?: '—' }}</td></tr>
                                    @endif
                                    @php
                                        $validIdCount = count($application->documentPaths('valid_id'));
                                        $permitCount = count($application->documentPaths('business_permit'));
                                    @endphp
                                    <tr>
                                        <td class="s-label">Valid ID</td>
                                        <td class="s-value">
                                            @if ($validIdCount > 0)
                                                <span class="s-badge">Uploaded ({{ $validIdCount }})</span>
                                            @else
                                                <span class="empty">Not uploaded</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="s-label">Business Permit</td>
                                        <td class="s-value">
                                            @if ($permitCount > 0)
                                                <span class="s-badge">Uploaded ({{ $permitCount }})</span>
                                            @else
                                                <span class="empty">Not uploaded</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="s-label">Business Proposal</td>
                                        <td class="s-value" style="white-space:pre-line;">{{ $application->business_proposal ?: '—' }}</td>
                                    </tr>
                                </table>
                            @endif

                            @if (in_array($status, ['form_pending', 'form_rejected'], true))
                                <form action="{{ route('application.wizard-form') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    {{-- Section 1: Personal --}}
                                    <div class="fsec">
                                        <p class="fsec-label">01 &mdash; Personal Information</p>
                                        <div class="fgrid">
                                            <div class="field">
                                                <label class="field-label" for="first_name">First Name <em>*</em></label>
                                                <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $application->first_name) }}" placeholder="e.g. Juan">
                                                @error('first_name')<div class="field-err">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="field">
                                                <label class="field-label" for="last_name">Last Name <em>*</em></label>
                                                <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $application->last_name) }}" placeholder="e.g. Dela Cruz">
                                                @error('last_name')<div class="field-err">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="field">
                                                <label class="field-label" for="phone_number">Contact Number <em>*</em></label>
                                                <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number', $application->phone_number) }}" placeholder="09XXXXXXXXX or +639XXXXXXXXX">
                                                <div class="field-note">Format: 09XXXXXXXXX or +639XXXXXXXXX</div>
                                                @error('phone_number')<div class="field-err">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="field">
                                                <label class="field-label" for="email">Email Address <em>*</em></label>
                                                <input id="email" type="email" value="{{ auth()->user()->email }}" readonly>
                                                <div class="field-note">Linked to your account &mdash; cannot be changed</div>
                                            </div>
                                            <div class="field full">
                                                <label class="field-label" for="address">Home Address <em>*</em></label>
                                                <input id="address" name="address" type="text" value="{{ old('address', $application->address) }}" placeholder="House No., Street, Barangay, City">
                                                @error('address')<div class="field-err">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section 2: Business --}}
                                    <div class="fsec">
                                        <p class="fsec-label">02 &mdash; Business Information</p>
                                        <div class="fgrid">
                                            <div class="field full">
                                                <label class="field-label" for="business_name">Business Name <em>*</em></label>
                                                <input id="business_name" name="business_name" type="text" value="{{ old('business_name', $application->business_name) }}" placeholder="The name your customers will know you by">
                                                @error('business_name')<div class="field-err">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="field full">
                                                <div class="field-label">Type of Business <em>*</em></div>
                                                <div class="chip-group" style="margin-top:4px;">
                                                    <div class="chip">
                                                        <input type="checkbox" name="type_of_business[]" value="food" id="type_food" {{ in_array('food', $selectedBusinessTypes, true) ? 'checked' : '' }}>
                                                        <label for="type_food">Food</label>
                                                    </div>
                                                    <div class="chip">
                                                        <input type="checkbox" name="type_of_business[]" value="non_food" id="type_nonfood" {{ in_array('non_food', $selectedBusinessTypes, true) ? 'checked' : '' }}>
                                                        <label for="type_nonfood">Non-Food</label>
                                                    </div>
                                                </div>
                                                @error('type_of_business')<div class="field-err" style="margin-top:6px;">{{ $message }}</div>@enderror
                                                @error('type_of_business.*')<div class="field-err" style="margin-top:6px;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="field">
                                                <label class="field-label" for="proposed_location">Proposed Location / Area <em>*</em></label>
                                                <input id="proposed_location" name="proposed_location" type="text" value="{{ old('proposed_location', $application->proposed_location) }}" placeholder="e.g. Near the main building entrance">
                                                @error('proposed_location')<div class="field-err">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="field">
                                                <label class="field-label" for="proposed_duration">Proposed Duration of Lease <em>*</em></label>
                                                <input id="proposed_duration" name="proposed_duration" type="text" value="{{ old('proposed_duration', $application->proposed_duration) }}" placeholder="e.g. 1 Academic Year, 6 Months">
                                                @error('proposed_duration')<div class="field-err">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="field full">
                                                <div class="field-label">Previous Concessionaire at CvSU? <em>*</em></div>
                                                <div class="rpill-group" style="margin-top:4px;">
                                                    <div class="rpill">
                                                        <input type="radio" name="is_previous_concessionaire" value="yes" id="prev_yes" {{ $isPreviousCon ? 'checked' : '' }}>
                                                        <label for="prev_yes">Yes</label>
                                                    </div>
                                                    <div class="rpill">
                                                        <input type="radio" name="is_previous_concessionaire" value="no" id="prev_no" {{ ! $isPreviousCon ? 'checked' : '' }}>
                                                        <label for="prev_no">No</label>
                                                    </div>
                                                </div>
                                                @error('is_previous_concessionaire')<div class="field-err" style="margin-top:6px;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="field full {{ $isPreviousCon ? '' : 'hidden' }}" id="prevLocWrap">
                                                <label class="field-label" for="previous_location_year">Previous Location and Year <em>*</em></label>
                                                <input id="previous_location_year" name="previous_location_year" type="text" value="{{ old('previous_location_year', $application->previous_location_year) }}" placeholder="e.g. Main Canteen, 2022">
                                                @error('previous_location_year')<div class="field-err">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section 3: Documents & Proposal --}}
                                    <div class="fsec">
                                        <p class="fsec-label">03 &mdash; Documents &amp; Proposal</p>
                                        <div class="fgrid" style="margin-bottom: 20px;">
                                            <div class="field">
                                                <div class="field-label">Valid ID <em>*</em></div>
                                                <div class="upload-wrap">
                                                    <input type="file" class="upload-input" name="valid_id[]" id="validIdFile" accept=".pdf,.jpg,.jpeg,.png,.webp" multiple data-max-files="5" required>
                                                    <label class="upload-label" for="validIdFile">
                                                        <span class="upload-label-cta">Upload Valid ID</span>
                                                        <span class="upload-label-sub">Up to 5 files &mdash; PDF, JPG, PNG &mdash; max 5 MB each</span>
                                                        <span class="upload-label-file" id="validIdName">No file selected</span>
                                                    </label>
                                                </div>
                                                @error('valid_id')<div class="field-err">{{ $message }}</div>@enderror
                                                @error('valid_id.*')<div class="field-err">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="field">
                                                <div class="field-label">Business Permit <span style="font-weight:500;color:var(--muted);">(if applicable)</span></div>
                                                <div class="upload-wrap">
                                                    <input type="file" class="upload-input" name="business_permit[]" id="permitFile" accept=".pdf,.jpg,.jpeg,.png,.webp" multiple data-max-files="5">
                                                    <label class="upload-label" for="permitFile">
                                                        <span class="upload-label-cta">Upload Business Permit</span>
                                                        <span class="upload-label-sub">Up to 5 files &mdash; PDF, JPG, PNG &mdash; max 5 MB each</span>
                                                        <span class="upload-label-file" id="permitName">No file selected</span>
                                                    </label>
                                                </div>
                                                @error('business_permit')<div class="field-err">{{ $message }}</div>@enderror
                                                @error('business_permit.*')<div class="field-err">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div class="field">
                                            <label class="field-label" for="business_proposal">Business Proposal <em>*</em></label>
                                            <textarea id="business_proposal" name="business_proposal" rows="7" placeholder="Tell us about your proposed business — what you'll offer, who your customers are, and why you'd be a great fit at CvSU. Think of this as your pitch to the EBA Office. (Min. 50 characters)">{{ old('business_proposal', $application->business_proposal) }}</textarea>
                                            <div class="char-track"><div class="char-fill" id="charFill" style="width:0%"></div></div>
                                            <div class="char-label"><span id="charCount">0</span> / 1000 characters</div>
                                            @error('business_proposal')<div class="field-err">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    {{-- Certification --}}
                                    <div style="margin-top:28px;padding-top:28px;border-top:1px solid #ede9df;">
                                        <label class="cert">
                                            <input type="checkbox" name="certification_agreed" value="1" required {{ old('certification_agreed', $application->certification_agreed ? '1' : null) ? 'checked' : '' }}>
                                            <span class="cert-text">I hereby certify that the above information is true and correct and that I agree to abide by the University's rules and regulations on rentals and concessions.</span>
                                        </label>
                                        @error('certification_agreed')<div class="field-err" style="margin-top:8px;">{{ $message }}</div>@enderror
                                        <button type="submit" class="btn-submit">Submit Application Form</button>
                                    </div>

                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ═══════════ STEP 3 — Office Requirements ═══════════ --}}
                @if ($status === 'docs_in_progress')
                    @php
                        $docs = [
                            ['label' => 'Recommendation for Approval of Rental',  'checked' => (bool) $application->docs_recommendation_checked],
                            ['label' => 'Notice to Occupy',                        'checked' => (bool) $application->docs_notice_occupy_checked],
                            ['label' => 'Notice of Termination of Rental',         'checked' => (bool) $application->docs_notice_termination_checked],
                            ['label' => 'Memorandum of Agreement (MOA) / Contract','checked' => (bool) $application->docs_moa_contract_checked],
                        ];
                        $doneCount = collect($docs)->where('checked', true)->count();
                    @endphp
                    <div class="step-card">
                        <div class="card-head">
                            <div class="card-head-num">03</div>
                            <div class="card-head-info">
                                <h2>Office Requirements</h2>
                                <p>Visit the EBA Office and bring the following documents. Staff will check each one off as they receive it.</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="banner b-violet" style="margin-bottom:24px;">
                                <div class="banner-title">Action Required — Visit the EBA Office</div>
                                <div class="banner-msg">Accomplish and bring the forms listed below to the EBA Office on campus. This checklist updates automatically as staff receives your documents. Page auto-refreshes every 30 seconds.</div>
                            </div>

                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                <span style="font-size:11px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:var(--muted);">Documents Received</span>
                                <span style="font-size:12px;font-weight:800;color:var(--g);">{{ $doneCount }} / 4</span>
                            </div>
                            <div class="doc-progress-bar">
                                <div class="doc-progress-fill" style="width:{{ ($doneCount / 4) * 100 }}%"></div>
                            </div>

                            @foreach ($docs as $doc)
                                <div class="doc-row {{ $doc['checked'] ? 'done' : 'wait' }}">
                                    <div class="doc-left">
                                        <div class="doc-tick">{{ $doc['checked'] ? '✓' : '' }}</div>
                                        <div class="doc-row-name">{{ $doc['label'] }}</div>
                                    </div>
                                    <div class="doc-badge">{{ $doc['checked'] ? 'Received' : 'Pending' }}</div>
                                </div>
                            @endforeach

                            <a href="{{ url()->current() }}" class="refresh-btn">Refresh Status</a>
                        </div>
                    </div>
                @endif

                {{-- ═══════════ STEP 4 — Payment Receipt ═══════════ --}}
                @if (in_array($status, ['receipt_pending', 'receipt_submitted'], true))
                    <div class="step-card" style="margin-top:20px;">
                        <div class="card-head">
                            <div class="card-head-num">04</div>
                            <div class="card-head-info">
                                <h2>Payment Receipt</h2>
                                <p>Upload the official receipt for your Operational Costs fee payment to complete your application.</p>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($status === 'receipt_pending')
                                <div class="banner b-blue" style="margin-bottom:24px;">
                                    <div class="banner-title">Payment Required</div>
                                    <div class="banner-msg">Please pay the required Operational Costs fee at the designated cashier, then upload the official receipt below to complete your application.</div>
                                </div>
                                <form action="{{ route('application.receipt') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="field" style="margin-bottom:0;">
                                        <div class="field-label">Receipt File <em>*</em></div>
                                        <div class="upload-wrap">
                                            <input type="file" class="upload-input" name="receipt_files[]" id="receiptFile" accept=".pdf,.jpg,.jpeg,.png,.webp" multiple data-max-files="5" required>
                                            <label class="upload-label" for="receiptFile">
                                                <span class="upload-label-cta">Upload Official Receipt</span>
                                                <span class="upload-label-sub">Up to 5 files &mdash; PDF, JPG, PNG, WebP &mdash; max 5 MB each</span>
                                                <span class="upload-label-file" id="receiptName">No file selected</span>
                                            </label>
                                        </div>
                                        @error('receipt_files')<div class="field-err">{{ $message }}</div>@enderror
                                        @error('receipt_files.*')<div class="field-err">{{ $message }}</div>@enderror
                                    </div>
                                    <button type="submit" class="btn-submit">Upload Receipt</button>
                                </form>
                            @endif
                            @if ($status === 'receipt_submitted')
                                <div class="receipt-wait">
                                    <div class="receipt-wait-num">04</div>
                                    <h3>Receipt Under Final Review</h3>
                                    <p>Your payment receipt has been submitted and is awaiting final review by the EBA admin. You will be notified once the review is complete.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ═══════════ APPROVED ═══════════ --}}
                @if ($status === 'final_approved' || $application->status === 'approved')
                    <div class="step-card">
                        <div class="approved-wrap">
                            <div class="approved-word">APPROVED</div>
                            <div class="approved-title">Congratulations!</div>
                            <div class="approved-sub">Your concessionaire application has been fully approved. You can now access your dashboard and begin managing your store on the EBA platform.</div>
                            <a href="{{ route('concessionaire.dashboard') }}" class="btn-dashboard">Go to My Dashboard</a>
                        </div>
                    </div>
                @endif

            </div>{{-- .main --}}
        @endif
    </div>{{-- .shell --}}

    <div id="wizard-meta" data-auto-refresh="{{ in_array(($application->wizard_status ?? null), ['docs_in_progress', 'receipt_submitted'], true) ? '1' : '0' }}"></div>

    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });
        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }
        (function () {
            document.querySelectorAll('[data-flash]').forEach(el => setTimeout(() => el.remove(), 5000));

            // Char counter
            const ta      = document.getElementById('business_proposal');
            const counter = document.getElementById('charCount');
            const fill    = document.getElementById('charFill');
            if (ta && counter) {
                const upd = () => {
                    const n = ta.value.length;
                    counter.textContent = n;
                    if (fill) {
                        fill.style.width = Math.min((n / 1000) * 100, 100) + '%';
                        fill.style.background = n < 50 ? '#dc2626' : '#0A5C2F';
                    }
                };
                upd();
                ta.addEventListener('input', upd);
            }

            // Previous concessionaire toggle
            document.querySelectorAll('input[name="is_previous_concessionaire"]').forEach(inp => {
                inp.addEventListener('change', () => {
                    const wrap = document.getElementById('prevLocWrap');
                    if (wrap) wrap.classList.toggle('hidden', inp.value !== 'yes');
                });
            });

            // File name display + max-file enforcement (supports multiple files)
            function bindFile(inputId, displayId) {
                const inp = document.getElementById(inputId);
                const disp = document.getElementById(displayId);
                if (!inp || !disp) return;
                const max = parseInt(inp.dataset.maxFiles || '1', 10);
                inp.addEventListener('change', () => {
                    const files = Array.from(inp.files || []);
                    if (max > 1 && files.length > max) {
                        alert(`You may upload up to ${max} files only. Please select fewer files.`);
                        inp.value = '';
                        disp.textContent = 'No file selected';
                        return;
                    }
                    if (files.length === 0) {
                        disp.textContent = 'No file selected';
                    } else if (files.length === 1) {
                        disp.textContent = files[0].name;
                    } else {
                        disp.textContent = `${files.length} files: ` + files.map(f => f.name).join(', ');
                    }
                });
            }
            bindFile('loiFile',    'loiFileName');
            bindFile('validIdFile','validIdName');
            bindFile('permitFile', 'permitName');
            bindFile('receiptFile','receiptName');

            // Auto-refresh
            const meta = document.getElementById('wizard-meta');
            if (meta?.dataset.autoRefresh === '1') setTimeout(() => location.reload(), 30000);
        })();
    </script>
</body>
</html>
