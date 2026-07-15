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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900|ibm-plex-mono:500,600&display=swap" rel="stylesheet" />
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
            --font: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            --font-mono: 'IBM Plex Mono', 'SFMono-Regular', Consolas, monospace;
        }

        body {
            font-family: var(--font);
            background: var(--paper);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        img { max-width: 100%; }
        a:focus-visible, button:focus-visible, input:focus-visible, textarea:focus-visible {
            outline: 2px solid var(--green);
            outline-offset: 2px;
            border-radius: 2px;
        }

        .eyebrow {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: var(--green);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .eyebrow::before {
            content: '';
            width: 22px;
            height: 2px;
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
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.2px;
            color: var(--green);
            line-height: 1.2;
        }
        .nav-brand-subtitle {
            font-size: 9.5px;
            font-weight: 600;
            color: var(--ink-faint);
            letter-spacing: 1.4px;
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

        .page-wrapper { flex: 1; padding-top: 70px; position: relative; overflow: clip; }
        .main-content { width: 100%; max-width: 1160px; margin: 0 auto; padding: 26px 24px 56px; position: relative; z-index: 1; }

        /* ===== Flow-line artwork (decorative) ===== */
        .flow-img {
            position: absolute;
            pointer-events: none;
            user-select: none;
            z-index: 0;
        }
        .page-flow-tr {
            top: 30px;
            right: -230px;
            width: 840px;
            opacity: 0.15;
            transform: rotate(14deg);
        }
        .page-flow-bl {
            bottom: -100px;
            left: -240px;
            width: 760px;
            opacity: 0.13;
            transform: rotate(-10deg);
        }
        .flow-plaque {
            top: -120px;
            right: -140px;
            width: 580px;
            opacity: 0.28;
            transform: rotate(-12deg);
        }
        @media (max-width: 768px) {
            .page-flow-bl { display: none; }
            .page-flow-tr { width: 520px; right: -190px; }
            .flow-plaque { width: 400px; top: -90px; right: -120px; }
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 18px;
            font-size: 12.5px;
            font-weight: 500;
        }
        .breadcrumb a { color: var(--ink-faint); text-decoration: none; transition: color .2s ease; }
        .breadcrumb a:hover { color: var(--green); }
        .breadcrumb span { color: var(--line-strong); }
        .breadcrumb .current { color: var(--green); font-weight: 600; }

        /* ===== BANNER ===== */
        .banner {
            position: relative;
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
            background: var(--green-deep);
            box-shadow: 0 14px 36px rgba(24, 36, 32, 0.10);
        }
        .banner-carousel {
            position: relative;
            height: clamp(250px, 33vw, 380px);
        }
        .banner-carousel.is-fallback { height: clamp(190px, 24vw, 260px); }
        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity .8s ease;
        }
        .hero-slide.active { opacity: 1; }
        .hero-slide img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .banner-scrim {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(7, 24, 14, 0.28) 0%, rgba(7, 24, 14, 0) 38%);
            pointer-events: none;
        }

        /* Fallback banner when no images */
        .banner-fallback {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 84% 10%, rgba(201, 154, 46, 0.22), transparent 44%),
                radial-gradient(circle at 12% 90%, rgba(13, 122, 62, 0.5), transparent 55%),
                linear-gradient(135deg, var(--green-deep) 0%, #0A4526 60%, var(--green) 100%);
        }
        .banner-fallback::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.09) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .banner-mark {
            position: absolute;
            top: 46%; left: 50%;
            transform: translate(-50%, -50%);
            font-size: clamp(72px, 13vw, 130px);
            font-weight: 900;
            letter-spacing: 6px;
            color: rgba(255, 255, 255, 0.07);
            user-select: none;
        }

        /* Carousel controls */
        .hero-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 38px; height: 38px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: rgba(7, 24, 14, 0.42);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
            transition: background-color .2s ease;
        }
        .hero-nav:hover { background: rgba(7, 24, 14, 0.72); }
        .hero-nav svg { width: 17px; height: 17px; }
        .hero-nav.prev { left: 16px; }
        .hero-nav.next { right: 16px; }
        .hero-dots {
            position: absolute;
            top: 16px; right: 18px;
            display: flex;
            align-items: center;
            gap: 7px;
            z-index: 3;
            background: rgba(7, 24, 14, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 999px;
            padding: 7px 11px;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
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
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            font-variant-numeric: tabular-nums;
            color: #fff;
            background: rgba(7, 24, 14, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 999px;
            padding: 4px 11px;
        }

        /* ===== REGISTRY PLAQUE ===== */
        .plaque {
            position: relative;
            z-index: 5;
            margin: -58px 28px 0;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 18px 44px rgba(24, 36, 32, 0.10);
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            flex-wrap: wrap;
            overflow: hidden;
        }
        .plaque-id, .plaque-stats { position: relative; z-index: 1; }
        .plaque-id { min-width: 0; }
        .store-name {
            font-size: clamp(26px, 3.4vw, 37px);
            font-weight: 800;
            letter-spacing: -0.025em;
            line-height: 1.12;
            color: var(--ink);
            margin: 8px 0 10px;
            overflow-wrap: anywhere;
        }
        .store-meta {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--ink-soft);
        }
        .meta-item svg { width: 15px; height: 15px; color: var(--ink-faint); flex-shrink: 0; }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            border: 1px solid;
        }
        .status-pill .status-dot { width: 7px; height: 7px; border-radius: 50%; }
        .status-pill.is-active {
            color: var(--green);
            background: rgba(10, 92, 47, 0.06);
            border-color: rgba(10, 92, 47, 0.2);
        }
        .status-pill.is-active .status-dot { background: var(--green-bright); }
        .status-pill.is-inactive {
            color: var(--red-deep);
            background: rgba(180, 35, 42, 0.06);
            border-color: rgba(180, 35, 42, 0.22);
        }
        .status-pill.is-inactive .status-dot { background: var(--red); }

        .plaque-stats { display: flex; gap: 12px; flex-wrap: wrap; }
        .stat-chip {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--card-soft);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px 18px 10px 12px;
        }
        .stat-ic {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: rgba(201, 154, 46, 0.15);
            color: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stat-ic svg { width: 17px; height: 17px; }
        .stat-chip.is-empty .stat-ic { background: var(--paper-deep); color: var(--line-strong); }
        .stat-txt { display: flex; flex-direction: column; line-height: 1.3; }
        .stat-val {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.01em;
            font-variant-numeric: tabular-nums;
            color: var(--ink);
        }
        .stat-val em { font-style: normal; font-size: 12px; font-weight: 600; color: var(--ink-faint); }
        .stat-chip.is-empty .stat-val { color: var(--ink-faint); font-weight: 600; font-size: 14px; letter-spacing: 0; }
        .stat-cap { font-size: 11px; font-weight: 600; color: var(--ink-faint); }

        /* ===== TAB BAR ===== */
        .tabbar {
            position: sticky;
            top: 70px;
            z-index: 50;
            margin: 30px 0 28px;
            display: flex;
            gap: 30px;
            border-bottom: 1px solid var(--line);
            background: rgba(237, 242, 232, 0.92);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            overflow-x: auto;
            scrollbar-width: none;
        }
        .tabbar::-webkit-scrollbar { display: none; }
        .tab-btn {
            border: none;
            background: transparent;
            font-family: var(--font);
            font-size: 14px;
            font-weight: 600;
            color: var(--ink-soft);
            padding: 15px 2px;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            transition: color .2s ease;
        }
        .tab-btn:hover { color: var(--green); }
        .tab-btn.active { color: var(--green); font-weight: 700; }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            left: 0; right: 0; bottom: -1px;
            height: 2px;
            background: var(--green);
            border-radius: 2px 2px 0 0;
        }
        .tab-btn .tab-count {
            font-size: 11px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: var(--ink-faint);
            background: var(--paper-deep);
            border-radius: 999px;
            padding: 1px 8px;
        }
        .tab-btn.active .tab-count { background: rgba(10, 92, 47, 0.1); color: var(--green); }

        .tab-panel { display: none; animation: fade .35s ease; }
        .tab-panel.active { display: block; }
        @keyframes fade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

        .section-head { margin-bottom: 20px; }
        .section-head h2 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--ink);
            margin-top: 8px;
        }

        /* ===== CARDS / OVERVIEW ===== */
        .overview-grid { display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 20px; align-items: start; }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 28px;
        }
        .card-title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: var(--ink);
            margin-bottom: 14px;
        }
        .about-text { font-size: 15px; line-height: 1.8; color: var(--ink-soft); }

        .overview-facts { margin-top: 24px; padding-top: 22px; border-top: 1px solid var(--line); display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .fact-label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.3px;
            text-transform: uppercase;
            color: var(--ink-faint);
            margin-bottom: 5px;
        }
        .fact-value {
            font-size: 15.5px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .fact-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .fact-dot.dot-green { background: var(--green-bright); }
        .fact-dot.dot-red { background: var(--red); }

        .rating-summary { background: var(--card-soft); }
        .rating-big { display: flex; align-items: center; gap: 18px; margin-bottom: 18px; }
        .rating-number {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: -0.03em;
            font-variant-numeric: tabular-nums;
            color: var(--ink);
            line-height: 1;
        }
        .rating-stars-lg { display: flex; gap: 3px; margin-bottom: 6px; }
        .star-lg { width: 20px; height: 20px; color: var(--line-strong); }
        .star-lg.filled { color: var(--gold); }
        .rating-text { font-size: 12.5px; font-weight: 500; color: var(--ink-faint); }
        .rating-bars { display: flex; flex-direction: column; gap: 9px; }
        .rating-bar-row { display: flex; align-items: center; gap: 12px; }
        .rating-bar-label { font-size: 12px; font-weight: 600; color: var(--ink-faint); width: 44px; }
        .rating-bar { flex: 1; height: 6px; background: var(--line); border-radius: 3px; overflow: hidden; }
        .rating-bar-fill { height: 100%; background: var(--gold); border-radius: 3px; }
        .rating-bar-count { font-size: 12px; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink-faint); width: 22px; text-align: right; }

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
            border-radius: 14px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .3s cubic-bezier(0.16, 1, 0.3, 1), border-color .3s ease, box-shadow .3s ease;
        }
        .product-card:hover {
            transform: translateY(-3px);
            border-color: rgba(10, 92, 47, 0.35);
            box-shadow: 0 16px 36px rgba(24, 36, 32, 0.10);
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
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--line);
            border-radius: 5px;
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
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.01em;
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
            font-size: 11.5px;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
            color: var(--ink-faint);
            white-space: nowrap;
        }
        .pagination-wrap { margin-top: 26px; }
        .pagination-wrap nav { display: flex; justify-content: center; }

        /* ===== REVIEWS ===== */
        .reviews-wrap { background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 30px; }
        .review-form-card {
            background: var(--card-soft);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 28px;
        }
        .review-form-card h3 { font-size: 15.5px; font-weight: 700; letter-spacing: -0.01em; margin-bottom: 16px; color: var(--ink); }
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
            font-family: var(--font);
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
            border-radius: 10px;
            background: var(--green-deep);
            color: var(--gold-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12.5px;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }
        .review-meta { flex: 1; min-width: 0; }
        .review-author { font-size: 14.5px; font-weight: 700; color: var(--ink); margin-bottom: 2px; }
        .review-sub { font-size: 12px; font-weight: 500; color: var(--ink-faint); }
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
            font-family: var(--font);
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
            border-radius: 16px;
        }
        .empty-state svg { width: 46px; height: 46px; color: var(--line-strong); margin-bottom: 12px; }
        .empty-state h3 { font-size: 17px; font-weight: 700; letter-spacing: -0.01em; color: var(--ink); margin-bottom: 4px; }
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
        .footer-text { font-size: 12px; font-weight: 500; color: var(--ink-faint); }
        .footer-text a { color: var(--green); text-decoration: none; }

        /* Entrance */
        .banner, .plaque { animation: rise .5s ease both; }
        .plaque { animation-delay: .06s; }
        @keyframes rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

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
            .plaque { margin: -48px 14px 0; padding: 20px; gap: 16px; }
            .tabbar { gap: 24px; margin-top: 26px; }
        }
        @media (max-width: 640px) {
            .nav-brand-subtitle { display: none; }
            .main-content { padding: 20px 16px 44px; }
            .reviews-wrap { padding: 22px; }
            .card { padding: 22px; }
            .overview-facts { grid-template-columns: 1fr; }
            .plaque-stats { width: 100%; }
            .stat-chip { flex: 1; min-width: 130px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
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
        <img class="flow-img page-flow-tr" src="{{ asset('images/flowlines1-green.png') }}" alt="" aria-hidden="true">
        <img class="flow-img page-flow-bl" src="{{ asset('images/flowlines3-green.png') }}" alt="" aria-hidden="true" loading="lazy">
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

            <!-- ===== BANNER CAROUSEL ===== -->
            @php $slides = $carouselImages ?? collect(); @endphp
            <div class="banner">
                <div class="banner-carousel {{ $slides->count() === 0 ? 'is-fallback' : '' }}" id="heroCarousel">
                    @if ($slides->count() > 0)
                        @foreach ($slides as $i => $src)
                            <div class="hero-slide {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                                <img src="{{ $src }}" alt="{{ $user->business_name ?: $user->name }} banner {{ $i + 1 }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                            </div>
                        @endforeach
                        <div class="banner-scrim" aria-hidden="true"></div>
                    @else
                        <div class="banner-fallback" aria-hidden="true">
                            <span class="banner-mark">{{ strtoupper(substr($user->business_name ?: $user->name, 0, 2)) }}</span>
                        </div>
                    @endif

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
                </div>
            </div>

            <!-- ===== REGISTRY PLAQUE ===== -->
            <header class="plaque">
                <img class="flow-img flow-plaque" src="{{ asset('images/flowlines3-gold.png') }}" alt="" aria-hidden="true">
                <div class="plaque-id">
                    <span class="eyebrow">Campus Registry &mdash; Concessionaire</span>
                    <h1 class="store-name">{{ $user->business_name ?: $user->name }}</h1>
                    <div class="store-meta">
                        @if ($user->location)
                            <span class="meta-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $user->location }}
                            </span>
                        @endif
                        @if ($isConcessionaireActive)
                            <span class="status-pill is-active"><span class="status-dot"></span>Active</span>
                        @else
                            <span class="status-pill is-inactive"><span class="status-dot"></span>Currently inactive</span>
                        @endif
                    </div>
                </div>
                <div class="plaque-stats">
                    <div class="stat-chip {{ $concessionaireReviewCount > 0 ? '' : 'is-empty' }}">
                        <span class="stat-ic" aria-hidden="true">
                            <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </span>
                        <span class="stat-txt">
                            @if ($concessionaireReviewCount > 0)
                                <span class="stat-val">{{ number_format((float) $concessionaireAvgRating, 1) }} <em>({{ $concessionaireReviewCount }})</em></span>
                            @else
                                <span class="stat-val">No ratings yet</span>
                            @endif
                            <span class="stat-cap">Store rating</span>
                        </span>
                    </div>
                    <div class="stat-chip {{ $productReviewCount > 0 ? '' : 'is-empty' }}">
                        <span class="stat-ic" aria-hidden="true">
                            <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </span>
                        <span class="stat-txt">
                            @if ($productReviewCount > 0)
                                <span class="stat-val">{{ number_format((float) $productAvgRating, 1) }} <em>({{ $productReviewCount }})</em></span>
                            @else
                                <span class="stat-val">No ratings yet</span>
                            @endif
                            <span class="stat-cap">Product rating</span>
                        </span>
                    </div>
                </div>
            </header>

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
                                <div class="fact-value">
                                    <span class="fact-dot {{ $isConcessionaireActive ? 'dot-green' : 'dot-red' }}"></span>
                                    {{ $isConcessionaireActive ? 'Active' : 'Inactive' }}
                                </div>
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
                            <span class="rating-number">{{ $concessionaireReviewCount > 0 ? number_format((float) $concessionaireAvgRating, 1) : '—' }}</span>
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
