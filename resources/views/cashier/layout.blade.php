<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Cashier Portal</title>
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
                if (localStorage.getItem('eba-cashier-theme') === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
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
            --danger: #B91C1C;
            --field: #FFFFFF;
            --hover: #F6F9F7;
            --font-ui: 'Manrope', ui-sans-serif, system-ui, sans-serif;
            --font-mono: 'IBM Plex Mono', ui-monospace, 'Cascadia Mono', monospace;
            --shadow-card: 0 1px 2px rgba(23, 37, 28, 0.04);
            --shadow-pop: 0 12px 32px rgba(23, 37, 28, 0.14);
        }

        /* ---------- Dark theme (mint on forest — keeps the portal's green identity) ---------- */
        html[data-theme="dark"] {
            color-scheme: dark;
            --green: #7BD3A0;
            --green-dark: #97E0B4;
            --pine: #7BD3A0;
            --pine-strong: #97E0B4;
            --pine-soft: rgba(123, 211, 160, 0.12);
            --ink: #E6EDE8;
            --muted: #98A89E;
            --faint: #66756C;
            --paper: #0D1210;
            --card: #151C18;
            --line: #232D26;
            --line-strong: #35443A;
            --amber: #E3A448;
            --danger: #E36A6A;
            --field: #181F1B;
            --hover: #1B241F;
            --shadow-card: 0 1px 2px rgba(0, 0, 0, 0.40);
            --shadow-pop: 0 12px 32px rgba(0, 0, 0, 0.55);
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
        .btn-primary, .btn-green { background: var(--pine); color: #fff; }
        .btn-primary:hover, .btn-green:hover { background: var(--pine-strong); }
        .btn-secondary, .btn-outline { background: var(--field); color: var(--ink); border-color: var(--line-strong); }
        .btn-secondary:hover, .btn-outline:hover { background: var(--hover); border-color: var(--line-strong); }
        .btn-danger-soft { background: #FDF3F3; color: var(--danger); border-color: #F2D8D8; }
        .btn-danger-soft:hover { background: #FAE5E5; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #991B1B; }
        .btn-sm { padding: 7px 12px; font-size: 12.5px; }
        .btn-xs { padding: 6px 10px; font-size: 12px; }
        .btn[disabled], .btn:disabled { opacity: 0.6; cursor: not-allowed; }

        .control {
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            background: var(--field);
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
            margin-bottom: 16px;
        }
        .alert-success { background: #F0F7F2; border-color: #CDE3D4; color: #14532D; }
        .alert-warning { background: #FDF8EC; border-color: #F0E1BC; color: #92400E; }
        .alert-error { background: #FDF3F3; border-color: #F2D8D8; color: var(--danger); }

        .pop {
            border-radius: 10px;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: var(--shadow-pop);
            overflow: hidden;
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

        /* ---------- Notification bell + dropdown rows ---------- */
        .nb-badge {
            position: absolute;
            top: 7px;
            right: 8px;
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--amber);
            box-shadow: 0 0 0 2px var(--card);
        }
        .nb-badge-danger { background: var(--danger); }
        .notif-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border-radius: 6px;
            padding: 10px;
            text-decoration: none;
            transition: background-color 0.15s ease;
        }
        a.notif-row:hover { background: var(--hover); }
        .notif-ic {
            margin-top: 2px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border-radius: 6px;
        }
        .notif-ic-ok { background: var(--pine-soft); color: var(--pine); }
        .notif-ic-warn { background: rgba(180, 83, 9, 0.10); color: var(--amber); }
        .notif-ic-danger { background: rgba(185, 28, 28, 0.08); color: var(--danger); }
        .notif-ic-muted { background: var(--hover); color: var(--muted); }
        .notif-title { display: block; font-size: 13px; font-weight: 700; color: var(--ink); }
        .notif-title-ok { color: var(--green-dark); }
        .notif-title-warn { color: var(--amber); }
        .notif-title-danger { color: var(--danger); }
        .notif-body {
            margin-top: 2px;
            display: block;
            font-size: 12px;
            line-height: 1.4;
            color: var(--muted);
        }
        html[data-theme="dark"] .notif-ic-warn { background: rgba(227, 164, 72, 0.13); }
        html[data-theme="dark"] .notif-ic-danger { background: rgba(227, 106, 106, 0.13); }

        /* ---------- Cards & tables ---------- */
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-card);
        }
        .card + .card { margin-top: 20px; }
        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
        }
        .card-body {
            padding: 0;
            overflow-x: auto;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }
        .payments-table th,
        .payments-table td {
            padding: 13px 18px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: top;
            font-size: 13.5px;
        }
        .payments-table th {
            font-family: var(--font-mono);
            background: var(--hover);
            color: var(--muted);
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            white-space: nowrap;
        }
        .payments-table tr:last-child td { border-bottom: none; }
        .payments-table tbody tr { transition: background-color 0.12s ease; }
        .payments-table tbody tr:hover { background: var(--hover); }
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

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 6px;
            background: var(--pine);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .user-name {
            font-weight: 700;
            color: var(--ink);
        }
        .user-email {
            font-size: 12.5px;
            color: var(--muted);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 5px;
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 600;
            margin-top: 6px;
            white-space: nowrap;
        }
        .status-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 999px;
            background: currentColor;
        }
        .status-badge-paid { background: #E5F3EA; color: #14532D; }
        .status-badge-due { background: #FDF8EC; color: #92400E; }
        .status-badge-overdue { background: #FBEAEA; color: #B3261E; }
        .status-badge-none { background: #F0F2F0; color: var(--muted); }

        .paid-month-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 6px;
            border: 1px solid #CDE3D4;
            background: #F0F7F2;
            color: #14532D;
            font-size: 12.5px;
            font-weight: 600;
            cursor: default;
            white-space: nowrap;
        }
        .row-action-links {
            display: inline-flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .empty-state {
            background: var(--card);
            border: 1px dashed var(--line-strong);
            border-radius: 10px;
            padding: 28px;
            text-align: center;
            color: var(--muted);
            font-size: 13.5px;
            margin: 16px;
        }

        /* ---------- Filter bar ---------- */
        .history-filter-bar {
            padding: 14px 18px;
            border-bottom: 1px solid var(--line);
            background: var(--hover);
        }
        .table-search {
            position: relative;
            width: 100%;
            max-width: 360px;
        }
        .table-search svg {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: var(--faint);
            pointer-events: none;
        }
        .table-search input {
            width: 100%;
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            background: var(--field);
            color: var(--ink);
            font-family: var(--font-ui);
            font-size: 13px;
            padding: 8px 12px 8px 33px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .table-search input::placeholder { color: var(--faint); }
        .table-search input:focus {
            outline: none;
            border-color: var(--pine);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
        }

        /* ---------- Modals ---------- */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(23, 37, 28, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2100;
            padding: 16px;
        }
        .modal-backdrop.active { display: flex; }
        .modal {
            width: min(540px, 100%);
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow-pop);
            max-height: calc(100vh - 48px);
            overflow-y: auto;
        }
        .modal h3 {
            margin: 0 0 4px;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: var(--ink);
        }
        .modal .notice {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 16px;
        }
        .field {
            display: grid;
            gap: 6px;
            margin-bottom: 12px;
        }
        .field label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ink);
        }
        .field input,
        .field select,
        .field textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            font-family: var(--font-ui);
            font-size: 13.5px;
            background: var(--field);
            color: var(--ink);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            outline: none;
            border-color: var(--pine);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
        }
        .field textarea {
            min-height: 88px;
            resize: vertical;
        }
        .field input[readonly] {
            background: var(--hover);
            color: var(--muted);
        }
        .field-help {
            display: block;
            margin-top: 2px;
            font-size: 12px;
            color: var(--muted);
        }
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 16px;
        }
        .modal-feedback {
            display: none;
            margin-top: 12px;
            padding: 11px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid transparent;
        }
        .modal-feedback.error {
            display: block;
            background: #FDF3F3;
            border-color: #F2D8D8;
            color: var(--danger);
        }
        .modal-feedback.success {
            display: block;
            background: #F0F7F2;
            border-color: #CDE3D4;
            color: #14532D;
        }
        .required { color: var(--danger); }

        .flash-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(23, 37, 28, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2600;
            padding: 16px;
        }
        .flash-modal-backdrop.active { display: flex; }
        .flash-modal {
            width: min(480px, 100%);
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: var(--shadow-pop);
            overflow: hidden;
        }
        .flash-modal-head {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 14px 18px;
            background: #F0F7F2;
            border-bottom: 1px solid #CDE3D4;
            color: #14532D;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }
        .flash-modal-head svg { width: 16px; height: 16px; flex-shrink: 0; }
        .flash-modal-body {
            padding: 18px;
            color: var(--ink);
            font-size: 14px;
            line-height: 1.55;
        }
        .flash-modal-actions {
            display: flex;
            justify-content: flex-end;
            padding: 0 18px 18px;
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
            background: var(--card);
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
            background: var(--field);
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

        /* ---------- Dark-only overrides ---------- */
        /* --pine flips to light mint in dark mode, so anything sitting on it
           needs dark text instead of white. */
        html[data-theme="dark"] .btn-primary,
        html[data-theme="dark"] .btn-green { color: #0C130F; }
        html[data-theme="dark"] .nb-avatar-fallback { color: #0C130F; }
        html[data-theme="dark"] .user-avatar { color: #0C130F; }
        html[data-theme="dark"] .btn-danger { color: #fff; }
        html[data-theme="dark"] .btn-danger-soft {
            background: rgba(227, 106, 106, 0.12);
            border-color: rgba(227, 106, 106, 0.35);
            color: #F0A0A0;
        }
        html[data-theme="dark"] .btn-danger-soft:hover { background: rgba(227, 106, 106, 0.2); }
        html[data-theme="dark"] .alert-success {
            background: rgba(123, 211, 160, 0.10);
            border-color: rgba(123, 211, 160, 0.30);
            color: #A9E4C2;
        }
        html[data-theme="dark"] .alert-warning {
            background: rgba(227, 164, 72, 0.10);
            border-color: rgba(227, 164, 72, 0.32);
            color: #EEC084;
        }
        html[data-theme="dark"] .alert-error {
            background: rgba(227, 106, 106, 0.12);
            border-color: rgba(227, 106, 106, 0.35);
            color: #F0A0A0;
        }
        html[data-theme="dark"] .status-badge-paid { background: rgba(123, 211, 160, 0.16); color: #8CD6AF; }
        html[data-theme="dark"] .status-badge-due { background: rgba(227, 164, 72, 0.14); color: #E9C288; }
        html[data-theme="dark"] .status-badge-overdue { background: rgba(227, 106, 106, 0.14); color: #F0A0A0; }
        html[data-theme="dark"] .status-badge-none { background: rgba(255, 255, 255, 0.07); }
        html[data-theme="dark"] .paid-month-badge {
            background: rgba(123, 211, 160, 0.12);
            border-color: rgba(123, 211, 160, 0.30);
            color: #A9E4C2;
        }
        html[data-theme="dark"] .modal-feedback.error {
            background: rgba(227, 106, 106, 0.12);
            border-color: rgba(227, 106, 106, 0.35);
            color: #F0A0A0;
        }
        html[data-theme="dark"] .modal-feedback.success {
            background: rgba(123, 211, 160, 0.10);
            border-color: rgba(123, 211, 160, 0.30);
            color: #A9E4C2;
        }
        html[data-theme="dark"] .flash-modal-head {
            background: rgba(123, 211, 160, 0.10);
            border-bottom-color: rgba(123, 211, 160, 0.28);
            color: #A9E4C2;
        }
        html[data-theme="dark"] .pop-item.pop-item-danger:hover { background: rgba(227, 106, 106, 0.12); }
        html[data-theme="dark"] select.control {
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2398A89E' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        }
        html[data-theme="dark"] .control:focus,
        html[data-theme="dark"] .table-search input:focus,
        html[data-theme="dark"] .field input:focus,
        html[data-theme="dark"] .field select:focus,
        html[data-theme="dark"] .field textarea:focus,
        html[data-theme="dark"] .nb-search-input:focus {
            box-shadow: 0 0 0 3px rgba(123, 211, 160, 0.18);
        }
        html[data-theme="dark"] .btn:focus-visible,
        html[data-theme="dark"] .nb-icon-btn:focus-visible,
        html[data-theme="dark"] .nb-user-btn:focus-visible {
            outline-color: rgba(123, 211, 160, 0.55);
        }
        /* Sidebar is dark in both themes; nudge it to match the dark page chrome. */
        html[data-theme="dark"] .sb { background: #0B100D; border-right-color: #070B09; }
        html[data-theme="dark"] .sb-brand-logo { background: #E6EDE8; }

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
        $cashierUser = auth()->user();
        $cashierName = $cashierUser?->name ?? 'Cashier';
        $cashierEmail = $cashierUser?->email ?? 'cashier@example.com';
        $cashierInitial = method_exists($cashierUser, 'initials')
            ? $cashierUser->initials()
            : strtoupper(substr((string) $cashierName, 0, 1));
        $cashierAvatarUrl = $cashierUser?->profile_photo
            ? asset('storage/' . $cashierUser->profile_photo)
            : null;
        $ebaLogoUrl = file_exists(public_path('images/eba-logo.png'))
            ? asset('images/eba-logo.png')
            : null;
        $cashierSettingsRoute = \Illuminate\Support\Facades\Route::has('profile.edit')
            ? route('profile.edit')
            : url('/');

        // Outstanding active concessionaires for the notification bell, using
        // the shared fee service: overdue = unpaid past months, due = current
        // month still unpaid.
        $cashierFeePlans = app(\App\Services\ConcessionaireFeeService::class)->plans(
            \App\Models\User::query()
                ->where('role', 'concessionaire')
                ->where('is_approved', true)
                ->where('is_active_concessionaire', true)
                ->where('monthly_fee', '>', 0)
                ->orderByRaw('COALESCE(NULLIF(business_name, ""), name) asc')
                ->get(['id', 'name', 'business_name', 'monthly_fee'])
        );
        $cashierOverduePlans = collect($cashierFeePlans)->where('status', 'overdue');
        $cashierDuePlans = collect($cashierFeePlans)->whereIn('status', ['due', 'due_soon']);
        $cashierOverdueCount = $cashierOverduePlans->count();
        $cashierDueCount = $cashierDuePlans->count();
        $cashierOverdueNames = $cashierOverduePlans->take(3)->pluck('business')->implode(', ');
        $cashierDueNames = $cashierDuePlans->take(3)->pluck('business')->implode(', ');
        $cashierOverdueMore = max(0, $cashierOverdueCount - 3);
        $cashierDueMore = max(0, $cashierDueCount - 3);
    @endphp

    <aside id="cashier-sidebar" class="sb fixed top-0 left-0 z-40 h-screen w-64 -translate-x-full transition-transform duration-300 lg:translate-x-0" aria-label="Cashier sidebar" data-drawer-backdrop="true">
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
                <button type="button" class="nb-icon-btn mobile-only" style="color:rgba(255,255,255,0.6);" data-drawer-hide="cashier-sidebar" aria-controls="cashier-sidebar">
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
                            <a href="{{ route('cashier.dashboard') }}" class="sb-item {{ request()->routeIs('cashier.dashboard') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cashier.payments') }}" class="sb-item {{ request()->routeIs('cashier.payments') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Record Payment</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cashier.history') }}" class="sb-item {{ request()->routeIs('cashier.history') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Payment Logs</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h3 class="sb-group-label">General</h3>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ $cashierSettingsRoute }}" class="sb-item {{ request()->routeIs('profile.edit') ? 'is-active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Settings</span>
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
                        <button type="button" class="nb-icon-btn mobile-only" data-drawer-target="cashier-sidebar" data-drawer-toggle="cashier-sidebar" aria-controls="cashier-sidebar">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-5 w-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 5A.75.75 0 0 1 2.75 9h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 9.75Zm0 5A.75.75 0 0 1 2.75 14h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z"/>
                            </svg>
                        </button>

                        @php
                            $breadcrumbs = [];
                            if (request()->routeIs('cashier.dashboard')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'active' => true]
                                ];
                            } elseif (request()->routeIs('cashier.payments')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('cashier.dashboard')],
                                    ['label' => 'Record Payment', 'active' => true]
                                ];
                            } elseif (request()->routeIs('cashier.history')) {
                                $breadcrumbs = [
                                    ['label' => 'Dashboard', 'url' => route('cashier.dashboard')],
                                    ['label' => 'Payment Logs', 'active' => true]
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
                            placeholder="Search pages or payments…"
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

                        <button id="cashier-notification-button" type="button" class="nb-icon-btn" title="Notifications" data-dropdown-toggle="cashier-notification-dropdown" data-dropdown-placement="bottom-end">
                            <svg class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($cashierOverdueCount + $cashierDueCount > 0)
                                <span class="nb-badge {{ $cashierOverdueCount > 0 ? 'nb-badge-danger' : '' }}" aria-hidden="true"></span>
                            @endif
                        </button>

                        <div id="cashier-notification-dropdown" class="pop z-50 hidden w-[320px]" role="menu" aria-labelledby="cashier-notification-button">
                            <div class="border-b px-4 py-3" style="border-color: var(--line);">
                                <span class="panel-title" style="font-size: 13.5px;">Notifications</span>
                            </div>
                            <div class="p-2">
                                @if($cashierOverdueCount > 0)
                                    <a href="{{ route('cashier.payments') }}" class="notif-row">
                                        <span class="notif-ic notif-ic-danger">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="notif-title notif-title-danger">{{ $cashierOverdueCount }} {{ \Illuminate\Support\Str::plural('concessionaire', $cashierOverdueCount) }} overdue</span>
                                            <span class="notif-body">
                                                Unpaid past months: {{ $cashierOverdueNames }}{{ $cashierOverdueMore > 0 ? ' +' . $cashierOverdueMore . ' more' : '' }}
                                            </span>
                                        </span>
                                    </a>
                                @endif
                                @if($cashierDueCount > 0)
                                    <a href="{{ route('cashier.payments') }}" class="notif-row">
                                        <span class="notif-ic notif-ic-warn">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="notif-title notif-title-warn">{{ $cashierDueCount }} {{ \Illuminate\Support\Str::plural('payment', $cashierDueCount) }} due for {{ now()->format('F') }}</span>
                                            <span class="notif-body">
                                                Still unpaid this month: {{ $cashierDueNames }}{{ $cashierDueMore > 0 ? ' +' . $cashierDueMore . ' more' : '' }}
                                            </span>
                                        </span>
                                    </a>
                                @endif
                                @if($cashierOverdueCount + $cashierDueCount === 0)
                                    <div class="notif-row">
                                        <span class="notif-ic notif-ic-ok">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="notif-title notif-title-ok">All caught up</span>
                                            <span class="notif-body">
                                                Every active concessionaire is settled for {{ now()->format('F') }}.
                                            </span>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="hidden h-6 w-px sm:block" style="background: var(--line);"></div>

                        <button id="cashier-user-menu-button" type="button" class="nb-user-btn" data-dropdown-toggle="cashier-user-menu" data-dropdown-placement="bottom-end" aria-expanded="false">
                            @if($cashierAvatarUrl)
                                <img src="{{ $cashierAvatarUrl }}" alt="{{ $cashierName }}" class="nb-avatar">
                            @else
                                <span class="nb-avatar-fallback">{{ $cashierInitial }}</span>
                            @endif
                            <span class="hidden lg:flex lg:flex-col lg:items-start">
                                <span class="max-w-[130px] truncate text-[13px] font-bold" style="color:var(--ink);">{{ $cashierName }}</span>
                                <span class="eyebrow" style="font-size:9.5px;">Cashier</span>
                            </span>
                            <svg class="hidden h-3.5 w-3.5 lg:block" style="color:var(--muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div id="cashier-user-menu" class="pop z-50 hidden w-64" role="menu" aria-labelledby="cashier-user-menu-button">
                            <div class="border-b px-4 py-3.5" style="border-color: var(--line);">
                                <div class="flex items-center gap-3">
                                    @if($cashierAvatarUrl)
                                        <img src="{{ $cashierAvatarUrl }}" alt="{{ $cashierName }}" class="nb-avatar" style="width:38px;height:38px;">
                                    @else
                                        <span class="nb-avatar-fallback" style="width:38px;height:38px;font-size:15px;">{{ $cashierInitial }}</span>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-[13px] font-bold" style="color:var(--ink);">{{ $cashierName }}</div>
                                        <div class="truncate text-xs" style="color:var(--muted);">{{ $cashierEmail }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-1.5">
                                <a href="{{ $cashierSettingsRoute }}" class="pop-item">
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
            @hasSection('content')
                @yield('content')
            @elseif (isset($slot))
                {{ $slot }}
            @endif
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
                try { localStorage.setItem('eba-cashier-theme', dark ? 'dark' : 'light'); } catch (e) {}
                window.dispatchEvent(new CustomEvent('eba:theme', { detail: { theme: dark ? 'dark' : 'light' } }));
            });
        })();

        (() => {
            const input = document.getElementById('portal-search-input');
            const panel = document.getElementById('portal-search-panel');
            if (!input || !panel) return;

            const pages = [
                { label: 'Dashboard', hint: 'Page', url: @json(route('cashier.dashboard')), keywords: 'home overview stats collections summary charts' },
                { label: 'Record Payment', hint: 'Page', url: @json(route('cashier.payments')), keywords: 'concessionaires record pay fee monthly collect receipt' },
                { label: 'Payment Logs', hint: 'Page', url: @json(route('cashier.history')), keywords: 'payments history log recent receipts transactions download' },
                { label: 'Settings', hint: 'Page', url: @json($cashierSettingsRoute), keywords: 'profile account password name email' },
            ];
            const historySearchUrl = @json(route('cashier.history'));

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
                        url: historySearchUrl + '?q=' + encodeURIComponent(input.value.trim()),
                        label: 'Search payment logs for "' + input.value.trim() + '"',
                        hint: 'History',
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
                        window.location.href = historySearchUrl + '?q=' + encodeURIComponent(input.value.trim());
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
