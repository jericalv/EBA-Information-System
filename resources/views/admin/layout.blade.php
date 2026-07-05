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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green: #0A5C2F;
            --green-light: #0D7A3E;
            --green-dark: #064420;
            --gold: #D4A843;
            --gold-light: #E8C96A;
            --sidebar-w: 260px;
            --header-h: 64px;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--green-dark);
            color: #fff;
            display: flex; flex-direction: column;
            z-index: 100;
            transition: transform 0.3s;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 12px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-decoration: none; color: #fff;
        }
        .sidebar-brand img { width: 38px; height: 38px; object-fit: contain; border-radius: 6px; }
        .sidebar-brand-text { display: flex; flex-direction: column; }
        .sidebar-brand-title { font-size: 14px; font-weight: 800; line-height: 1.2; }
        .sidebar-brand-sub { font-size: 10px; opacity: 0.7; font-weight: 500; }
        .sidebar-nav { flex: 1; padding: 16px 12px; display: flex; flex-direction: column; gap: 4px; }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: 8px;
            text-decoration: none; color: rgba(255,255,255,0.75);
            font-size: 14px; font-weight: 500; transition: all 0.2s;
        }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar-link.active { background: rgba(255,255,255,0.15); color: #fff; font-weight: 600; }
        .sidebar-link svg { width: 20px; height: 20px; flex-shrink: 0; }
        .sidebar-section { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); padding: 16px 14px 6px; font-weight: 700; }
        .sidebar-logout-form { margin: 0; }
        .sidebar-logout-form .sidebar-link {
            width: 100%; background: none; border: none;
            cursor: pointer; font-family: inherit; font-size: 14px;
            font-weight: 500; color: rgba(255,255,255,0.75);
        }
        .sidebar-footer {
            padding: 16px 12px; border-top: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px; padding: 8px 14px;
            border-radius: 8px; background: rgba(255,255,255,0.08);
        }
        .sidebar-avatar {
            width: 34px; height: 34px; border-radius: 50%; background: var(--gold);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px; color: var(--green-dark); flex-shrink: 0;
        }
        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-name { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-size: 11px; opacity: 0.6; }
        .btn-sidebar-logout {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 8px 14px; margin-bottom: 8px;
            border-radius: 8px; border: 1px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.06); color: #fca5a5;
            font-size: 13px; font-weight: 600; font-family: inherit;
            cursor: pointer; transition: background 0.2s;
        }
        .btn-sidebar-logout:hover { background: rgba(239,68,68,0.18); }

        /* ===== HEADER ===== */
        .main-header {
            position: fixed; top: 0; right: 0; left: var(--sidebar-w);
            height: var(--header-h); z-index: 90;
            background: #fff; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px;
        }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .header-title { font-size: 18px; font-weight: 700; }
        .header-right { display: flex; align-items: center; gap: 12px; position: relative; }
        .btn-header {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
            text-decoration: none; border: none; cursor: pointer; font-family: inherit;
            transition: all 0.2s;
        }
        .btn-home { background: #f1f5f9; color: #475569; }
        .btn-home:hover { background: #e2e8f0; }
        .btn-logout-header { background: transparent; color: #ef4444; border: 1px solid #fecaca; }
        .btn-logout-header:hover { background: #fef2f2; }
        .role-badge-admin {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .hamburger {
            display: none; background: none; border: none; cursor: pointer; padding: 6px; color: #475569;
        }

        /* ===== ADMIN NOTIFICATIONS ===== */
        .admin-notif-btn {
            position: relative;
            width: 52px;
            height: 52px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #e2eacc 0%, #e2eacc 100%);
            color: #ea580c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .admin-notif-btn:hover {
            transform: scale(1.04);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.18);
        }
        .admin-notif-btn:focus-visible {
            outline: 2px solid #f97316;
            outline-offset: 2px;
        }
        .admin-notif-btn svg {
            width: 30px;
            height: 30px;
        }
        .admin-notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 20px;
            height: 20px;
            border-radius: 999px;
            background: linear-gradient(135deg, #f97316 0%, #ef4444 100%);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            line-height: 1;
        }
        .admin-notif-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: 320px;
            border-radius: 16px;
            border: 1px solid #d1fae5;
            background: #fff;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.16);
            z-index: 150;
            overflow: hidden;
        }
        .admin-notif-dropdown.hidden {
            display: none;
        }
        .admin-notif-head {
            background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%);
            border-bottom: 1px solid #d1fae5;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 700;
            color: #065f46;
        }
        .admin-notif-list {
            max-height: 320px;
            overflow-y: auto;
            padding: 8px;
        }
        .admin-notif-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            text-decoration: none;
            border-radius: 10px;
            padding: 10px;
            transition: background-color 0.2s ease;
        }
        .admin-notif-item:hover {
            background: #ecfdf5;
        }
        .admin-notif-item-icon {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: #d1fae5;
            color: #047857;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .admin-notif-item-icon svg {
            width: 18px;
            height: 18px;
        }
        .admin-notif-item-title {
            font-size: 13px;
            font-weight: 700;
            color: #047857;
            margin-bottom: 2px;
        }
        .admin-notif-item-meta {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.25;
        }
        .admin-notif-foot {
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            padding: 8px;
        }
        .admin-notif-foot-link {
            display: block;
            text-align: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            color: #047857;
            border-radius: 10px;
            padding: 10px 12px;
            transition: background-color 0.2s ease;
        }
        .admin-notif-foot-link:hover {
            background: #ecfdf5;
        }

        /* ===== MAIN ===== */
        .main-content {
            margin-left: var(--sidebar-w);
            padding-top: var(--header-h);
            min-height: 100vh;
        }
        .page-body { padding: 28px; }

        /* ===== ALERTS ===== */
        .alert {
            padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 500;
        }
        .alert-success { background: rgba(10,92,47,0.08); border: 1px solid rgba(10,92,47,0.2); color: var(--green); }
        .alert-error { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #dc2626; }

        /* ===== CARDS ===== */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: #fff; border-radius: 14px; padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid #e2e8f0;
        }
        .stat-card-label { font-size: 13px; color: #64748b; font-weight: 500; margin-bottom: 6px; }
        .stat-card-value { font-size: 32px; font-weight: 800; color: #0f172a; }
        .stat-card-icon {
            width: 44px; height: 44px; border-radius: 10px; display: flex;
            align-items: center; justify-content: center; margin-bottom: 14px;
        }
        .stat-card-icon svg { width: 22px; height: 22px; }
        .stat-icon-green { background: rgba(10,92,47,0.1); color: var(--green); }
        .stat-icon-gold { background: rgba(212,168,67,0.15); color: #b8860b; }
        .stat-icon-blue { background: rgba(59,130,246,0.1); color: #2563eb; }

        /* ===== TABLE ===== */
        .card {
            background: #fff; border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid #e2e8f0; overflow: hidden;
        }
        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 24px; border-bottom: 1px solid #f1f5f9;
        }
        .card-header h3 { font-size: 16px; font-weight: 700; }
        .card-body { padding: 0; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            text-align: left; padding: 12px 24px; font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;
            background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        tbody td {
            padding: 14px 24px; font-size: 14px; border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        tbody tr:hover { background: #f8fafc; }
        tbody tr:last-child td { border-bottom: none; }
        .user-cell { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%; background: var(--green);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; flex-shrink: 0;
        }
        .user-name { font-weight: 600; }
        .user-email { font-size: 13px; color: #64748b; }
        .badge {
            display: inline-flex; padding: 3px 10px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
        }
        .badge-admin { background: rgba(10,92,47,0.1); color: var(--green); }
        .badge-user { background: #f1f5f9; color: #64748b; }
        .badge-warning { background: rgba(245,158,11,0.1); color: #d97706; }
        .badge-success { background: rgba(10,92,47,0.1); color: var(--green); }
        .badge-danger { background: rgba(239,68,68,0.1); color: #dc2626; }
        .badge-secondary { background: #f1f5f9; color: #64748b; }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 7px; font-size: 13px; font-weight: 600;
            border: none; cursor: pointer; font-family: inherit; text-decoration: none;
            transition: all 0.2s;
        }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-green { background: var(--green); color: #fff; }
        .btn-green:hover { background: var(--green-light); }
        .btn-red { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .btn-red:hover { background: #fee2e2; }
        .btn-outline { background: transparent; color: #475569; border: 1px solid #e2e8f0; }
        .btn-outline:hover { background: #f8fafc; }

        /* ===== SEARCH / FILTER ===== */
        .toolbar {
            display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
        }
        .search-box {
            display: flex; align-items: center; gap: 8px;
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 0 12px; flex: 1; max-width: 320px;
        }
        .search-box svg { width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0; }
        .search-box input {
            border: none; background: transparent; padding: 9px 0; font-size: 14px;
            font-family: inherit; width: 100%; outline: none; color: #1e293b;
        }
        .filter-select {
            padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px;
            font-size: 14px; font-family: inherit; background: #f8fafc; color: #1e293b;
            cursor: pointer;
        }

        /* ===== PAGINATION ===== */
        .pagination-wrap { padding: 16px 24px; }
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
            font-size: 13px;
            color: #64748b;
            white-space: nowrap;
        }

        .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between > div:last-child {
            margin-left: auto;
        }

        .pagination-wrap .d-flex.justify-content-between.flex-fill.d-sm-none {
            display: none;
        }

        .pagination-wrap .pagination {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .pagination-wrap .page-item { list-style: none; }
        .pagination-wrap .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none;
            color: #475569; border: 1px solid #e2e8f0;
            background: #fff;
        }
        .pagination-wrap .page-item.disabled .page-link {
            color: #94a3b8;
            background: #f8fafc;
            border-color: #e2e8f0;
            cursor: not-allowed;
        }
        .pagination-wrap .page-link:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .pagination-wrap .page-item.active .page-link {
            background: var(--green); color: #fff; border-color: var(--green);
        }
        .pagination-wrap svg { width: 14px; height: 14px; }

        @media (max-width: 640px) {
            .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between {
                display: none;
            }

            .pagination-wrap .d-flex.justify-content-between.flex-fill.d-sm-none {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }
        }

        /* ===== BREADCRUMB ===== */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 0;
            font-size: 16px;
            color: #64748b;
        }
        .breadcrumb a {
            color: #059669;
            text-decoration: none;
            transition: color 0.2s;
            font-weight: 600;
        }
        .breadcrumb a:hover {
            color: #047857;
            text-decoration: underline;
        }
        .breadcrumb-separator {
            color: #94a3b8;
            font-size: 15px;
        }
        .breadcrumb-current {
            color: #0f172a;
            font-weight: 800;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-header { left: 0; }
            .main-content { margin-left: 0; }
            .hamburger { display: block; }
            .stats-grid { grid-template-columns: 1fr; }
            .card-body { overflow-x: auto; }
            table { min-width: 600px; }
        }
    </style>
    @yield('extra-css')
</head>
<body>
    @php
        $unreadApplicationSteps = $unreadApplicationSteps ?? collect();
        $unreadApplicationCount = $unreadApplicationCount ?? 0;
    @endphp

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <div style="background: #ffffff; border-radius: 8px; padding: 4px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <img src="{{ asset('images/eba-logo.png') }}" alt="EBA Logo" style="width:32px;height:32px;object-fit:contain;">
            </div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-title">EBA Admin Panel</span>
                <span class="sidebar-brand-sub">CvSU — Trece Martires</span>
            </div>
        </a>

        <nav class="sidebar-nav">
            <div class="sidebar-section">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>

            <div class="sidebar-section">Management</div>
            <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Users
            </a>
            <a href="{{ route('admin.stocks') }}" class="sidebar-link {{ request()->routeIs('admin.stocks*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5Z"/><path d="M3 12l9 4.5 9-4.5"/><path d="M3 16.5L12 21l9-4.5"/></svg>
                Stocks
            </a>
            <a href="{{ route('admin.partnerships') }}" class="sidebar-link {{ request()->routeIs('admin.partnerships') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Partnerships
            </a>
            <a href="{{ route('admin.concessionaires') }}" class="sidebar-link {{ request()->routeIs('admin.concessionaires*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l1-5h16l1 5"/><path d="M4 9v11a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V9"/><path d="M3 9h18"/><path d="M9 21v-6h6v6"/></svg>
                Concessionaires
            </a>
            <a href="{{ route('admin.reviews') }}" class="sidebar-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 21h10"/><path d="M12 17v4"/><path d="M5 3h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="m9 9 2 2 4-4"/></svg>
                Reviews
            </a>
            <a href="{{ route('admin.transaction-logs') }}" class="sidebar-link {{ request()->routeIs('admin.transaction-logs*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
            </svg>
            Transaction Logs
        </a>

            <div class="sidebar-section">System</div>
            <a href="{{ route('admin.site-settings') }}" class="sidebar-link {{ request()->routeIs('admin.site-settings*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Site Settings
            </a>
            <a href="{{ route('admin.activity-logs') }}" class="sidebar-link {{ request()->routeIs('admin.activity-logs*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                Activity Logs
            </a>
            <a href="{{ route('admin.logs') }}" class="sidebar-link {{ request()->routeIs('admin.logs*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/></svg>
                System Logs
            </a>

            <div class="sidebar-section">General</div>
            <form method="POST" action="{{ route('admin.logout') }}" class="sidebar-logout-form">
                @csrf
                <button type="submit" class="sidebar-link">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </button>
            </form>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ auth()->user()->initials() }}</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- HEADER -->
    <header class="main-header">
        <div class="header-left">
            <button class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
            </button>
            <div style="display: flex; flex-direction: column;">
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
                    <div class="breadcrumb d-none d-sm-flex" aria-label="Breadcrumb">
                        @foreach($breadcrumbs as $index => $crumb)
                            @if($index > 0)
                                <span class="breadcrumb-separator">/</span>
                            @endif
                            @if(isset($crumb['active']) && $crumb['active'])
                                <span class="breadcrumb-current">{{ $crumb['label'] }}</span>
                            @else
                                <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="header-right">
            <button id="application-notification-button" type="button" class="admin-notif-btn" title="Notifications" data-unread-count="{{ $unreadApplicationCount }}" aria-expanded="false" aria-controls="application-notification-dropdown">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @if($unreadApplicationCount > 0)
                    <span id="application-notification-badge" class="admin-notif-badge">{{ $unreadApplicationCount }}</span>
                @endif
            </button>

            <div id="application-notification-dropdown" class="admin-notif-dropdown hidden" role="menu" aria-labelledby="application-notification-button">
                <div class="admin-notif-head">
                    Notifications
                </div>
                <div class="admin-notif-list">
                    @if($unreadApplicationCount > 0)
                        @foreach($unreadApplicationSteps as $event)
                            <a href="{{ route('admin.partnerships', ['search' => $event['concessionaire_name']]) }}" class="admin-notif-item">
                                <div class="admin-notif-item-icon">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="admin-notif-item-title">{{ $event['step_label'] }}</p>
                                    <p class="admin-notif-item-meta">
                                        {{ $event['concessionaire_name'] }}
                                    </p>
                                    <p class="admin-notif-item-meta">
                                        {{ $event['submitted_at']->diffForHumans() }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="admin-notif-item">
                            <div class="admin-notif-item-icon" style="background:#f3f4f6;color:#6b7280;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="admin-notif-item-title" style="color:#374151;">No New Submissions</p>
                                <p class="admin-notif-item-meta">
                                    You're all caught up
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="admin-notif-foot">
                    <a href="{{ route('admin.partnerships') }}" class="admin-notif-foot-link">
                        View Partnerships &rarr;
                    </a>
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
        document.addEventListener('DOMContentLoaded', () => {
            const notificationButton = document.getElementById('application-notification-button');
            const notificationDropdown = document.getElementById('application-notification-dropdown');
            const notificationBadge = document.getElementById('application-notification-badge');

            if (!notificationButton || !notificationDropdown) {
                return;
            }

            let markedRead = false;

            notificationButton.addEventListener('click', (event) => {
                event.stopPropagation();

                const isHidden = notificationDropdown.classList.contains('hidden');
                if (isHidden) {
                    notificationDropdown.classList.remove('hidden');
                    notificationButton.setAttribute('aria-expanded', 'true');
                } else {
                    notificationDropdown.classList.add('hidden');
                    notificationButton.setAttribute('aria-expanded', 'false');
                }

                if (markedRead) {
                    return;
                }

                const unreadCount = Number(notificationButton.dataset.unreadCount || '0');
                if (unreadCount < 1) {
                    return;
                }

                fetch("{{ route('admin.notifications.mark-read') }}", {
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

            document.addEventListener('click', (event) => {
                if (notificationDropdown.classList.contains('hidden')) {
                    return;
                }

                if (notificationDropdown.contains(event.target) || notificationButton.contains(event.target)) {
                    return;
                }

                notificationDropdown.classList.add('hidden');
                notificationButton.setAttribute('aria-expanded', 'false');
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
