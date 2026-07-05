<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Concessionaire Portal</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|ibm-plex-mono:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --green: #0A5C2F;
            --green-dark: #064420;
            --pine: #0A5C2F;
            --pine-strong: #084A26;
            --pine-soft: #EAF3ED;
            --ink: #1A2B21;
            --muted: #66756C;
            --faint: #93A198;
            --paper: #F5F7F5;
            --card: #FFFFFF;
            --line: #E2E8E3;
            --line-strong: #CBD6CE;
            --amber: #B45309;
            --star: #D97706;
            --danger: #B91C1C;
            --font-ui: 'Manrope', ui-sans-serif, system-ui, sans-serif;
            --font-mono: 'IBM Plex Mono', ui-monospace, 'Cascadia Mono', monospace;
            --shadow-card: 0 1px 2px rgba(23, 37, 28, 0.04);
            --shadow-pop: 0 12px 32px rgba(23, 37, 28, 0.14);
        }

        body {
            font-family: var(--font-ui);
            background: var(--paper);
            color: var(--ink);
        }

        /* ---------- Shared components ---------- */
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

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border-radius: 6px;
            font-family: var(--font-ui);
            font-size: 13.5px;
            font-weight: 600;
            line-height: 1;
            padding: 10px 16px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }
        .btn:focus-visible {
            outline: 2px solid rgba(10, 92, 47, 0.45);
            outline-offset: 2px;
        }
        .btn svg { width: 15px; height: 15px; flex-shrink: 0; }
        .btn-primary { background: var(--pine); color: #fff; }
        .btn-primary:hover { background: var(--pine-strong); }
        .btn-secondary { background: #fff; color: var(--ink); border-color: var(--line-strong); }
        .btn-secondary:hover { background: #F6F9F7; border-color: #AEC1B4; }
        .btn-danger-soft { background: #FDF3F3; color: var(--danger); border-color: #F2D8D8; }
        .btn-danger-soft:hover { background: #FAE5E5; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #991B1B; }
        .btn-sm { padding: 7px 12px; font-size: 12.5px; }

        .control {
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            background: #fff;
            color: var(--ink);
            font-family: var(--font-ui);
            font-size: 13.5px;
            padding: 9px 12px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .control::placeholder { color: var(--faint); }
        .control:focus {
            outline: none;
            border-color: var(--pine);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
        }
        select.control {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2366756C' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 34px;
        }

        .alert {
            border-radius: 8px;
            border: 1px solid;
            padding: 12px 16px;
            font-size: 13.5px;
            font-weight: 500;
            line-height: 1.5;
        }
        .alert-success { background: #F0F7F2; border-color: #CDE3D4; color: #14532D; }
        .alert-warning { background: #FDF8EC; border-color: #F0E1BC; color: #92400E; }
        .alert-error { background: #FDF3F3; border-color: #F2D8D8; color: var(--danger); }

        .pop {
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #fff;
            box-shadow: var(--shadow-pop);
            overflow: hidden;
        }

        /* ---------- Stat ledger ---------- */
        .stat-ledger {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }
        .stat-cell {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 22px 24px;
            border-left: 1px solid var(--line);
            min-width: 0;
        }
        .stat-cell:first-child {
            border-left: 0;
        }
        .stat-value-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }
        .stat-value {
            font-family: var(--font-mono);
            font-size: 30px;
            font-weight: 600;
            letter-spacing: -0.03em;
            line-height: 1;
            color: var(--ink);
            font-variant-numeric: tabular-nums;
        }
        .stat-value-sm {
            font-size: 17px;
            line-height: 1.35;
        }
        .stat-unit {
            font-family: var(--font-mono);
            font-size: 13px;
            color: var(--faint);
        }
        .stat-foot {
            margin-top: auto;
            font-size: 12.5px;
            color: var(--muted);
        }
        @media (max-width: 1024px) {
            .stat-ledger {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .stat-cell:nth-child(odd) {
                border-left: 0;
            }
            .stat-cell:nth-child(n+3) {
                border-top: 1px solid var(--line);
            }
        }
        @media (max-width: 560px) {
            .stat-ledger {
                grid-template-columns: 1fr;
            }
            .stat-cell {
                border-left: 0;
            }
            .stat-cell + .stat-cell {
                border-top: 1px solid var(--line);
            }
        }

        /* ---------- Sidebar ---------- */
        .sb {
            background: #0B3120;
            border-right: 1px solid #0A2A1B;
        }
        .sb-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 18px 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .sb-brand-logo {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: #fff;
            padding: 3px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .sb-brand-logo-fallback {
            width: 36px;
            height: 36px;
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
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.45);
            margin-top: 3px;
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
            color: #B9CDBF;
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
            background: #7BD3A0;
        }
        .sb-item.sb-item-danger { color: #E7B4B4; }
        .sb-item.sb-item-danger:hover { background: rgba(185, 28, 28, 0.22); color: #FCDCDC; }

        /* ---------- Navbar ---------- */
        .nb {
            background: #fff;
            border-bottom: 1px solid var(--line);
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
        .nb-icon-btn:focus-visible { outline: 2px solid rgba(10, 92, 47, 0.45); outline-offset: 2px; }
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
            background: #fff;
            border-color: var(--pine);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
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
            background: #fff;
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
        .nb-user-btn:focus-visible { outline: 2px solid rgba(10, 92, 47, 0.45); outline-offset: 2px; }
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
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--amber);
            box-shadow: 0 0 0 2px #fff;
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
            transition: background-color 0.15s ease;
        }
        .pop-item svg { width: 15px; height: 15px; color: var(--muted); flex-shrink: 0; }
        .pop-item:hover { background: var(--pine-soft); }
        .pop-item.pop-item-danger { color: var(--danger); }
        .pop-item.pop-item-danger svg { color: var(--danger); }
        .pop-item.pop-item-danger:hover { background: #FDF3F3; }

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
        $layoutUser = $user ?? auth()->user();
        $layoutUserName = $layoutUser?->name ?? 'Concessionaire';
        $layoutUserEmail = $layoutUser?->email ?? 'concessionaire@example.com';
        $layoutInitial = strtoupper(substr((string) $layoutUserName, 0, 1));
        $layoutAvatarUrl = $layoutUser?->profile_photo
            ? asset('storage/' . $layoutUser->profile_photo)
            : null;

        $productsRoute = \Illuminate\Support\Facades\Route::has('concessionaire.products.index')
            ? route('concessionaire.products.index')
            : route('concessionaire.products');

        $mediaRoute = route('concessionaire.media');

        $settingsRoute = \Illuminate\Support\Facades\Route::has('concessionaire.settings')
            ? route('concessionaire.settings')
            : route('profile.edit');

        $homeRoute = \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/');
        $hasPaymentAlert = ($hasOverduePayment ?? false) || ($isDueSoon ?? false);
        $ebaLogoUrl = file_exists(public_path('images/eba-logo.png'))
            ? asset('images/eba-logo.png')
            : null;
    @endphp

    <aside id="concessionaire-sidebar" class="sb fixed top-0 left-0 z-40 h-screen w-64 -translate-x-full transition-transform duration-300 lg:translate-x-0" aria-label="Concessionaire sidebar" data-drawer-backdrop="true">
        <div class="flex h-full flex-col">
            <div class="sb-brand">
                @if($ebaLogoUrl)
                    <img src="{{ $ebaLogoUrl }}" alt="EBA Logo" class="sb-brand-logo">
                @else
                    <div class="sb-brand-logo-fallback">EBA</div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="sb-brand-name">EBA Information System</p>
                    <p class="sb-brand-campus">CvSU · TMC Campus</p>
                </div>
                <button type="button" class="nb-icon-btn mobile-only" style="color:rgba(255,255,255,0.6);" data-drawer-hide="concessionaire-sidebar" aria-controls="concessionaire-sidebar">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-3.5 w-3.5" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-3 py-5">
                <div class="mb-6">
                    <h3 class="sb-group-label">Main</h3>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ route('concessionaire.dashboard') }}" class="sb-item {{ request()->routeIs('concessionaire.dashboard') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h3 class="sb-group-label">Management</h3>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ $productsRoute }}" class="sb-item {{ request()->routeIs('concessionaire.products') || request()->routeIs('concessionaire.products.*') || request()->routeIs('concessionaire.products.index') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 0 1-2-2V3h20v2a2 2 0 0 1-2 2Zm0 0v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7"/>
                                </svg>
                                <span>Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('concessionaire.reviews') }}" class="sb-item {{ request()->routeIs('concessionaire.reviews') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                <span>Reviews</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $mediaRoute }}" class="sb-item {{ request()->routeIs('concessionaire.media') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Media</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('concessionaire.payments') }}" class="sb-item {{ request()->routeIs('concessionaire.payments') || request()->routeIs('concessionaire.payments.*') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Payments</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h3 class="sb-group-label">General</h3>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ $settingsRoute }}" class="sb-item {{ request()->routeIs('concessionaire.settings') || request()->routeIs('profile.edit') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $homeRoute }}" class="sb-item">
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
                        <button type="button" class="nb-icon-btn mobile-only" data-drawer-target="concessionaire-sidebar" data-drawer-toggle="concessionaire-sidebar" aria-controls="concessionaire-sidebar">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-5 w-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 5A.75.75 0 0 1 2.75 9h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 9.75Zm0 5A.75.75 0 0 1 2.75 14h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z"/>
                            </svg>
                        </button>

                        @php
                            $breadcrumbs = [];
                            if (request()->routeIs('concessionaire.dashboard')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'active' => true]
                                ];
                            } elseif (request()->routeIs('concessionaire.products*')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('concessionaire.dashboard')],
                                    ['label' => 'Products', 'active' => true]
                                ];
                            } elseif (request()->routeIs('concessionaire.reviews*')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('concessionaire.dashboard')],
                                    ['label' => 'Reviews', 'active' => true]
                                ];
                            } elseif (request()->routeIs('concessionaire.media*')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('concessionaire.dashboard')],
                                    ['label' => 'Media', 'active' => true]
                                ];
                            } elseif (request()->routeIs('concessionaire.payments*')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('concessionaire.dashboard')],
                                    ['label' => 'Payments', 'active' => true]
                                ];
                            } elseif (request()->routeIs('concessionaire.settings*') || request()->routeIs('profile.edit')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('concessionaire.dashboard')],
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
                            placeholder="Search pages or products…"
                            autocomplete="off"
                            role="combobox"
                            aria-expanded="false"
                            aria-controls="portal-search-panel"
                        >
                        <kbd class="nb-search-kbd">Ctrl K</kbd>
                        <div id="portal-search-panel" class="nb-search-panel pop" role="listbox"></div>
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2.5">
                        <button id="payment-notification-button" type="button" class="nb-icon-btn" title="Notifications" data-dropdown-toggle="payment-notification-dropdown" data-dropdown-placement="bottom-end">
                            <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($hasPaymentAlert)
                                <span class="nb-badge" aria-hidden="true"></span>
                            @endif
                        </button>

                        <div id="payment-notification-dropdown" class="pop z-50 hidden w-[320px]" role="menu" aria-labelledby="payment-notification-button">
                            <div class="border-b px-4 py-3" style="border-color: var(--line);">
                                <span class="panel-title" style="font-size: 13.5px;">Notifications</span>
                            </div>
                            <div class="p-2">
                                @if($hasPaidThisMonth ?? false)
                                    @php
                                        $lastPayment = \App\Models\ConcessionairePayment::where('concessionaire_id', auth()->id())->latest('payment_date')->first();
                                    @endphp
                                    <a href="{{ route('concessionaire.payments') }}" class="flex items-start gap-3 rounded-md p-2.5 transition-colors" style="text-decoration:none;" onmouseover="this.style.background='var(--pine-soft)'" onmouseout="this.style.background='transparent'">
                                        <span class="mt-0.5 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md" style="background:#F0F7F2;color:var(--pine);">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="block text-[13px] font-bold" style="color:#14532D;">Payment recorded this month</span>
                                            <span class="mt-0.5 block text-xs leading-snug" style="color:var(--muted);">
                                                ₱{{ number_format((float) auth()->user()->monthly_fee, 2) }} recorded{{ $lastPayment ? ' on ' . \Carbon\Carbon::parse($lastPayment->payment_date)->format('M d') : '' }}
                                            </span>
                                        </span>
                                    </a>
                                @elseif($isDueSoon ?? false)
                                    <a href="{{ route('concessionaire.payments') }}" class="flex items-start gap-3 rounded-md p-2.5 transition-colors" style="text-decoration:none;" onmouseover="this.style.background='#FDF8EC'" onmouseout="this.style.background='transparent'">
                                        <span class="mt-0.5 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md" style="background:#FDF8EC;color:#92400E;">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="block text-[13px] font-bold" style="color:#92400E;">Payment due soon</span>
                                            <span class="mt-0.5 block text-xs leading-snug" style="color:var(--muted);">
                                                Your monthly fee of ₱{{ number_format((float) auth()->user()->monthly_fee, 2) }} is due by the 1st
                                            </span>
                                        </span>
                                    </a>
                                @elseif($hasOverduePayment ?? false)
                                    <a href="{{ route('concessionaire.payments') }}" class="flex items-start gap-3 rounded-md p-2.5 transition-colors" style="text-decoration:none;" onmouseover="this.style.background='#FDF3F3'" onmouseout="this.style.background='transparent'">
                                        <span class="mt-0.5 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md" style="background:#FDF3F3;color:var(--danger);">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="block text-[13px] font-bold" style="color:var(--danger);">Payment overdue</span>
                                            <span class="mt-0.5 block text-xs leading-snug" style="color:var(--muted);">
                                                No payment recorded for this month
                                            </span>
                                        </span>
                                    </a>
                                @else
                                    <div class="flex items-start gap-3 p-2.5">
                                        <span class="mt-0.5 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md" style="background:var(--paper);color:var(--muted);">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="block text-[13px] font-bold" style="color:var(--ink);">No contract</span>
                                            <span class="mt-0.5 block text-xs leading-snug" style="color:var(--muted);">
                                                No active contract on file
                                            </span>
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="border-t p-2" style="border-color: var(--line);">
                                <a href="{{ route('concessionaire.payments') }}" class="pop-item" style="justify-content:center;border-radius:6px;color:var(--pine);">
                                    View payment page
                                </a>
                            </div>
                        </div>

                        <div class="hidden h-6 w-px sm:block" style="background: var(--line);"></div>

                        <button id="concessionaire-user-menu-button" type="button" class="nb-user-btn" data-dropdown-toggle="concessionaire-user-menu" data-dropdown-placement="bottom-end" aria-expanded="false">
                            @if($layoutAvatarUrl)
                                <img src="{{ $layoutAvatarUrl }}" alt="{{ $layoutUserName }}" class="nb-avatar">
                            @else
                                <span class="nb-avatar-fallback">{{ $layoutInitial }}</span>
                            @endif
                            <span class="hidden lg:flex lg:flex-col lg:items-start">
                                <span class="max-w-[130px] truncate text-[13px] font-bold" style="color:var(--ink);">{{ $layoutUserName }}</span>
                                <span class="eyebrow" style="font-size:9.5px;">Concessionaire</span>
                            </span>
                            <svg class="hidden h-3.5 w-3.5 lg:block" style="color:var(--muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div id="concessionaire-user-menu" class="pop z-50 hidden w-64" role="menu" aria-labelledby="concessionaire-user-menu-button">
                            <div class="border-b px-4 py-3.5" style="border-color: var(--line);">
                                <div class="flex items-center gap-3">
                                    @if($layoutAvatarUrl)
                                        <img src="{{ $layoutAvatarUrl }}" alt="{{ $layoutUserName }}" class="nb-avatar" style="width:38px;height:38px;">
                                    @else
                                        <span class="nb-avatar-fallback" style="width:38px;height:38px;font-size:15px;">{{ $layoutInitial }}</span>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-[13px] font-bold" style="color:var(--ink);">{{ $layoutUserName }}</div>
                                        <div class="truncate text-xs" style="color:var(--muted);">{{ $layoutUserEmail }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-1.5">
                                <a href="{{ $settingsRoute }}" class="pop-item">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Account settings</span>
                                </a>
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
            @yield('content')
        </main>
    </div>

    <script>
        (() => {
            const input = document.getElementById('portal-search-input');
            const panel = document.getElementById('portal-search-panel');
            if (!input || !panel) return;

            const pages = [
                { label: 'Dashboard', hint: 'Page', url: @json(route('concessionaire.dashboard')), keywords: 'home overview stats ratings summary' },
                { label: 'Products', hint: 'Page', url: @json($productsRoute), keywords: 'catalog menu items food listing add product' },
                { label: 'Reviews', hint: 'Page', url: @json(route('concessionaire.reviews')), keywords: 'ratings feedback stars customers students' },
                { label: 'Media', hint: 'Page', url: @json($mediaRoute), keywords: 'banner carousel image description store photo' },
                { label: 'Payments', hint: 'Page', url: @json(route('concessionaire.payments')), keywords: 'fees rent receipt monthly invoice history' },
                { label: 'Settings', hint: 'Page', url: @json($settingsRoute), keywords: 'profile account password name email' },
            ];
            const productsSearchUrl = @json($productsRoute);

            const pageIcon = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg>';
            const searchIcon = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>';

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

                options = matches.map((page) => ({ url: page.url, label: page.label, hint: page.hint, icon: pageIcon }));
                if (query !== '') {
                    options.push({
                        url: productsSearchUrl + '?q=' + encodeURIComponent(input.value.trim()),
                        label: 'Search products for "' + input.value.trim() + '"',
                        hint: 'Products',
                        icon: searchIcon,
                    });
                }

                if (!options.length) {
                    panel.innerHTML = '<div class="nb-search-empty">Nothing matches that search.</div>';
                } else {
                    panel.innerHTML = '<div class="nb-search-group">Go to</div>' + options.map((option, index) =>
                        '<a class="nb-search-option" role="option" data-index="' + index + '" href="' + escapeHtml(option.url) + '">'
                        + option.icon
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
                    } else if (input.value.trim() !== '') {
                        window.location.href = productsSearchUrl + '?q=' + encodeURIComponent(input.value.trim());
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

    @yield('scripts')
</body>
</html>
