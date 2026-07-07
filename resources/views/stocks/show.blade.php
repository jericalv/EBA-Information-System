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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900|manrope:400,500,600,700,800|ibm-plex-mono:400,500,600&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink: #16231C;
            --ink-soft: #43534A;
            --ink-faint: #68786D;
            --paper: #EDF2E8;
            --paper-deep: #E0E8D9;
            --card: #FFFFFF;
            --card-soft: #F7FAF4;
            --green: #0A5C2F;
            --green-bright: #0D7A3E;
            --green-deep: #07341C;
            --gold: #C99A2E;
            --gold-soft: #E4C36B;
            --line: #D7E0CF;
            --line-strong: #BECBB3;
            --red: #B4232A;
            --red-deep: #7C1D22;
            --amber: #B45309;
            --font-display: 'Inter', 'Manrope', system-ui, sans-serif;
            --font-body: 'Manrope', system-ui, -apple-system, sans-serif;
            --font-mono: 'IBM Plex Mono', 'SFMono-Regular', Consolas, monospace;
        }

        body {
            font-family: var(--font-body);
            background: var(--paper);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
        }

        img { max-width: 100%; }

        a:focus-visible, button:focus-visible {
            outline: 2px solid var(--green);
            outline-offset: 2px;
            border-radius: 2px;
        }

        .eyebrow {
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--green);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .eyebrow::before {
            content: '';
            width: 22px;
            height: 1px;
            background: var(--gold);
            flex-shrink: 0;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(237, 242, 232, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
            transition: box-shadow .3s ease, background-color .3s ease;
        }
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 2px 24px rgba(24, 36, 32, 0.07);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--ink);
        }
        .nav-brand img { width: 42px; height: 42px; object-fit: contain; }
        .nav-brand-text { display: flex; flex-direction: column; }
        .nav-brand-title {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.2px;
            color: var(--green);
            line-height: 1.2;
        }
        .nav-brand-subtitle {
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 500;
            color: var(--ink-faint);
            letter-spacing: 1.1px;
            text-transform: uppercase;
        }

        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-links a {
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink-soft);
            padding: 8px 14px;
            border-radius: 6px;
            transition: color .2s ease, background-color .2s ease;
        }
        .nav-links a:hover { color: var(--green); background: rgba(10, 92, 47, 0.06); }
        .nav-links a.active { color: var(--green); background: rgba(10, 92, 47, 0.09); }
        .nav-links .btn-login {
            color: #fff;
            background: var(--green);
            padding: 9px 20px;
            margin-left: 8px;
        }
        .nav-links .btn-login:hover { background: var(--green-bright); color: #fff; }

        .mobile-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; }
        .mobile-toggle span {
            display: block;
            width: 22px; height: 2px;
            background: var(--ink);
            margin: 5px 0;
            border-radius: 2px;
        }
        .nav-mobile-actions { display: none; align-items: center; gap: 10px; }
        .nav-profile-mobile { display: none; }

        /* ===== MAIN ===== */
        .page-wrapper { flex: 1; padding-top: 70px; }

        .main-content {
            width: 100%;
            max-width: 1160px;
            margin: 0 auto;
            padding: 34px 24px 56px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 26px;
            font-family: var(--font-mono);
            font-size: 11.5px;
            letter-spacing: 0.3px;
        }
        .breadcrumb a {
            color: var(--ink-faint);
            text-decoration: none;
            transition: color .2s ease;
        }
        .breadcrumb a:hover { color: var(--green); }
        .breadcrumb span { color: var(--line-strong); }
        .breadcrumb .current { color: var(--green); font-weight: 600; }

        /* ===== STOCK DETAIL ===== */
        .stock-detail {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 44px;
            align-items: start;
        }

        .sd-media {
            position: relative;
            background-color: var(--paper-deep);
            background-image: radial-gradient(rgba(24, 36, 32, 0.1) 1px, transparent 1px);
            background-size: 16px 16px;
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sd-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .sd-media svg {
            width: 88px;
            height: 88px;
            color: var(--line-strong);
        }
        .sd-cat {
            position: absolute;
            top: 14px; left: 14px;
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--line);
            border-radius: 5px;
            padding: 6px 10px;
            color: var(--ink-soft);
            z-index: 2;
        }

        .sd-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(26px, 3vw, 34px);
            line-height: 1.14;
            letter-spacing: -0.5px;
            color: var(--ink);
            margin: 12px 0 14px;
        }

        .sd-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 14px;
            font-family: var(--font-mono);
            font-size: 11.5px;
            color: var(--ink-faint);
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--line);
        }
        .sd-meta strong { color: var(--ink-soft); font-weight: 600; }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            border: 1px solid var(--line-strong);
            border-radius: 999px;
            padding: 5px 13px;
            background: var(--card);
            color: var(--ink-soft);
        }
        .status-pill::before {
            content: '';
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--green-bright);
            flex-shrink: 0;
        }
        .status-pill.available { color: var(--green); }
        .status-pill.low-stock { color: var(--amber); }
        .status-pill.low-stock::before { background: var(--amber); }
        .status-pill.out-of-stock { color: var(--red); }
        .status-pill.out-of-stock::before { background: var(--red); }

        /* Summary cards */
        .summary-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 24px;
        }
        .summary-row.single { grid-template-columns: 1fr; }
        .summary-card {
            background: var(--card-soft);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 16px 18px;
        }
        .summary-label {
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--ink-faint);
            margin-bottom: 6px;
        }
        .summary-value {
            font-family: var(--font-mono);
            font-size: 28px;
            font-weight: 600;
            color: var(--green);
            line-height: 1.1;
            font-variant-numeric: tabular-nums;
        }
        .summary-value.low-stock { color: var(--amber); }
        .summary-value.out-of-stock { color: var(--red); }
        .summary-value small {
            font-size: 12px;
            font-weight: 500;
            color: var(--ink-faint);
            margin-left: 4px;
        }

        /* ===== SIZE RUN BOARD (signature) ===== */
        .size-board {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 22px;
        }
        .size-board-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }
        .size-board-title {
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: var(--ink-soft);
        }
        .size-board-title strong { color: var(--green); font-weight: 600; }

        /* Segmented Price / Sizes switch */
        .view-switch {
            position: relative;
            display: inline-flex;
            background: var(--paper-deep);
            border: 1px solid var(--line-strong);
            border-radius: 8px;
            padding: 3px;
        }
        .view-switch::before {
            content: '';
            position: absolute;
            top: 3px; bottom: 3px;
            left: 3px;
            width: calc(50% - 3px);
            background: var(--green-deep);
            border-radius: 6px;
            transition: transform .25s cubic-bezier(0.34, 1.1, 0.64, 1);
        }
        .view-switch[data-view="sizes"]::before { transform: translateX(100%); }
        .view-switch button {
            position: relative;
            z-index: 1;
            background: none;
            border: none;
            cursor: pointer;
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--ink-faint);
            padding: 7px 18px;
            min-width: 96px;
            border-radius: 6px;
            transition: color .2s ease;
        }
        .view-switch button[aria-pressed="true"] { color: var(--gold-soft); }
        .view-switch button:hover:not([aria-pressed="true"]) { color: var(--green); }

        .size-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .size-tile {
            position: relative;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--card-soft);
            padding: 14px 12px 12px;
            text-align: center;
            overflow: hidden;
            transition: border-color .2s ease, background-color .2s ease;
        }
        .size-tile:hover { border-color: var(--line-strong); background: #fff; }
        .size-tile.is-out { background: repeating-linear-gradient(-45deg, var(--card-soft), var(--card-soft) 6px, rgba(24, 36, 32, 0.025) 6px, rgba(24, 36, 32, 0.025) 12px); }

        .size-tag {
            display: inline-block;
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.2px;
            color: var(--ink-soft);
            border: 1px solid var(--line-strong);
            border-radius: 5px;
            background: #fff;
            padding: 2px 9px;
            margin-bottom: 10px;
        }

        .size-values {
            position: relative;
            height: 42px;
        }
        .size-val {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1px;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity .22s ease, transform .22s ease;
            pointer-events: none;
        }
        .size-board[data-view="price"] .size-val.val-price,
        .size-board[data-view="sizes"] .size-val.val-sizes {
            opacity: 1;
            transform: translateY(0);
        }

        .size-price {
            font-family: var(--font-mono);
            font-size: 17px;
            font-weight: 600;
            color: var(--green);
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
        }
        .size-qty {
            font-family: var(--font-mono);
            font-size: 17px;
            font-weight: 600;
            color: var(--ink);
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .size-qty .dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--green-bright);
            flex-shrink: 0;
        }
        .size-qty .dot.amber { background: var(--amber); }
        .size-qty .dot.red { background: var(--red); }
        .size-sub {
            font-family: var(--font-mono);
            font-size: 9.5px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--ink-faint);
        }
        .size-none {
            font-family: var(--font-mono);
            font-size: 15px;
            color: var(--line-strong);
        }
        .size-none-sub { color: var(--ink-faint); }
        .size-out-label { color: var(--red); }

        .size-board-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
            font-family: var(--font-mono);
            font-size: 10.5px;
            letter-spacing: 0.6px;
            color: var(--ink-faint);
        }
        .size-board-foot .legend { display: inline-flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .size-board-foot .legend span { display: inline-flex; align-items: center; gap: 6px; }
        .size-board-foot .legend i {
            width: 7px; height: 7px;
            border-radius: 50%;
            display: inline-block;
        }
        .size-board-foot .legend i.g { background: var(--green-bright); }
        .size-board-foot .legend i.a { background: var(--amber); }
        .size-board-foot .legend i.r { background: var(--red); }

        /* Login notice */
        .login-notice {
            border: 1px solid var(--line);
            border-left: 3px solid var(--gold);
            background: var(--card);
            border-radius: 10px;
            padding: 18px 20px;
            font-size: 14px;
            color: var(--ink-soft);
            margin-top: 8px;
        }
        .login-notice a {
            color: var(--green);
            font-weight: 700;
            text-decoration: underline;
        }
        .login-notice a:hover { color: var(--green-bright); }

        /* ===== FOOTER ===== */
        .footer { background: var(--green-deep); margin-top: auto; }
        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 22px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .footer-text {
            font-family: var(--font-mono);
            font-size: 11px;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.65);
        }
        .footer-text a { color: var(--gold-soft); text-decoration: none; font-weight: 600; }
        .footer-text a:hover { text-decoration: underline; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .stock-detail { grid-template-columns: 1fr; gap: 26px; }
            .sd-media { max-height: 380px; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .mobile-toggle { display: block; }
            .nav-mobile-actions { display: flex; margin-left: auto; }
            .nav-profile-mobile { display: inline-flex; }
            .nav-links.active {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                position: absolute;
                top: 70px; left: 0; right: 0;
                background: var(--card);
                padding: 16px 24px;
                border-bottom: 1px solid var(--line);
                box-shadow: 0 10px 40px rgba(24, 36, 32, 0.08);
            }
            .nav-links.active .btn-login { margin-left: 0; text-align: center; justify-content: center; display: flex; }
        }
        @media (max-width: 600px) {
            .sd-title { font-size: 24px; }
            .summary-row { grid-template-columns: 1fr; }
            .size-grid { grid-template-columns: repeat(2, 1fr); }
            .size-board { padding: 16px; }
            .size-board-head { justify-content: center; }
            .view-switch { width: 100%; }
            .view-switch button { flex: 1; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition-duration: 0.01ms !important; }
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
                    <span class="nav-brand-subtitle">CvSU &mdash; Trece Martires</span>
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
        $sizeRun = [];

        if (($stock->item_type ?? null) === 'uniforms') {
            foreach ($sizeOrder as $sizeKey) {
                $price = (float) ($stockPrices[$sizeKey] ?? 0);
                $sizeQty = (int) ($stockSizes[$sizeKey] ?? 0);

                if ($price > 0 || $sizeQty > 0) {
                    $sizeRun[$sizeKey] = ['price' => $price, 'qty' => $sizeQty];
                }
            }
        }

        $sizesTracked = !empty($stockSizes);
        $sizesInStock = count(array_filter($sizeRun, fn ($entry) => $entry['qty'] > 0));
    @endphp

    <div class="page-wrapper">
        <div class="main-content">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="/">Home</a>
                <span>/</span>
                <a href="{{ route('products.index') }}">Products</a>
                <span>/</span>
                <span class="current">{{ $stock->item_name }}</span>
            </div>

            <div class="stock-detail">
                <div class="sd-media">
                    @if($stock->image)
                        <img src="{{ asset('storage/' . $stock->image) }}" alt="{{ $stock->item_name }}">
                    @else
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5 12 3l8.25 4.5L12 12 3.75 7.5Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12 12 16.5 20.25 12"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 16.5 12 21l8.25-4.5"/>
                        </svg>
                    @endif
                    <span class="sd-cat">{{ $stock->item_type ?? 'Stock' }}</span>
                </div>

                <section class="stock-content" aria-label="Stock details">
                    <span class="eyebrow">Campus Registry &mdash; Official Stock</span>
                    <h1 class="sd-title">{{ $stock->item_name }}</h1>

                    <div class="sd-meta">
                        <span class="status-pill {{ $stockStatusClass }}">{{ $stockStatusLabel }}</span>
                        <span><strong>REF</strong> #{{ str_pad($stock->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span>&middot;</span>
                        <span><strong>As of</strong> {{ date('F Y') }}</span>
                    </div>

                    @auth
                    <div class="summary-row {{ ($stock->item_type ?? null) === 'books' && $stock->unit_price > 0 ? '' : 'single' }}">
                        <div class="summary-card">
                            <p class="summary-label">Quantity on hand</p>
                            <div class="summary-value {{ $stockStatusClass }}">{{ number_format($stockQty) }}<small>pcs</small></div>
                        </div>

                        @if(($stock->item_type ?? null) === 'books' && $stock->unit_price > 0)
                            <div class="summary-card">
                                <p class="summary-label">Price</p>
                                <div class="summary-value">&#8369;{{ number_format($stock->unit_price, 2) }}</div>
                            </div>
                        @endif
                    </div>

                    @if(($stock->item_type ?? null) === 'uniforms' && !empty($sizeRun))
                        <div class="size-board" id="sizeBoard" data-view="price">
                            <div class="size-board-head">
                                @if($sizesTracked)
                                    <span class="size-board-title">Size Run &mdash; <strong>{{ $sizesInStock }}</strong> of {{ count($sizeRun) }} sizes in stock</span>
                                @else
                                    <span class="size-board-title">Size Run &mdash; <strong>{{ count($sizeRun) }}</strong> sizes listed</span>
                                @endif
                                <div class="view-switch" id="viewSwitch" data-view="price" role="group" aria-label="Switch between price and size availability">
                                    <button type="button" data-view="price" aria-pressed="true">Price</button>
                                    <button type="button" data-view="sizes" aria-pressed="false">Sizes</button>
                                </div>
                            </div>

                            <div class="size-grid">
                                @foreach($sizeRun as $sizeKey => $entry)
                                    <div class="size-tile {{ $sizesTracked && $entry['qty'] === 0 ? 'is-out' : '' }}">
                                        <span class="size-tag">{{ $sizeKey }}</span>
                                        <div class="size-values">
                                            <div class="size-val val-price">
                                                @if($entry['price'] > 0)
                                                    <span class="size-price">&#8369;{{ number_format($entry['price'], 2) }}</span>
                                                    <span class="size-sub">per piece</span>
                                                @else
                                                    <span class="size-none">&mdash;</span>
                                                    <span class="size-sub size-none-sub">No price set</span>
                                                @endif
                                            </div>
                                            <div class="size-val val-sizes">
                                                @if($entry['qty'] > 0)
                                                    <span class="size-qty">
                                                        <i class="dot {{ $entry['qty'] <= 5 ? 'amber' : '' }}"></i>{{ number_format($entry['qty']) }}
                                                    </span>
                                                    <span class="size-sub">pcs available</span>
                                                @elseif($sizesTracked)
                                                    <span class="size-qty size-out-label"><i class="dot red"></i>0</span>
                                                    <span class="size-sub size-out-label">Out of stock</span>
                                                @else
                                                    <span class="size-none">&mdash;</span>
                                                    <span class="size-sub size-none-sub">Not tracked per size</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="size-board-foot">
                                @if($sizesTracked)
                                    <span class="legend">
                                        <span><i class="g"></i> In stock</span>
                                        <span><i class="a"></i> Running low</span>
                                        <span><i class="r"></i> Out of stock</span>
                                    </span>
                                @else
                                    <span>Per-size availability not yet recorded</span>
                                @endif
                                <span>CvSU Trece Martires</span>
                            </div>
                        </div>
                    @endif
                    @else
                    <div class="login-notice" role="status" aria-live="polite">
                        Log in to view prices and size availability for this item.
                        <a href="{{ route('login') }}">Log in here</a>.
                    </div>
                    @endauth
                </section>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-inner">
            <p class="footer-text">&copy; {{ date('Y') }} <a href="/">CvSU</a> &mdash; Trece Martires City Campus. All rights reserved.</p>
            <p class="footer-text">EBA Information System</p>
        </div>
    </footer>

    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }

        // Price / Sizes view switch
        const sizeBoard = document.getElementById('sizeBoard');
        const viewSwitch = document.getElementById('viewSwitch');

        if (sizeBoard && viewSwitch) {
            viewSwitch.querySelectorAll('button').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const view = btn.dataset.view;
                    sizeBoard.dataset.view = view;
                    viewSwitch.dataset.view = view;
                    viewSwitch.querySelectorAll('button').forEach((b) => {
                        b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
                    });
                });
            });
        }
    </script>
</body>
</html>
