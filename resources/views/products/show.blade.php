<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }} | EBA Information System</title>
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
        .page-wrapper {
            flex: 1;
            padding-top: 70px;
        }

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

        /* ===== FLASH ===== */
        .flash {
            border: 1px solid var(--line-strong);
            border-left: 3px solid var(--green);
            background: var(--card);
            border-radius: 8px;
            padding: 13px 16px;
            margin-bottom: 22px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink);
        }
        .flash.flash-error { border-left-color: var(--red); color: var(--red-deep); }
        .flash.flash-info { border-left-color: var(--gold); color: var(--ink-soft); }
        .flash a { color: inherit; text-decoration: underline; }

        /* ===== PRODUCT DETAIL ===== */
        .product-detail {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 44px;
            margin-bottom: 52px;
        }
        .pd-media {
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
        .pd-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pd-media svg {
            width: 88px;
            height: 88px;
            color: var(--line-strong);
        }
        .pd-cat {
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
        }

        .pd-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(26px, 3vw, 34px);
            line-height: 1.14;
            letter-spacing: -0.5px;
            color: var(--ink);
            margin: 12px 0 18px;
        }
        .pd-vendor {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }
        .pd-vendor-avatar {
            width: 42px;
            height: 42px;
            border-radius: 9px;
            background: var(--green-deep);
            color: var(--gold-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-mono);
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .pd-vendor-details { flex: 1; min-width: 0; }
        .pd-vendor-name {
            font-family: var(--font-body);
            font-weight: 700;
            color: var(--ink);
            text-decoration: none;
        }
        .pd-vendor-name:hover { color: var(--green); text-decoration: underline; }
        .pd-vendor-label {
            font-family: var(--font-mono);
            font-size: 10px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--ink-faint);
        }
        .pd-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 14px;
            font-family: var(--font-mono);
            font-size: 11.5px;
            color: var(--ink-faint);
            margin-bottom: 22px;
            padding-bottom: 22px;
            border-bottom: 1px solid var(--line);
        }
        .pd-meta strong { color: var(--ink-soft); font-weight: 600; }

        .pd-price {
            font-family: var(--font-mono);
            font-size: 30px;
            font-weight: 600;
            color: var(--green);
            margin-bottom: 20px;
            font-variant-numeric: tabular-nums;
        }
        .pd-description {
            font-size: 15px;
            line-height: 1.75;
            color: var(--ink-soft);
            margin-bottom: 26px;
        }

        /* Rating summary */
        .rating-summary {
            background: var(--card-soft);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 22px;
        }
        .rating-big {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 18px;
        }
        .rating-number {
            font-family: var(--font-mono);
            font-size: 40px;
            font-weight: 600;
            color: var(--ink);
            line-height: 1;
        }
        .rating-stars-lg { display: flex; gap: 3px; margin-bottom: 6px; }
        .star-lg { width: 22px; height: 22px; color: var(--line-strong); }
        .star-lg.filled { color: var(--gold); }
        .rating-text {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--ink-faint);
        }
        .rating-bars {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .rating-bar-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .rating-bar-label {
            font-family: var(--font-mono);
            font-size: 11.5px;
            color: var(--ink-faint);
            width: 46px;
        }
        .rating-bar {
            flex: 1;
            height: 6px;
            background: var(--line);
            border-radius: 3px;
            overflow: hidden;
        }
        .rating-bar-fill {
            height: 100%;
            background: var(--gold);
            border-radius: 3px;
        }
        .rating-bar-count {
            font-family: var(--font-mono);
            font-size: 11.5px;
            color: var(--ink-faint);
            width: 22px;
            text-align: right;
        }

        /* ===== BUTTONS ===== */
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
        .btn-green {
            background: var(--green);
            color: #fff;
        }
        .btn-green:hover { background: var(--green-bright); }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--line-strong);
            color: var(--ink-soft);
        }
        .btn-outline:hover { border-color: var(--green); color: var(--green); }
        .btn-outline.btn-danger:hover { border-color: var(--red); color: var(--red); }
        .btn-red { background: var(--red); color: #fff; }
        .btn-red:hover { background: var(--red-deep); }
        .btn-sm { padding: 8px 16px; font-size: 12.5px; }

        /* ===== REVIEWS SECTION ===== */
        .reviews-section {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 32px;
        }
        .reviews-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--line);
        }
        .reviews-header h2 {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: var(--ink);
            margin-top: 10px;
        }
        .reviews-count {
            font-family: var(--font-mono);
            font-size: 11.5px;
            color: var(--ink-faint);
        }
        .reviews-count strong { color: var(--green); font-weight: 600; }

        /* Review Form */
        .review-form-card {
            background: var(--card-soft);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }
        .review-form-card h3 {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--ink);
        }
        .star-rating-input {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }
        .star-btn {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            transition: transform 0.1s;
        }
        .star-btn:hover { transform: scale(1.1); }
        .star-btn svg {
            width: 32px;
            height: 32px;
            color: var(--line-strong);
            transition: color 0.2s;
        }
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
            min-height: 96px;
            margin-bottom: 16px;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .review-textarea::placeholder { color: var(--ink-faint); }
        .review-textarea:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
        }
        .field-error {
            color: var(--red-deep);
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        /* Review List */
        .reviews-list {
            display: flex;
            flex-direction: column;
        }
        .review-card {
            border-bottom: 1px solid var(--line);
            padding: 22px 0;
        }
        .review-card:first-child { padding-top: 0; }
        .review-card:last-child { border-bottom: none; padding-bottom: 0; }
        .review-header {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            margin-bottom: 12px;
        }
        .review-avatar {
            width: 38px;
            height: 38px;
            border-radius: 8px;
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
        .review-author {
            font-family: var(--font-body);
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 3px;
        }
        .review-date {
            font-family: var(--font-mono);
            font-size: 11.5px;
            color: var(--ink-faint);
        }
        .review-stars { display: flex; gap: 2px; margin-top: 2px; }
        .review-stars svg { width: 15px; height: 15px; color: var(--line-strong); }
        .review-stars svg.filled { color: var(--gold); }
        .review-comment {
            font-size: 14.5px;
            line-height: 1.75;
            color: var(--ink-soft);
        }
        .your-review-badge {
            display: inline-flex;
            padding: 3px 9px;
            border-radius: 5px;
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            background: rgba(10, 92, 47, 0.1);
            color: var(--green);
            margin-left: 8px;
        }

        /* Edit Review Button */
        .btn-edit-review {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 20px;
            background: var(--green);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            font-family: var(--font-body);
            cursor: pointer;
            transition: background-color .2s ease;
        }
        .btn-edit-review:hover { background: var(--green-bright); }
        .btn-edit-review svg { width: 15px; height: 15px; }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(11, 20, 15, 0.55);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .modal-overlay.active { opacity: 1; visibility: visible; }

        .review-modal {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            width: 90%;
            max-width: 480px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(11, 20, 15, 0.3);
            transform: scale(0.94) translateY(20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.2, 0.64, 1);
        }
        .modal-overlay.active .review-modal { transform: scale(1) translateY(0); opacity: 1; }

        .review-modal-header {
            padding: 22px 26px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .review-modal-title {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 800;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .review-modal-title svg { width: 21px; height: 21px; color: var(--gold); }
        .btn-close-modal {
            width: 30px; height: 30px;
            border-radius: 7px;
            border: none;
            background: var(--card-soft);
            color: var(--ink-faint);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .btn-close-modal:hover { background: var(--paper-deep); color: var(--ink); }
        .btn-close-modal svg { width: 18px; height: 18px; }

        .review-modal-body { padding: 22px 26px; overflow-y: auto; }
        .review-modal-body .star-rating-input { justify-content: center; margin-bottom: 18px; }
        .review-modal-body .star-btn svg { width: 36px; height: 36px; }
        .review-modal-body .review-textarea { margin-bottom: 6px; }
        .review-modal-footer {
            padding: 18px 26px;
            border-top: 1px solid var(--line);
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .review-modal-footer .btn { flex: 1; }

        .modal-overlay.closing { opacity: 0; }
        .modal-overlay.closing .review-modal { transform: scale(0.94) translateY(20px); opacity: 0; }

        /* Success / confirm modal */
        .success-modal {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            width: 90%;
            max-width: 380px;
            padding: 30px 26px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(11, 20, 15, 0.3);
            transform: scale(0.94) translateY(16px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.2, 0.64, 1);
        }
        .modal-overlay.active .success-modal { transform: scale(1) translateY(0); opacity: 1; }
        .success-modal-icon {
            width: 60px; height: 60px;
            background: rgba(10, 92, 47, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            color: var(--green);
        }
        .success-modal-icon svg { width: 30px; height: 30px; }
        .success-modal-title {
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 800;
            color: var(--ink);
            margin-bottom: 8px;
        }
        .success-modal-message {
            font-size: 14px;
            color: var(--ink-soft);
            line-height: 1.6;
            margin-bottom: 22px;
        }
        .btn-modal-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            font-family: var(--font-body);
            color: #fff;
            background: var(--green);
            border: none;
            cursor: pointer;
            transition: background-color .2s ease;
        }
        .btn-modal-close:hover { background: var(--green-bright); }

        .modal-overlay.closing .success-modal { transform: scale(0.94) translateY(16px); opacity: 0; }

        /* Your Review Display Card */
        .your-review-display {
            background: var(--card-soft);
            border: 1px solid var(--line);
            border-left: 3px solid var(--green);
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 30px;
        }
        .your-review-display h3 {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 14px;
            color: var(--ink);
        }
        .your-review-rating { display: flex; gap: 3px; margin-bottom: 14px; }
        .your-review-rating svg { width: 20px; height: 20px; color: var(--line-strong); }
        .your-review-rating svg.filled { color: var(--gold); }
        .your-review-comment {
            background: var(--card);
            border: 1px solid var(--line);
            padding: 15px;
            border-radius: 8px;
            font-size: 14.5px;
            line-height: 1.7;
            color: var(--ink-soft);
            margin-bottom: 16px;
        }

        /* Empty Reviews */
        .no-reviews {
            text-align: center;
            padding: 56px 24px;
            border: 1px dashed var(--line-strong);
            border-radius: 12px;
        }
        .no-reviews svg { width: 44px; height: 44px; color: var(--line-strong); margin-bottom: 12px; }
        .no-reviews h3 {
            font-family: var(--font-display);
            font-size: 17px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
        }
        .no-reviews p { color: var(--ink-faint); font-size: 13.5px; }

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
            .product-detail { grid-template-columns: 1fr; gap: 26px; }
            .pd-media { max-height: 380px; }
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
            .pd-title { font-size: 24px; }
            .pd-price { font-size: 26px; }
            .rating-number { font-size: 32px; }
            .reviews-section { padding: 22px; }
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

    <div class="page-wrapper">
        <div class="main-content">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="/">Home</a>
                <span>/</span>
                <a href="{{ route('products.index') }}">Products</a>
                <span>/</span>
                <span class="current">{{ $product->name }}</span>
            </div>

            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif

            <!-- Product Detail -->
            <div class="product-detail">
                <div class="pd-media">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                    @endif
                    <span class="pd-cat">{{ $product->category }}</span>
                </div>

                <div class="product-content">
                    <span class="eyebrow">Campus Registry &mdash; Product Listing</span>
                    <h1 class="pd-title">{{ $product->name }}</h1>

                    <div class="pd-vendor">
                        <a href="{{ route('concessionaires.show', $product->concessionaire_id) }}" class="pd-vendor-avatar" title="View concessionaire">
                            {{ strtoupper(substr($product->concessionaire->business_name ?? $product->concessionaire->name, 0, 2)) }}
                        </a>
                        <div class="pd-vendor-details">
                            <a href="{{ route('concessionaires.show', $product->concessionaire_id) }}" class="pd-vendor-name">{{ $product->concessionaire->business_name ?? $product->concessionaire->name }}</a>
                            <div class="pd-vendor-label">Concessionaire</div>
                        </div>
                    </div>

                    <div class="pd-meta">
                        <span><strong>ID</strong> #{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span>&middot;</span>
                        <span><strong>Listed</strong> {{ optional($product->created_at)->format('M d, Y') ?? '—' }}</span>
                    </div>

                    <div class="pd-price">&#8369;{{ number_format($product->price, 2) }}</div>

                    @if($product->description)
                        <p class="pd-description">{{ $product->description }}</p>
                    @endif

                    <!-- Rating Summary -->
                    <div class="rating-summary">
                        <div class="rating-big">
                            <span class="rating-number">{{ $reviewStats['average'] }}</span>
                            <div>
                                <div class="rating-stars-lg">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="star-lg {{ $i <= round($reviewStats['average']) ? 'filled' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <div class="rating-text">Based on {{ $reviewStats['total'] }} {{ Str::plural('review', $reviewStats['total']) }}</div>
                            </div>
                        </div>
                        <div class="rating-bars">
                            @foreach ([5, 4, 3, 2, 1] as $star)
                                @php $percent = $reviewStats['total'] > 0 ? ($reviewStats['distribution'][$star] / $reviewStats['total']) * 100 : 0; @endphp
                                <div class="rating-bar-row">
                                    <span class="rating-bar-label">{{ $star }} star</span>
                                    <div class="rating-bar">
                                        <div class="rating-bar-fill" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="rating-bar-count">{{ $reviewStats['distribution'][$star] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="reviews-section">
                <div class="reviews-header">
                    <div>
                        <span class="eyebrow">Feedback Ledger</span>
                        <h2>Customer Reviews</h2>
                    </div>
                    <span class="reviews-count"><strong>{{ $reviewStats['total'] }}</strong> {{ Str::plural('entry', $reviewStats['total']) }} on record</span>
                </div>

                <!-- Review Form -->
                @auth
                    @php
                        $reviewerRole = auth()->user()?->role;
                        $canWriteProductReview = $reviewerRole === 'student';
                        $isOwnProduct = (int) auth()->id() === (int) $product->concessionaire_id;
                    @endphp
                    @if ($reviewerRole === 'concessionaire')
                        <div class="flash flash-info" style="margin-bottom: 30px;">
                            Concessionaires are not able to leave reviews.
                        </div>
                    @elseif (! $canWriteProductReview)
                        <div class="flash flash-info" style="margin-bottom: 30px;">
                            {{ $reviewerRole === 'admin' ? 'Admin accounts cannot leave reviews.' : 'Cashier accounts cannot leave reviews.' }}
                        </div>
                    @elseif($isOwnProduct)
                        <div class="flash flash-info" style="margin-bottom: 30px;">
                            You cannot review your own products.
                        </div>
                    @elseif($userReview)
                        <!-- Display user's review -->
                        <div class="your-review-display">
                            <h3>Your Review</h3>
                            <div class="your-review-rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="{{ $i <= $userReview->rating ? 'filled' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            @if($userReview->comment)
                                <div class="your-review-comment">{{ $userReview->comment }}</div>
                            @endif
                            <button type="button" class="btn-edit-review" onclick="openEditReviewModal()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Edit Review
                            </button>
                        </div>
                    @else
                        <!-- New review -->
                        <div class="review-form-card">
                            <h3>Write a Review</h3>
                            <form method="POST" action="{{ route('products.review.store', $product) }}">
                                @csrf
                                <div class="star-rating-input" id="newStarRating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-btn" data-rating="{{ $i }}">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" id="newRatingInput" value="">
                                </div>
                                @error('rating')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                                <textarea name="comment" class="review-textarea" placeholder="Share your experience with this product...">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                                <button type="submit" class="btn btn-green">Submit Review</button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="flash flash-info" style="margin-bottom: 30px;">
                        <a href="{{ route('login') }}">Log in</a> to write a review for this product.
                    </div>
                @endauth

                <!-- Reviews List -->
                @if($reviews->count() > 0)
                    <div class="reviews-list">
                        @foreach($reviews as $review)
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="review-avatar">
                                        {{ strtoupper(substr($review->user->name, 0, 2)) }}
                                    </div>
                                    <div class="review-meta">
                                        <div class="review-author">
                                            {{ $review->user->name }}
                                            @if(auth()->check() && $review->user_id === auth()->id())
                                                <span class="your-review-badge">Your Review</span>
                                            @endif
                                        </div>
                                        <div class="review-date">{{ $review->created_at->format('M d, Y') }}</div>
                                        <div class="review-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="{{ $i <= $review->rating ? 'filled' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="review-comment">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    {{ $reviews->links('vendor.pagination.simple') }}
                @else
                    <div class="no-reviews">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                        </svg>
                        <h3>No Reviews Yet</h3>
                        <p>Be the first to review this product.</p>
                    </div>
                @endif
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

    <!-- Edit Review Modal -->
    @if($userReview)
        <div class="modal-overlay" id="editReviewModal">
            <div class="review-modal">
                <div class="review-modal-header">
                    <h3 class="review-modal-title">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Edit Your Review
                    </h3>
                    <button type="button" class="btn-close-modal" onclick="closeEditReviewModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('products.review.update', $product) }}" id="editReviewForm">
                    @csrf
                    @method('PUT')
                    <div class="review-modal-body">
                        <div class="star-rating-input" id="editStarRating">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" class="star-btn {{ $i <= $userReview->rating ? 'active' : '' }}" data-rating="{{ $i }}">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="rating" id="editRatingInput" value="{{ $userReview->rating }}">
                        </div>
                        <textarea name="comment" class="review-textarea" placeholder="Update your review...">{{ old('comment', $userReview->comment) }}</textarea>
                        @error('comment')
                            <div class="field-error" style="text-align: center;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="review-modal-footer">
                        <button type="button" class="btn btn-outline btn-danger" onclick="openDeleteConfirmModal()">Delete Review</button>
                        <button type="submit" class="btn btn-green">Update Review</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal-overlay" id="deleteConfirmModal">
            <div class="success-modal">
                <div class="success-modal-icon" style="background: rgba(180, 35, 42, 0.1); color: var(--red);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <h3 class="success-modal-title">Delete Review?</h3>
                <p class="success-modal-message">Are you sure you want to delete your review? This action cannot be undone.</p>
                <form method="POST" action="{{ route('products.review.delete', $product) }}" style="width: 100%;">
                    @csrf
                    @method('DELETE')
                    <div style="display: flex; gap: 12px;">
                        <button type="button" class="btn btn-outline" onclick="closeDeleteConfirmModal()" style="flex: 1;">Cancel</button>
                        <button type="submit" class="btn-modal-close" style="background: var(--red);" onmouseover="this.style.background='var(--red-deep)'" onmouseout="this.style.background='var(--red)'">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Success Modal -->
    @if(session('success'))
        <div class="modal-overlay" id="successModal">
            <div class="success-modal">
                <div class="success-modal-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="success-modal-title">Success</h3>
                <p class="success-modal-message">{{ session('success') }}</p>
                <button type="button" class="btn-modal-close" onclick="closeSuccessModal()">Continue</button>
            </div>
        </div>
    @endif

    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        // Edit Review Modal Functions
        function openEditReviewModal() {
            const modal = document.getElementById('editReviewModal');
            if (modal) {
                modal.classList.remove('closing');
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeEditReviewModal() {
            const modal = document.getElementById('editReviewModal');
            if (modal) {
                modal.classList.add('closing');
                setTimeout(() => {
                    modal.classList.remove('active', 'closing');
                    document.body.style.overflow = '';
                }, 300);
            }
        }

        // Delete Confirmation Modal Functions
        function openDeleteConfirmModal() {
            closeEditReviewModal();
            setTimeout(() => {
                const modal = document.getElementById('deleteConfirmModal');
                if (modal) {
                    modal.classList.remove('closing');
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            }, 350);
        }

        function closeDeleteConfirmModal() {
            const modal = document.getElementById('deleteConfirmModal');
            if (modal) {
                modal.classList.add('closing');
                setTimeout(() => {
                    modal.classList.remove('active', 'closing');
                    document.body.style.overflow = '';
                    openEditReviewModal();
                }, 300);
            }
        }

        // Success Modal Functions
        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            if (modal) {
                modal.classList.add('closing');
                document.body.style.overflow = '';
                setTimeout(() => {
                    modal.classList.remove('active', 'closing');
                }, 300);
            }
        }

        // Show success modal on page load if there's a success message
        window.addEventListener('DOMContentLoaded', function() {
            const successModal = document.getElementById('successModal');
            if (successModal) {
                setTimeout(() => {
                    successModal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }, 100);
            }
        });

        // Close modals on outside click
        document.addEventListener('click', function(event) {
            const editModal = document.getElementById('editReviewModal');
            const deleteModal = document.getElementById('deleteConfirmModal');
            const successModal = document.getElementById('successModal');

            if (editModal && event.target === editModal) {
                closeEditReviewModal();
            }
            if (deleteModal && event.target === deleteModal) {
                closeDeleteConfirmModal();
            }
            if (successModal && event.target === successModal) {
                closeSuccessModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeEditReviewModal();
                closeDeleteConfirmModal();
                closeSuccessModal();
            }
        });

        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }

        // Star rating functionality
        function setupStarRating(containerId, inputId) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const stars = container.querySelectorAll('.star-btn');
            const input = document.getElementById(inputId);
            let selectedRating = Number(input?.value || 0);

            const paint = (rating) => {
                selectedRating = rating;
                if (input) {
                    input.value = String(rating);
                }

                stars.forEach((star, index) => {
                    star.querySelector('svg').style.color = index < rating ? '#C99A2E' : '#BECBB3';
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
                    paint(rating);
                });

                star.addEventListener('mouseenter', () => {
                    const rating = Number(star.dataset.rating || 0);
                    stars.forEach((s, index) => {
                        s.querySelector('svg').style.color = index < rating ? '#C99A2E' : '#BECBB3';
                    });
                });

                star.addEventListener('mouseleave', () => {
                    paint(selectedRating);
                });
            });

            paint(selectedRating);
        }

        setupStarRating('newStarRating', 'newRatingInput');
        setupStarRating('editStarRating', 'editRatingInput');
    </script>
</body>
</html>
