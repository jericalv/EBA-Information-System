<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Canteen Products | EBA Information System</title>
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

        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible {
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
        .page-wrapper {
            flex: 1;
            padding-top: 70px;
        }

        .reg-head {
            border-bottom: 1px solid var(--line);
        }
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
            padding: 30px 24px 44px;
        }

        /* ===== FILTERS ===== */
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
        .search-box {
            flex: 1;
            min-width: 220px;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 10px 14px 10px 38px;
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
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--ink-faint);
            pointer-events: none;
        }
        .filter-select {
            padding: 10px 12px;
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            font-size: 13.5px;
            font-family: var(--font-body);
            color: var(--ink);
            background: var(--card);
            cursor: pointer;
            min-width: 150px;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
        }

        /* ===== FLASH MESSAGES ===== */
        .flash {
            border: 1px solid var(--line-strong);
            border-left: 3px solid var(--green);
            background: var(--card);
            border-radius: 8px;
            padding: 13px 16px;
            margin-bottom: 22px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink);
        }
        .flash.flash-error {
            border-left-color: #B4232A;
            color: #7C1D22;
        }

        /* ===== PRODUCTS GRID ===== */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(252px, 1fr));
            gap: 20px;
        }
        .hidden { display: none !important; }

        .product-card {
            position: relative;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .3s cubic-bezier(0.16, 1, 0.3, 1), border-color .3s ease, box-shadow .3s ease;
        }
        .product-card:hover {
            transform: translateY(-4px);
            border-color: rgba(10, 92, 47, 0.4);
            box-shadow: 0 18px 40px rgba(24, 36, 32, 0.12);
        }

        .p-media {
            position: relative;
            display: block;
            aspect-ratio: 4 / 3;
            background-color: var(--paper-deep);
            background-image: radial-gradient(rgba(24, 36, 32, 0.1) 1px, transparent 1px);
            background-size: 15px 15px;
            border-bottom: 1px solid var(--line);
            overflow: hidden;
        }
        .p-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .product-card:hover .p-media img { transform: scale(1.045); }
        .p-media-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .p-media-placeholder svg {
            width: 52px;
            height: 52px;
            color: var(--line-strong);
        }
        .p-cat {
            position: absolute;
            top: 12px; left: 12px;
            max-width: calc(100% - 24px);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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

        .p-body {
            padding: 15px 16px 13px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .p-name {
            font-family: var(--font-display);
            font-size: 16.5px;
            font-weight: 700;
            letter-spacing: -0.2px;
            line-height: 1.3;
        }
        .p-name a {
            color: var(--ink);
            text-decoration: none;
            transition: color .2s ease;
        }
        .p-name a:hover { color: var(--green); }
        .p-name a::after {
            content: '';
            position: absolute;
            inset: 0;
        }
        .p-vendor {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: auto;
            position: relative;
            z-index: 1;
        }
        .p-vendor-avatar {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: var(--green-deep);
            color: var(--gold-soft);
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            flex-shrink: 0;
        }
        .p-vendor-name {
            color: var(--ink-faint);
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .p-vendor-name:hover { color: var(--green); text-decoration: underline; }

        .p-foot {
            border-top: 1px solid var(--line);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .p-price {
            font-family: var(--font-mono);
            font-size: 14.5px;
            font-weight: 600;
            color: var(--green);
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        .p-rating {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }
        .stars { display: flex; gap: 1px; flex-shrink: 0; }
        .star { width: 13px; height: 13px; color: var(--line-strong); }
        .star.filled { color: var(--gold); }
        .rating-count {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--ink-faint);
            white-space: nowrap;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 72px 24px;
            background: var(--card);
            border: 1px dashed var(--line-strong);
            border-radius: 12px;
        }
        .empty-state svg {
            width: 52px;
            height: 52px;
            color: var(--line-strong);
            margin-bottom: 14px;
        }
        .empty-state h3 {
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 700;
            letter-spacing: -0.2px;
            margin-bottom: 6px;
            color: var(--ink);
        }
        .empty-state p {
            color: var(--ink-soft);
            font-size: 14px;
        }

        /* ===== PAGINATION ===== */
        .pagination-wrap {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }
        .pagination-wrap nav {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
        }
        .pagination-wrap a, .pagination-wrap span {
            padding: 8px 13px;
            border-radius: 6px;
            font-family: var(--font-mono);
            font-size: 12.5px;
            font-weight: 500;
            text-decoration: none;
            color: var(--ink-soft);
            background: var(--card);
            border: 1px solid var(--line);
            transition: border-color .2s ease, color .2s ease;
        }
        .pagination-wrap a:hover {
            border-color: var(--green);
            color: var(--green);
        }
        .pagination-wrap span[aria-current="page"] {
            background: var(--green);
            border-color: var(--green);
            color: #fff;
        }

        /* ===== CAMPUS STOCKS BAND ===== */
        .stocks-band {
            background: var(--card-soft);
            border-top: 1px solid var(--line);
        }
        .stocks-band .main-content {
            padding-top: 44px;
            padding-bottom: 52px;
        }
        .stocks-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 18px;
            margin-bottom: 26px;
            border-bottom: 1px solid var(--line);
        }
        .stocks-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(23px, 2.8vw, 30px);
            line-height: 1.1;
            letter-spacing: -0.5px;
            color: var(--ink);
            margin: 12px 0 8px;
        }
        .stocks-sub {
            font-size: 14.5px;
            color: var(--ink-soft);
            line-height: 1.7;
            max-width: 520px;
        }
        .stocks-count {
            flex-shrink: 0;
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 500;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--ink-faint);
            padding-bottom: 4px;
        }
        .stocks-count strong {
            color: var(--green);
            font-weight: 600;
        }

        .stocks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 20px;
        }

        .stock-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            color: var(--ink);
            display: flex;
            flex-direction: column;
            transition: transform .3s cubic-bezier(0.16, 1, 0.3, 1), border-color .3s ease, box-shadow .3s ease;
        }
        .stock-card:hover {
            transform: translateY(-4px);
            border-color: rgba(10, 92, 47, 0.4);
            box-shadow: 0 18px 40px rgba(24, 36, 32, 0.12);
        }

        .stock-media {
            position: relative;
            aspect-ratio: 5 / 4;
            background-color: var(--paper-deep);
            background-image: radial-gradient(rgba(24, 36, 32, 0.1) 1px, transparent 1px);
            background-size: 15px 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--line);
            overflow: hidden;
        }
        .stock-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .stock-card:hover .stock-media img { transform: scale(1.045); }
        .stock-media-placeholder {
            width: 48px; height: 48px;
            color: var(--line-strong);
        }
        .stock-media-placeholder svg { width: 100%; height: 100%; }
        .stock-card.is-out .stock-media img { filter: grayscale(0.75); opacity: 0.75; }

        .stock-status {
            position: absolute;
            top: 12px; left: 12px;
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--line);
            border-radius: 4px;
            padding: 5px 9px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--ink-soft);
        }
        .stock-status .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #16A34A;
            flex-shrink: 0;
        }
        .stock-status.low .dot { background: #D97706; }
        .stock-status.out .dot { background: #DC2626; }

        .stock-body { padding: 15px 16px 13px; flex: 1; }
        .stock-kicker {
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 6px;
        }
        .stock-name {
            font-family: var(--font-display);
            font-size: 16.5px;
            font-weight: 700;
            letter-spacing: -0.2px;
            line-height: 1.3;
        }

        .stock-foot {
            border-top: 1px solid var(--line);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .stock-price {
            font-family: var(--font-mono);
            font-size: 14px;
            font-weight: 600;
            color: var(--green);
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        .stock-qty {
            font-family: var(--font-mono);
            font-size: 14px;
            font-weight: 600;
            color: var(--green);
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        .stock-qty em {
            font-style: normal;
            font-size: 9.5px;
            font-weight: 500;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--ink-faint);
            margin-left: 4px;
        }
        .stock-qty.low-stock { color: #C2410C; }
        .stock-qty.out-of-stock { color: #DC2626; }
        .stock-view {
            margin-left: auto;
            font-size: 12px;
            font-weight: 700;
            color: var(--ink-faint);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            transition: color .2s ease;
        }
        .stock-view svg { width: 12px; height: 12px; }
        .stock-card:hover .stock-view { color: var(--green); }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--green-deep);
            margin-top: auto;
        }
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
        .footer-text a {
            color: var(--gold-soft);
            text-decoration: none;
            font-weight: 600;
        }
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
            .stocks-head { flex-direction: column; align-items: flex-start; gap: 6px; }
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
                top: 70px;
                left: 0;
                right: 0;
                background: var(--card);
                padding: 16px 24px;
                border-bottom: 1px solid var(--line);
                box-shadow: 0 10px 40px rgba(24, 36, 32, 0.08);
            }
            .nav-links.active .btn-login { margin-left: 0; text-align: center; justify-content: center; display: flex; }
            .filters-bar { flex-direction: column; align-items: stretch; }
            .search-box { width: 100%; min-width: 0; }
            .filter-select { width: 100%; min-width: 0; }
        }
        @media (max-width: 560px) {
            .products-grid, .stocks-grid { grid-template-columns: 1fr; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition-duration: 0.01ms !important; }
            .product-card:hover, .stock-card:hover { transform: none; }
            .product-card:hover .p-media img, .stock-card:hover .stock-media img { transform: none; }
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
                    <span></span>
                    <span></span>
                    <span></span>
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
        <!-- Page Header -->
        <header class="reg-head">
            <div class="wrap">
                <div class="reg-head-inner">
                    <div>
                        <span class="eyebrow">Campus Registry &mdash; Canteen</span>
                        <h1 class="reg-title">Canteen Products</h1>
                        <p class="reg-sub">Browse and review food items from the campus concessionaires. Ratings come from registered students and faculty.</p>
                    </div>
                    <div class="reg-meta" aria-label="Registry summary">
                        <div class="reg-meta-row">
                            <span>Items listed</span>
                            <strong>{{ number_format($products->total()) }}</strong>
                        </div>
                        <div class="reg-meta-row">
                            <span>Concessionaires</span>
                            <strong>{{ number_format($concessionaires->count()) }}</strong>
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
            <!-- Filters -->
            <form id="products-filters-form" method="GET" action="{{ route('products.index') }}" class="filters-bar">
                <div class="search-box">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search" placeholder="Search products or vendors..." value="{{ request('search') }}">
                </div>
                <select name="category" class="filter-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                    @endforeach
                </select>
                <select name="concessionaire_id" class="filter-select">
                    <option value="">All Concessionaires</option>
                    @foreach ($concessionaires as $concessionaire)
                        <option value="{{ $concessionaire->id }}" {{ (string) request('concessionaire_id') === (string) $concessionaire->id ? 'selected' : '' }}>
                            {{ $concessionaire->business_name ?: $concessionaire->name }} - {{ $concessionaire->products_count }} items
                        </option>
                    @endforeach
                </select>
                <select name="sort" class="filter-select">
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Reviewed</option>
                </select>
            </form>

            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif

            <!-- Products Grid -->
            <div id="products-grid">
                @if($products->count() > 0)
                    <div class="products-grid">
                        @foreach ($products as $product)
                            <article
                                class="product-card"
                                data-name="{{ strtolower($product->name) }}"
                                data-concessionaire="{{ strtolower($product->concessionaire->business_name ?? $product->concessionaire->name ?? '') }}"
                                data-category="{{ strtolower($product->category) }}"
                                data-concessionaire-id="{{ $product->concessionaire_id }}"
                                data-price="{{ $product->price }}"
                                data-rating="{{ $product->reviews_avg_rating ?? 0 }}"
                                data-reviews="{{ $product->reviews_count ?? 0 }}"
                                data-created-at="{{ optional($product->created_at)->timestamp ?? 0 }}"
                            >
                                <a href="{{ route('products.show', $product) }}" class="p-media" aria-label="View {{ $product->name }}">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <span class="p-media-placeholder" aria-hidden="true">
                                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                            </svg>
                                        </span>
                                    @endif
                                    <span class="p-cat">{{ $product->category }}</span>
                                </a>
                                <div class="p-body">
                                    <h3 class="p-name">
                                        <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                                    </h3>
                                    <div class="p-vendor">
                                        <a href="{{ route('concessionaires.show', $product->concessionaire_id) }}" class="p-vendor-avatar" title="View concessionaire">
                                            {{ strtoupper(substr($product->concessionaire->business_name ?? $product->concessionaire->name, 0, 2)) }}
                                        </a>
                                        <a href="{{ route('concessionaires.show', $product->concessionaire_id) }}" class="p-vendor-name">
                                            {{ $product->concessionaire->business_name ?? $product->concessionaire->name }}
                                        </a>
                                    </div>
                                </div>
                                <div class="p-foot">
                                    <span class="p-price">&#8369;{{ number_format($product->price, 2) }}</span>
                                    <div class="p-rating">
                                        @if(($product->reviews_count ?? 0) > 0)
                                            <div class="stars" aria-hidden="true">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="star {{ $i <= round($product->reviews_avg_rating ?? 0) ? 'filled' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="rating-count">{{ number_format($product->reviews_avg_rating ?? 0, 1) }} ({{ $product->reviews_count }})</span>
                                        @else
                                            <span class="rating-count">No ratings</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($products->hasPages())
                        <div class="pagination-wrap">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                        <h3>No Products Found</h3>
                        <p>There are no products matching your criteria. Try adjusting your filters.</p>
                    </div>
                @endif
            </div>
        </div>

        @if(isset($visibleStocks) && $visibleStocks->count() > 0)
            <section class="stocks-band" aria-label="Campus available items">
                <div class="main-content">
                    <div class="stocks-head">
                        <div>
                            <span class="eyebrow">Campus Registry &mdash; Stockroom</span>
                            <h2 class="stocks-title">Campus Available Items</h2>
                            <p class="stocks-sub">Uniforms, books, and supplies on hand at CvSU Trece Martires City Campus.</p>
                        </div>
                        <p class="stocks-count"><strong>{{ number_format($visibleStocks->count()) }}</strong> {{ \Illuminate\Support\Str::plural('line', $visibleStocks->count()) }} on record</p>
                    </div>

                    <div class="stocks-grid">
                        @foreach($visibleStocks as $stock)
                            @php
                                $stockQty = (int) $stock->quantity;
                                $isOutOfStock = $stockQty === 0;
                                $isLowStock = $stockQty > 0 && $stockQty <= 10;
                            @endphp
                            <a href="{{ route('stocks.show', $stock) }}" class="stock-card {{ $isOutOfStock ? 'is-out' : '' }}">
                                <div class="stock-media">
                                    @if($stock->image)
                                        <img src="{{ asset('storage/' . $stock->image) }}" alt="{{ $stock->item_name }}">
                                    @else
                                        <span class="stock-media-placeholder" aria-hidden="true">
                                            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5 12 3l8.25 4.5L12 12 3.75 7.5Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12 12 16.5 20.25 12"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 16.5 12 21l8.25-4.5"/>
                                            </svg>
                                        </span>
                                    @endif
                                    @if($isOutOfStock)
                                        <span class="stock-status out"><span class="dot" aria-hidden="true"></span>Out of Stock</span>
                                    @elseif($isLowStock)
                                        <span class="stock-status low"><span class="dot" aria-hidden="true"></span>Low Stock</span>
                                    @else
                                        <span class="stock-status"><span class="dot" aria-hidden="true"></span>Available</span>
                                    @endif
                                </div>
                                <div class="stock-body">
                                    @if($stock->item_type)
                                        <div class="stock-kicker">{{ $stock->item_type }}</div>
                                    @endif
                                    <h3 class="stock-name">{{ $stock->item_name }}</h3>
                                </div>
                                <div class="stock-foot">
                                    @if(($stock->item_type ?? null) === 'books' && $stock->unit_price > 0)
                                        <span class="stock-price">&#8369;{{ number_format($stock->unit_price, 2) }}</span>
                                    @endif
                                    @auth
                                        <span class="stock-qty {{ $isOutOfStock ? 'out-of-stock' : ($isLowStock ? 'low-stock' : '') }}">{{ number_format($stockQty) }}<em>on hand</em></span>
                                    @endauth
                                    <span class="stock-view">
                                        View
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3"/>
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-inner">
            <p class="footer-text">&copy; {{ date('Y') }} <a href="/">CvSU</a> &mdash; Trece Martires City Campus. All rights reserved.</p>
            <p class="footer-text">EBA Information System</p>
        </div>
    </footer>

    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }

        const filtersForm = document.getElementById('products-filters-form');
        const productsGrid = document.querySelector('.products-grid');

        if (filtersForm && productsGrid) {
            const searchInput = filtersForm.querySelector('input[name="search"]');
            const categorySelect = filtersForm.querySelector('select[name="category"]');
            const concessionaireSelect = filtersForm.querySelector('select[name="concessionaire_id"]');
            const sortSelect = filtersForm.querySelector('select[name="sort"]');

            const getSortValue = (card, field) => Number(card.dataset[field] || 0);

            const sortCards = (cards, sortValue) => {
                const sortedCards = [...cards];

                sortedCards.sort((leftCard, rightCard) => {
                    switch (sortValue) {
                        case 'price_low':
                            return getSortValue(leftCard, 'price') - getSortValue(rightCard, 'price');
                        case 'price_high':
                            return getSortValue(rightCard, 'price') - getSortValue(leftCard, 'price');
                        case 'rating':
                            return getSortValue(rightCard, 'rating') - getSortValue(leftCard, 'rating')
                                || getSortValue(rightCard, 'reviews') - getSortValue(leftCard, 'reviews');
                        case 'popular':
                            return getSortValue(rightCard, 'reviews') - getSortValue(leftCard, 'reviews')
                                || getSortValue(rightCard, 'rating') - getSortValue(leftCard, 'rating');
                        case 'newest':
                        default:
                            return getSortValue(rightCard, 'createdAt') - getSortValue(leftCard, 'createdAt');
                    }
                });

                sortedCards.forEach((card) => productsGrid.appendChild(card));
            };

            const filterCards = () => {
                const query = (searchInput?.value || '').trim().toLowerCase();
                const selectedCategory = (categorySelect?.value || '').toLowerCase();
                const selectedConcessionaire = concessionaireSelect?.value || '';
                const selectedSort = sortSelect?.value || 'newest';
                const cards = Array.from(productsGrid.querySelectorAll('.product-card'));

                cards.forEach((card) => {
                    const matchesQuery = !query
                        || card.dataset.name.includes(query)
                        || card.dataset.concessionaire.includes(query);
                    const matchesCategory = !selectedCategory || card.dataset.category === selectedCategory;
                    const matchesConcessionaire = !selectedConcessionaire || card.dataset.concessionaireId === selectedConcessionaire;

                    card.classList.toggle('hidden', !(matchesQuery && matchesCategory && matchesConcessionaire));
                });

                sortCards(cards, selectedSort);
            };

            if (searchInput) {
                searchInput.addEventListener('input', filterCards);
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', filterCards);
            }

            if (concessionaireSelect) {
                concessionaireSelect.addEventListener('change', filterCards);
            }

            if (sortSelect) {
                sortSelect.addEventListener('change', filterCards);
            }

            filtersForm.addEventListener('submit', (event) => {
                event.preventDefault();
                filterCards();
            });

            filterCards();
        }
    </script>
</body>
</html>
