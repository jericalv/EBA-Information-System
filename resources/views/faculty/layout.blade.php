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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        nav {
            animation: navbarGlow 3s ease-in-out infinite;
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

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #064420;
            margin: 0 0 8px;
        }
        .page-subtitle {
            color: #64748b;
            margin: 0 0 20px;
            font-size: 14px;
        }
        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: 500;
        }
        .alert-success {
            background: rgba(10, 92, 47, 0.08);
            border: 1px solid rgba(10, 92, 47, 0.18);
            color: #0a5c2f;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid #e5e7eb;
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
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 12px;
            flex: 1;
            min-width: 220px;
            max-width: 360px;
        }
        .search-box input {
            border: none;
            background: transparent;
            padding: 9px 0;
            font-size: 14px;
            font-family: inherit;
            width: 100%;
            outline: none;
            color: #1e293b;
        }
        .filter-select {
            padding: 9px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background: #f8fafc;
            color: #1e293b;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 0;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 13px;
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
            background: #0b6a36;
        }
        .btn-outline {
            background: #fff;
            color: #334155;
            border: 1px solid #dbe2ea;
        }
        .btn-outline:hover {
            background: #f8fafc;
        }
        .pagination-wrap {
            padding: 14px 20px;
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
    </style>
    @yield('extra-css')
</head>
<body class="bg-gray-100 text-gray-900 antialiased" style="font-family: 'Inter', sans-serif;">
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

        $pageTitle = trim($__env->yieldContent('page-title'));
        if ($pageTitle === '') {
            $pageTitle = trim($__env->yieldContent('title')) ?: 'Faculty Portal';
        }

        $unreadPayments = $unreadPayments ?? collect();
        $unreadCount = $unreadCount ?? 0;
    @endphp

    <aside id="faculty-sidebar" class="fixed top-0 left-0 z-40 h-screen w-64 -translate-x-full border-r border-emerald-900/70 bg-[#1a3c2e] transition-transform duration-300 lg:translate-x-0" aria-label="Faculty sidebar" data-drawer-backdrop="true">
        <div class="h-full overflow-y-auto px-3 py-4">
            <div class="mb-5 flex items-center justify-end lg:hidden">
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-200 hover:bg-emerald-800/60 hover:text-white" data-drawer-hide="faculty-sidebar" aria-controls="faculty-sidebar">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13" />
                    </svg>
                </button>
            </div>

            <div class="mb-5 rounded-lg border border-emerald-800/40 bg-emerald-900/30 p-3">
                <div class="flex items-center gap-3">
                    @if($ebaLogoUrl)
                        <img src="{{ $ebaLogoUrl }}" alt="EBA Logo" class="h-10 w-10 rounded-lg border border-emerald-700/60 bg-white/90 p-1 object-contain">
                    @else
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-emerald-700/60 bg-emerald-700 text-sm font-semibold text-white">EBA</div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-white whitespace-normal break-words leading-4">EBA IS</p>
                        <p class="text-xs text-emerald-300/80 whitespace-normal break-words leading-4 mt-1">CvSU - TMC Campus</p>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <h3 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-emerald-400/60">Main</h3>
                <ul class="space-y-1 font-medium">
                    <li>
                        <a href="{{ route('staff.dashboard') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('staff.dashboard') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                            </svg>
                            <span class="ms-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $partnershipsRoute }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('staff.partnerships') || request()->routeIs('staff.partnerships.*') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="ms-3">Partnerships</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $concessionairesRoute }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('staff.concessionaires') || request()->routeIs('staff.concessionaires.*') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="ms-3">Concessionaires</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('staff.history') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('staff.history') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="ms-3">History</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('staff.transaction-logs') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('staff.transaction-logs') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h6m-6 8h6m-6 0H7a2 2 0 01-2-2V7a2 2 0 012-2h6m0 0V3m0 2v2m4 4h2m-2 4h2" />
                            </svg>
                            <span class="ms-3">Transaction Logs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('staff.uniform-checkout') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('staff.uniform-checkout*') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="ms-3">Uniform Checkout</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('staff.stocks.index') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('staff.stocks*') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 12 3l9 4.5-9 4.5-9-4.5Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9 4.5 9-4.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5 12 21l9-4.5" />
                            </svg>
                            <span class="ms-3">Stocks</span>
                        </a>
                    </li>
                    @if ($settingsRoute)
                        <li>
                            <a href="{{ $settingsRoute }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('staff.settings') || request()->routeIs('staff.settings.*') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="ms-3">Settings</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="mt-auto">
                <ul class="space-y-1 font-medium">
                    <li>
                        <a href="{{ url('/') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm text-emerald-100 transition-colors hover:bg-emerald-800/50 hover:text-white">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span class="ms-3">Back to Main Site</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="group flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm text-red-200 transition-colors hover:bg-red-500/20 hover:text-red-100">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
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
                        <button type="button" class="inline-flex items-center rounded-xl bg-gradient-to-br from-emerald-50 to-white p-2.5 text-emerald-700 shadow-sm transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 lg:hidden" data-drawer-target="faculty-sidebar" data-drawer-toggle="faculty-sidebar" aria-controls="faculty-sidebar">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-5 w-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 5A.75.75 0 0 1 2.75 9h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 9.75Zm0 5A.75.75 0 0 1 2.75 14h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" />
                            </svg>
                        </button>
                        <div class="flex flex-col">
                            @php
                                $breadcrumbs = [];
                                if (request()->routeIs('staff.dashboard')) {
                                    $breadcrumbs = [
                                        ['label' => 'Dashboard', 'active' => true]
                                    ];
                                } elseif (request()->routeIs('staff.partnerships*')) {
                                    $breadcrumbs = [
                                        ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                        ['label' => 'Partnerships', 'active' => true]
                                    ];
                                } elseif (request()->routeIs('staff.concessionaires*')) {
                                    $breadcrumbs = [
                                        ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                        ['label' => 'Concessionaires', 'active' => true]
                                    ];
                                } elseif (request()->routeIs('staff.history*')) {
                                    $breadcrumbs = [
                                        ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                        ['label' => 'History', 'active' => true]
                                    ];
                                } elseif (request()->routeIs('staff.transaction-logs')) {
                                    $breadcrumbs = [
                                        ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                        ['label' => 'Transaction Logs', 'active' => true]
                                    ];
                                } elseif (request()->routeIs('staff.uniform-checkout*')) {
                                    $breadcrumbs = [
                                        ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                        ['label' => 'Uniform Checkout', 'active' => true]
                                    ];
                                } elseif (request()->routeIs('staff.stocks*')) {
                                    $breadcrumbs = [
                                        ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                        ['label' => 'Stocks', 'active' => true]
                                    ];
                                } elseif (request()->routeIs('staff.settings*')) {
                                    $breadcrumbs = [
                                        ['label' => 'Dashboard', 'url' => route('staff.dashboard')],
                                        ['label' => 'Settings', 'active' => true]
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
                        <button id="payment-notification-button" type="button" class="group relative inline-flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-orange-50 to-amber-50 text-orange-600 shadow-sm transition-all hover:shadow-md hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-500" title="Notifications" data-dropdown-toggle="payment-notification-dropdown" data-dropdown-placement="bottom-end" data-unread-count="{{ $unreadCount }}">
                            <svg class="h-7 w-7 transition-transform group-hover:rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>

                            @if($unreadCount > 0)
                                <span id="payment-notification-badge" class="absolute -right-0.5 -top-0.5 flex h-5 w-5">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"></span>
                                    <span class="relative inline-flex h-5 w-5 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-red-500 text-[9px] font-bold text-white shadow-lg ring-2 ring-white">{{ $unreadCount }}</span>
                                </span>
                            @endif
                        </button>

                        <div id="payment-notification-dropdown" class="z-50 hidden w-[320px] divide-y divide-emerald-100 rounded-2xl bg-white shadow-xl border border-emerald-100" role="menu" aria-labelledby="payment-notification-button">
                            <div class="bg-gradient-to-br from-emerald-50 to-white px-5 py-3 rounded-t-2xl border-b border-emerald-100">
                                <span class="text-sm font-bold text-emerald-900">Notifications</span>
                            </div>
                            <div class="p-4">
                                @if($unreadCount > 0)
                                    @foreach($unreadPayments as $payment)
                                        <a href="{{ route('staff.transaction-logs') }}" class="flex items-start gap-4 p-2 rounded-xl transition-colors hover:bg-emerald-50">
                                            <div class="rounded-full bg-emerald-100 p-2 text-emerald-600 shrink-0">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-emerald-700">Payment Recorded</p>
                                                <p class="mt-1 text-xs text-emerald-600/80 font-medium leading-tight">
                                                    {{ $payment->concessionaire?->business_name ?: ($payment->concessionaire?->name ?: 'Concessionaire') }} paid ₱{{ number_format((float) $payment->amount, 2) }}
                                                </p>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="flex items-start gap-4 p-2">
                                        <div class="rounded-full bg-gray-100 p-2 text-gray-500 shrink-0">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-700">No New Payments</p>
                                            <p class="mt-1 text-xs text-gray-500 font-medium leading-tight">
                                                You're all caught up
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-2 bg-gray-50/50 rounded-b-2xl">
                                <a href="{{ route('staff.transaction-logs') }}" class="block w-full rounded-xl px-4 py-2.5 text-center text-sm font-bold text-emerald-700 hover:bg-emerald-100/50 transition-colors">
                                    View Transaction Logs &rarr;
                                </a>
                            </div>
                        </div>

                        <div class="h-8 w-px bg-gradient-to-b from-transparent via-emerald-200 to-transparent hidden sm:block"></div>

                        <button id="faculty-user-menu-button" type="button" class="group inline-flex items-center gap-2.5 rounded-xl bg-gradient-to-br from-emerald-50 to-white px-3 py-2 shadow-sm transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1" data-dropdown-toggle="faculty-user-menu" data-dropdown-placement="bottom-end" aria-expanded="false">
                            @if($facultyAvatarUrl)
                                <img src="{{ $facultyAvatarUrl }}" alt="{{ $facultyName }}" class="h-9 w-9 rounded-full border-2 border-emerald-600 object-cover shadow-sm ring-2 ring-emerald-100">
                            @else
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-emerald-600 bg-gradient-to-br from-emerald-700 to-emerald-900 text-sm font-bold text-white shadow-sm ring-2 ring-emerald-100">{{ $facultyInitial }}</span>
                            @endif
                            <div class="hidden sm:flex sm:flex-col sm:items-start">
                                <span class="max-w-[120px] truncate text-sm font-bold text-emerald-900">{{ $facultyName }}</span>
                                <span class="text-xs font-medium text-emerald-600/70">Faculty</span>
                            </div>
                            <svg class="hidden h-4 w-4 text-emerald-600 transition-transform group-hover:translate-y-0.5 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="faculty-user-menu" class="z-50 hidden w-56 divide-y divide-emerald-100 rounded-xl border border-emerald-100 bg-white shadow-xl" role="menu" aria-labelledby="faculty-user-menu-button">
                            <div class="px-4 py-3">
                                <p class="truncate text-sm font-semibold text-emerald-900">{{ $facultyName }}</p>
                                <p class="truncate text-xs text-emerald-600/80">{{ $facultyEmail }}</p>
                            </div>
                            <ul class="py-1 text-sm text-gray-700" aria-labelledby="faculty-user-menu-button">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-red-600 transition-colors hover:bg-red-50" role="menuitem">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
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
            <div>
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
            </div>
        </main>
    </div>

    <script>
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
    </script>

    @livewireScripts
    @yield('scripts')
</body>
</html>