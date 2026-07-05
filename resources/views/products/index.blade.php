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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green: #0A5C2F;
            --green-light: #0D7A3E;
            --green-dark: #064420;
            --gold: #D4A843;
            --gold-light: #E8C96A;
            --cvsu-green: #0A5C2F;
            --cvsu-green-light: #0D7A3E;
            --cvsu-green-dark: #064420;
            --cvsu-gold: #D4A843;
            --cvsu-gold-light: #E8C96A;
            --white: #FFFFFF;
            --gray-50: #FAFAF8;
            --gray-100: #F5F5F4;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #F5F5F4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #1a202c;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: var(--gray-900);
        }

        .nav-brand img { width: 44px; height: 44px; border-radius: 10px; object-fit: contain; }

        .nav-brand-text { display: flex; flex-direction: column; }

        .nav-brand-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.2px;
            color: var(--cvsu-green);
            line-height: 1.2;
        }

        .nav-brand-subtitle {
            font-size: 11px;
            font-weight: 500;
            color: var(--gray-600);
            letter-spacing: 0.3px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a {
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-600);
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-links a:hover { color: var(--cvsu-green); background: rgba(10, 92, 47, 0.06); }

        .nav-links a.active {
            color: var(--cvsu-green);
            background: rgba(10, 92, 47, 0.1);
            font-weight: 700;
        }

        .nav-links .btn-login { color: var(--cvsu-green); font-weight: 600; }

        .nav-links .btn-register {
            background: var(--cvsu-green);
            color: var(--white);
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 8px;
        }

        .nav-links .btn-register:hover { background: var(--cvsu-green-light); color: var(--white); }

        .nav-links .btn-logout {
            background: transparent;
            color: #dc2626;
            font-weight: 600;
            padding: 8px 20px;
            border: 2px solid #dc2626;
            border-radius: 8px;
        }
        .nav-links .btn-logout:hover { background: #dc2626; color: var(--white); }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
        }

        .mobile-toggle span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--gray-700);
            margin: 5px 0;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .nav-mobile-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-profile-mobile { display: none; }

        /* ===== MAIN ===== */
        .page-wrapper {
            flex: 1;
            padding-top: 72px;
        }

        .page-header {
            background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
            padding: 48px 24px;
            color: #fff;
        }
        .page-header-inner {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }
        .page-header h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        .page-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .main-content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 16px;
        }

        /* ===== FILTERS ===== */
        .filters-bar {
            background: #fff;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
        }
        .search-box {
            flex: 1;
            min-width: 200px;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        .search-box input:focus {
            outline: none;
            border-color: var(--green);
        }
        .search-box svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #94a3b8;
        }
        .filter-select {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: #fff;
            cursor: pointer;
            min-width: 140px;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--green);
        }
        .btn {
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
        }
        .btn-green {
            background: var(--green);
            color: #fff;
        }
        .btn-green:hover {
            background: var(--green-light);
        }
        .btn-outline {
            background: transparent;
            border: 2px solid #e2e8f0;
            color: #64748b;
        }
        .btn-outline:hover {
            border-color: var(--green);
            color: var(--green);
        }

        /* ===== PRODUCTS GRID ===== */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }
        .hidden {
            display: none !important;
        }
        .product-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            color: inherit;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        .product-main-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .product-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-image svg {
            width: 48px;
            height: 48px;
            color: #cbd5e1;
        }
        .product-info {
            padding: 20px;
        }
        .product-category {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(212,168,67,0.12);
            color: #b8860b;
            margin-bottom: 10px;
        }
        .product-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #1a202c;
        }
        .product-vendor {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 0;
        }
        .product-vendor-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 12px 0;
        }
        .vendor-avatar-link {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--green) 0%, var(--green-light) 100%);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            flex-shrink: 0;
        }
        .vendor-name-link {
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
        }
        .vendor-name-link:hover {
            color: var(--green);
            text-decoration: underline;
        }
        .product-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .product-price {
            font-size: 20px;
            font-weight: 800;
            color: var(--green);
        }
        .product-rating {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .stars {
            display: flex;
            gap: 2px;
        }
        .star {
            width: 16px;
            height: 16px;
            color: #e2e8f0;
        }
        .star.filled {
            color: #fbbf24;
        }
        .rating-count {
            font-size: 13px;
            color: #94a3b8;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 80px 24px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .empty-state svg {
            width: 64px;
            height: 64px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }
        .empty-state h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #374151;
        }
        .empty-state p {
            color: #64748b;
            font-size: 15px;
        }

        /* ===== PAGINATION ===== */
        .pagination-wrap {
            margin-top: 32px;
            display: flex;
            justify-content: center;
        }
        .pagination-wrap nav {
            display: flex;
            gap: 4px;
        }
        .pagination-wrap a, .pagination-wrap span {
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            color: #64748b;
            background: #fff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
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

        /* ===== AVAILABLE STOCKS ===== */
        .stocks-section {
            margin-top: 0;
        }
        .stocks-grid-cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }
        @media (min-width: 640px) {
            .stocks-grid-cards {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .stocks-grid-cards {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        .stock-chip {
            background: #fff;
            border-radius: 14px;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: all 0.2s;
        }
        .stock-chip-link {
            color: inherit;
            text-decoration: none;
            display: block;
        }
        .stock-chip.out-of-stock {
            background: #fff5f5;
            border: 1px solid #fecaca;
            opacity: 0.82;
        }
        .stock-chip:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            transform: translateY(-4px);
        }
        .stock-chip-image {
            width: 100%;
            height: 180px;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stock-chip-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .stock-chip-placeholder {
            width: 30px;
            height: 30px;
            color: #94a3b8;
        }
        .stock-chip-placeholder svg {
            width: 100%;
            height: 100%;
        }
        .stock-chip-body {
            padding: 20px;
        }
        .stock-chip-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #1a202c;
            text-transform: none;
            letter-spacing: 0;
            text-align: left;
        }
        .stock-chip-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .stock-chip-qty {
            font-size: 28px;
            font-weight: 800;
            color: #0A5C2F;
            line-height: 1;
            margin: 0;
        }
        .stock-chip-qty.low-stock {
            color: #ea580c;
        }
        .stock-chip-qty.out-of-stock {
            color: #dc2626;
        }
        .stock-chip-available {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #166534;
            font-weight: 700;
            margin: 0;
            background: #f0fdf4;
            padding: 5px 10px;
            border-radius: 999px;
        }
        .stock-chip-available.low-stock {
            color: #c2410c;
            background: #fff7ed;
        }
        .stock-chip-available.out-of-stock {
            color: #dc2626;
            background: #fef2f2;
        }
        .stock-indicator-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #16a34a;
            flex-shrink: 0;
        }
        .stock-chip-available.low-stock .stock-indicator-dot {
            background: #f97316;
        }
        .stock-chip-available.out-of-stock .stock-indicator-dot {
            background: #dc2626;
        }
        .stock-chip-hidden-qty {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            margin: 0;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px;
            text-align: center;
            margin-top: auto;
        }
        .footer-text {
            font-size: 13px;
            color: #64748b;
        }
        .footer-text a {
            color: var(--green);
            text-decoration: none;
            font-weight: 600;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 640px) {
            .products-grid { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .mobile-toggle { display: block; }
            .nav-mobile-actions { margin-left: auto; }
            .nav-profile-mobile { display: inline-flex; }
            .nav-links.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 72px;
                left: 0;
                right: 0;
                background: var(--white);
                padding: 16px 24px;
                border-bottom: 1px solid var(--gray-200);
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            }
            .filters-bar {
                flex-direction: column;
            }
            .search-box {
                width: 100%;
            }
            .filter-select, .btn {
                width: 100%;
            }
            .page-header h1 {
                font-size: 24px;
            }
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
                    <span class="nav-brand-subtitle">CvSU &mdash; Trece Martires City Campus</span>
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
        <div class="page-header">
            <div class="page-header-inner">
                <h1>Canteen Products</h1>
                <p>Browse and review food items from our campus concessionaires</p>
            </div>
        </div>

        <div class="main-content">
            <!-- Filters -->
            <form id="products-filters-form" method="GET" action="{{ route('products.index') }}" class="filters-bar">
                <div class="search-box">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                <div style="background: #dcfce7; border: 1px solid #86efac; padding: 16px; border-radius: 10px; margin-bottom: 24px; color: #166534; font-weight: 500;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: #fee2e2; border: 1px solid #fca5a5; padding: 16px; border-radius: 10px; margin-bottom: 24px; color: #991b1b; font-weight: 500;">
                    {{ session('error') }}
                </div>
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
                                <a href="{{ route('products.show', $product) }}" class="product-main-link">
                                    <div class="product-image">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                        @else
                                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="product-info">
                                        <span class="product-category">{{ $product->category }}</span>
                                        <h3 class="product-name">{{ $product->name }}</h3>
                                        <div class="product-vendor-row">
                                            <a href="{{ route('concessionaires.show', $product->concessionaire_id) }}" class="vendor-avatar-link" title="View concessionaire">
                                                {{ strtoupper(substr($product->concessionaire->business_name ?? $product->concessionaire->name, 0, 2)) }}
                                            </a>
                                            <a href="{{ route('concessionaires.show', $product->concessionaire_id) }}" class="vendor-name-link">
                                                {{ $product->concessionaire->business_name ?? $product->concessionaire->name }}
                                            </a>
                                        </div>
                                        <div class="product-meta">
                                            <span class="product-price">₱{{ number_format($product->price, 2) }}</span>
                                            <div class="product-rating">
                                                @if(($product->reviews_count ?? 0) > 0)
                                                    <div class="stars">
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
                                    </div>
                                </a>
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
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                        <h3>No Products Found</h3>
                        <p>There are no products matching your criteria. Try adjusting your filters.</p>
                    </div>
                @endif
            </div>

            @if(isset($visibleStocks) && $visibleStocks->count() > 0)
                </div>

                <section class="page-header" aria-label="Campus Available Attires">
                    <div class="page-header-inner">
                        <h1>Campus Available Items</h1>
                        <p>Check the available items that you can find at CvSU Trece Martires City Campus</p>
                    </div>
                </section>

                <div class="main-content stocks-section">
                    <div class="stocks-grid-cards">
                        @foreach($visibleStocks as $stock)
                            @php
                                $stockQty = (int) $stock->quantity;
                                $isOutOfStock = $stockQty === 0;
                                $isLowStock = $stockQty > 0 && $stockQty <= 10;
                            @endphp
                            <a href="{{ route('stocks.show', $stock) }}" class="stock-chip-link">
                                <article class="stock-chip {{ $isOutOfStock ? 'out-of-stock' : '' }}">
                                    <div class="stock-chip-image">
                                        @if($stock->image)
                                            <img src="{{ asset('storage/' . $stock->image) }}" alt="{{ $stock->item_name }}">
                                        @else
                                            <span class="stock-chip-placeholder" aria-hidden="true">
                                                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5 12 3l8.25 4.5L12 12 3.75 7.5Z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12 12 16.5 20.25 12"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 16.5 12 21l8.25-4.5"/>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="stock-chip-body">
                                        <h3 class="stock-chip-name">{{ $stock->item_name }}</h3>
                                        @if(($stock->item_type ?? null) === 'books' && $stock->unit_price > 0)
                                            <p style="font-size:16px;font-weight:700;color:#0A5C2F;margin-bottom:8px;">&#8369;{{ number_format($stock->unit_price, 2) }}</p>
                                        @endif
                                        <div class="stock-chip-meta">
                                            @auth
                                                <span class="stock-chip-qty {{ $isOutOfStock ? 'out-of-stock' : ($isLowStock ? 'low-stock' : '') }}">{{ number_format($stockQty) }}</span>
                                            @else
                                                <span class="stock-chip-hidden-qty"> </span>
                                            @endauth
                                            @if($isOutOfStock)
                                                <span class="stock-chip-available out-of-stock"><span class="stock-indicator-dot" aria-hidden="true"></span>Out of Stock</span>
                                            @elseif($isLowStock)
                                                <span class="stock-chip-available low-stock"><span class="stock-indicator-dot" aria-hidden="true"></span>Low Stock</span>
                                            @else
                                                <span class="stock-chip-available"><span class="stock-indicator-dot" aria-hidden="true"></span>Available</span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                </div>
            @endif
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p class="footer-text">&copy; {{ date('Y') }} <a href="/">CvSU</a> — Trece Martires City Campus. All rights reserved.</p>
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
