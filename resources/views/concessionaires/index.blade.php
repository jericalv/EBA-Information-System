<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Concessionaires | EBA Information System</title>
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
            --white: #FFFFFF;
            --gray-50: #FAFAF8;
            --gray-100: #F5F5F4;
            --gray-200: #E5E7EB;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #F0F4F1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #1a202c;
        }

        /* ── Navbar ── */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }
        .navbar.scrolled { box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
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
        .nav-brand-title { font-size: 16px; font-weight: 800; letter-spacing: -0.2px; color: var(--green); line-height: 1.2; }
        .nav-brand-subtitle { font-size: 11px; font-weight: 500; color: var(--gray-600); letter-spacing: 0.3px; }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-links a {
            text-decoration: none; font-size: 14px; font-weight: 500;
            color: var(--gray-600); padding: 8px 16px; border-radius: 8px; transition: all 0.2s;
        }
        .nav-links a:hover { color: var(--green); background: rgba(10,92,47,0.06); }
        .nav-links a.active { color: var(--green); background: rgba(10,92,47,0.1); font-weight: 700; }
        .nav-links .btn-login { color: var(--green); font-weight: 600; }
        .nav-links .btn-register { background: var(--green); color: var(--white); font-weight: 600; padding: 8px 20px; border-radius: 8px; }
        .nav-links .btn-register:hover { background: var(--green-light); color: var(--white); }
        .nav-links .btn-logout { background: transparent; color: #dc2626; font-weight: 600; padding: 8px 20px; border: 2px solid #dc2626; border-radius: 8px; }
        .nav-links .btn-logout:hover { background: #dc2626; color: var(--white); }
        .mobile-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; }
        .mobile-toggle span { display: block; width: 24px; height: 2px; background: var(--gray-700); margin: 5px 0; border-radius: 2px; transition: all 0.3s ease; }
        .nav-mobile-actions { display: flex; align-items: center; gap: 10px; }
        .nav-profile-mobile { display: none; }

        /* ── Page wrapper ── */
        .page-wrapper { flex: 1; padding-top: 72px; }

        /* ══ Hero header ══ */
        .cc-hero {
            background: linear-gradient(160deg, var(--green-dark) 0%, var(--green) 58%, var(--green-light) 100%);
            color: #fff;
            padding: 56px 24px 60px;
            position: relative;
            overflow: hidden;
        }
        .cc-hero::after {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(1100px 380px at 82% -12%, rgba(212,168,67,0.28), transparent 60%);
            pointer-events: none;
        }
        .cc-hero-inner { max-width: 1280px; margin: 0 auto; position: relative; z-index: 1; }
        .cc-eyebrow {
            display: inline-block;
            font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--gold);
            background: rgba(255,255,255,0.1);
            padding: 6px 14px; border-radius: 999px; margin-bottom: 18px;
            border: 1px solid rgba(255,255,255,0.16);
        }
        .cc-hero-title {
            font-size: clamp(30px, 5vw, 52px); font-weight: 800; letter-spacing: -1px; line-height: 1.05;
            margin-bottom: 14px; max-width: 760px;
        }
        .cc-hero-sub {
            font-size: clamp(15px, 2vw, 18px); color: rgba(255,255,255,0.82);
            max-width: 600px; line-height: 1.6; margin-bottom: 26px;
        }
        .cc-search {
            display: flex; align-items: center;
            background: rgba(255,255,255,0.96);
            border-radius: 14px; padding: 0 20px; max-width: 460px;
            box-shadow: 0 14px 44px rgba(0,0,0,0.2);
        }
        .cc-search input {
            border: none; outline: none; background: transparent;
            padding: 16px 0; font-size: 15px; width: 100%; color: var(--gray-900); font-family: inherit;
        }

        /* ── Hero stats ── */
        .cc-stats { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }
        .cc-stat {
            display: inline-flex; align-items: baseline; gap: 9px;
            background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.16);
            backdrop-filter: blur(8px); padding: 12px 20px; border-radius: 13px;
        }
        .cc-stat-num { font-size: 20px; font-weight: 800; color: #fff; line-height: 1; }
        .cc-stat-label { font-size: 12.5px; color: rgba(255,255,255,0.82); font-weight: 600; }

        /* ══ Main ══ */
        .cc-main { max-width: 1280px; margin: 0 auto; padding: 38px 24px 56px; width: 100%; }
        .cc-section { margin-bottom: 0; }
        .cc-section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; gap: 16px; }
        .cc-section-head h2 {
            font-size: 22px; font-weight: 800; color: var(--gray-900); letter-spacing: -0.4px;
            display: flex; align-items: center; gap: 11px;
        }
        .cc-accent { width: 6px; height: 22px; border-radius: 3px; background: var(--green); display: inline-block; }
        .cc-count { font-size: 14px; font-weight: 600; color: var(--gray-600); white-space: nowrap; }

        /* ══ Grid ══ */
        .cc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 22px; }
        .cc-noresult { text-align: center; padding: 60px 20px; color: var(--gray-600); font-size: 15px; font-weight: 500; }

        /* ══ Card ══ */
        .cc-card {
            display: flex; flex-direction: column;
            background: #fff; border-radius: 18px; overflow: hidden; text-decoration: none;
            border: 1px solid var(--gray-200);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: transform 0.3s cubic-bezier(0.22,1,0.36,1), box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .cc-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 22px 44px rgba(6,68,32,0.18);
            border-color: rgba(10,92,47,0.22);
        }

        .cc-poster {
            position: relative; width: 100%; aspect-ratio: 16 / 10;
            background-size: cover; background-position: center;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
            transition: transform 0.5s cubic-bezier(0.22,1,0.36,1);
        }
        .cc-card:hover .cc-poster { transform: scale(1.06); }
        .cc-monogram { font-size: 56px; font-weight: 800; color: rgba(255,255,255,0.32); letter-spacing: 2px; }
        .cc-poster-shade {
            position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0) 45%, rgba(0,0,0,0.28) 100%);
        }

        .cc-card-body { padding: 15px 17px 17px; display: flex; flex-direction: column; gap: 5px; }
        .cc-card-name {
            font-size: 16px; font-weight: 800; color: var(--gray-900); letter-spacing: -0.3px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            transition: color 0.2s;
        }
        .cc-card:hover .cc-card-name { color: var(--green); }
        .cc-card-loc {
            font-size: 13px; color: var(--gray-600); font-weight: 500;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .cc-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 7px; font-size: 12.5px; color: var(--gray-600); font-weight: 600; margin-top: 4px; }
        .cc-meta-dot { color: var(--gray-200); }
        .cc-rating {
            color: var(--green-dark); font-weight: 800;
            background: rgba(212,168,67,0.18); padding: 1px 9px; border-radius: 999px;
        }

        /* Menu peek (product thumbnails) */
        .cc-peek { display: flex; gap: 6px; margin-top: 12px; }
        .cc-peek-img {
            width: 42px; height: 42px; border-radius: 9px; flex-shrink: 0;
            background-size: cover; background-position: center;
            border: 1px solid var(--gray-200);
        }
        .cc-peek-more {
            width: 42px; height: 42px; border-radius: 9px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: var(--gray-100); color: var(--gray-600); font-size: 12px; font-weight: 700;
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 72px 24px;
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
        }
        .empty-mark {
            display: inline-flex; align-items: center; justify-content: center;
            width: 72px; height: 72px; margin: 0 auto 18px;
            border-radius: 20px; background: rgba(10,92,47,0.08);
            color: var(--green); font-size: 22px; font-weight: 800; letter-spacing: 1px;
        }
        .empty-state h3 { font-size: 20px; font-weight: 800; margin-bottom: 8px; }
        .empty-state p { color: #64748b; font-size: 15px; }

        /* ── Footer ── */
        .footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 28px 24px;
            text-align: center;
            margin-top: auto;
        }
        .footer p { font-size: 13px; color: #64748b; font-weight: 500; }
        .footer a { color: var(--green); text-decoration: none; font-weight: 700; }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .nav-brand-subtitle { display: none; }
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
                top: 72px; left: 0; right: 0;
                background: var(--white);
                padding: 16px 24px;
                border-bottom: 1px solid var(--gray-200);
                box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            }
            .cc-hero { padding: 40px 20px 46px; }
            .cc-main { padding: 28px 16px 44px; }
            .cc-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 14px; }
            .cc-monogram { font-size: 44px; }
            .cc-card-name { font-size: 14px; }
            .cc-peek-img, .cc-peek-more { width: 34px; height: 34px; }
        }
        @media (max-width: 460px) {
            .cc-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .cc-card-body { padding: 11px 12px 13px; }
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

    <div class="page-wrapper">

        @php
            $gradients = [
                'linear-gradient(135deg,#10b981 0%,#059669 100%)',
                'linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%)',
                'linear-gradient(135deg,#f59e0b 0%,#d97706 100%)',
                'linear-gradient(135deg,#a855f7 0%,#7e22ce 100%)',
                'linear-gradient(135deg,#ef4444 0%,#b91c1c 100%)',
                'linear-gradient(135deg,#06b6d4 0%,#0891b2 100%)',
                'linear-gradient(135deg,#ec4899 0%,#be185d 100%)',
            ];
        @endphp

        @if ($concessionaires->isNotEmpty())

            {{-- ═══ HERO HEADER ═══ --}}
            <header class="cc-hero">
                <div class="cc-hero-inner">
                    <h1 class="cc-hero-title">Discover Campus Concessionaires</h1>
                    <p class="cc-hero-sub">
                        Explore the food stalls and stores around campus — browse menus,
                        check ratings, and find your next favorite meal.
                    </p>
                    <label class="cc-search">
                        <input id="ccSearch" type="text" placeholder="Search by name or location…" autocomplete="off">
                    </label>

            </header>

            <main class="cc-main">
                <section class="cc-section" id="allSection">
                    <div class="cc-section-head">
                        <h2><span class="cc-accent"></span> All Concessionaires</h2>
                        <span class="cc-count">{{ $concessionaires->count() }} total</span>
                    </div>
                    <div class="cc-grid" id="ccGrid">
                        @foreach ($concessionaires as $c)
                            @include('concessionaires._card', ['c' => $c, 'gradients' => $gradients])
                        @endforeach
                    </div>
                    <div class="cc-noresult" id="ccNoResult" hidden>No concessionaires match your search.</div>
                </section>
            </main>

        @else
            <div style="display:flex;align-items:center;justify-content:center;min-height:calc(100vh - 72px);padding:40px 24px;">
                <div class="empty-state">
                    <span class="empty-mark" aria-hidden="true">EBA</span>
                    <h3>No Active Concessionaires</h3>
                    <p>Our campus canteens and food stalls are currently wrapping up store details. Please revisit shortly.</p>
                </div>
            </div>
        @endif

    </div>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} <a href="/">CvSU</a> &mdash; Trece Martires City Campus. All rights reserved.</p>
    </footer>

    <script>
        // ── Navbar scroll ──
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }

        // ── Search filter (grid) ──
        const ccSearch = document.getElementById('ccSearch');
        if (ccSearch) {
            ccSearch.addEventListener('input', () => {
                const q = ccSearch.value.trim().toLowerCase();
                let visible = 0;
                document.querySelectorAll('#ccGrid .cc-card').forEach(card => {
                    const hay = (card.dataset.name || '') + ' ' + (card.dataset.location || '');
                    const show = hay.indexOf(q) !== -1;
                    card.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
                const none = document.getElementById('ccNoResult');
                if (none) none.hidden = visible !== 0;
            });
        }
    </script>
</body>
</html>
