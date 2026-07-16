<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Faculty') | EBA Information System</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|ibm-plex-mono:400,500,600&display=swap" rel="stylesheet" />
    <script>
        // Apply saved theme before first paint to avoid a light-mode flash.
        (function () {
            try {
                if (localStorage.getItem('eba-faculty-theme') === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --green: #1F2937;
            --green-dark: #111827;
            --pine: #1F2937;
            --pine-strong: #111827;
            --pine-soft: #EEF0F3;
            --ink: #1B232B;
            --muted: #687180;
            --faint: #98A1AD;
            --paper: #F5F6F8;
            --card: #FFFFFF;
            --line: #E4E7EB;
            --line-strong: #CBD1D8;
            --amber: #B45309;
            --danger: #B91C1C;
            /* Green success/primary accent — the ONLY green on faculty, reserved
               for positive actions + confirmations so they read apart from the
               graphite chrome. Neutral chrome stays graphite (--pine). */
            --success: #0A5C2F;
            --success-strong: #094B27;
            --success-soft: #F0F7F2;
            --success-line: #CDE3D4;
            --success-text: #14532D;
            --field: #FFFFFF;
            --hover: #F2F4F6;
            --hover-2: #E9EDF1;
            --header-h: 65px;
            --font-ui: 'Manrope', ui-sans-serif, system-ui, sans-serif;
            --font-mono: 'IBM Plex Mono', ui-monospace, 'Cascadia Mono', monospace;
            --shadow-card: 0 1px 2px rgba(17, 24, 39, 0.05);
            --shadow-pop: 0 12px 32px rgba(17, 24, 39, 0.14);
        }

        /* ---------- Dark theme (graphite, no green — matches faculty monochrome) ---------- */
        html[data-theme="dark"] {
            color-scheme: dark;
            --green: #A9B4C4;
            --green-dark: #C5CDD9;
            --pine: #A9B4C4;
            --pine-strong: #C5CDD9;
            --pine-soft: rgba(169, 180, 196, 0.12);
            --ink: #E7EAEF;
            --muted: #9CA5B1;
            --faint: #6C7581;
            --paper: #0F1216;
            --card: #171B21;
            --line: #262C35;
            --line-strong: #39424E;
            --amber: #E3A448;
            --danger: #E36A6A;
            /* Green stays green in dark (mint) — success/primary accent only. */
            --success: #7BD3A0;
            --success-strong: #6BC492;
            --success-soft: rgba(30, 149, 96, 0.16);
            --success-line: rgba(30, 149, 96, 0.40);
            --success-text: #8CD6AF;
            --field: #1A1F26;
            --hover: #1C222A;
            --hover-2: #222933;
            /* Checkout component accent (co-modal head, cash badge) → green. */
            --accent-soft: rgba(30, 149, 96, 0.16);
            --accent-line: rgba(30, 149, 96, 0.40);
            --accent-text: #8CD6AF;
            --shadow-card: 0 1px 2px rgba(0, 0, 0, 0.40);
            --shadow-pop: 0 12px 32px rgba(0, 0, 0, 0.55);
        }

        body {
            font-family: var(--font-ui);
            background: var(--paper);
            color: var(--ink);
        }

        /* ---------- Shared primitives ---------- */
        .eyebrow {
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: var(--shadow-card);
        }
        .panel-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.01em;
        }
        .panel-sub {
            font-size: 13px;
            color: var(--muted);
            margin-top: 2px;
        }

        .pop {
            border-radius: 10px;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: var(--shadow-pop);
            overflow: hidden;
        }

        .page-title {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--ink);
            margin: 0 0 6px;
            line-height: 1.2;
        }
        .page-subtitle {
            color: var(--muted);
            margin: 0 0 20px;
            font-size: 13.5px;
        }

        .alert {
            border-radius: 8px;
            border: 1px solid;
            padding: 12px 16px;
            margin-bottom: 18px;
            font-size: 13.5px;
            font-weight: 500;
            line-height: 1.5;
        }
        .alert-success { background: var(--success-soft); border-color: var(--success-line); color: var(--success-text); }
        .alert-error { background: #FDF3F3; border-color: #F2D8D8; color: var(--danger); }

        /* ---------- Legacy shared components (used across faculty pages) ---------- */
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-card);
        }
        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
        }
        .card-body {
            padding: 0;
            overflow-x: auto;
        }
        .toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--field);
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            padding: 0 12px;
            flex: 1;
            min-width: 220px;
            max-width: 360px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .search-box:focus-within {
            border-color: var(--pine);
            box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.12);
        }
        .search-box input {
            border: none;
            background: transparent;
            padding: 9px 0;
            font-size: 13.5px;
            font-family: inherit;
            width: 100%;
            outline: none;
            color: var(--ink);
        }
        .search-box input::placeholder { color: var(--faint); }
        .filter-select {
            padding: 9px 34px 9px 12px;
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            font-size: 13.5px;
            font-family: inherit;
            background-color: var(--field);
            color: var(--ink);
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23687180' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--pine);
            box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.12);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid transparent;
            border-radius: 6px;
            padding: 9px 15px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
            white-space: nowrap;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }
        .btn:focus-visible {
            outline: 2px solid rgba(31, 41, 55, 0.40);
            outline-offset: 2px;
        }
        .btn-green {
            background: var(--success);
            color: #fff;
        }
        .btn-green:hover {
            background: var(--success-strong);
        }
        .btn-outline {
            background: var(--field);
            color: var(--ink);
            border-color: var(--line-strong);
        }
        .btn-outline:hover {
            background: var(--hover);
            border-color: #AEB6C0;
        }
        .btn-red {
            background: #FDF3F3;
            color: var(--danger);
            border-color: #F2D8D8;
        }
        .btn-red:hover {
            background: #FAE5E5;
        }
        .btn-danger {
            background: var(--danger);
            color: #fff;
        }
        .btn-danger:hover {
            background: #991B1B;
        }
        .btn-sm {
            padding: 6px 11px;
            font-size: 12.5px;
        }
        .btn svg { width: 15px; height: 15px; flex-shrink: 0; }
        .btn-sm svg { width: 14px; height: 14px; }
        .pagination-wrap {
            padding: 14px 20px;
            border-top: 1px solid var(--line);
        }

        /* ---------- Pagination ---------- */
        .pg {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            width: 100%;
        }
        .pg-info {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
        }
        .pg-list {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .pg-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 10px;
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            background: var(--field);
            color: var(--ink);
            font-family: var(--font-mono);
            font-size: 12.5px;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }
        .pg-btn:hover {
            background: var(--hover);
            border-color: #AEB6C0;
        }
        .pg-btn.is-current {
            background: var(--pine);
            border-color: var(--pine);
            color: #fff;
        }
        .pg-btn.is-disabled {
            color: var(--faint);
            background: var(--paper);
            pointer-events: none;
        }
        .pg-dots {
            color: var(--faint);
            font-family: var(--font-mono);
            font-size: 12px;
            padding: 0 2px;
        }
        @media (max-width: 860px) {
            .toolbar {
                align-items: stretch;
            }
            .search-box,
            .filter-select {
                max-width: none;
                width: 100%;
            }
        }

        /* ---------- Sidebar ---------- */
        .sb {
            background: #181D24;
            border-right: 1px solid #10141A;
        }
        .sb-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            height: var(--header-h);
            padding: 0 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-sizing: border-box;
        }
        .sb-brand-logo {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: #fff;
            padding: 3px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .sb-brand-logo-fallback {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.04em;
            flex-shrink: 0;
        }
        .sb-brand-name {
            font-size: 13.5px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }
        .sb-brand-campus {
            font-family: var(--font-mono);
            font-size: 9.5px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.45);
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sb-group-label {
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.38);
            padding: 0 12px;
            margin-bottom: 6px;
        }
        .sb-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 11px;
            border-radius: 6px;
            padding: 9px 12px;
            font-size: 13.5px;
            font-weight: 600;
            color: #A7B0BC;
            text-decoration: none;
            transition: background-color 0.15s ease, color 0.15s ease;
        }
        .sb-item svg { width: 17px; height: 17px; flex-shrink: 0; opacity: 0.85; }
        .sb-item:hover { background: rgba(255, 255, 255, 0.06); color: #fff; }
        .sb-item.is-active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .sb-item.is-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 8px;
            width: 2px;
            border-radius: 2px;
            background: #CAD2DC;
        }
        .sb-item.sb-item-danger { color: #E7B4B4; }
        .sb-item.sb-item-danger:hover { background: rgba(185, 28, 28, 0.22); color: #FCDCDC; }

        /* ---------- Navbar ---------- */
        .nb {
            background: var(--card);
            border-bottom: 1px solid var(--line);
        }
        /* Lock the navbar and sidebar brand block to the same height so their
           bottom borders align (nav border sits at var(--header-h)). */
        .nb > div {
            height: calc(var(--header-h) - 1px);
            padding-top: 0;
            padding-bottom: 0;
            display: flex;
            align-items: center;
        }
        .nb > div > div {
            flex: 1;
            min-width: 0;
        }
        .nb-icon-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 6px;
            border: 1px solid transparent;
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }
        .nb-icon-btn:hover { background: var(--pine-soft); color: var(--pine); }
        .nb-icon-btn:focus-visible { outline: 2px solid rgba(31, 41, 55, 0.40); outline-offset: 2px; }
        @media (min-width: 1024px) {
            .mobile-only { display: none !important; }
        }
        .nb-crumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--muted);
            white-space: nowrap;
        }
        .nb-crumbs a {
            color: var(--muted);
            text-decoration: none;
            font-weight: 500;
        }
        .nb-crumbs a:hover { color: var(--pine); }
        .nb-crumb-sep { color: var(--line-strong); }
        .nb-crumb-current { color: var(--ink); font-weight: 700; }

        .nb-search { position: relative; width: 100%; max-width: 380px; }
        .nb-search-input {
            width: 100%;
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            background: var(--paper);
            color: var(--ink);
            font-family: var(--font-ui);
            font-size: 13px;
            padding: 8px 62px 8px 34px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }
        .nb-search-input::placeholder { color: var(--faint); }
        .nb-search-input:focus {
            outline: none;
            background: var(--field);
            border-color: var(--pine);
            box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.12);
        }
        .nb-search-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: var(--faint);
            pointer-events: none;
        }
        .nb-search-kbd {
            position: absolute;
            right: 9px;
            top: 50%;
            transform: translateY(-50%);
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--faint);
            border: 1px solid var(--line);
            border-radius: 4px;
            background: var(--card);
            padding: 2px 5px;
            pointer-events: none;
        }
        .nb-search-panel {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            z-index: 60;
            display: none;
            padding: 6px;
        }
        .nb-search-panel.is-open { display: block; }
        .nb-search-group {
            font-family: var(--font-mono);
            font-size: 10px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--faint);
            padding: 8px 10px 4px;
        }
        .nb-search-option {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            border-radius: 6px;
            padding: 9px 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            text-decoration: none;
            cursor: pointer;
        }
        .nb-search-option svg { width: 15px; height: 15px; color: var(--muted); flex-shrink: 0; }
        .nb-search-option small {
            font-weight: 500;
            font-size: 12px;
            color: var(--muted);
            margin-left: auto;
            white-space: nowrap;
        }
        .nb-search-option.is-selected,
        .nb-search-option:hover { background: var(--pine-soft); color: var(--pine-strong); }
        .nb-search-option.is-selected svg,
        .nb-search-option:hover svg { color: var(--pine); }
        .nb-search-empty {
            padding: 12px 10px;
            font-size: 13px;
            color: var(--muted);
        }

        .nb-user-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid transparent;
            border-radius: 6px;
            background: transparent;
            padding: 4px 8px 4px 4px;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }
        .nb-user-btn:hover { background: var(--pine-soft); }
        .nb-user-btn:focus-visible { outline: 2px solid rgba(31, 41, 55, 0.40); outline-offset: 2px; }
        .nb-avatar {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .nb-avatar-fallback {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: var(--pine);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .nb-badge {
            position: absolute;
            top: 7px;
            right: 8px;
            min-width: 15px;
            height: 15px;
            padding: 0 4px;
            border-radius: 999px;
            background: var(--amber);
            color: #fff;
            font-family: var(--font-mono);
            font-size: 9px;
            font-weight: 600;
            line-height: 15px;
            text-align: center;
            box-shadow: 0 0 0 2px var(--card);
        }

        .pop-item {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            text-decoration: none;
            text-align: left;
            background: none;
            border: 0;
            cursor: pointer;
            font-family: inherit;
            transition: background-color 0.15s ease;
        }
        .pop-item svg { width: 15px; height: 15px; color: var(--muted); flex-shrink: 0; }
        .pop-item:hover { background: var(--pine-soft); }
        .pop-item.pop-item-danger { color: var(--danger); }
        .pop-item.pop-item-danger svg { color: var(--danger); }
        .pop-item.pop-item-danger:hover { background: #FDF3F3; }

        /* ---------- Dark-only overrides ---------- */
        /* --success is light mint and --pine flips to light graphite in dark, so
           anything sitting on either needs dark text instead of white. */
        html[data-theme="dark"] .btn-green { color: #10151B; }
        html[data-theme="dark"] .pg-btn.is-current { color: #10151B; }
        html[data-theme="dark"] .nb-avatar-fallback { color: #10151B; }
        html[data-theme="dark"] .btn-outline:hover { border-color: var(--line-strong); }
        html[data-theme="dark"] .pg-btn:hover { border-color: var(--line-strong); }
        html[data-theme="dark"] .btn-red {
            background: rgba(227, 106, 106, 0.12);
            border-color: rgba(227, 106, 106, 0.35);
            color: #F0A0A0;
        }
        html[data-theme="dark"] .btn-red:hover { background: rgba(227, 106, 106, 0.2); }
        html[data-theme="dark"] .btn-danger { color: #fff; }
        html[data-theme="dark"] .alert-error {
            background: rgba(227, 106, 106, 0.12);
            border-color: rgba(227, 106, 106, 0.35);
            color: #F0A0A0;
        }
        html[data-theme="dark"] .pop-item.pop-item-danger:hover { background: rgba(227, 106, 106, 0.12); }
        html[data-theme="dark"] .filter-select {
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239CA5B1' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        }
        html[data-theme="dark"] .search-box:focus-within,
        html[data-theme="dark"] .filter-select:focus,
        html[data-theme="dark"] .nb-search-input:focus {
            box-shadow: 0 0 0 3px rgba(169, 180, 196, 0.18);
        }
        html[data-theme="dark"] .btn:focus-visible,
        html[data-theme="dark"] .nb-icon-btn:focus-visible,
        html[data-theme="dark"] .nb-user-btn:focus-visible {
            outline-color: rgba(169, 180, 196, 0.55);
        }
        /* Sidebar is dark in both themes; nudge it to match the dark page chrome. */
        html[data-theme="dark"] .sb { background: #12161C; border-right-color: #0B0E12; }

        /* Theme toggle: show the icon for the mode you'd switch to. */
        .nb-theme-btn .icon-moon { display: inline-flex; }
        .nb-theme-btn .icon-sun { display: none; }
        html[data-theme="dark"] .nb-theme-btn .icon-moon { display: none; }
        html[data-theme="dark"] .nb-theme-btn .icon-sun { display: inline-flex; }
        .nb-theme-btn svg { width: 22px; height: 22px; }
        .nb-icon-btn svg { width: 22px; height: 22px; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    @yield('extra-css')
</head>
<body class="antialiased">
    @php
        $facultyUser = auth()->user();
        $facultyName = $facultyUser?->name ?? 'Faculty';
        $facultyEmail = $facultyUser?->email ?? 'faculty@example.com';
        $facultyInitial = strtoupper(substr((string) $facultyName, 0, 1));
        $facultyAvatarUrl = $facultyUser?->profile_photo
            ? asset('storage/' . $facultyUser->profile_photo)
            : null;
        $ebaLogoUrl = file_exists(public_path('images/eba-logo.png'))
            ? asset('images/eba-logo.png')
            : null;

        $partnershipsRoute = \Illuminate\Support\Facades\Route::has('staff.partnerships')
            ? route('staff.partnerships')
            : route('staff.partnerships.index');
        $concessionairesRoute = \Illuminate\Support\Facades\Route::has('staff.concessionaires')
            ? route('staff.concessionaires')
            : route('staff.concessionaires.index');
        $settingsRoute = \Illuminate\Support\Facades\Route::has('staff.settings')
            ? route('staff.settings')
            : null;

        $unreadPayments = $unreadPayments ?? collect();
        $unreadCount = $unreadCount ?? 0;
    @endphp

    <aside id="faculty-sidebar" class="sb fixed top-0 left-0 z-40 h-screen w-64 -translate-x-full transition-transform duration-300 lg:translate-x-0" aria-label="Faculty sidebar" data-drawer-backdrop="true">
        <div class="flex h-full flex-col">
            <div class="sb-brand">
                @if($ebaLogoUrl)
                    <img src="{{ $ebaLogoUrl }}" alt="EBA Logo" class="sb-brand-logo">
                @else
                    <div class="sb-brand-logo-fallback">EBA</div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="sb-brand-name">EBA Information System</p>
                    <p class="sb-brand-campus">CvSU &middot; TMC Campus</p>
                </div>
                <button type="button" class="nb-icon-btn mobile-only" style="color:rgba(255,255,255,0.6);" data-drawer-hide="faculty-sidebar" aria-controls="faculty-sidebar">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-3.5 w-3.5" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-3 py-5">
                <div class="mb-6">
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ route('staff.dashboard') }}" class="sb-item {{ request()->routeIs('staff.dashboard') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h3 class="sb-group-label">Concessionaire</h3>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ $partnershipsRoute }}" class="sb-item {{ request()->routeIs('staff.partnerships') || request()->routeIs('staff.partnerships.*') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Partnerships</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $concessionairesRoute }}" class="sb-item {{ request()->routeIs('staff.concessionaires') || request()->routeIs('staff.concessionaires.*') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Fee Tracking</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h3 class="sb-group-label">POS</h3>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ route('staff.stocks.index') }}" class="sb-item {{ request()->routeIs('staff.stocks*') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 12 3l9 4.5-9 4.5-9-4.5Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9 4.5 9-4.5"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5 12 21l9-4.5"/>
                                </svg>
                                <span>Stocks</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.uniform-checkout') }}" class="sb-item {{ request()->routeIs('staff.uniform-checkout*') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Item Checkout</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.transaction-logs') }}" class="sb-item {{ request()->routeIs('staff.transaction-logs') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h6m-6 8h6m-6 0H7a2 2 0 01-2-2V7a2 2 0 012-2h6m0 0V3m0 2v2m4 4h2m-2 4h2"/>
                                </svg>
                                <span>Transaction Logs</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h3 class="sb-group-label">General</h3>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ route('staff.history') }}" class="sb-item {{ request()->routeIs('staff.history') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>History</span>
                            </a>
                        </li>
                        @if ($settingsRoute)
                            <li>
                                <a href="{{ $settingsRoute }}" class="sb-item {{ request()->routeIs('staff.settings') || request()->routeIs('staff.settings.*') ? 'is-active' : '' }}">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Settings</span>
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ url('/') }}" class="sb-item">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                <span>Back to Main Site</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 px-3 py-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sb-item sb-item-danger w-full">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="relative min-h-screen lg:ml-64">
        <nav class="nb fixed top-0 z-30 w-full lg:w-[calc(100%-16rem)]">
            <div class="px-4 py-3 lg:px-8">
                <div class="flex items-center justify-between gap-3 sm:gap-5">
                    <div class="flex min-w-0 items-center gap-2 sm:gap-4">
                        <button type="button" class="nb-icon-btn mobile-only" data-drawer-target="faculty-sidebar" data-drawer-toggle="faculty-sidebar" aria-controls="faculty-sidebar">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-5 w-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 5A.75.75 0 0 1 2.75 9h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 9.75Zm0 5A.75.75 0 0 1 2.75 14h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z"/>
                            </svg>
                        </button>

                        @php
                            $breadcrumbs = [];
                            if (request()->routeIs('staff.dashboard')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'active' => true]
                                ];
                            } elseif (request()->routeIs('staff.partnerships.show')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                    ['label' => 'Partnerships', 'url' => route('staff.partnerships.index')],
                                    ['label' => 'Review', 'active' => true]
                                ];
                            } elseif (request()->routeIs('staff.partnerships*')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                    ['label' => 'Partnerships', 'active' => true]
                                ];
                            } elseif (request()->routeIs('staff.concessionaires*')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                    ['label' => 'Fee Tracking', 'active' => true]
                                ];
                            } elseif (request()->routeIs('staff.stocks*')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                    ['label' => 'Stocks', 'active' => true]
                                ];
                            } elseif (request()->routeIs('staff.uniform-checkout*')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                    ['label' => 'Item Checkout', 'active' => true]
                                ];
                            } elseif (request()->routeIs('staff.transaction-logs')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                    ['label' => 'Transaction Logs', 'active' => true]
                                ];
                            } elseif (request()->routeIs('staff.history*')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                    ['label' => 'History', 'active' => true]
                                ];
                            } elseif (request()->routeIs('staff.settings*')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                    ['label' => 'Settings', 'active' => true]
                                ];
                            }
                        @endphp
                        @if(count($breadcrumbs) > 0)
                            <div class="nb-crumbs hidden md:flex" aria-label="Breadcrumb">
                                @foreach($breadcrumbs as $index => $crumb)
                                    @if($index > 0)
                                        <span class="nb-crumb-sep">/</span>
                                    @endif
                                    @if(isset($crumb['active']) && $crumb['active'])
                                        <span class="nb-crumb-current">{{ $crumb['label'] }}</span>
                                    @else
                                        <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="nb-search hidden sm:block">
                        <svg class="nb-search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input
                            id="portal-search-input"
                            class="nb-search-input"
                            type="search"
                            placeholder="Search pages&hellip;"
                            autocomplete="off"
                            role="combobox"
                            aria-expanded="false"
                            aria-controls="portal-search-panel"
                        >
                        <kbd class="nb-search-kbd">Ctrl K</kbd>
                        <div id="portal-search-panel" class="nb-search-panel pop" role="listbox"></div>
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2.5">
                        <button type="button" class="nb-icon-btn nb-theme-btn" id="theme-toggle" title="Toggle dark mode" aria-label="Toggle dark mode">
                            <svg class="icon-moon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <svg class="icon-sun" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="12" r="4"/>
                                <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32l1.41-1.41"/>
                            </svg>
                        </button>
                        <button id="payment-notification-button" type="button" class="nb-icon-btn" title="Notifications" data-dropdown-toggle="payment-notification-dropdown" data-dropdown-placement="bottom-end" data-unread-count="{{ $unreadCount }}">
                            <svg class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($unreadCount > 0)
                                <span id="payment-notification-badge" class="nb-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </button>

                        <div id="payment-notification-dropdown" class="pop z-50 hidden w-[320px]" role="menu" aria-labelledby="payment-notification-button">
                            <div class="border-b px-4 py-3" style="border-color: var(--line);">
                                <span class="panel-title" style="font-size: 13.5px;">Notifications</span>
                            </div>
                            <div class="max-h-[300px] overflow-y-auto p-2">
                                @if($unreadCount > 0)
                                    @foreach($unreadPayments as $payment)
                                        <a href="{{ route('staff.transaction-logs') }}" class="flex items-start gap-3 rounded-md p-2.5 transition-colors" style="text-decoration:none;" onmouseover="this.style.background='var(--pine-soft)'" onmouseout="this.style.background='transparent'">
                                            <span class="mt-0.5 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md" style="background:var(--pine-soft);color:var(--pine);">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                            <span>
                                                <span class="block text-[13px] font-bold" style="color:var(--ink);">Payment recorded</span>
                                                <span class="mt-0.5 block text-xs leading-snug" style="color:var(--muted);">
                                                    {{ $payment->concessionaire?->business_name ?: ($payment->concessionaire?->name ?: 'Concessionaire') }} paid &#8369;{{ number_format((float) $payment->amount, 2) }}
                                                </span>
                                            </span>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="flex items-start gap-3 p-2.5">
                                        <span class="mt-0.5 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md" style="background:var(--paper);color:var(--muted);">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="block text-[13px] font-bold" style="color:var(--ink);">No new payments</span>
                                            <span class="mt-0.5 block text-xs leading-snug" style="color:var(--muted);">
                                                You're all caught up
                                            </span>
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="border-t p-2" style="border-color: var(--line);">
                                <a href="{{ route('staff.transaction-logs') }}" class="pop-item" style="justify-content:center;border-radius:6px;color:var(--pine);">
                                    View transaction logs
                                </a>
                            </div>
                        </div>

                        <div class="hidden h-6 w-px sm:block" style="background: var(--line);"></div>

                        <button id="faculty-user-menu-button" type="button" class="nb-user-btn" data-dropdown-toggle="faculty-user-menu" data-dropdown-placement="bottom-end" aria-expanded="false">
                            @if($facultyAvatarUrl)
                                <img src="{{ $facultyAvatarUrl }}" alt="{{ $facultyName }}" class="nb-avatar">
                            @else
                                <span class="nb-avatar-fallback">{{ $facultyInitial }}</span>
                            @endif
                            <span class="hidden lg:flex lg:flex-col lg:items-start">
                                <span class="max-w-[130px] truncate text-[13px] font-bold" style="color:var(--ink);">{{ $facultyName }}</span>
                                <span class="eyebrow" style="font-size:9.5px;">Faculty</span>
                            </span>
                            <svg class="hidden h-3.5 w-3.5 lg:block" style="color:var(--muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div id="faculty-user-menu" class="pop z-50 hidden w-64" role="menu" aria-labelledby="faculty-user-menu-button">
                            <div class="border-b px-4 py-3.5" style="border-color: var(--line);">
                                <div class="flex items-center gap-3">
                                    @if($facultyAvatarUrl)
                                        <img src="{{ $facultyAvatarUrl }}" alt="{{ $facultyName }}" class="nb-avatar" style="width:38px;height:38px;">
                                    @else
                                        <span class="nb-avatar-fallback" style="width:38px;height:38px;font-size:15px;">{{ $facultyInitial }}</span>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-[13px] font-bold" style="color:var(--ink);">{{ $facultyName }}</div>
                                        <div class="truncate text-xs" style="color:var(--muted);">{{ $facultyEmail }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="pop-item pop-item-danger">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto w-full max-w-[1320px] px-4 pb-10 pt-[76px] lg:px-8 lg:pt-[84px]">
            @php
                $suppressSuccessAlert = trim($__env->yieldContent('suppress-success-alert')) === '1';
            @endphp

            @if (session('success') && ! $suppressSuccessAlert)
                <div class="alert alert-success">{{ session('success') }}</div>
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

            @yield('content')
        </main>
    </div>

    <script>
        // ---------- Dark mode toggle ----------
        (() => {
            const btn = document.getElementById('theme-toggle');
            if (!btn) return;
            btn.addEventListener('click', () => {
                const root = document.documentElement;
                const dark = root.getAttribute('data-theme') !== 'dark';
                if (dark) {
                    root.setAttribute('data-theme', 'dark');
                } else {
                    root.removeAttribute('data-theme');
                }
                try { localStorage.setItem('eba-faculty-theme', dark ? 'dark' : 'light'); } catch (e) {}
                window.dispatchEvent(new CustomEvent('eba:theme', { detail: { theme: dark ? 'dark' : 'light' } }));
            });
        })();

        document.addEventListener('DOMContentLoaded', () => {
            const notificationButton = document.getElementById('payment-notification-button');
            const notificationBadge = document.getElementById('payment-notification-badge');

            if (!notificationButton) {
                return;
            }

            let markedRead = false;

            notificationButton.addEventListener('click', () => {
                if (markedRead) {
                    return;
                }

                const unreadCount = Number(notificationButton.dataset.unreadCount || '0');
                if (unreadCount < 1) {
                    return;
                }

                fetch("{{ route('staff.notifications.mark-read') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                }).then((response) => {
                    if (!response.ok) {
                        return;
                    }

                    markedRead = true;
                    notificationButton.dataset.unreadCount = '0';
                    if (notificationBadge) {
                        notificationBadge.remove();
                    }
                }).catch(() => {
                    // Keep UI unchanged if request fails.
                });
            });
        });

        (() => {
            const input = document.getElementById('portal-search-input');
            const panel = document.getElementById('portal-search-panel');
            if (!input || !panel) return;

            const pages = [
                { label: 'Dashboard', hint: 'Page', url: @json(route('staff.dashboard')), keywords: 'home overview stats charts applications summary' },
                { label: 'Partnerships', hint: 'Page', url: @json($partnershipsRoute), keywords: 'applications review approve reject recommend loi documents wizard' },
                { label: 'Fee Tracking', hint: 'Page', url: @json($concessionairesRoute), keywords: 'concessionaires vendors stores business monthly fee edit contract set fee' },
                { label: 'Stocks', hint: 'Page', url: @json(route('staff.stocks.index')), keywords: 'uniform inventory sizes quantity add stock items' },
                { label: 'Item Checkout', hint: 'Page', url: @json(route('staff.uniform-checkout')), keywords: 'uniform sell pos sale student purchase cart' },
                { label: 'Transaction Logs', hint: 'Page', url: @json(route('staff.transaction-logs')), keywords: 'payments sales records receipts logs' },
                { label: 'History', hint: 'Page', url: @json(route('staff.history')), keywords: 'past archive previous contracts records' },
            ];

            const pageIcon = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg>';

            let options = [];
            let selectedIndex = -1;

            const escapeHtml = (value) => value.replace(/[&<>"']/g, (ch) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[ch]));

            const closePanel = () => {
                panel.classList.remove('is-open');
                input.setAttribute('aria-expanded', 'false');
                selectedIndex = -1;
            };

            const render = () => {
                const query = input.value.trim().toLowerCase();
                const matches = query === ''
                    ? pages
                    : pages.filter((page) =>
                        page.label.toLowerCase().includes(query) || page.keywords.includes(query));

                options = matches.map((page) => ({ url: page.url, label: page.label, hint: page.hint }));

                if (!options.length) {
                    panel.innerHTML = '<div class="nb-search-empty">No pages match that search.</div>';
                } else {
                    panel.innerHTML = '<div class="nb-search-group">Go to</div>' + options.map((option, index) =>
                        '<a class="nb-search-option" role="option" data-index="' + index + '" href="' + escapeHtml(option.url) + '">'
                        + pageIcon
                        + '<span>' + escapeHtml(option.label) + '</span>'
                        + '<small>' + escapeHtml(option.hint) + '</small>'
                        + '</a>'
                    ).join('');
                }

                selectedIndex = -1;
                panel.classList.add('is-open');
                input.setAttribute('aria-expanded', 'true');
            };

            const highlight = () => {
                panel.querySelectorAll('.nb-search-option').forEach((el, index) => {
                    el.classList.toggle('is-selected', index === selectedIndex);
                });
            };

            input.addEventListener('input', render);
            input.addEventListener('focus', render);

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closePanel();
                    input.blur();
                    return;
                }
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (!panel.classList.contains('is-open')) render();
                    if (!options.length) return;
                    const step = event.key === 'ArrowDown' ? 1 : -1;
                    selectedIndex = (selectedIndex + step + options.length) % options.length;
                    highlight();
                    return;
                }
                if (event.key === 'Enter') {
                    event.preventDefault();
                    if (selectedIndex >= 0 && options[selectedIndex]) {
                        window.location.href = options[selectedIndex].url;
                    } else if (options.length) {
                        window.location.href = options[0].url;
                    }
                }
            });

            document.addEventListener('click', (event) => {
                if (!panel.contains(event.target) && event.target !== input) {
                    closePanel();
                }
            });

            document.addEventListener('keydown', (event) => {
                const tag = document.activeElement ? document.activeElement.tagName : '';
                const typing = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT';
                if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
                    event.preventDefault();
                    input.focus();
                } else if (event.key === '/' && !typing) {
                    event.preventDefault();
                    input.focus();
                }
            });
        })();
    </script>

    @livewireScripts
    @yield('scripts')
</body>
</html>
