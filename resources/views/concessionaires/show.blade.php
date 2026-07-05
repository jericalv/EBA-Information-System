<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $user->business_name ?: $user->name }} | Concessionaire</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; }
        :root {
            --green: #0A5C2F;
            --green-light: #0D7A3E;
            --green-dark: #064420;
            --gold: #D4A843;
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
            margin: 0;
            background: #F5F5F4;
            color: #1f2937;
        }

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
            justify-content: space-between;
            align-items: center;
            height: 72px;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: var(--gray-900);
        }
        .nav-brand img {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: contain;
        }
        .nav-brand-text {
            display: flex;
            flex-direction: column;
        }
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
            gap: 8px;
            align-items: center;
        }
        .nav-links a {
            color: var(--gray-600);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .nav-links a:hover {
            color: var(--cvsu-green);
            background: rgba(10,92,47,0.06);
        }
        .nav-links a.active {
            color: var(--cvsu-green);
            background: rgba(10,92,47,0.1);
            font-weight: 700;
        }
        .nav-links .btn-login { color: var(--cvsu-green); font-weight: 600; }
        .nav-links .btn-register { background: var(--cvsu-green); color: #fff; font-weight: 600; padding: 8px 20px; }
        .nav-links .btn-register:hover { background: var(--cvsu-green-light); color: #fff; }
        .nav-links .btn-logout { background: transparent; color: #dc2626; font-weight: 600; padding: 8px 20px; border: 2px solid #dc2626; border-radius: 8px; }
        .nav-links .btn-logout:hover { background: #dc2626; color: #fff; }
        .mobile-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; }
        .mobile-toggle span { display: block; width: 24px; height: 2px; background: var(--gray-700); margin: 5px 0; border-radius: 2px; transition: all 0.3s ease; }
        .nav-mobile-actions { display: flex; align-items: center; gap: 10px; }
        .nav-profile-mobile { display: none; }

        .wrap {
            width: 100%;
            max-width: 1200px;
            margin: 96px auto 28px;
            padding: 0 16px;
        }
        .breadcrumb { font-size: 13px; color: #64748b; margin-bottom: 12px; display: flex; gap: 8px; flex-wrap: wrap; }
        .breadcrumb a { color: #64748b; text-decoration: none; }
        .breadcrumb a:hover { color: var(--green); }

        .header-card { background: #fff; border-radius: 18px; box-shadow: 0 6px 24px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .profile-body { padding: 24px; }
        .profile-top { margin-top: 0; }
        .profile-main {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: center;
            flex-wrap: wrap;
        }
        .profile-main-left {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .avatar {
            width: 112px;
            height: 112px;
            border-radius: 24px;
            border: 4px solid #fff;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.2);
            background: #e2e8f0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 800;
            color: #334155;
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-info { padding-bottom: 2px; }
        .title { margin: 0; color: #0f172a; font-size: clamp(30px, 3.6vw, 38px); line-height: 1.1; letter-spacing: -0.4px; }
        .location {
            margin-top: 8px;
            color: #64748b;
            font-size: 14px;
            display: inline-flex;
            gap: 6px;
            align-items: center;
        }
        .rating-badges { margin-top: 12px; display: flex; gap: 10px; flex-wrap: wrap; }
        .rating-badge {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            background: rgba(10,92,47,0.09);
            border: 1px solid rgba(10,92,47,0.22);
            color: var(--green);
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }
        .rating-badge.product { background: rgba(212,168,67,0.14); border-color: rgba(212,168,67,0.5); color: #8a6513; }

        .tabs {
            margin-top: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            gap: 4px;
            overflow-x: auto;
            padding-bottom: 2px;
        }
        .tab-btn {
            border: none;
            background: transparent;
            font: inherit;
            font-weight: 700;
            color: #64748b;
            padding: 12px 14px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: color 0.2s ease, border-color 0.2s ease;
            white-space: nowrap;
        }
        .tab-btn:hover { color: var(--green); }
        .tab-btn.active { color: var(--green); border-bottom-color: var(--green); }
        .tab-panel { display: none; padding: 24px 0 0; }
        .tab-panel.active { display: block; }

        .content-grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 18px; }
        .card { background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .card h3 { margin: 0 0 12px; color: #0f172a; }
        .muted { color: #64748b; line-height: 1.7; font-size: 14px; }
        .rating-big { font-size: 34px; font-weight: 800; color: var(--green); }
        .dist-row { display: grid; grid-template-columns: 60px 1fr 34px; align-items: center; gap: 8px; margin: 6px 0; font-size: 13px; color: #475569; }
        .bar { height: 8px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
        .bar > span { display: block; height: 100%; background: var(--green); }

        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(245px, 1fr)); gap: 16px; }
        .product-card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06); text-decoration: none; color: inherit; border: 1px solid #e2e8f0; transition: all 0.2s; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
        .product-img { height: 150px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #94a3b8; }
        .product-img img { width: 100%; height: 100%; object-fit: cover; }
        .product-info { padding: 12px; }
        .product-name { font-weight: 700; color: #0f172a; }
        .product-price { color: var(--green); font-weight: 800; margin-top: 4px; }
        .product-meta { margin-top: 6px; font-size: 12px; color: #64748b; display: flex; justify-content: space-between; }

        .review-list { display: flex; flex-direction: column; gap: 12px; }
        .review-item { background: #fff; border-radius: 14px; padding: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .review-head { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
        .reviewer { font-weight: 700; color: #0f172a; }
        .review-meta { font-size: 12px; color: #64748b; margin-top: 2px; }
        .stars { color: #f59e0b; letter-spacing: 1px; font-size: 13px; }
        .review-comment { margin-top: 10px; font-size: 14px; color: #334155; line-height: 1.6; }

        .empty { background: #fff; border-radius: 14px; padding: 24px; text-align: center; color: #64748b; border: 1px solid #e2e8f0; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .review-form-card { background: #fff; border-radius: 14px; padding: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; margin-bottom: 14px; }
        .review-form-card h3 { margin: 0 0 12px; }
        .star-input { display: flex; gap: 8px; margin-bottom: 12px; }
        .star-btn { border: none; background: none; cursor: pointer; font-size: 28px; color: #d1d5db; line-height: 1; }
        .star-btn.active { color: #f59e0b; }
        .review-textarea { width: 100%; border: 1px solid #d1d5db; border-radius: 10px; padding: 10px 12px; font: inherit; min-height: 90px; resize: vertical; }
        .review-actions { margin-top: 12px; display: flex; gap: 8px; }
        .btn { border: none; border-radius: 10px; padding: 10px 14px; font: inherit; font-weight: 700; cursor: pointer; text-decoration: none; }
        .btn-green { background: var(--green); color: #fff; }
        .btn-red { background: #dc2626; color: #fff; }
        .btn-outline { background: #fff; border: 1px solid #d1d5db; color: #334155; }
        .notice { background: #fff7ed; color: #9a3412; border: 1px solid #fed7aa; border-radius: 10px; padding: 10px 12px; font-size: 14px; margin-bottom: 12px; }
        @media (max-width: 1024px) {
            .profile-main { align-items: flex-start; }
            .profile-main-left { align-items: center; }
        }
        @media (max-width: 900px) {
            .content-grid { grid-template-columns: 1fr; }
            .avatar { width: 98px; height: 98px; font-size: 26px; }
            .title { font-size: 32px; }
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
        }
        @media (max-width: 640px) {
            .nav-brand-subtitle { display: none; }
            .wrap { margin-top: 88px; padding: 0 16px; }
            .profile-body { padding: 16px; }
            .profile-main-left { width: 100%; }
            .tabs { gap: 0; }
            .tab-btn { padding: 10px 12px; }
            .products-grid { grid-template-columns: 1fr !important; }
        }
    </style>
</head>
<body>
    @include('partials.pending-application-banner')

    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="nav-brand">
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

    <div class="wrap">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('concessionaires.index') }}">Concessionaires</a>
            <span>/</span>
            <span>{{ $user->business_name ?: $user->name }}</span>
        </div>

        <div class="header-card">
            <div class="profile-body">
                <div class="profile-top">
                    <div class="profile-main">
                        <div class="profile-main-left">
                            <div class="avatar">
                                @if ($user->profile_photo)
                                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}">
                                @else
                                    {{ strtoupper(substr($user->business_name ?: $user->name, 0, 2)) }}
                                @endif
                            </div>
                            <div class="profile-info">
                                <h1 class="title">{{ $user->business_name ?: $user->name }}</h1>
                                @if ($user->location)
                                    <div class="location">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                        {{ $user->location }}
                                    </div>
                                @endif
                                <div class="rating-badges">
                                    <div class="rating-badge">
                                        @if ($concessionaireReviewCount > 0)
                                            Store: {{ number_format((float) $concessionaireAvgRating, 1) }} ★ ({{ $concessionaireReviewCount }} reviews)
                                        @else
                                            Store: No ratings yet
                                        @endif
                                    </div>
                                    <div class="rating-badge product">
                                        @if ($productReviewCount > 0)
                                            Products: {{ number_format((float) $productAvgRating, 1) }} ★ ({{ $productReviewCount }} reviews)
                                        @else
                                            Products: No ratings yet
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tabs">
                    <button class="tab-btn active" data-tab="overview">Overview</button>
                    <button class="tab-btn" data-tab="products">Products</button>
                    <button class="tab-btn" data-tab="customer-reviews">Customer Reviews</button>
                    <button class="tab-btn" data-tab="product-reviews">Product Reviews</button>
                </div>
            </div>
        </div>

        <section class="tab-panel active" data-panel="overview">
            <div class="content-grid">
                <div class="card">
                    <h3>About</h3>
                    <p class="muted">{{ $publicDescription }}</p>
                </div>
                <div class="card">
                    <h3>Ratings Snapshot</h3>
                    <div class="rating-big">{{ number_format((float) $concessionaireAvgRating, 1) }}</div>
                    <p class="muted" style="margin:4px 0 10px;">Total store reviews: {{ $concessionaireReviewCount }}</p>
                    @foreach ([5,4,3,2,1] as $star)
                        @php
                            $totalReviews = (int) $concessionaireReviewCount;
                            $count = (int) ($concessionaireRatingDistribution[$star] ?? 0);
                            $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="dist-row">
                            <span>{{ $star }} star</span>
                            <div class="bar"><span style="width: {{ $percent }}%"></span></div>
                            <span>{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tab-panel" data-panel="products">
            @if ($products->count() > 0)
                <div class="products-grid">
                    @foreach ($products as $product)
                        <a href="{{ route('products.show', $product) }}" class="product-card">
                            <div class="product-img">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                @else
                                    No Image
                                @endif
                            </div>
                            <div class="product-info">
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                                <div class="product-meta">
                                    <span>{{ ucfirst($product->category) }}</span>
                                    <span>{{ number_format((float) ($product->reviews_avg_rating ?? 0), 1) }} ★</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div style="margin-top: 16px;">{{ $products->links() }}</div>
            @else
                <div class="empty">No products available yet.</div>
            @endif
        </section>

        <section class="tab-panel" data-panel="customer-reviews">
            @if (! $isConcessionaireActive)
                <div class="notice">This concessionaire is currently inactive.</div>
            @endif

            @auth
                @php
                    $reviewerRole = auth()->user()?->role;
                    $canWriteStoreReview = $reviewerRole === 'student';
                @endphp
                @if ($reviewerRole === 'concessionaire')
                    <div class="notice">Concessionaires are not able to leave reviews.</div>
                @elseif ($canWriteStoreReview && (int) auth()->id() !== (int) $user->id && $isConcessionaireActive)
                    @if ($userConcessionaireReview)
                        <div class="review-form-card">
                            <h3>Your Store Review</h3>
                            <form method="POST" action="{{ route('concessionaires.review.update', $user) }}">
                                @csrf
                                @method('PUT')
                                <div class="star-input" id="editStoreReviewStars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-btn {{ $i <= $userConcessionaireReview->rating ? 'active' : '' }}" data-rating="{{ $i }}">★</button>
                                    @endfor
                                    <input type="hidden" id="editStoreRatingInput" name="rating" value="{{ $userConcessionaireReview->rating }}">
                                </div>
                                <textarea class="review-textarea" name="comment" placeholder="Update your review...">{{ old('comment', $userConcessionaireReview->comment) }}</textarea>
                                @error('comment')
                                    <div style="font-size:12px; color:#dc2626; margin-bottom:8px;">{{ $message }}</div>
                                @enderror
                                <div class="review-actions">
                                    <button class="btn btn-green" type="submit">Update Review</button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('concessionaires.review.delete', $user) }}" style="margin-top:10px;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-red" type="submit" onclick="return confirm('Delete your store review?');">Delete Review</button>
                            </form>
                        </div>
                    @else
                        <div class="review-form-card">
                            <h3>Write a Store Review</h3>
                            <form method="POST" action="{{ route('concessionaires.review.store', $user) }}">
                                @csrf
                                <div class="star-input" id="newStoreReviewStars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-btn" data-rating="{{ $i }}">★</button>
                                    @endfor
                                    <input type="hidden" id="newStoreRatingInput" name="rating" value="">
                                </div>
                                @error('rating')
                                    <div style="font-size:13px; color:#dc2626; margin-bottom:8px;">{{ $message }}</div>
                                @enderror
                                <textarea class="review-textarea" name="comment" placeholder="Share your experience with this store...">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div style="font-size:13px; color:#dc2626; margin-bottom:8px;">{{ $message }}</div>
                                @enderror
                                <div class="review-actions">
                                    <button class="btn btn-green" type="submit">Submit Review</button>
                                </div>
                            </form>
                        </div>
                    @endif
                @elseif ($reviewerRole === 'admin')
                    <div class="notice">Admin accounts cannot leave reviews.</div>
                @elseif ($reviewerRole === 'cashier')
                    <div class="notice">Cashier accounts cannot leave reviews.</div>
                @elseif ((int) auth()->id() === (int) $user->id)
                    <div class="notice">You cannot review your own store.</div>
                @endif
            @else
                <div class="notice"><a href="{{ route('login') }}" style="color:inherit; font-weight:700;">Log in</a> to write a review.</div>
            @endauth

            @if ($concessionaireReviews->count() > 0)
                <div class="review-list">
                    @foreach ($concessionaireReviews as $review)
                        <div class="review-item">
                            <div class="review-head">
                                <div>
                                    <div class="reviewer">{{ $review->user->name }}</div>
                                    <div class="review-meta">{{ $review->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="stars">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</div>
                            </div>
                            <div class="review-comment">{{ $review->comment ?: 'No written comment.' }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty">No customer reviews yet.</div>
            @endif
        </section>

        <section class="tab-panel" data-panel="product-reviews">
            @if ($productReviews->count() > 0)
                <div class="review-list">
                    @foreach ($productReviews as $review)
                        <div class="review-item">
                            <div class="review-head">
                                <div>
                                    <div class="reviewer">{{ $review->user->name }}</div>
                                    <div class="review-meta"><a href="{{ route('products.show', $review->product) }}" style="color:#64748b; text-decoration:underline;">{{ $review->product->name }}</a> • {{ $review->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="stars">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</div>
                            </div>
                            <div class="review-comment">{{ $review->comment ?: 'No written comment.' }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty">No product reviews yet.</div>
            @endif
        </section>
    </div>

    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }

        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');

        tabButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;

                tabButtons.forEach((b) => b.classList.remove('active'));
                tabPanels.forEach((p) => p.classList.remove('active'));

                btn.classList.add('active');
                document.querySelector(`.tab-panel[data-panel="${tab}"]`)?.classList.add('active');
            });
        });

        function setupStars(containerId, inputId) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const stars = container.querySelectorAll('.star-btn');
            const input = document.getElementById(inputId);

            const paint = (rating) => {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            };

            stars.forEach((star) => {
                star.addEventListener('click', () => {
                    const rating = Number(star.dataset.rating || 0);
                    input.value = String(rating);
                    paint(rating);
                });
            });

            paint(Number(input?.value || 0));
        }

        setupStars('newStoreReviewStars', 'newStoreRatingInput');
        setupStars('editStoreReviewStars', 'editStoreRatingInput');
    </script>
</body>
</html>
