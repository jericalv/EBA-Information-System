<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cashier Portal')</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --green: #0A5C2F;
        }
        @keyframes navbarGlow {
            0%, 100% {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 15px rgba(16, 185, 129, 0.1);
            }
            50% {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 25px rgba(16, 185, 129, 0.15);
            }
        }
        nav {
            animation: navbarGlow 3s ease-in-out infinite;
        }

        .page-title {
            font-size: 30px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .page-subtitle {
            color: #64748b;
            margin-bottom: 18px;
            font-size: 14px;
        }
        .alert {
            padding: 14px 20px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: 500;
        }
        .alert-success {
            background: rgba(10,92,47,0.08);
            border: 1px solid rgba(10,92,47,0.2);
            color: var(--green);
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 18px;
        }
        .summary-card {
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .summary-label {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 0.025em;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 36px;
            font-weight: 800;
            line-height: 1;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .chart-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 16px;
            min-height: 334px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 8px;
        }
        .status-badge-overdue { background: #fee2e2; color: #991b1b; }
        .status-badge-paid { background: #dcfce7; color: #166534; }
        .status-badge-due { background: #fef3c7; color: #92400e; }
        .status-badge-none { background: #e2e8f0; color: #475569; }

        .btn-warning { background: #f59e0b; color: #fff; }
        .btn-warning:hover { background: #d97706; }
        .btn-disabled {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 700;
            background: #e5e7eb;
            color: #6b7280;
        }
        .history-receipt-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid rgba(10,92,47,0.2);
            color: #0a5c2f;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            background: #fff;
            white-space: nowrap;
        }
        .history-receipt-link:hover { background: #f0fdf4; }
        .payments-cell {
            white-space: nowrap;
        }
        .record-payment-btn {
            background: #3b82f6;
            color: #fff;
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 6px;
            width: auto;
            min-width: unset;
            display: inline-flex;
        }
        .record-payment-btn:hover {
            background: #2563eb;
        }
        .paid-month-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #e5e7eb;
            color: #6b7280;
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 6px;
            cursor: default;
            font-weight: 700;
            white-space: nowrap;
        }
        .row-action-links {
            display: inline-flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .btn-xs {
            padding: 7px 10px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 7px;
            white-space: nowrap;
        }
        .history-filter-bar {
            padding: 14px 18px;
            border-bottom: 1px solid #eef2f7;
            background: #fcfdff;
        }
        .history-filter-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 0.9fr auto;
            gap: 10px;
            align-items: end;
        }
        .history-filter-field {
            display: grid;
            gap: 6px;
        }
        .history-filter-field label {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
        }
        .history-filter-field input,
        .history-filter-field select {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
            font-size: 13px;
            background: #fff;
            color: #0f172a;
        }
        .history-filter-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        .payment-history-section {
            margin-top: 34px;
            padding-top: 14px;
            border-top: 2px solid #dbe6f3;
        }
        .flash-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2600;
            padding: 16px;
        }
        .flash-modal-backdrop.active {
            display: flex;
        }
        .flash-modal {
            width: min(520px, 100%);
            background: #fff;
            border-radius: 14px;
            border: 1px solid #d1fae5;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.2);
            overflow: hidden;
        }
        .flash-modal-head {
            padding: 14px 16px;
            background: linear-gradient(180deg, #ecfdf5 0%, #dcfce7 100%);
            border-bottom: 1px solid #bbf7d0;
            color: #065f46;
            font-size: 14px;
            font-weight: 800;
        }
        .flash-modal-body {
            padding: 16px;
            color: #0f172a;
            font-size: 15px;
            line-height: 1.5;
        }
        .flash-modal-actions {
            display: flex;
            justify-content: flex-end;
            padding: 0 16px 16px;
        }

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .card + .card {
            margin-top: 20px;
        }
        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid #e5e7eb;
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
            padding: 14px 18px;
            border-bottom: 1px solid #eef2f7;
            text-align: left;
            vertical-align: top;
        }
        .payments-table th {
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .payments-table tr:last-child td {
            border-bottom: none;
        }
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #0a5c2f;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .user-name {
            font-weight: 700;
            color: #111827;
        }
        .user-email {
            font-size: 13px;
            color: #64748b;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
        }
        .btn-green {
            background: #0a5c2f;
            color: #fff;
        }
        .btn-green:hover {
            background: #0d7a3e;
        }
        .btn-outline {
            background: #fff;
            color: #334155;
            border: 1px solid #dbe2ea;
        }
        .btn-outline:hover {
            background: #f8fafc;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2100;
            padding: 16px;
        }
        .modal-backdrop.active { display: flex; }
        .modal {
            width: min(560px, 100%);
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
        }
        .modal h3 {
            margin: 0 0 16px;
            font-size: 20px;
            color: #064420;
        }
        .modal .notice {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 14px;
        }
        .field {
            display: grid;
            gap: 6px;
            margin-bottom: 12px;
        }
        .field label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }
        .field input,
        .field select,
        .field textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
            background: #fff;
            color: #0f172a;
        }
        .field textarea {
            min-height: 88px;
            resize: vertical;
        }
        .field input[readonly] {
            background: #f8fafc;
            color: #475569;
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
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
        }
        .modal-feedback.error {
            display: block;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .modal-feedback.success {
            display: block;
            background: rgba(10,92,47,0.08);
            border: 1px solid rgba(10,92,47,0.18);
            color: #0a5c2f;
        }
        .required { color: #dc2626; }
        .empty-state {
            background: #fff;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 28px;
            text-align: center;
            color: #64748b;
            margin: 12px;
        }

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

        @media (max-width: 1260px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 860px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
            .history-filter-grid {
                grid-template-columns: 1fr;
            }
            .history-filter-actions {
                justify-content: flex-start;
            }
            .payment-history-section {
                margin-top: 24px;
                padding-top: 10px;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 antialiased" style="font-family: 'Inter', sans-serif;">
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
    @endphp

    <aside id="cashier-sidebar" class="fixed top-0 left-0 z-40 h-screen w-64 -translate-x-full border-r border-emerald-900/70 bg-[#1a3c2e] transition-transform duration-300 lg:translate-x-0" aria-label="Cashier sidebar" data-drawer-backdrop="true">
        <div class="h-full overflow-y-auto px-3 py-4">
            <div class="mb-5 flex items-center justify-end lg:hidden">
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-200 hover:bg-emerald-800/60 hover:text-white" data-drawer-hide="cashier-sidebar" aria-controls="cashier-sidebar">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                    </svg>
                </button>
            </div>

            <div class="mb-5 rounded-lg border border-emerald-800/40 bg-emerald-900/30 p-3">
                <div class="flex items-start gap-3">
                    @if($ebaLogoUrl)
                        <img src="{{ $ebaLogoUrl }}" alt="EBA Logo" class="h-10 w-10 rounded-lg border border-emerald-700/60 bg-white/90 p-1 object-contain">
                    @else
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-emerald-700/60 bg-emerald-700 text-sm font-semibold text-white">EBA</div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold leading-4 text-white break-words">EBA IS</p>
                        <p class="mt-1 text-xs leading-4 text-emerald-300/80 break-words">Cvsu - TMC Campus</p>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <h3 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-emerald-400/60">Main</h3>
                <ul class="space-y-1 font-medium">
                    <li>
                        <a href="{{ route('cashier.dashboard') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('cashier.dashboard') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            <span class="ms-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cashier.payments') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('cashier.payments') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="ms-3">Record Payment</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cashier.history') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('cashier.history') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="ms-3">Payment History</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="mb-3">
                <ul class="space-y-1 font-medium">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="group flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm text-red-200 transition-colors hover:bg-red-500/20 hover:text-red-100">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="ms-3">Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </aside>

    <div class="relative min-h-screen lg:ml-64">
        <nav class="fixed top-0 z-30 w-full border-b border-emerald-100 bg-gradient-to-r from-white via-emerald-50/30 to-white backdrop-blur-sm shadow-md lg:w-[calc(100%-16rem)]">
            <div class="px-4 py-3.5 lg:px-8">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <button type="button" class="inline-flex items-center rounded-xl bg-gradient-to-br from-emerald-50 to-white p-2.5 text-emerald-700 shadow-sm transition-all hover:shadow-md hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 lg:hidden" data-drawer-target="cashier-sidebar" data-drawer-toggle="cashier-sidebar" aria-controls="cashier-sidebar">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-5 w-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 5A.75.75 0 0 1 2.75 9h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 9.75Zm0 5A.75.75 0 0 1 2.75 14h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z"/>
                            </svg>
                        </button>
                        <div class="flex flex-col">
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
                                        ['label' => 'Payment History', 'active' => true]
                                    ];
                                }
                            @endphp
                            @if(count($breadcrumbs) > 0)
                                <div class="breadcrumb hidden sm:flex" aria-label="Breadcrumb">
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

                    <div class="flex items-center gap-3 sm:gap-4">
                        <button id="cashier-user-menu-button" type="button" class="group inline-flex items-center gap-2.5 rounded-xl bg-gradient-to-br from-emerald-50 to-white px-3 py-2 shadow-sm transition-all hover:shadow-md hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1" data-dropdown-toggle="cashier-user-menu" data-dropdown-placement="bottom-end" aria-expanded="false">
                            @if($cashierAvatarUrl)
                                <img src="{{ $cashierAvatarUrl }}" alt="{{ $cashierName }}" class="h-9 w-9 rounded-full border-2 border-emerald-600 object-cover shadow-sm ring-2 ring-emerald-100">
                            @else
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-emerald-600 bg-gradient-to-br from-emerald-700 to-emerald-900 text-sm font-bold text-white shadow-sm ring-2 ring-emerald-100">{{ $cashierInitial }}</span>
                            @endif
                            <div class="hidden sm:flex sm:flex-col sm:items-start">
                                <span class="text-sm font-bold text-emerald-900 truncate max-w-[120px]">{{ $cashierName }}</span>
                                <span class="text-xs text-emerald-600/70 font-medium">Cashier</span>
                            </div>
                            <svg class="h-4 w-4 text-emerald-600 transition-transform group-hover:translate-y-0.5 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div id="cashier-user-menu" class="z-50 hidden w-56 divide-y divide-emerald-100 rounded-xl bg-white shadow-xl border border-emerald-100" role="menu" aria-labelledby="cashier-user-menu-button">
                            <div class="px-4 py-3">
                                <p class="truncate text-sm font-semibold text-emerald-900">{{ $cashierName }}</p>
                                <p class="truncate text-xs text-emerald-600/80">{{ $cashierEmail }}</p>
                            </div>
                            <ul class="py-2 text-sm">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        <ul class="py-2 text-sm">
                                        <li>
                                            <a href="{{ route('profile.edit') }}" class="group flex w-full items-center gap-3 px-4 py-2.5 text-left text-emerald-700 transition-colors hover:bg-emerald-50">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="font-semibold">Settings</span>
                                            </a>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="group flex w-full items-center gap-3 px-4 py-2.5 text-left text-red-600 transition-colors hover:bg-red-50">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            <span class="font-semibold">Logout</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="px-4 pb-6 pt-20 lg:px-6 lg:pt-24">
            @hasSection('content')
                @yield('content')
            @elseif (isset($slot))
                {{ $slot }}
            @endif
        </main>
    </div>

    @yield('scripts')
</body>
</html>
