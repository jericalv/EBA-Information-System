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
        a:focus-visible, button:focus-visible, input:focus-visible, textarea:focus-visible {
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
        .nav-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--ink); }
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
        .nav-links .btn-login { color: #fff; background: var(--green); padding: 9px 20px; margin-left: 8px; }
        .nav-links .btn-login:hover { background: var(--green-bright); color: #fff; }
        .nav-links .btn-register { background: var(--green); color: #fff; padding: 9px 20px; }
        .nav-links .btn-register:hover { background: var(--green-bright); color: #fff; }
        .nav-links .btn-logout { color: var(--red); font-weight: 600; }
        .nav-links .btn-logout:hover { background: rgba(180, 35, 42, 0.08); color: var(--red); }
        .mobile-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; }
        .mobile-toggle span { display: block; width: 22px; height: 2px; background: var(--ink); margin: 5px 0; border-radius: 2px; }
        .nav-mobile-actions { display: none; align-items: center; gap: 10px; }
        .nav-profile-mobile { display: none; }

        .page-wrapper { flex: 1; padding-top: 70px; }
        .main-content { width: 100%; max-width: 1160px; margin: 0 auto; padding: 26px 24px 56px; }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-family: var(--font-mono);
            font-size: 11.5px;
            letter-spacing: 0.3px;
        }
        .breadcrumb a { color: var(--ink-faint); text-decoration: none; transition: color .2s ease; }
        .breadcrumb a:hover { color: var(--green); }
        .breadcrumb span { color: var(--line-strong); }
        .breadcrumb .current { color: var(--green); font-weight: 600; }

        /* ===== HERO CAROUSEL BANNER ===== */
        .hero {
            position: relative;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            background: var(--green-deep);
            box-shadow: 0 12px 40px rgba(24, 36, 32, 0.12);
        }
        .hero-carousel {
            position: relative;
            height: clamp(300px, 42vw, 460px);
        }
        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity .8s ease;
        }
        .hero-slide.active { opacity: 1; }
        .hero-slide img { width: 100%; height: 100%; object-fit: cover; display: block; }

        /* Fallback banner when no images */
        .hero-fallback {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 18% 22%, rgba(201, 154, 46, 0.28), transparent 42%),
                linear-gradient(135deg, var(--green-deep) 0%, var(--green) 55%, var(--green-bright) 100%);
        }
        .hero-fallback::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.10) 1px, transparent 1px);
            background-size: 18px 18px;
        }
        .hero-fallback-mark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            font-family: var(--font-display);
            font-size: clamp(64px, 12vw, 128px);
            font-weight: 900;
            letter-spacing: -2px;
            color: rgba(255, 255, 255, 0.08);
            user-select: none;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(7, 24, 14, 0.90) 0%, rgba(7, 24, 14, 0.45) 40%, rgba(7, 24, 14, 0.05) 72%);
            pointer-events: none;
        }
        .hero-content {
            position: absolute;
            left: 0; right: 0; bottom: 0;
            padding: clamp(20px, 4vw, 40px);
            z-index: 2;
        }
        .hero-eyebrow {
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gold-soft);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .hero-eyebrow::before { content: ''; width: 22px; height: 1px; background: var(--gold-soft); }
        .hero-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(28px, 4.4vw, 46px);
            line-height: 1.08;
            letter-spacing: -0.6px;
            color: #fff;
            margin-bottom: 12px;
            max-width: 900px;
            text-shadow: 0 2px 18px rgba(0, 0, 0, 0.3);
        }
        .hero-location {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: var(--font-mono);
            font-size: 12px;
            color: rgba(255, 255, 255, 0.82);
            margin-bottom: 14px;
        }
        .hero-location svg { width: 14px; height: 14px; }
        .hero-badges { display: flex; flex-wrap: wrap; gap: 10px; }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #fff;
            font-size: 12.5px;
            font-weight: 700;
            font-family: var(--font-body);
        }
        .hero-badge .hb-label {
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.66);
        }
        .hero-badge .hb-star { color: var(--gold-soft); }
        .hero-badge.is-empty { color: rgba(255, 255, 255, 0.78); font-weight: 600; }
        .hero-status-inactive {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(180, 35, 42, 0.22);
            border: 1px solid rgba(255, 180, 180, 0.4);
            color: #ffdcdc;
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Carousel controls */
        .hero-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px; height: 40px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(7, 24, 14, 0.45);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
            transition: background-color .2s ease, transform .2s ease;
        }
        .hero-nav:hover { background: rgba(7, 24, 14, 0.75); }
        .hero-nav svg { width: 18px; height: 18px; }
        .hero-nav.prev { left: 16px; }
        .hero-nav.next { right: 16px; }
        .hero-dots {
            position: absolute;
            top: 18px; right: 18px;
            display: flex;
            gap: 7px;
            z-index: 3;
        }
        .hero-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            padding: 0;
            transition: background-color .2s ease, width .2s ease;
        }
        .hero-dot.active { background: #fff; width: 22px; border-radius: 999px; }
        .hero-count {
            position: absolute;
            top: 16px; left: 18px;
            z-index: 3;
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 1px;
            color: #fff;
            background: rgba(7, 24, 14, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 999px;
            padding: 4px 11px;
        }

        /* ===== TAB BAR ===== */
        .tabbar {
            position: sticky;
            top: 70px;
            z-index: 50;
            margin: 22px 0 26px;
            background: rgba(237, 242, 232, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 6px;
            display: flex;
            gap: 4px;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .tabbar::-webkit-scrollbar { display: none; }
        .tab-btn {
            flex: 1;
            border: none;
            background: transparent;
            font-family: var(--font-body);
            font-size: 13.5px;
            font-weight: 700;
            color: var(--ink-soft);
            padding: 11px 14px;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: color .2s ease, background-color .2s ease;
        }
        .tab-btn:hover { color: var(--green); }
        .tab-btn.active { color: var(--green); background: var(--card); box-shadow: var(--shadow-card, 0 1px 2px rgba(23,37,28,0.06)); }
        .tab-btn .tab-count {
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 600;
            color: var(--ink-faint);
            background: var(--paper-deep);
            border-radius: 999px;
            padding: 1px 7px;
        }
        .tab-btn.active .tab-count { background: rgba(10, 92, 47, 0.1); color: var(--green); }

        .tab-panel { display: none; animation: fade .35s ease; }
        .tab-panel.active { display: block; }
        @keyframes fade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

        .section-head { margin-bottom: 18px; }
        .section-head h2 {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: var(--ink);
            margin-top: 8px;
        }

        /* ===== CARDS / OVERVIEW ===== */
        .overview-grid { display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 20px; align-items: start; }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 26px;
        }
        .card-title {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 14px;
        }
        .about-text { font-size: 15px; line-height: 1.8; color: var(--ink-soft); }

        .overview-facts { margin-top: 22px; padding-top: 20px; border-top: 1px solid var(--line); display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .fact-label {
            font-family: var(--font-mono);
            font-size: 10px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--ink-faint);
            margin-bottom: 4px;
        }
        .fact-value { font-family: var(--font-mono); font-size: 15px; font-weight: 600; color: var(--ink); }

        .rating-summary { background: var(--card-soft); }
        .rating-big { display: flex; align-items: center; gap: 18px; margin-bottom: 18px; }
        .rating-number { font-family: var(--font-mono); font-size: 44px; font-weight: 600; color: var(--ink); line-height: 1; }
        .rating-stars-lg { display: flex; gap: 3px; margin-bottom: 6px; }
        .star-lg { width: 20px; height: 20px; color: var(--line-strong); }
        .star-lg.filled { color: var(--gold); }
        .rating-text { font-family: var(--font-mono); font-size: 11.5px; color: var(--ink-faint); }
        .rating-bars { display: flex; flex-direction: column; gap: 8px; }
        .rating-bar-row { display: flex; align-items: center; gap: 12px; }
        .rating-bar-label { font-family: var(--font-mono); font-size: 11.5px; color: var(--ink-faint); width: 46px; }
        .rating-bar { flex: 1; height: 6px; background: var(--line); border-radius: 3px; overflow: hidden; }
        .rating-bar-fill { height: 100%; background: var(--gold); border-radius: 3px; }
        .rating-bar-count { font-family: var(--font-mono); font-size: 11.5px; color: var(--ink-faint); width: 22px; text-align: right; }

        /* ===== PRODUCTS (matches products index cards) ===== */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(252px, 1fr));
            gap: 20px;
        }
        .product-card {
            position: relative;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .3s cubic-bezier(0.16, 1, 0.3, 1), border-color .3s ease, box-shadow .3s ease;
        }
        .product-card:hover {
            transform: translateY(-4px);
            border-color: rgba(10, 92, 47, 0.4);
            box-shadow: 0 18px 40px rgba(24, 36, 32, 0.12);
        }
        .p-media {
            position: relative;
            display: block;
            aspect-ratio: 4 / 3;
            background-color: var(--paper-deep);
            background-image: radial-gradient(rgba(24, 36, 32, 0.1) 1px, transparent 1px);
            background-size: 15px 15px;
            border-bottom: 1px solid var(--line);
            overflow: hidden;
        }
        .p-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .product-card:hover .p-media img { transform: scale(1.045); }
        .p-media-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .p-media-placeholder svg { width: 52px; height: 52px; color: var(--line-strong); }
        .p-cat {
            position: absolute;
            top: 12px; left: 12px;
            max-width: calc(100% - 24px);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--line);
            border-radius: 4px;
            padding: 5px 9px;
            color: var(--ink-soft);
        }
        .p-body {
            padding: 15px 16px 13px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .p-name {
            font-family: var(--font-display);
            font-size: 16.5px;
            font-weight: 700;
            letter-spacing: -0.2px;
            line-height: 1.3;
        }
        .p-name a { color: var(--ink); text-decoration: none; transition: color .2s ease; }
        .p-name a:hover { color: var(--green); }
        .p-name a::after { content: ''; position: absolute; inset: 0; }
        .p-foot {
            border-top: 1px solid var(--line);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .p-price {
            font-family: var(--font-mono);
            font-size: 14.5px;
            font-weight: 600;
            color: var(--green);
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        .p-rating { display: flex; align-items: center; gap: 6px; min-width: 0; }
        .stars { display: flex; gap: 1px; flex-shrink: 0; }
        .star { width: 13px; height: 13px; color: var(--line-strong); }
        .star.filled { color: var(--gold); }
        .rating-count {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--ink-faint);
            white-space: nowrap;
        }
        .pagination-wrap { margin-top: 26px; }
        .pagination-wrap nav { display: flex; justify-content: center; }

        /* ===== REVIEWS ===== */
        .reviews-wrap { background: var(--card); border: 1px solid var(--line); border-radius: 14px; padding: 30px; }
        .review-form-card {
            background: var(--card-soft);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 28px;
        }
        .review-form-card h3 { font-family: var(--font-display); font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--ink); }
        .star-rating-input { display: flex; gap: 8px; margin-bottom: 16px; }
        .star-btn { background: none; border: none; padding: 0; cursor: pointer; transition: transform 0.1s; }
        .star-btn:hover { transform: scale(1.1); }
        .star-btn svg { width: 30px; height: 30px; color: var(--line-strong); transition: color 0.2s; }
        .star-btn.active svg, .star-btn:hover svg { color: var(--gold); }
        .review-textarea {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid var(--line-strong);
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 14px;
            color: var(--ink);
            background: var(--card);
            resize: vertical;
            min-height: 92px;
            margin-bottom: 14px;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .review-textarea::placeholder { color: var(--ink-faint); }
        .review-textarea:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12); }
        .field-error { color: var(--red-deep); font-size: 12.5px; font-weight: 600; margin-bottom: 12px; }

        .reviews-list { display: flex; flex-direction: column; }
        .review-card { border-bottom: 1px solid var(--line); padding: 22px 0; }
        .review-card:first-child { padding-top: 0; }
        .review-card:last-child { border-bottom: none; padding-bottom: 0; }
        .review-top { display: flex; align-items: flex-start; gap: 13px; margin-bottom: 10px; }
        .review-avatar {
            width: 38px; height: 38px;
            border-radius: 9px;
            background: var(--green-deep);
            color: var(--gold-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-mono);
            font-weight: 600;
            font-size: 13px;
            flex-shrink: 0;
        }
        .review-meta { flex: 1; min-width: 0; }
        .review-author { font-family: var(--font-body); font-weight: 700; color: var(--ink); margin-bottom: 3px; }
        .review-sub { font-family: var(--font-mono); font-size: 11.5px; color: var(--ink-faint); }
        .review-sub a { color: var(--ink-faint); text-decoration: underline; }
        .review-sub a:hover { color: var(--green); }
        .review-stars { display: flex; gap: 2px; margin-top: 3px; flex-shrink: 0; }
        .review-stars svg { width: 15px; height: 15px; color: var(--line-strong); }
        .review-stars svg.filled { color: var(--gold); }
        .review-comment { font-size: 14.5px; line-height: 1.75; color: var(--ink-soft); }

        /* Buttons */
        .btn {
            padding: 11px 22px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            font-family: var(--font-body);
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid transparent;
        }
        .btn-green { background: var(--green); color: #fff; }
        .btn-green:hover { background: var(--green-bright); }
        .btn-red { background: var(--red); color: #fff; }
        .btn-red:hover { background: var(--red-deep); }
        .btn-outline { background: transparent; border: 1px solid var(--line-strong); color: var(--ink-soft); }
        .btn-outline:hover { border-color: var(--green); color: var(--green); }
        .btn-sm { padding: 8px 16px; font-size: 12.5px; }

        .notice {
            border: 1px solid var(--line-strong);
            border-left: 3px solid var(--gold);
            background: var(--card);
            border-radius: 8px;
            padding: 13px 16px;
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink-soft);
        }
        .notice a { color: var(--green); text-decoration: underline; }
        .flash {
            border: 1px solid var(--line-strong);
            border-left: 3px solid var(--green);
            background: var(--card);
            border-radius: 8px;
            padding: 13px 16px;
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink);
        }
        .flash.flash-error { border-left-color: var(--red); color: var(--red-deep); }

        .empty-state {
            text-align: center;
            padding: 54px 24px;
            background: var(--card);
            border: 1px dashed var(--line-strong);
            border-radius: 14px;
        }
        .empty-state svg { width: 46px; height: 46px; color: var(--line-strong); margin-bottom: 12px; }
        .empty-state h3 { font-family: var(--font-display); font-size: 17px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
        .empty-state p { font-size: 13.5px; color: var(--ink-faint); }

        /* ===== FOOTER ===== */
        .footer { border-top: 1px solid var(--line); background: var(--card-soft); margin-top: auto; }
        .footer-inner {
            max-width: 1160px;
            margin: 0 auto;
            padding: 26px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .footer-text { font-family: var(--font-mono); font-size: 11.5px; color: var(--ink-faint); }
        .footer-text a { color: var(--green); text-decoration: none; }

        @media (max-width: 900px) {
            .overview-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .mobile-toggle { display: block; }
            .nav-mobile-actions { display: flex; }
            .nav-profile-mobile { display: inline-flex; }
            .nav-links.active {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                position: absolute;
                top: 70px; left: 0; right: 0;
                background: var(--card);
                padding: 14px 24px;
                border-bottom: 1px solid var(--line);
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
                gap: 2px;
            }
            .tabbar { top: 70px; }
            .tab-btn { flex: 0 0 auto; }
        }
        @media (max-width: 640px) {
            .nav-brand-subtitle { display: none; }
            .main-content { padding: 20px 16px 44px; }
            .reviews-wrap { padding: 22px; }
            .card { padding: 20px; }
            .overview-facts { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('partials.pending-application-banner')

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="nav-brand">
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
        <div class="main-content">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                <a href="{{ route('concessionaires.index') }}">Concessionaires</a>
                <span>/</span>
                <span class="current">{{ $user->business_name ?: $user->name }}</span>
            </div>

            @if (session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif

            <!-- ===== HERO CAROUSEL ===== -->
            @php $slides = $carouselImages ?? collect(); @endphp
            <div class="hero">
                <div class="hero-carousel" id="heroCarousel">
                    @if ($slides->count() > 0)
                        @foreach ($slides as $i => $src)
                            <div class="hero-slide {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                                <img src="{{ $src }}" alt="{{ $user->business_name ?: $user->name }} banner {{ $i + 1 }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                            </div>
                        @endforeach
                    @else
                        <div class="hero-fallback">
                            <span class="hero-fallback-mark">{{ strtoupper(substr($user->business_name ?: $user->name, 0, 2)) }}</span>
                        </div>
                    @endif

                    <div class="hero-overlay"></div>

                    @if ($slides->count() > 1)
                        <span class="hero-count" id="heroCount">01 / {{ str_pad($slides->count(), 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="hero-dots" id="heroDots">
                            @foreach ($slides as $i => $src)
                                <button class="hero-dot {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}" aria-label="Go to slide {{ $i + 1 }}"></button>
                            @endforeach
                        </div>
                        <button class="hero-nav prev" id="heroPrev" aria-label="Previous slide">
                            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button class="hero-nav next" id="heroNext" aria-label="Next slide">
                            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif

                    <div class="hero-content">
                        <span class="hero-eyebrow">Campus Registry &mdash; Concessionaire</span>
                        <h1 class="hero-title">{{ $user->business_name ?: $user->name }}</h1>
                        @if ($user->location)
                            <div class="hero-location">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $user->location }}
                            </div>
                        @endif
                        <div class="hero-badges">
                            <div class="hero-badge {{ $concessionaireReviewCount > 0 ? '' : 'is-empty' }}">
                                <span class="hb-label">Store</span>
                                @if ($concessionaireReviewCount > 0)
                                    <span class="hb-star">&#9733;</span>{{ number_format((float) $concessionaireAvgRating, 1) }}
                                    <span class="hb-label">({{ $concessionaireReviewCount }})</span>
                                @else
                                    No ratings yet
                                @endif
                            </div>
                            <div class="hero-badge {{ $productReviewCount > 0 ? '' : 'is-empty' }}">
                                <span class="hb-label">Products</span>
                                @if ($productReviewCount > 0)
                                    <span class="hb-star">&#9733;</span>{{ number_format((float) $productAvgRating, 1) }}
                                    <span class="hb-label">({{ $productReviewCount }})</span>
                                @else
                                    No ratings yet
                                @endif
                            </div>
                            @if (! $isConcessionaireActive)
                                <span class="hero-status-inactive">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4M12 16h.01"/></svg>
                                    Currently Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== TAB BAR ===== -->
            <div class="tabbar">
                <button class="tab-btn active" data-tab="overview">Overview</button>
                <button class="tab-btn" data-tab="products">Products <span class="tab-count">{{ $products->total() }}</span></button>
                <button class="tab-btn" data-tab="customer-reviews">Customer Reviews <span class="tab-count">{{ $concessionaireReviewCount }}</span></button>
                <button class="tab-btn" data-tab="product-reviews">Product Reviews <span class="tab-count">{{ $productReviews->count() }}</span></button>
            </div>

            <!-- ===== OVERVIEW ===== -->
            <section class="tab-panel active" data-panel="overview">
                <div class="overview-grid">
                    <div class="card">
                        <div class="card-title">About This Store</div>
                        <p class="about-text">{{ $publicDescription }}</p>

                        <div class="overview-facts">
                            <div>
                                <div class="fact-label">Products Listed</div>
                                <div class="fact-value">{{ $products->total() }}</div>
                            </div>
                            <div>
                                <div class="fact-label">Store Reviews</div>
                                <div class="fact-value">{{ $concessionaireReviewCount }}</div>
                            </div>
                            <div>
                                <div class="fact-label">Status</div>
                                <div class="fact-value">{{ $isConcessionaireActive ? 'Active' : 'Inactive' }}</div>
                            </div>
                            @if ($user->location)
                                <div>
                                    <div class="fact-label">Location</div>
                                    <div class="fact-value">{{ $user->location }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card rating-summary">
                        <div class="card-title">Store Rating</div>
                        <div class="rating-big">
                            <span class="rating-number">{{ number_format((float) $concessionaireAvgRating, 1) }}</span>
                            <div>
                                <div class="rating-stars-lg">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="star-lg {{ $i <= round($concessionaireAvgRating) ? 'filled' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <div class="rating-text">Based on {{ $concessionaireReviewCount }} {{ \Illuminate\Support\Str::plural('review', $concessionaireReviewCount) }}</div>
                            </div>
                        </div>
                        <div class="rating-bars">
                            @foreach ([5, 4, 3, 2, 1] as $star)
                                @php
                                    $count = (int) ($concessionaireRatingDistribution[$star] ?? 0);
                                    $percent = $concessionaireReviewCount > 0 ? ($count / $concessionaireReviewCount) * 100 : 0;
                                @endphp
                                <div class="rating-bar-row">
                                    <span class="rating-bar-label">{{ $star }} star</span>
                                    <div class="rating-bar"><div class="rating-bar-fill" style="width: {{ $percent }}%"></div></div>
                                    <span class="rating-bar-count">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===== PRODUCTS ===== -->
            <section class="tab-panel" data-panel="products">
                <div class="section-head">
                    <span class="eyebrow">Catalog</span>
                    <h2>Products</h2>
                </div>

                @if ($products->count() > 0)
                    <div class="products-grid">
                        @foreach ($products as $product)
                            <article class="product-card">
                                <a href="{{ route('products.show', $product) }}" class="p-media" aria-label="View {{ $product->name }}">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <span class="p-media-placeholder" aria-hidden="true">
                                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                            </svg>
                                        </span>
                                    @endif
                                    @if($product->category)
                                        <span class="p-cat">{{ $product->category }}</span>
                                    @endif
                                </a>
                                <div class="p-body">
                                    <h3 class="p-name">
                                        <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                                    </h3>
                                </div>
                                <div class="p-foot">
                                    <span class="p-price">&#8369;{{ number_format($product->price, 2) }}</span>
                                    <div class="p-rating">
                                        @if(($product->reviews_count ?? 0) > 0)
                                            <div class="stars" aria-hidden="true">
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
                            </article>
                        @endforeach
                    </div>
                    <div class="pagination-wrap">{{ $products->links() }}</div>
                @else
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <h3>No products yet</h3>
                        <p>This store hasn’t listed any products for sale.</p>
                    </div>
                @endif
            </section>

            <!-- ===== CUSTOMER REVIEWS ===== -->
            <section class="tab-panel" data-panel="customer-reviews">
                <div class="reviews-wrap">
                    <div class="section-head">
                        <span class="eyebrow">Feedback Ledger</span>
                        <h2>Customer Reviews</h2>
                    </div>

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
                                        <div class="star-rating-input" id="editStoreReviewStars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button" class="star-btn {{ $i <= $userConcessionaireReview->rating ? 'active' : '' }}" data-rating="{{ $i }}">
                                                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                </button>
                                            @endfor
                                            <input type="hidden" id="editStoreRatingInput" name="rating" value="{{ $userConcessionaireReview->rating }}">
                                        </div>
                                        <textarea class="review-textarea" name="comment" placeholder="Update your review...">{{ old('comment', $userConcessionaireReview->comment) }}</textarea>
                                        @error('comment')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                            <button class="btn btn-green" type="submit">Update Review</button>
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('concessionaires.review.delete', $user) }}" style="margin-top:10px;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline" type="submit" onclick="return confirm('Delete your store review?');" style="color:var(--red); border-color:var(--line-strong);">Delete Review</button>
                                    </form>
                                </div>
                            @else
                                <div class="review-form-card">
                                    <h3>Write a Store Review</h3>
                                    <form method="POST" action="{{ route('concessionaires.review.store', $user) }}">
                                        @csrf
                                        <div class="star-rating-input" id="newStoreReviewStars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button" class="star-btn" data-rating="{{ $i }}">
                                                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                </button>
                                            @endfor
                                            <input type="hidden" id="newStoreRatingInput" name="rating" value="">
                                        </div>
                                        @error('rating')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                        <textarea class="review-textarea" name="comment" placeholder="Share your experience with this store...">{{ old('comment') }}</textarea>
                                        @error('comment')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                        <button class="btn btn-green" type="submit">Submit Review</button>
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
                        <div class="notice"><a href="{{ route('login') }}">Log in</a> to write a review.</div>
                    @endauth

                    @if ($concessionaireReviews->count() > 0)
                        <div class="reviews-list">
                            @foreach ($concessionaireReviews as $review)
                                <div class="review-card">
                                    <div class="review-top">
                                        <div class="review-avatar">{{ strtoupper(substr($review->user->name, 0, 2)) }}</div>
                                        <div class="review-meta">
                                            <div class="review-author">{{ $review->user->name }}</div>
                                            <div class="review-sub">{{ $review->created_at->format('M d, Y') }}</div>
                                        </div>
                                        <div class="review-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="{{ $i <= (int) $review->rating ? 'filled' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="review-comment">{{ $review->comment ?: 'No written comment.' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state" style="border:none; padding:40px 20px;">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l1.3-3.9A7.6 7.6 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <h3>No customer reviews yet</h3>
                            <p>Be the first to review this store.</p>
                        </div>
                    @endif
                </div>
            </section>

            <!-- ===== PRODUCT REVIEWS ===== -->
            <section class="tab-panel" data-panel="product-reviews">
                <div class="reviews-wrap">
                    <div class="section-head">
                        <span class="eyebrow">Product Feedback</span>
                        <h2>Product Reviews</h2>
                    </div>

                    @if ($productReviews->count() > 0)
                        <div class="reviews-list">
                            @foreach ($productReviews as $review)
                                <div class="review-card">
                                    <div class="review-top">
                                        <div class="review-avatar">{{ strtoupper(substr($review->user->name, 0, 2)) }}</div>
                                        <div class="review-meta">
                                            <div class="review-author">{{ $review->user->name }}</div>
                                            <div class="review-sub">
                                                <a href="{{ route('products.show', $review->product) }}">{{ $review->product->name }}</a>
                                                &middot; {{ $review->created_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                        <div class="review-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="{{ $i <= (int) $review->rating ? 'filled' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="review-comment">{{ $review->comment ?: 'No written comment.' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state" style="border:none; padding:40px 20px;">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l1.3-3.9A7.6 7.6 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <h3>No product reviews yet</h3>
                            <p>Reviews left on this store’s products will appear here.</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="footer-inner">
            <p class="footer-text">&copy; {{ date('Y') }} <a href="{{ route('home') }}">CvSU</a> &mdash; Trece Martires City Campus. All rights reserved.</p>
            <p class="footer-text">EBA Information System</p>
        </div>
    </footer>

    <script>
        // Navbar shadow on scroll
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }

        // Tabs (with hash support)
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');
        function activateTab(tab) {
            const btn = document.querySelector(`.tab-btn[data-tab="${tab}"]`);
            const panel = document.querySelector(`.tab-panel[data-panel="${tab}"]`);
            if (!btn || !panel) return;
            tabButtons.forEach((b) => b.classList.remove('active'));
            tabPanels.forEach((p) => p.classList.remove('active'));
            btn.classList.add('active');
            panel.classList.add('active');
        }
        tabButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                activateTab(tab);
                history.replaceState(null, '', '#' + tab);
            });
        });
        if (window.location.hash) {
            activateTab(window.location.hash.replace('#', ''));
        }

        // Hero carousel
        (function () {
            const carousel = document.getElementById('heroCarousel');
            if (!carousel) return;
            const slides = carousel.querySelectorAll('.hero-slide');
            if (slides.length < 2) return;

            const dots = carousel.querySelectorAll('.hero-dot');
            const counter = document.getElementById('heroCount');
            let current = 0;
            let timer = null;
            const total = slides.length;
            const pad = (n) => String(n).padStart(2, '0');

            function go(index) {
                current = (index + total) % total;
                slides.forEach((s, i) => s.classList.toggle('active', i === current));
                dots.forEach((d, i) => d.classList.toggle('active', i === current));
                if (counter) counter.textContent = pad(current + 1) + ' / ' + pad(total);
            }
            function next() { go(current + 1); }
            function prev() { go(current - 1); }
            function start() { stop(); timer = setInterval(next, 5000); }
            function stop() { if (timer) clearInterval(timer); }

            document.getElementById('heroNext')?.addEventListener('click', () => { next(); start(); });
            document.getElementById('heroPrev')?.addEventListener('click', () => { prev(); start(); });
            dots.forEach((dot) => dot.addEventListener('click', () => { go(Number(dot.dataset.index)); start(); }));
            carousel.addEventListener('mouseenter', stop);
            carousel.addEventListener('mouseleave', start);
            start();
        })();

        // Store review star inputs
        function setupStars(containerId, inputId) {
            const container = document.getElementById(containerId);
            if (!container) return;
            const stars = container.querySelectorAll('.star-btn');
            const input = document.getElementById(inputId);
            const paint = (rating) => stars.forEach((star, index) => star.classList.toggle('active', index < rating));
            stars.forEach((star) => {
                star.addEventListener('click', () => {
                    const rating = Number(star.dataset.rating || 0);
                    if (input) input.value = String(rating);
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
