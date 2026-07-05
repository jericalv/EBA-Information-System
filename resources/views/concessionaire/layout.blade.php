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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
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
    </style>
    @yield('extra-css')
</head>
<body class="bg-gray-100 text-gray-900 antialiased" style="font-family: 'Inter', sans-serif;">
    @php
        $layoutUser = $user ?? auth()->user();
        $layoutUserName = $layoutUser?->name ?? 'Concessionaire';
        $layoutUserEmail = $layoutUser?->email ?? 'concessionaire@example.com';
        $layoutInitial = strtoupper(substr((string) $layoutUserName, 0, 1));
        $layoutAvatarUrl = $layoutUser?->profile_photo
            ? asset('storage/' . $layoutUser->profile_photo)
            : null;

        $pageTitle = trim($__env->yieldContent('page-title'));
        if ($pageTitle === '') {
            $pageTitle = trim($__env->yieldContent('title')) ?: 'Concessionaire Portal';
        }

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

    <aside id="concessionaire-sidebar" class="fixed top-0 left-0 z-40 h-screen w-64 -translate-x-full border-r border-emerald-900/70 bg-[#1a3c2e] transition-transform duration-300 lg:translate-x-0" aria-label="Concessionaire sidebar" data-drawer-backdrop="true">
        <div class="h-full overflow-y-auto px-3 py-4">
            <div class="mb-5 flex items-center justify-end lg:hidden">
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-200 hover:bg-emerald-800/60 hover:text-white" data-drawer-hide="concessionaire-sidebar" aria-controls="concessionaire-sidebar">
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
                        <a href="{{ route('concessionaire.dashboard') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('concessionaire.dashboard') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            <span class="ms-3">Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="mb-3">
                <h3 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-emerald-400/60">Management</h3>
                <ul class="space-y-1 font-medium">
                    <li>
                        <a href="{{ $productsRoute }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('concessionaire.products') || request()->routeIs('concessionaire.products.*') || request()->routeIs('concessionaire.products.index') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 0 1-2-2V3h20v2a2 2 0 0 1-2 2Zm0 0v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7"/>
                            </svg>
                            <span class="ms-3">Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('concessionaire.reviews') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('concessionaire.reviews') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            <span class="ms-3">Reviews</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $mediaRoute }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('concessionaire.media') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="ms-3">Media</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('concessionaire.payments') }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('concessionaire.payments') || request()->routeIs('concessionaire.payments.*') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="ms-3">Payments</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="mb-3">
                <h3 class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-emerald-400/60">General</h3>
                <ul class="space-y-1 font-medium">
                    <li>
                        <a href="{{ $settingsRoute }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('concessionaire.settings') || request()->routeIs('profile.edit') ? 'bg-emerald-800/80 text-white' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="ms-3">Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $homeRoute }}" class="group flex items-center rounded-lg px-3 py-2.5 text-sm text-emerald-100 transition-colors hover:bg-emerald-800/50 hover:text-white">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <span class="ms-3">Back to Main Site</span>
                        </a>
                    </li>
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
                        <button type="button" class="inline-flex items-center rounded-xl bg-gradient-to-br from-emerald-50 to-white p-2.5 text-emerald-700 shadow-sm transition-all hover:shadow-md hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 lg:hidden" data-drawer-target="concessionaire-sidebar" data-drawer-toggle="concessionaire-sidebar" aria-controls="concessionaire-sidebar">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-5 w-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 5A.75.75 0 0 1 2.75 9h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 9.75Zm0 5A.75.75 0 0 1 2.75 14h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z"/>
                            </svg>
                        </button>
                        <div class="flex flex-col">
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
                        <button id="payment-notification-button" type="button" class="group relative inline-flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-orange-50 to-amber-50 text-orange-600 shadow-sm transition-all hover:shadow-md hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-500" title="Notifications" data-dropdown-toggle="payment-notification-dropdown" data-dropdown-placement="bottom-end">
                            <svg class="h-5 w-5 transition-transform group-hover:rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($hasPaymentAlert)
                                <span class="absolute -right-1 -top-1 flex h-5 w-5">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"></span>
                                    <span class="relative inline-flex h-5 w-5 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-red-500 text-[9px] font-bold text-white shadow-lg ring-2 ring-white">!</span>
                                </span>
                            @endif
                        </button>

                        <div id="payment-notification-dropdown" class="z-50 hidden w-[320px] divide-y divide-emerald-100 rounded-2xl bg-white shadow-xl border border-emerald-100" role="menu" aria-labelledby="payment-notification-button">
                            <div class="bg-gradient-to-br from-emerald-50 to-white px-5 py-3 rounded-t-2xl border-b border-emerald-100">
                                <span class="text-sm font-bold text-emerald-900">Notifications</span>
                            </div>
                            <div class="p-4">
                                @if($hasPaidThisMonth ?? false)
                                    @php
                                        $lastPayment = \App\Models\ConcessionairePayment::where('concessionaire_id', auth()->id())->latest('payment_date')->first();
                                    @endphp
                                    <a href="{{ route('concessionaire.payments') }}" class="flex items-start gap-4 p-2 rounded-xl transition-colors hover:bg-emerald-50">
                                        <div class="rounded-full bg-emerald-100 p-2 text-emerald-600 shrink-0">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-emerald-700">Payment Recorded This Month</p>
                                            <p class="mt-1 text-xs text-emerald-600/80 font-medium leading-tight">
                                                ₱{{ number_format((float) auth()->user()->monthly_fee, 2) }} recorded{{ $lastPayment ? ' on ' . \Carbon\Carbon::parse($lastPayment->payment_date)->format('M d') : '' }}
                                            </p>
                                        </div>
                                    </a>
                                @elseif($isDueSoon ?? false)
                                    <a href="{{ route('concessionaire.payments') }}" class="flex items-start gap-4 p-2 rounded-xl transition-colors hover:bg-amber-50">
                                        <div class="rounded-full bg-amber-100 p-2 text-amber-600 shrink-0">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-amber-700">Payment Due Soon</p>
                                            <p class="mt-1 text-xs text-amber-600/80 font-medium leading-tight">
                                                Your monthly fee of ₱{{ number_format((float) auth()->user()->monthly_fee, 2) }} is due by the 1st
                                            </p>
                                        </div>
                                    </a>
                                @elseif($hasOverduePayment ?? false)
                                    <a href="{{ route('concessionaire.payments') }}" class="flex items-start gap-4 p-2 rounded-xl transition-colors hover:bg-red-50">
                                        <div class="rounded-full bg-red-100 p-2 text-red-600 shrink-0">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-red-700">Payment Overdue</p>
                                            <p class="mt-1 text-xs text-red-600/80 font-medium leading-tight">
                                                No payment recorded for this month
                                            </p>
                                        </div>
                                    </a>
                                @else
                                    <div class="flex items-start gap-4 p-2">
                                        <div class="rounded-full bg-gray-100 p-2 text-gray-500 shrink-0">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-700">No Contract</p>
                                            <p class="mt-1 text-xs text-gray-500 font-medium leading-tight">
                                                No active contract on file
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-2 bg-gray-50/50 rounded-b-2xl">
                                <a href="{{ route('concessionaire.payments') }}" class="block w-full rounded-xl px-4 py-2.5 text-center text-sm font-bold text-emerald-700 hover:bg-emerald-100/50 transition-colors">
                                    View Payment Page &rarr;
                                </a>
                            </div>
                        </div>

                        <div class="h-8 w-px bg-gradient-to-b from-transparent via-emerald-200 to-transparent hidden sm:block"></div>

                        <button id="concessionaire-user-menu-button" type="button" class="group inline-flex items-center gap-2.5 rounded-xl bg-gradient-to-br from-emerald-50 to-white px-3 py-2 shadow-sm transition-all hover:shadow-md hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1" data-dropdown-toggle="concessionaire-user-menu" data-dropdown-placement="bottom-end" aria-expanded="false">
                            @if($layoutAvatarUrl)
                                <img src="{{ $layoutAvatarUrl }}" alt="{{ $layoutUserName }}" class="h-9 w-9 rounded-full border-2 border-emerald-600 object-cover shadow-sm ring-2 ring-emerald-100">
                            @else
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-emerald-600 bg-gradient-to-br from-emerald-700 to-emerald-900 text-sm font-bold text-white shadow-sm ring-2 ring-emerald-100">
                                    {{ $layoutInitial }}
                                </span>
                            @endif
                            <div class="hidden lg:flex lg:flex-col lg:items-start">
                                <span class="text-sm font-bold text-emerald-900 truncate max-w-[120px]">{{ $layoutUserName }}</span>
                                <span class="text-xs text-emerald-600/70 font-medium">Concessionaire</span>
                            </div>
                            <svg class="h-4 w-4 text-emerald-600 transition-transform group-hover:translate-y-0.5 hidden lg:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div id="concessionaire-user-menu" class="z-50 hidden w-64 divide-y divide-emerald-100 rounded-2xl bg-white shadow-xl border border-emerald-100" role="menu" aria-labelledby="concessionaire-user-menu-button">
                            <div class="bg-gradient-to-br from-emerald-50 to-white px-5 py-4 rounded-t-2xl">
                                <div class="flex items-center gap-3 mb-3">
                                    @if($layoutAvatarUrl)
                                        <img src="{{ $layoutAvatarUrl }}" alt="{{ $layoutUserName }}" class="h-12 w-12 rounded-full border-2 border-emerald-600 object-cover shadow-md">
                                    @else
                                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full border-2 border-emerald-600 bg-gradient-to-br from-emerald-700 to-emerald-900 text-base font-bold text-white shadow-md">
                                            {{ $layoutInitial }}
                                        </span>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="truncate font-bold text-emerald-900 text-sm">{{ $layoutUserName }}</div>
                                        <div class="truncate text-emerald-600 text-xs font-medium">{{ $layoutUserEmail }}</div>
                                    </div>
                                </div>
                                <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white shadow-sm">
                                    <div class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></div>
                                    <span>Active Account</span>
                                </div>
                            </div>
                            <ul class="py-2 text-sm">
                                <li>
                                    <a href="{{ $settingsRoute }}" class="group flex items-center gap-3 px-5 py-3 text-gray-700 transition-colors hover:bg-emerald-50">
                                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="font-semibold group-hover:text-emerald-700">Account Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="group flex w-full items-center gap-3 px-5 py-3 text-left text-red-600 transition-colors hover:bg-red-50">
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
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
