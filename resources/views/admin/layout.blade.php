<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | EBA Information System</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|ibm-plex-mono:400,500,600&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green: #0A5C2F;
            --green-light: #0D7A3E;
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
            --danger: #B91C1C;
            --font-ui: 'Manrope', ui-sans-serif, system-ui, sans-serif;
            --font-mono: 'IBM Plex Mono', ui-monospace, 'Cascadia Mono', monospace;
            --shadow-card: 0 1px 2px rgba(23, 37, 28, 0.04);
            --shadow-pop: 0 12px 32px rgba(23, 37, 28, 0.14);
            --sidebar-w: 256px;
            --header-h: 60px;
        }
        body {
            font-family: var(--font-ui);
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== Shared typography ===== */
        .eyebrow {
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .page-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .page-title {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--ink);
            margin-top: 4px;
            line-height: 1.15;
        }
        .page-date {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            padding-bottom: 3px;
        }
        .page-head-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
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

        /* ===== Buttons ===== */
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
            padding: 9px 15px;
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
        .btn-primary, .btn-green { background: var(--pine); color: #fff; }
        .btn-primary:hover, .btn-green:hover { background: var(--pine-strong); }
        .btn-secondary, .btn-outline { background: #fff; color: var(--ink); border-color: var(--line-strong); }
        .btn-secondary:hover, .btn-outline:hover { background: #F6F9F7; border-color: #AEC1B4; }
        .btn-red, .btn-danger-soft { background: #FDF3F3; color: var(--danger); border-color: #F2D8D8; }
        .btn-red:hover, .btn-danger-soft:hover { background: #FAE5E5; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #991B1B; }
        .btn-sm { padding: 7px 12px; font-size: 12.5px; }
        .btn-xs { padding: 6px 10px; font-size: 12px; }
        .btn[disabled], .btn:disabled { opacity: 0.6; cursor: not-allowed; }

        /* ===== Controls ===== */
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
        .toolbar {
            display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        }
        .search-box {
            display: flex; align-items: center; gap: 8px;
            background: #fff; border: 1px solid var(--line-strong); border-radius: 6px;
            padding: 0 12px; flex: 1; max-width: 320px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .search-box:focus-within {
            border-color: var(--pine);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
        }
        .search-box svg { width: 15px; height: 15px; color: var(--faint); flex-shrink: 0; }
        .search-box input {
            border: none; background: transparent; padding: 9px 0; font-size: 13px;
            font-family: var(--font-ui); width: 100%; outline: none; color: var(--ink);
        }
        .search-box input::placeholder { color: var(--faint); }
        .filter-select {
            padding: 9px 34px 9px 12px; border: 1px solid var(--line-strong); border-radius: 6px;
            font-size: 13px; font-family: var(--font-ui); background: #fff; color: var(--ink);
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2366756C' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--pine);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
        }

        /* ===== Dropdown pops ===== */
        .pop {
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #fff;
            box-shadow: var(--shadow-pop);
            overflow: hidden;
        }
        .pop-item {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 16px;
            font-family: var(--font-ui);
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

        /* ===== Alerts ===== */
        .alert {
            border-radius: 8px;
            border: 1px solid;
            padding: 12px 16px;
            font-size: 13.5px;
            font-weight: 500;
            line-height: 1.5;
            margin-bottom: 16px;
        }
        .alert-success { background: #F0F7F2; border-color: #CDE3D4; color: #14532D; }
        .alert-warning { background: #FDF8EC; border-color: #F0E1BC; color: #92400E; }
        .alert-error { background: #FDF3F3; border-color: #F2D8D8; color: var(--danger); }

        /* ===== Cards & tables ===== */
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }
        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; flex-wrap: wrap;
            padding: 16px 20px; border-bottom: 1px solid var(--line);
        }
        .card-header h3 {
            font-size: 15px; font-weight: 700; letter-spacing: -0.01em;
        }
        .card-body { padding: 0; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            text-align: left; padding: 11px 20px;
            font-family: var(--font-mono);
            font-size: 10.5px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted);
            background: #FAFCFA; border-bottom: 1px solid var(--line);
            white-space: nowrap;
        }
        tbody td {
            padding: 13px 20px; font-size: 13.5px; border-bottom: 1px solid var(--line);
            vertical-align: middle;
        }
        tbody tr { transition: background-color 0.12s ease; }
        tbody tr:hover { background: #FAFCFA; }
        tbody tr:last-child td { border-bottom: none; }
        .table-num {
            font-family: var(--font-mono);
            font-variant-numeric: tabular-nums;
            font-weight: 600;
            color: var(--ink);
            white-space: nowrap;
        }
        .table-num.is-pine { color: var(--pine); }
        .table-strong { font-weight: 700; color: var(--ink); }
        .table-dim { color: var(--faint); }
        .user-cell { display: flex; align-items: center; gap: 12px; }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 6px; background: var(--pine);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; flex-shrink: 0;
        }
        .user-name { font-weight: 700; color: var(--ink); }
        .user-email { font-size: 12.5px; color: var(--muted); }
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 5px;
            font-family: var(--font-mono);
            font-size: 11px; font-weight: 600;
            white-space: nowrap;
        }
        .badge::before {
            content: '';
            width: 5px; height: 5px; border-radius: 999px; background: currentColor;
        }
        .badge-admin { background: #E5F3EA; color: #14532D; }
        .badge-user { background: #F0F2F0; color: var(--muted); }
        .badge-warning { background: #FDF8EC; color: #92400E; }
        .badge-success { background: #E5F3EA; color: #14532D; }
        .badge-danger { background: #FBEAEA; color: #B3261E; }
        .badge-secondary { background: #F0F2F0; color: var(--muted); }
        .empty-state {
            background: #fff;
            border: 1px dashed var(--line-strong);
            border-radius: 10px;
            padding: 28px;
            text-align: center;
            color: var(--muted);
            font-size: 13.5px;
            margin: 16px;
        }

        /* ===== Pagination (bootstrap-5 markup, custom styling) ===== */
        .pagination-wrap { padding: 14px 20px; border-top: 1px solid var(--line); }
        .pagination-wrap nav { width: 100%; }
        .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }
        .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between > div:first-child p {
            margin: 0;
            font-size: 12.5px;
            color: var(--muted);
            white-space: nowrap;
        }
        .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between > div:first-child p .fw-semibold {
            font-family: var(--font-mono);
            font-weight: 600;
            color: var(--ink);
        }
        .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between > div:last-child {
            margin-left: auto;
        }
        .pagination-wrap .d-flex.justify-content-between.flex-fill.d-sm-none { display: none; }
        .pagination-wrap .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .pagination-wrap .page-item { list-style: none; }
        .pagination-wrap .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 5px 11px; border-radius: 6px; font-size: 12.5px; font-weight: 600;
            text-decoration: none;
            color: var(--ink); border: 1px solid var(--line-strong);
            background: #fff;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }
        .pagination-wrap .page-item.disabled .page-link {
            color: var(--faint);
            background: #FAFCFA;
            border-color: var(--line);
            cursor: not-allowed;
        }
        .pagination-wrap .page-link:hover { background: #F6F9F7; border-color: #AEC1B4; }
        .pagination-wrap .page-item.active .page-link {
            background: var(--pine); color: #fff; border-color: var(--pine);
        }
        .pagination-wrap svg { width: 14px; height: 14px; }
        @media (max-width: 640px) {
            .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between { display: none; }
            .pagination-wrap .d-flex.justify-content-between.flex-fill.d-sm-none {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }
        }

        /* ===== Sidebar ===== */
        .sb {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: #0B3120;
            border-right: 1px solid #0A2A1B;
            display: flex; flex-direction: column;
            z-index: 100;
            transition: transform 0.3s ease;
        }
        .sb-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 18px 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
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
        .sb-brand-text { min-width: 0; flex: 1; }
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
        .sb-nav {
            flex: 1;
            overflow-y: auto;
            padding: 20px 12px;
        }
        .sb-group { margin-bottom: 24px; }
        .sb-group:last-child { margin-bottom: 0; }
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
        .sb-group ul { list-style: none; display: flex; flex-direction: column; gap: 2px; }
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
        .sb-foot {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px;
        }
        .sb-foot form { margin: 0; }
        .sb-foot .sb-item {
            width: 100%;
            background: none;
            border: 0;
            font-family: var(--font-ui);
            cursor: pointer;
        }
        .sb-foot .sb-item.sb-item-danger:hover { background: rgba(185, 28, 28, 0.22); }
        .sb-backdrop {
            position: fixed; inset: 0;
            background: rgba(23, 37, 28, 0.5);
            z-index: 99;
            display: none;
        }
        .sb-backdrop.is-open { display: block; }

        /* ===== Navbar ===== */
        .nb {
            position: fixed; top: 0; right: 0; left: var(--sidebar-w);
            height: var(--header-h); z-index: 90;
            background: #fff;
            border-bottom: 1px solid var(--line);
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px;
            padding: 0 28px;
        }
        .nb-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .nb-right { display: flex; align-items: center; gap: 10px; }
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
        .nb-icon-btn svg { width: 19px; height: 19px; }
        .nb-icon-btn:hover { background: var(--pine-soft); color: var(--pine); }
        .nb-icon-btn:focus-visible { outline: 2px solid rgba(10, 92, 47, 0.45); outline-offset: 2px; }
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
            transition: color 0.15s ease;
        }
        .nb-crumbs a:hover { color: var(--pine); }
        .nb-crumb-sep { color: var(--line-strong); }
        .nb-crumb-current { color: var(--ink); font-weight: 700; }

        .nb-search { position: relative; flex: 1; max-width: 380px; }
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
            z-index: 160;
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

        .nb-badge {
            position: absolute;
            top: 3px;
            right: 3px;
            min-width: 16px;
            height: 16px;
            padding: 0 4px;
            border-radius: 999px;
            background: var(--danger);
            color: #fff;
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            line-height: 1;
        }
        .nb-pop-wrap { position: relative; }
        .nb-pop {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            z-index: 150;
            display: none;
        }
        .nb-pop.is-open { display: block; }
        .nb-divider { width: 1px; height: 24px; background: var(--line); }

        .nb-user-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid transparent;
            border-radius: 6px;
            background: transparent;
            font-family: var(--font-ui);
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
        .nb-user-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 1px;
        }
        .nb-user-name {
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 13px;
            font-weight: 700;
            color: var(--ink);
        }
        .nb-user-chevron { width: 14px; height: 14px; color: var(--muted); }

        /* Notifications dropdown */
        .notif-pop { width: 320px; }
        .notif-head {
            padding: 12px 16px;
            border-bottom: 1px solid var(--line);
            font-size: 13.5px;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: var(--ink);
        }
        .notif-list {
            max-height: 320px;
            overflow-y: auto;
            padding: 6px;
        }
        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            text-decoration: none;
            border-radius: 8px;
            padding: 10px;
            transition: background-color 0.15s ease;
        }
        a.notif-item:hover { background: var(--pine-soft); }
        .notif-item-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            background: var(--pine-soft);
            color: var(--pine);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .notif-item-icon svg { width: 15px; height: 15px; }
        .notif-item-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 2px;
        }
        .notif-item-meta {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.35;
        }
        .notif-item-time {
            font-family: var(--font-mono);
            font-size: 10.5px;
            color: var(--faint);
            margin-top: 3px;
        }
        .notif-foot {
            border-top: 1px solid var(--line);
            background: #FAFCFA;
            padding: 6px;
        }
        .notif-foot-link {
            display: block;
            text-align: center;
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--pine);
            border-radius: 6px;
            padding: 9px 12px;
            transition: background-color 0.15s ease;
        }
        .notif-foot-link:hover { background: var(--pine-soft); }

        /* ===== Main ===== */
        .main-content {
            margin-left: var(--sidebar-w);
            padding-top: var(--header-h);
            min-height: 100vh;
        }
        .page-body {
            max-width: 1440px;
            margin: 0 auto;
            padding: 24px 28px 40px;
        }
        .mobile-only { display: none; }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .sb { transform: translateX(-100%); }
            .sb.is-open { transform: translateX(0); }
            .nb { left: 0; padding: 0 16px; }
            .main-content { margin-left: 0; }
            .page-body { padding: 20px 16px 32px; }
            .mobile-only { display: inline-flex; }
            .nb-crumbs { display: none; }
            .nb-search { max-width: none; }
            .nb-user-meta, .nb-user-chevron { display: none; }
            .card-body { overflow-x: auto; }
            table { min-width: 600px; }
        }
        @media (max-width: 480px) {
            .nb-search { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    @yield('extra-css')
</head>
<body>
    @php
        $unreadApplicationSteps = $unreadApplicationSteps ?? collect();
        $unreadApplicationCount = $unreadApplicationCount ?? 0;
        $adminUser = auth()->user();
        $adminName = $adminUser?->name ?? 'Administrator';
        $adminEmail = $adminUser?->email ?? '';
        $adminInitials = $adminUser && method_exists($adminUser, 'initials')
            ? $adminUser->initials()
            : strtoupper(substr((string) $adminName, 0, 1));
        $adminAvatarUrl = $adminUser?->profile_photo
            ? asset('storage/' . $adminUser->profile_photo)
            : null;
        $ebaLogoUrl = file_exists(public_path('images/eba-logo.png'))
            ? asset('images/eba-logo.png')
            : null;
    @endphp

    <!-- SIDEBAR -->
    <aside class="sb" id="admin-sidebar" aria-label="Admin sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sb-brand">
            @if($ebaLogoUrl)
                <img src="{{ $ebaLogoUrl }}" alt="EBA Logo" class="sb-brand-logo">
            @else
                <div class="sb-brand-logo-fallback">EBA</div>
            @endif
            <div class="sb-brand-text">
                <p class="sb-brand-name">EBA Admin Panel</p>
                <p class="sb-brand-campus">CvSU · Trece Martires</p>
            </div>
        </a>

        <nav class="sb-nav">
            <div class="sb-group">
                <h3 class="sb-group-label">Main</h3>
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="sb-item {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sb-group">
                <h3 class="sb-group-label">Management</h3>
                <ul>
                    <li>
                        <a href="{{ route('admin.users') }}" class="sb-item {{ request()->routeIs('admin.users') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.partnerships') }}" class="sb-item {{ request()->routeIs('admin.partnerships') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Partnerships</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sb-group">
                <h3 class="sb-group-label">Concessionaires</h3>
                <ul>
                    <li>
                        <a href="{{ route('admin.concessionaires') }}" class="sb-item {{ request()->routeIs('admin.concessionaires*') || request()->routeIs('admin.payments*') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l1-5h16l1 5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 9v11a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V9"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 21v-6h6v6"/>
                            </svg>
                            <span>Fee Tracking</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.record-payment') }}" class="sb-item {{ request()->routeIs('admin.record-payment*') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <rect x="2" y="6" width="20" height="12" rx="2"/>
                                <circle cx="12" cy="12" r="2.5"/>
                                <path stroke-linecap="round" d="M6 12h.01M18 12h.01"/>
                            </svg>
                            <span>Record Payment</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reviews') }}" class="sb-item {{ request()->routeIs('admin.reviews*') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 17v4"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 2 2 4-4"/>
                            </svg>
                            <span>Reviews</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sb-group">
                <h3 class="sb-group-label">POS</h3>
                <ul>
                    <li>
                        <a href="{{ route('admin.stocks') }}" class="sb-item {{ request()->routeIs('admin.stocks*') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9 4.5 9-4.5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5L12 21l9-4.5"/>
                            </svg>
                            <span>Stocks</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.uniform-checkout') }}" class="sb-item {{ request()->routeIs('admin.uniform-checkout') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <circle cx="9" cy="21" r="1"/>
                                <circle cx="20" cy="21" r="1"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            <span>Checkout</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.transaction-logs') }}" class="sb-item {{ request()->routeIs('admin.transaction-logs*') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
                            </svg>
                            <span>Transaction Logs</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sb-group">
                <h3 class="sb-group-label">System</h3>
                <ul>
                    <li>
                        <a href="{{ route('admin.site-settings') }}" class="sb-item {{ request()->routeIs('admin.site-settings*') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Site Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.activity-logs') }}" class="sb-item {{ request()->routeIs('admin.activity-logs*') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <span>Activity Logs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.logs') }}" class="sb-item {{ request()->routeIs('admin.logs*') ? 'is-active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
                            </svg>
                            <span>System Logs</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="sb-foot">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="sb-item sb-item-danger">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>
    <div class="sb-backdrop" id="sb-backdrop"></div>

    <!-- NAVBAR -->
    <header class="nb">
        <div class="nb-left">
            <button type="button" class="nb-icon-btn mobile-only" id="sb-toggle" aria-controls="admin-sidebar" aria-expanded="false">
                <span class="sr-only" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);">Open sidebar</span>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 12h18M3 6h18M3 18h18"/></svg>
            </button>

            @php
                $breadcrumbs = [];
                if (request()->routeIs('admin.dashboard')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.users*')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'Users', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.stocks*')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'Stocks', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.partnerships*')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'Partnerships', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.uniform-checkout')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'Uniform Checkout', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.record-payment*')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'Record Payment', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.concessionaires*') || request()->routeIs('admin.payments*')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'Concessionaires', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.reviews*')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'Reviews', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.activity-logs*')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'Activity Logs', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.logs*')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'System Logs', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.transaction-logs*')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'Transaction Logs', 'active' => true]
                    ];
                } elseif (request()->routeIs('admin.site-settings*')) {
                    $breadcrumbs = [
                        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                        ['label' => 'Site Settings', 'active' => true]
                    ];
                }
            @endphp
            @if(count($breadcrumbs) > 0)
                <div class="nb-crumbs" aria-label="Breadcrumb">
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

        <div class="nb-search">
            <svg class="nb-search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input
                id="portal-search-input"
                class="nb-search-input"
                type="search"
                placeholder="Search pages, users or partnerships…"
                autocomplete="off"
                role="combobox"
                aria-expanded="false"
                aria-controls="portal-search-panel"
            >
            <kbd class="nb-search-kbd">Ctrl K</kbd>
            <div id="portal-search-panel" class="nb-search-panel pop" role="listbox"></div>
        </div>

        <div class="nb-right">
            <div class="nb-pop-wrap">
                <button id="application-notification-button" type="button" class="nb-icon-btn" title="Notifications" data-unread-count="{{ $unreadApplicationCount }}" aria-expanded="false" aria-controls="application-notification-dropdown">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unreadApplicationCount > 0)
                        <span id="application-notification-badge" class="nb-badge">{{ $unreadApplicationCount }}</span>
                    @endif
                </button>

                <div id="application-notification-dropdown" class="nb-pop pop notif-pop" role="menu" aria-labelledby="application-notification-button">
                    <div class="notif-head">Notifications</div>
                    <div class="notif-list">
                        @if($unreadApplicationCount > 0)
                            @foreach($unreadApplicationSteps as $event)
                                <a href="{{ route('admin.partnerships', ['search' => $event['concessionaire_name']]) }}" class="notif-item">
                                    <div class="notif-item-icon">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="notif-item-title">{{ $event['step_label'] }}</p>
                                        <p class="notif-item-meta">{{ $event['concessionaire_name'] }}</p>
                                        <p class="notif-item-time">{{ $event['submitted_at']->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="notif-item">
                                <div class="notif-item-icon" style="background:#F0F2F0;color:var(--muted);">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="notif-item-title">No new submissions</p>
                                    <p class="notif-item-meta">You're all caught up.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="notif-foot">
                        <a href="{{ route('admin.partnerships') }}" class="notif-foot-link">View Partnerships &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="nb-divider"></div>

            <div class="nb-pop-wrap">
                <button id="admin-user-menu-button" type="button" class="nb-user-btn" aria-expanded="false" aria-controls="admin-user-menu">
                    @if($adminAvatarUrl)
                        <img src="{{ $adminAvatarUrl }}" alt="{{ $adminName }}" class="nb-avatar">
                    @else
                        <span class="nb-avatar-fallback">{{ $adminInitials }}</span>
                    @endif
                    <span class="nb-user-meta">
                        <span class="nb-user-name">{{ $adminName }}</span>
                        <span class="eyebrow" style="font-size:9.5px;">Administrator</span>
                    </span>
                    <svg class="nb-user-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="admin-user-menu" class="nb-pop pop" style="width:256px;" role="menu" aria-labelledby="admin-user-menu-button">
                    <div style="border-bottom:1px solid var(--line);padding:14px 16px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            @if($adminAvatarUrl)
                                <img src="{{ $adminAvatarUrl }}" alt="{{ $adminName }}" class="nb-avatar" style="width:38px;height:38px;">
                            @else
                                <span class="nb-avatar-fallback" style="width:38px;height:38px;font-size:15px;">{{ $adminInitials }}</span>
                            @endif
                            <div style="min-width:0;flex:1;">
                                <div style="font-size:13px;font-weight:700;color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $adminName }}</div>
                                <div style="font-size:12px;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $adminEmail }}</div>
                            </div>
                        </div>
                    </div>
                    <div style="padding:6px 0;">
                        <form method="POST" action="{{ route('admin.logout') }}">
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
    </header>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="page-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        // ---------- Mobile sidebar drawer ----------
        (() => {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('sb-backdrop');
            const toggle = document.getElementById('sb-toggle');
            if (!sidebar || !backdrop || !toggle) return;

            const setOpen = (open) => {
                sidebar.classList.toggle('is-open', open);
                backdrop.classList.toggle('is-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            };

            toggle.addEventListener('click', () => setOpen(!sidebar.classList.contains('is-open')));
            backdrop.addEventListener('click', () => setOpen(false));
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') setOpen(false);
            });
        })();

        // ---------- Navbar dropdowns (notifications + user menu) ----------
        (() => {
            const pairs = [
                ['application-notification-button', 'application-notification-dropdown'],
                ['admin-user-menu-button', 'admin-user-menu'],
            ];

            const closeAll = () => {
                pairs.forEach(([btnId, popId]) => {
                    const btn = document.getElementById(btnId);
                    const pop = document.getElementById(popId);
                    if (btn && pop) {
                        pop.classList.remove('is-open');
                        btn.setAttribute('aria-expanded', 'false');
                    }
                });
            };

            pairs.forEach(([btnId, popId]) => {
                const btn = document.getElementById(btnId);
                const pop = document.getElementById(popId);
                if (!btn || !pop) return;

                btn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const willOpen = !pop.classList.contains('is-open');
                    closeAll();
                    if (willOpen) {
                        pop.classList.add('is-open');
                        btn.setAttribute('aria-expanded', 'true');
                    }
                });
            });

            document.addEventListener('click', (event) => {
                if (!event.target.closest('.nb-pop-wrap')) closeAll();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeAll();
            });
        })();

        // ---------- Mark application notifications as read ----------
        (() => {
            const notificationButton = document.getElementById('application-notification-button');
            const notificationBadge = document.getElementById('application-notification-badge');
            if (!notificationButton) return;

            let markedRead = false;

            notificationButton.addEventListener('click', () => {
                if (markedRead) return;

                const unreadCount = Number(notificationButton.dataset.unreadCount || '0');
                if (unreadCount < 1) return;

                fetch("{{ route('admin.notifications.mark-read') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                }).then((response) => {
                    if (!response.ok) return;
                    markedRead = true;
                    notificationButton.dataset.unreadCount = '0';
                    if (notificationBadge) notificationBadge.remove();
                }).catch(() => {
                    // Keep UI unchanged if request fails.
                });
            });
        })();

        // ---------- Ctrl+K page search palette ----------
        (() => {
            const input = document.getElementById('portal-search-input');
            const panel = document.getElementById('portal-search-panel');
            if (!input || !panel) return;

            const pages = [
                { label: 'Dashboard', hint: 'Page', url: @json(route('admin.dashboard')), keywords: 'home overview stats charts summary' },
                { label: 'Users', hint: 'Page', url: @json(route('admin.users')), keywords: 'accounts roles staff cashier faculty concessionaire create' },
                { label: 'Stocks', hint: 'Page', url: @json(route('admin.stocks')), keywords: 'uniforms inventory items sizes prices quantity' },
                { label: 'Uniform Checkout', hint: 'Page', url: @json(route('admin.uniform-checkout')), keywords: 'sale sell pos cart books uniforms checkout' },
                { label: 'Record Payment', hint: 'Page', url: @json(route('admin.record-payment')), keywords: 'collections monthly fee concessionaire cash arrears advance record' },
                { label: 'Partnerships', hint: 'Page', url: @json(route('admin.partnerships')), keywords: 'applications loi documents approve reject wizard' },
                { label: 'Concessionaires', hint: 'Page', url: @json(route('admin.concessionaires')), keywords: 'payments monthly fees stalls vendors business' },
                { label: 'Reviews', hint: 'Page', url: @json(route('admin.reviews')), keywords: 'ratings feedback stores products stars' },
                { label: 'Transaction Logs', hint: 'Page', url: @json(route('admin.transaction-logs')), keywords: 'orders purchases receipts sales' },
                { label: 'Site Settings', hint: 'Page', url: @json(route('admin.site-settings')), keywords: 'cms landing content homepage hero about' },
                { label: 'Activity Logs', hint: 'Page', url: @json(route('admin.activity-logs')), keywords: 'audit actions history admin trail' },
                { label: 'System Logs', hint: 'Page', url: @json(route('admin.logs')), keywords: 'errors laravel debug exceptions' },
            ];
            const usersSearchUrl = @json(route('admin.users'));
            const partnershipsSearchUrl = @json(route('admin.partnerships'));

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
                        url: usersSearchUrl + '?search=' + encodeURIComponent(input.value.trim()),
                        label: 'Search users for "' + input.value.trim() + '"',
                        hint: 'Users',
                        icon: searchIcon,
                    });
                    options.push({
                        url: partnershipsSearchUrl + '?search=' + encodeURIComponent(input.value.trim()),
                        label: 'Search partnerships for "' + input.value.trim() + '"',
                        hint: 'Partnerships',
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
                        window.location.href = usersSearchUrl + '?search=' + encodeURIComponent(input.value.trim());
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
