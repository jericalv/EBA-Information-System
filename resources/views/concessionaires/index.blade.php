<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Concessionaires | EBA Information System</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900|manrope:400,500,600,700,800|ibm-plex-mono:400,500,600&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink: #16231C;
            --ink-soft: #43534A;
            --ink-faint: #68786D;
            --paper: #EDF2E8;
            --paper-deep: #E0E8D9;
            --card: #FFFFFF;
            --card-soft: #F7FAF4;
            --green: #0A5C2F;
            --green-bright: #0D7A3E;
            --green-deep: #07341C;
            --gold: #C99A2E;
            --gold-soft: #E4C36B;
            --line: #D7E0CF;
            --line-strong: #BECBB3;
            --line-dark: rgba(255, 255, 255, 0.14);
            --font-display: 'Inter', 'Manrope', system-ui, sans-serif;
            --font-body: 'Manrope', system-ui, -apple-system, sans-serif;
            --font-mono: 'IBM Plex Mono', 'SFMono-Regular', Consolas, monospace;
        }

        body {
            font-family: var(--font-body);
            background: var(--paper);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
        }

        img { max-width: 100%; }

        a:focus-visible, button:focus-visible, input:focus-visible {
            outline: 2px solid var(--green);
            outline-offset: 2px;
            border-radius: 2px;
        }

        .wrap {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .eyebrow {
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--green);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .eyebrow::before {
            content: '';
            width: 22px;
            height: 1px;
            background: var(--gold);
            flex-shrink: 0;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(237, 242, 232, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
            transition: box-shadow .3s ease, background-color .3s ease;
        }
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 2px 24px rgba(24, 36, 32, 0.07);
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--ink);
        }
        .nav-brand img { width: 42px; height: 42px; object-fit: contain; }
        .nav-brand-text { display: flex; flex-direction: column; }
        .nav-brand-title {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.2px;
            color: var(--green);
            line-height: 1.2;
        }
        .nav-brand-subtitle {
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 500;
            color: var(--ink-faint);
            letter-spacing: 1.1px;
            text-transform: uppercase;
        }
        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-links a {
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink-soft);
            padding: 8px 14px;
            border-radius: 6px;
            transition: color .2s ease, background-color .2s ease;
        }
        .nav-links a:hover { color: var(--green); background: rgba(10, 92, 47, 0.06); }
        .nav-links a.active { color: var(--green); background: rgba(10, 92, 47, 0.09); }
        .nav-links .btn-login {
            color: #fff;
            background: var(--green);
            padding: 9px 20px;
            margin-left: 8px;
        }
        .nav-links .btn-login:hover { background: var(--green-bright); color: #fff; }
        .mobile-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; }
        .mobile-toggle span {
            display: block;
            width: 22px; height: 2px;
            background: var(--ink);
            margin: 5px 0;
            border-radius: 2px;
        }
        .nav-mobile-actions { display: none; align-items: center; gap: 10px; }
        .nav-profile-mobile { display: none; }

        /* ===== PAGE HEADER (registry style) ===== */
        .page-wrapper { flex: 1; padding-top: 70px; }

        .reg-head { border-bottom: 1px solid var(--line); }
        .reg-head-inner {
            padding: 46px 0 38px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 32px;
        }
        .reg-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(28px, 3.6vw, 40px);
            line-height: 1.08;
            letter-spacing: -0.7px;
            color: var(--ink);
            margin: 12px 0 10px;
        }
        .reg-sub {
            font-size: 15px;
            color: var(--ink-soft);
            line-height: 1.7;
            max-width: 520px;
        }
        .reg-meta {
            flex-shrink: 0;
            font-family: var(--font-mono);
            border-left: 1px solid var(--line-strong);
            padding-left: 24px;
            display: flex;
            flex-direction: column;
            gap: 9px;
            min-width: 210px;
        }
        .reg-meta-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 18px;
        }
        .reg-meta-row span {
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--ink-faint);
        }
        .reg-meta-row strong {
            font-size: 13px;
            font-weight: 600;
            color: var(--green);
            font-variant-numeric: tabular-nums;
        }

        .main-content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 24px 48px;
        }

        /* ===== SEARCH BAR ===== */
        .filters-bar {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 26px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .search-box { flex: 1; min-width: 220px; position: relative; }
        .search-box input {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            font-size: 13.5px;
            font-family: var(--font-body);
            color: var(--ink);
            background: var(--card);
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .search-box input::placeholder { color: var(--ink-faint); }
        .search-box input:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
        }
        .search-box svg {
            position: absolute;
            left: 12px; top: 50%;
            transform: translateY(-50%);
            width: 16px; height: 16px;
            color: var(--ink-faint);
            pointer-events: none;
        }
        .search-hint {
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 500;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--ink-faint);
            padding: 0 6px;
            white-space: nowrap;
        }
        .search-hint strong { color: var(--green); font-weight: 600; }

        /* ===== SECTION HEAD ===== */
        .cc-section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 16px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--line);
        }
        .cc-section-head h2 {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(21px, 2.6vw, 27px);
            line-height: 1.1;
            letter-spacing: -0.5px;
            color: var(--ink);
        }
        .cc-count {
            flex-shrink: 0;
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 500;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--ink-faint);
            padding-bottom: 4px;
        }
        .cc-count strong { color: var(--green); font-weight: 600; }

        /* ===== GRID ===== */
        .cc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(244px, 1fr));
            gap: 20px;
        }
        .cc-noresult {
            text-align: center;
            padding: 56px 20px;
            color: var(--ink-soft);
            font-size: 14px;
            font-weight: 600;
        }

        /* ===== CARD ===== */
        .cc-card {
            position: relative;
            display: flex;
            flex-direction: column;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            color: var(--ink);
            transition: transform .3s cubic-bezier(0.16, 1, 0.3, 1), border-color .3s ease, box-shadow .3s ease;
        }
        .cc-card:hover {
            transform: translateY(-4px);
            border-color: rgba(10, 92, 47, 0.4);
            box-shadow: 0 18px 40px rgba(24, 36, 32, 0.12);
        }
        .cc-poster {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 10;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--line);
            overflow: hidden;
        }
        .cc-poster::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0) 46%, rgba(7, 52, 28, 0.34) 100%);
            pointer-events: none;
        }
        .cc-monogram {
            font-family: var(--font-display);
            font-size: 52px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.42);
            letter-spacing: 1px;
        }
        .cc-rating-chip {
            position: absolute;
            top: 12px; right: 12px;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-family: var(--font-mono);
            font-size: 11.5px;
            font-weight: 600;
            color: var(--green-deep);
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 4px 9px;
        }
        .cc-rating-chip svg { width: 11px; height: 11px; color: var(--gold); }
        .cc-tag {
            position: absolute;
            top: 12px; left: 12px;
            z-index: 1;
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--line);
            border-radius: 4px;
            padding: 5px 9px;
            color: var(--ink-soft);
        }

        .cc-body {
            padding: 15px 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }
        .cc-name {
            font-family: var(--font-display);
            font-size: 16.5px;
            font-weight: 700;
            letter-spacing: -0.2px;
            line-height: 1.3;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color .2s ease;
        }
        .cc-card:hover .cc-name { color: var(--green); }
        .cc-loc {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--ink-faint);
            min-width: 0;
        }
        .cc-loc svg { width: 13px; height: 13px; flex-shrink: 0; color: var(--line-strong); }
        .cc-loc span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .cc-peek { display: flex; gap: 6px; margin-top: 6px; }
        .cc-peek-img {
            width: 38px; height: 38px;
            border-radius: 7px;
            flex-shrink: 0;
            background-size: cover;
            background-position: center;
            border: 1px solid var(--line);
        }
        .cc-peek-more {
            width: 38px; height: 38px;
            border-radius: 7px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--paper);
            color: var(--ink-faint);
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 600;
        }

        .cc-foot {
            border-top: 1px solid var(--line);
            padding: 11px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .cc-foot-meta {
            font-family: var(--font-mono);
            font-size: 11.5px;
            font-weight: 500;
            color: var(--ink-faint);
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-width: 0;
        }
        .cc-foot-meta strong { color: var(--green); font-weight: 600; font-variant-numeric: tabular-nums; }
        .cc-foot-dot { color: var(--line-strong); }
        .cc-view {
            font-size: 12px;
            font-weight: 700;
            color: var(--ink-faint);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            transition: color .2s ease;
        }
        .cc-view svg { width: 12px; height: 12px; transition: transform .2s ease; }
        .cc-card:hover .cc-view { color: var(--green); }
        .cc-card:hover .cc-view svg { transform: translateX(2px); }

        /* ===== VISION / MISSION / VALUES BANNER ===== */
        .vmv-sec {
            position: relative;
            background: var(--green-deep);
            color: #fff;
            overflow: hidden;
            border-top: 1px solid var(--green-deep);
        }
        .vmv-sec::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 22px 22px;
            pointer-events: none;
        }
        /* white flow-line artwork */
        .flow-img {
            position: absolute;
            pointer-events: none;
            user-select: none;
            z-index: 0;
            /* recolor the green line-art asset to pure white */
            filter: brightness(0) invert(1);
        }
        .flow-vmv-tr {
            top: -70px; right: -150px;
            width: 720px;
            opacity: 0.10;
            transform: rotate(12deg);
        }
        .flow-vmv-bl {
            bottom: -120px; left: -170px;
            width: 620px;
            opacity: 0.08;
            transform: rotate(-8deg);
        }
        .vmv-inner { position: relative; z-index: 1; padding: 62px 0 66px; }
        .vmv-head { margin-bottom: 44px; max-width: 640px; }
        .vmv-head .eyebrow { color: var(--gold-soft); }
        .vmv-head .eyebrow::before { background: var(--gold-soft); }
        .vmv-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(26px, 3.4vw, 38px);
            line-height: 1.08;
            letter-spacing: -0.6px;
            color: #fff;
            margin: 14px 0 12px;
        }
        .vmv-head p { color: rgba(255, 255, 255, 0.72); font-size: 15px; }

        .vmv-grid { display: grid; grid-template-columns: repeat(3, 1fr); }
        .vmv-col {
            padding: 4px 32px 4px 32px;
            border-left: 1px solid var(--line-dark);
        }
        .vmv-col:first-child { border-left: none; padding-left: 0; }
        .vmv-col h3 {
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gold-soft);
            margin-bottom: 18px;
        }
        .vmv-col > p {
            font-size: 14.5px;
            line-height: 1.85;
            color: rgba(255, 255, 255, 0.85);
        }
        .core-values-list { display: flex; flex-direction: column; gap: 14px; }
        .core-value-item { display: flex; align-items: center; gap: 12px; }
        .core-value-item svg { width: 15px; height: 15px; color: var(--gold); flex-shrink: 0; }
        .core-value-item strong { font-size: 15px; font-weight: 700; color: #fff; }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 72px 24px;
            background: var(--card);
            border: 1px dashed var(--line-strong);
            border-radius: 12px;
        }
        .empty-state svg { width: 52px; height: 52px; color: var(--line-strong); margin-bottom: 14px; }
        .empty-state h3 {
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 700;
            letter-spacing: -0.2px;
            margin-bottom: 6px;
            color: var(--ink);
        }
        .empty-state p { color: var(--ink-soft); font-size: 14px; max-width: 420px; margin: 0 auto; }

        /* ===== FOOTER ===== */
        .footer { background: var(--green-deep); margin-top: auto; }
        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 22px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .footer-text {
            font-family: var(--font-mono);
            font-size: 11px;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.65);
        }
        .footer-text a { color: var(--gold-soft); text-decoration: none; font-weight: 600; }
        .footer-text a:hover { text-decoration: underline; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .reg-head-inner {
                flex-direction: column;
                align-items: flex-start;
                gap: 22px;
                padding: 36px 0 30px;
            }
            .reg-meta {
                border-left: 0;
                border-top: 1px solid var(--line-strong);
                padding-left: 0;
                padding-top: 16px;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 24px;
                min-width: 0;
                width: 100%;
            }
            .reg-meta-row { flex-direction: column; gap: 3px; }
            .vmv-grid { grid-template-columns: 1fr; gap: 30px; }
            .vmv-col { border-left: none; padding: 0; padding-bottom: 26px; border-bottom: 1px solid var(--line-dark); }
            .vmv-col:last-child { border-bottom: none; padding-bottom: 0; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .mobile-toggle { display: block; }
            .nav-mobile-actions { display: flex; margin-left: auto; }
            .nav-profile-mobile { display: inline-flex; }
            .nav-links.active {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                position: absolute;
                top: 70px; left: 0; right: 0;
                background: var(--card);
                padding: 16px 24px;
                border-bottom: 1px solid var(--line);
                box-shadow: 0 10px 40px rgba(24, 36, 32, 0.08);
            }
            .nav-links.active .btn-login { margin-left: 0; text-align: center; justify-content: center; display: flex; }
            .filters-bar { flex-direction: column; align-items: stretch; }
            .search-box { width: 100%; min-width: 0; }
            .search-hint { text-align: center; }
        }
        @media (max-width: 560px) {
            .cc-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .cc-body { padding: 12px 12px 10px; }
            .cc-name { font-size: 14.5px; }
            .cc-foot { padding: 10px 12px; }
            .cc-monogram { font-size: 40px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition-duration: 0.01ms !important; }
            .cc-card:hover { transform: none; }
        }
    </style>
</head>
<body>
    @include('partials.pending-application-banner')

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="/" class="nav-brand">
                <img src="{{ asset('images/eba-logo.png') }}" alt="EBA Logo">
                <div class="nav-brand-text">
                    <span class="nav-brand-title">EBA Information System</span>
                    <span class="nav-brand-subtitle">CvSU &mdash; Trece Martires</span>
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

    <div class="page-wrapper">

        @php
            $gradients = [
                'linear-gradient(135deg,#0A5C2F 0%,#07341C 100%)',
                'linear-gradient(135deg,#0D7A3E 0%,#0A5C2F 100%)',
                'linear-gradient(135deg,#2B6A45 0%,#123D28 100%)',
                'linear-gradient(135deg,#4B7A3A 0%,#274A22 100%)',
                'linear-gradient(135deg,#1F6E5A 0%,#0C3B32 100%)',
                'linear-gradient(135deg,#5A6B2E 0%,#33401A 100%)',
                'linear-gradient(135deg,#0B5949 0%,#063A2F 100%)',
            ];
            $totalItems   = (int) $concessionaires->sum('products_count');
            $totalReviews = (int) $concessionaires->sum('display_review_count');
        @endphp

        <!-- ===== PAGE HEADER ===== -->
        <header class="reg-head">
            <div class="wrap">
                <div class="reg-head-inner">
                    <div>
                        <span class="eyebrow">Campus Registry &mdash; Concessionaires</span>
                        <h1 class="reg-title">Campus Concessionaires</h1>
                        <p class="reg-sub">Explore the food stalls and stores around CvSU Trece Martires City Campus &mdash; browse menus, check ratings, and find your next favorite meal.</p>
                    </div>
                    <div class="reg-meta" aria-label="Registry summary">
                        <div class="reg-meta-row">
                            <span>Active stalls</span>
                            <strong>{{ number_format($concessionaires->count()) }}</strong>
                        </div>
                        <div class="reg-meta-row">
                            <span>Menu items</span>
                            <strong>{{ number_format($totalItems) }}</strong>
                        </div>
                        <div class="reg-meta-row">
                            <span>Student reviews</span>
                            <strong>{{ number_format($totalReviews) }}</strong>
                        </div>
                        <div class="reg-meta-row">
                            <span>As of</span>
                            <strong>{{ now()->format('M d, Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="main-content">
            @if ($concessionaires->isNotEmpty())

                <!-- Search -->
                <form class="filters-bar" onsubmit="return false;">
                    <div class="search-box">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input id="ccSearch" type="text" placeholder="Search by name or location..." autocomplete="off">
                    </div>
                    <span class="search-hint"><strong id="ccVisible">{{ $concessionaires->count() }}</strong> showing</span>
                </form>

                <section class="cc-section" id="allSection">
                    <div class="cc-section-head">
                        <h2>All Concessionaires</h2>
                        <span class="cc-count"><strong>{{ $concessionaires->count() }}</strong> {{ \Illuminate\Support\Str::plural('stall', $concessionaires->count()) }} on record</span>
                    </div>
                    <div class="cc-grid" id="ccGrid">
                        @foreach ($concessionaires as $c)
                            @include('concessionaires._card', ['c' => $c, 'gradients' => $gradients])
                        @endforeach
                    </div>
                    <div class="cc-noresult" id="ccNoResult" hidden>No concessionaires match your search.</div>
                </section>

            @else
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                    </svg>
                    <h3>No Active Concessionaires</h3>
                    <p>Our campus canteens and food stalls are currently wrapping up store details. Please revisit shortly.</p>
                </div>
            @endif
        </div>

        <!-- ===== VISION / MISSION / VALUES BANNER ===== -->
        <section class="vmv-sec" aria-label="CvSU vision, mission and core values">
            <img class="flow-img flow-vmv-tr" src="{{ asset('images/flowlines3-green.png') }}" alt="" aria-hidden="true" loading="lazy">
            <img class="flow-img flow-vmv-bl" src="{{ asset('images/flowlines1-green.png') }}" alt="" aria-hidden="true" loading="lazy">
            <div class="wrap vmv-inner">
                <div class="vmv-head">
                    <span class="eyebrow">Cavite State University</span>
                    <h2 class="vmv-title">Our vision, mission &amp; core values</h2>
                    <p>Guiding principles that drive excellence at CvSU Trece Martires City Campus.</p>
                </div>

                <div class="vmv-grid">
                    <div class="vmv-col">
                        <h3>University Vision</h3>
                        <p>{{ \App\Models\SiteSetting::get('vision', 'The premier university in historic Cavite globally recognized for excellence in character development, academics, research, innovation and sustainable community engagement.') }}</p>
                    </div>
                    <div class="vmv-col">
                        <h3>University Mission</h3>
                        <p>{{ \App\Models\SiteSetting::get('mission', 'Cavite State University shall provide excellent, equitable and relevant educational opportunities in the arts, sciences and technology through quality instruction and responsive research and development activities. It shall produce professional, skilled and morally upright individuals for global competitiveness.') }}</p>
                    </div>
                    <div class="vmv-col">
                        <h3>Core Values</h3>
                        <div class="core-values-list">
                            @for ($i = 1; $i <= 5; $i++)
                                @php $cv = \App\Models\SiteSetting::get('core_value_'.$i, ''); @endphp
                                @if ($cv)
                                <div class="core-value-item">
                                    <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                    </svg>
                                    <strong>{{ $cv }}</strong>
                                </div>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-inner">
            <p class="footer-text">&copy; {{ date('Y') }} <a href="/">CvSU</a> &mdash; Trece Martires City Campus. All rights reserved.</p>
            <p class="footer-text">EBA Information System</p>
        </div>
    </footer>

    <script>
        // Navbar scroll shadow
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }

        // Search filter
        const ccSearch = document.getElementById('ccSearch');
        if (ccSearch) {
            const visibleCount = document.getElementById('ccVisible');
            ccSearch.addEventListener('input', () => {
                const q = ccSearch.value.trim().toLowerCase();
                let visible = 0;
                document.querySelectorAll('#ccGrid .cc-card').forEach(card => {
                    const hay = (card.dataset.name || '') + ' ' + (card.dataset.location || '');
                    const show = hay.indexOf(q) !== -1;
                    card.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
                const none = document.getElementById('ccNoResult');
                if (none) none.hidden = visible !== 0;
                if (visibleCount) visibleCount.textContent = visible;
            });
        }
    </script>
</body>
</html>
