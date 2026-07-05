<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $stock->item_name }} | EBA Information System</title>
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
            --cvsu-green: #0A5C2F;
            --cvsu-green-light: #0D7A3E;
            --white: #FFFFFF;
            --gray-50: #FAFAF8;
            --gray-100: #F5F5F4;
            --gray-200: #E5E7EB;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-900: #111827;

            --spacing-sm: 8px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--gray-900);
        }

        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .navbar.scrolled { box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08); }

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
            display: flex; align-items: center; gap: 14px;
            text-decoration: none; color: var(--gray-900);
        }

        .nav-brand img { width: 44px; height: 44px; border-radius: 10px; object-fit: contain; }
        .nav-brand-text { display: flex; flex-direction: column; }

        .nav-brand-title {
            font-size: 16px; font-weight: 800; letter-spacing: -0.2px;
            color: var(--cvsu-green); line-height: 1.2;
        }

        .nav-brand-subtitle {
            font-size: 11px; font-weight: 500;
            color: var(--gray-600); letter-spacing: 0.3px;
        }

        .nav-links { display: flex; align-items: center; gap: 8px; }

        .nav-links a {
            text-decoration: none; font-size: 14px; font-weight: 500;
            color: var(--gray-600); padding: 8px 16px;
            border-radius: 8px; transition: all 0.2s ease;
        }

        .nav-links a:hover { color: var(--cvsu-green); background: rgba(10, 92, 47, 0.06); }

        .nav-links a.active {
            color: var(--cvsu-green); background: rgba(10, 92, 47, 0.1); font-weight: 700;
        }

        .nav-links .btn-login { color: var(--cvsu-green); font-weight: 600; }

        .nav-links .btn-register {
            background: var(--cvsu-green); color: var(--white);
            font-weight: 600; padding: 8px 20px; border-radius: 8px;
        }

        .nav-links .btn-register:hover { background: var(--cvsu-green-light); color: var(--white); }

        .nav-links .btn-logout {
            background: transparent; color: #dc2626; font-weight: 600;
            padding: 8px 20px; border: 2px solid #dc2626; border-radius: 8px;
        }

        .nav-links .btn-logout:hover { background: #dc2626; color: var(--white); }

        .mobile-toggle {
            display: none; background: none; border: none;
            cursor: pointer; padding: 8px;
        }

        .mobile-toggle span {
            display: block; width: 24px; height: 2px;
            background: var(--gray-700); margin: 5px 0;
            border-radius: 2px; transition: all 0.3s ease;
        }

        .nav-mobile-actions { display: flex; align-items: center; gap: 10px; }
        .nav-profile-mobile { display: none; }

        .page-wrapper { flex: 1; padding-top: 72px; }

        .main-content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xl) var(--spacing-md);
        }

        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: var(--spacing-lg); font-size: 14px;
        }

        .breadcrumb a { color: var(--gray-600); text-decoration: none; }
        .breadcrumb a:hover { color: var(--cvsu-green); }
        .breadcrumb span { color: var(--gray-200); }
        .breadcrumb .current { color: var(--gray-900); font-weight: 500; }

        .stock-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-lg);
            align-items: stretch;
        }

        .stock-image-lg {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            min-height: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stock-image-lg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .stock-image-lg svg {
            width: 96px; height: 96px; color: var(--gray-200);
        }

        .stock-content {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            padding: var(--spacing-lg);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            gap: var(--spacing-sm);
        }

        .stock-type {
            font-size: 11px;
            font-weight: 500;
            color: var(--gray-600);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .stock-content h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0;
            line-height: 1.1;
        }

        .stock-status-badge {
            display: inline-block;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            width: fit-content;
            margin-top: 0;
            margin-bottom: var(--spacing-md);
            text-transform: uppercase;
            color: var(--white);
        }

        .stock-status-badge.available { background-color: var(--cvsu-green); }
        .stock-status-badge.low-stock { background-color: #ea580c; }
        .stock-status-badge.out-of-stock { background-color: #dc2626; }

        .stock-login-notice {
            margin-top: var(--spacing-sm);
            border: 1px solid rgba(59,130,246,0.18);
            background: rgba(59,130,246,0.07);
            color: #1d4ed8;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 14px;
            line-height: 1.5;
        }

        .stock-login-notice a {
            color: #1e40af;
            font-weight: 700;
            text-decoration: underline;
        }

        .stock-login-notice a:hover {
            color: #1d4ed8;
        }

        .quantity-box {
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: var(--spacing-md);
            background: var(--gray-50);
            margin-bottom: var(--spacing-md);
        }

        .quantity-label {
            font-size: 13px;
            color: var(--gray-600);
            font-weight: 500;
            margin-bottom: 4px;
        }

        .quantity-value {
            font-size: 36px;
            font-weight: 700;
            color: var(--cvsu-green);
            line-height: 1;
        }

        .quantity-value.low-stock { color: #ea580c; }
        .quantity-value.out-of-stock { color: #dc2626; }

        .unit-price-box {
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: var(--spacing-md);
            background: var(--gray-50);
            margin-bottom: var(--spacing-md);
        }

        .unit-price-label {
            font-size: 13px;
            color: var(--gray-600);
            font-weight: 500;
            margin-bottom: 4px;
        }

        .unit-price-value {
            font-size: 36px;
            font-weight: 700;
            color: var(--cvsu-green);
            line-height: 1;
        }

        .pricing-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-700);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: var(--spacing-sm);
            margin-bottom: var(--spacing-md);
        }

        .price-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }

        .price-card {
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: var(--spacing-md);
            background: #fff;
            display: flex;
            flex-direction: column;
            gap: 4px;
            text-align: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .price-card:hover {
            border-color: var(--cvsu-green-light);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .price-size {
            font-size: 13px;
            color: var(--gray-600);
            font-weight: 500;
            text-transform: uppercase;
        }

        .price-amount {
            font-size: 20px;
            font-weight: 700;
            color: var(--cvsu-green);
        }
        .stock-amount {
            font-size: 20px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .as-of-date {
            font-size: 13px;
            color: var(--gray-600);
            margin-top: auto;
            padding-top: var(--spacing-md);
            text-align: center;
            width: 100%;
        }

        .pricing-size-label,
        .pricing-size-table-wrap,
        .pricing-size-table { display: none; }

        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: var(--spacing-lg);
            text-align: center;
            margin-top: auto;
        }

        .footer-text { font-size: 13px; color: var(--gray-600); }
        .footer-text a { color: var(--cvsu-green); text-decoration: none; font-weight: 600; }

        @media (max-width: 900px) {
            .stock-detail { grid-template-columns: 1fr; gap: 20px; }
            .stock-image-lg { max-height: 360px; }
            .price-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .mobile-toggle { display: block; }
            .nav-mobile-actions { margin-left: auto; }
            .nav-profile-mobile { display: inline-flex; }
            .nav-links.active {
                display: flex; flex-direction: column;
                position: absolute; top: 72px; left: 0; right: 0;
                background: var(--white); padding: 16px 24px;
                border-bottom: 1px solid var(--gray-200);
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            }
        }

        @media (max-width: 600px) {
            .stock-content { padding: 20px; }
            .price-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('partials.pending-application-banner')

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
                    <span></span><span></span><span></span>
                </button>
            </div>

            @if (Route::has('login'))
                <div class="nav-links" id="navLinks">
                    @include('partials.public-nav-links', ['loginButtonClass' => 'btn-login'])
                </div>
            @endif
        </div>
    </nav>

    @php
        $stockQty = (int) $stock->quantity;

        if ($stockQty === 0) {
            $stockStatusClass = 'out-of-stock';
            $stockStatusLabel = 'Out of Stock';
        } elseif ($stockQty <= 10) {
            $stockStatusClass = 'low-stock';
            $stockStatusLabel = 'Low Stock';
        } else {
            $stockStatusClass = 'available';
            $stockStatusLabel = 'Available';
        }

        $sizeOrder = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];
        $stockPrices = is_array($stock->prices ?? null) ? $stock->prices : [];
        $stockSizes = is_array($stock->sizes ?? null) ? $stock->sizes : [];
        $visiblePrices = [];
        $visibleStockBySize = [];

        if (($stock->item_type ?? null) === 'uniforms') {
            foreach ($sizeOrder as $sizeKey) {
                $price = (float) ($stockPrices[$sizeKey] ?? 0);
                $sizeQty = (int) ($stockSizes[$sizeKey] ?? 0);

                if ($price > 0) {
                    $visiblePrices[$sizeKey] = $price;
                }

                if ($sizeQty > 0) {
                    $visibleStockBySize[$sizeKey] = $sizeQty;
                }
            }
        }
    @endphp

    <div class="page-wrapper">
        <div class="main-content">
            <div class="breadcrumb">
                <a href="/">Home</a>
                <span>/</span>
                <a href="{{ route('products.index') }}">Products</a>
                <span>/</span>
                <span class="current">{{ $stock->item_name }}</span>
            </div>

            <div class="stock-detail">
                <div class="stock-image-lg">
                    @if($stock->image)
                        <img src="{{ asset('storage/' . $stock->image) }}" alt="{{ $stock->item_name }}">
                    @else
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5 12 3l8.25 4.5L12 12 3.75 7.5Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12 12 16.5 20.25 12"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 16.5 12 21l8.25-4.5"/>
                        </svg>
                    @endif
                </div>

                <section class="stock-content" aria-label="Stock details">
                    <span class="stock-type">{{ strtoupper($stock->item_type ?? '') }}</span>
                    <h1>{{ $stock->item_name }}</h1>

                    @auth
                    <span class="stock-status-badge {{ $stockStatusClass }}">
                        {{ $stockStatusLabel }}
                    </span>

                    <div class="quantity-box">
                        <p class="quantity-label">Quantity available</p>
                        <div class="quantity-value {{ $stockStatusClass }}">{{ number_format($stockQty) }}</div>
                    </div>

                    @if(($stock->item_type ?? null) === 'books' && $stock->unit_price > 0)
                        <div class="unit-price-box">
                            <p class="unit-price-label">Price</p>
                            <div class="unit-price-value">&#8369;{{ number_format($stock->unit_price, 2) }}</div>
                        </div>
                    @endif

                    @if(($stock->item_type ?? null) === 'uniforms' && !empty($visiblePrices))
                        <p class="pricing-label">PRICING BY SIZE</p>
                        <div class="price-grid">
                            @foreach($visiblePrices as $sizeKey => $price)
                                <div class="price-card">
                                    <span class="price-size">{{ $sizeKey }}</span>
                                    <span class="price-amount">&#8369;{{ number_format($price, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(($stock->item_type ?? null) === 'uniforms' && !empty($visibleStockBySize))
                        <p class="pricing-label">STOCK BY SIZE</p>
                        <div class="price-grid">
                            @foreach($visibleStockBySize as $sizeKey => $sizeQty)
                                <div class="price-card">
                                    <span class="price-size">{{ $sizeKey }}</span>
                                    <span class="stock-amount">{{ number_format($sizeQty) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <p class="as-of-date">As of {{ date('F Y') }} &mdash; CvSU Trece Martires</p>
                    @else
                    <div class="stock-login-notice" role="status" aria-live="polite">
                        Log in to view stock details.
                        <a href="{{ route('login') }}">Log in here</a>.
                    </div>
                    @endauth
                </section>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p class="footer-text">&copy; {{ date('Y') }} <a href="/">CvSU</a> - Trece Martires City Campus. All rights reserved.</p>
    </footer>

    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }
    </script>
</body>
</html>
