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

        .nav-links { display: flex; align-items: center; gap: 8px; }

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

        .nav-mobile-actions { display: flex; align-items: center; gap: 10px; }

        .nav-profile-mobile { display: none; }

        /* ===== MAIN ===== */
        .page-wrapper {
            flex: 1;
            padding-top: 72px;
        }

        .main-content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 16px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }
        .breadcrumb a {
            color: #64748b;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            color: var(--green);
        }
        .breadcrumb span {
            color: #94a3b8;
        }
        .breadcrumb .current {
            color: #374151;
            font-weight: 500;
        }

        /* ===== PRODUCT DETAIL ===== */
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            margin-bottom: 48px;
        }
        .product-image-lg {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-image-lg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-image-lg svg {
            width: 96px;
            height: 96px;
            color: #cbd5e1;
        }
        .product-content h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
            color: #1a202c;
        }
        .vendor-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .vendor-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--green) 0%, var(--green-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
        }
        .vendor-details {
            flex: 1;
        }
        .vendor-name {
            font-weight: 600;
            color: #374151;
            text-decoration: none;
        }
        .vendor-name:hover {
            color: var(--green);
            text-decoration: underline;
        }
        .vendor-label {
            font-size: 12px;
            color: #94a3b8;
        }
        .product-category-badge {
            display: inline-flex;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(212,168,67,0.12);
            color: #b8860b;
            margin-bottom: 16px;
        }
        .product-price-lg {
            font-size: 36px;
            font-weight: 800;
            color: var(--green);
            margin-bottom: 20px;
        }
        .product-description {
            font-size: 15px;
            line-height: 1.7;
            color: #4b5563;
            margin-bottom: 24px;
        }
        .rating-summary {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .rating-big {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        .rating-number {
            font-size: 48px;
            font-weight: 800;
            color: #1a202c;
        }
        .rating-stars-lg {
            display: flex;
            gap: 4px;
        }
        .star-lg {
            width: 28px;
            height: 28px;
            color: #e2e8f0;
        }
        .star-lg.filled {
            color: #fbbf24;
        }
        .rating-text {
            font-size: 14px;
            color: #64748b;
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
            font-size: 13px;
            color: #64748b;
            width: 50px;
        }
        .rating-bar {
            flex: 1;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .rating-bar-fill {
            height: 100%;
            background: #fbbf24;
            border-radius: 4px;
        }
        .rating-bar-count {
            font-size: 13px;
            color: #94a3b8;
            width: 30px;
            text-align: right;
        }

        /* ===== REVIEWS SECTION ===== */
        .reviews-section {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .reviews-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .reviews-header h2 {
            font-size: 24px;
            font-weight: 700;
        }

        /* Review Form */
        .review-form-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 32px;
        }
        .review-form-card h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
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
        .star-btn:hover {
            transform: scale(1.1);
        }
        .star-btn svg {
            width: 36px;
            height: 36px;
            color: #e2e8f0;
            transition: color 0.2s;
        }
        .star-btn.active svg, .star-btn:hover svg {
            color: #fbbf24;
        }
        .review-textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 16px;
        }
        .review-textarea:focus {
            outline: none;
            border-color: var(--green);
        }
        .btn {
            padding: 12px 24px;
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
        .btn-outline.btn-danger:hover {
            border-color: #ef4444;
            color: #ef4444;
        }
        .btn-red {
            background: #ef4444;
            color: #fff;
        }
        .btn-red:hover {
            background: #dc2626;
        }
        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }

        /* Review List */
        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .review-card {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 24px;
        }
        .review-card:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .review-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 12px;
        }
        .review-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }
        .review-meta {
            flex: 1;
        }
        .review-author {
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }
        .review-date {
            font-size: 13px;
            color: #94a3b8;
        }
        .review-stars {
            display: flex;
            gap: 2px;
        }
        .review-stars svg {
            width: 18px;
            height: 18px;
            color: #e2e8f0;
        }
        .review-stars svg.filled {
            color: #fbbf24;
        }
        .review-comment {
            font-size: 15px;
            line-height: 1.7;
            color: #4b5563;
        }
        .review-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }
        .your-review-badge {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(10,92,47,0.1);
            color: var(--green);
            margin-left: 8px;
        }

        /* Edit Review Button */
        .btn-edit-review {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 20px;
            background: var(--green);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(10, 92, 47, 0.15);
            font-family: inherit;
            width: fit-content;
            margin: 0 auto;
        }
        .btn-edit-review:hover {
            background: var(--green-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(10, 92, 47, 0.25);
        }
        .btn-edit-review:active {
            transform: translateY(0);
        }
        .btn-edit-review svg {
            width: 16px;
            height: 16px;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        visibility 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        backdrop-filter 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .review-modal {
            background: #ffffff;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.9) translateY(30px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-overlay.active .review-modal {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .review-modal-header {
            padding: 24px 28px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .review-modal-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .review-modal-title svg {
            width: 24px;
            height: 24px;
            color: #fbbf24;
        }
        .btn-close-modal {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: #f1f5f9;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .btn-close-modal:hover {
            background: #e2e8f0;
            color: #334155;
        }
        .btn-close-modal svg {
            width: 20px;
            height: 20px;
        }

        .review-modal-body {
            padding: 24px 28px;
            overflow-y: auto;
        }
        .review-modal-body .star-rating-input {
            justify-content: center;
            margin-bottom: 20px;
        }
        .review-modal-body .star-btn svg {
            width: 40px;
            height: 40px;
        }
        .review-modal-body .review-textarea {
            margin-bottom: 20px;
        }
        .review-modal-footer {
            padding: 20px 28px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .review-modal-footer .btn {
            min-width: 140px;
        }

        .modal-overlay.closing {
            opacity: 0;
        }
        .modal-overlay.closing .review-modal {
            transform: scale(0.9) translateY(30px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Success Modal */
        .success-modal {
            background: #ffffff;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            padding: 32px 24px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: scale(0.9) translateY(20px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .modal-overlay.active .success-modal {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        .success-modal-icon {
            width: 72px;
            height: 72px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #166534;
            box-shadow: 0 0 0 10px rgba(220, 252, 231, 0.5);
        }
        .success-modal-icon svg {
            width: 36px;
            height: 36px;
        }
        .success-modal-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .success-modal-message {
            font-size: 15px;
            color: #475569;
            line-height: 1.5;
            margin-bottom: 24px;
        }
        .btn-modal-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            background: var(--green);
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(10, 92, 47, 0.2);
            font-family: inherit;
        }
        .btn-modal-close:hover {
            background: var(--green-light);
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -2px rgba(10, 92, 47, 0.3);
        }
        .btn-modal-close:active {
            transform: translateY(0);
        }

        .modal-overlay.closing .success-modal {
            transform: scale(0.9) translateY(20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Your Review Display Card */
        .your-review-display {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid #bbf7d0;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 32px;
        }
        .your-review-display h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #0f172a;
        }
        .your-review-rating {
            display: flex;
            gap: 4px;
            margin-bottom: 16px;
        }
        .your-review-rating svg {
            width: 24px;
            height: 24px;
            color: #e2e8f0;
        }
        .your-review-rating svg.filled {
            color: #fbbf24;
        }
        .your-review-comment {
            background: #fff;
            padding: 16px;
            border-radius: 10px;
            font-size: 15px;
            line-height: 1.7;
            color: #4b5563;
            margin-bottom: 16px;
        }

        /* Empty Reviews */
        .no-reviews {
            text-align: center;
            padding: 48px 24px;
            color: #64748b;
        }
        .no-reviews svg {
            width: 48px;
            height: 48px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }
        .no-reviews h3 {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        /* Alert */
        .alert {
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-weight: 500;
        }
        .alert-success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
        }
        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }
        .alert-info {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
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
        @media (max-width: 900px) {
            .product-detail {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            .product-image-lg {
                max-height: 400px;
            }
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
        @media (max-width: 600px) {
            .product-content h1 {
                font-size: 24px;
            }
            .product-price-lg {
                font-size: 28px;
            }
            .rating-number {
                font-size: 36px;
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
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <!-- Product Detail -->
            <div class="product-detail">
                <div class="product-image-lg">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                    @endif
                </div>

                <div class="product-content">
                    <span class="product-category-badge">{{ $product->category }}</span>
                    <h1>{{ $product->name }}</h1>
                    
                    <div class="vendor-info">
                        <a href="{{ route('concessionaires.show', $product->concessionaire_id) }}" class="vendor-avatar" title="View concessionaire">
                            {{ strtoupper(substr($product->concessionaire->business_name ?? $product->concessionaire->name, 0, 2)) }}
                        </a>
                        <div class="vendor-details">
                            <a href="{{ route('concessionaires.show', $product->concessionaire_id) }}" class="vendor-name">{{ $product->concessionaire->business_name ?? $product->concessionaire->name }}</a>
                            <div class="vendor-label">Concessionaire</div>
                        </div>
                    </div>

                    <div class="product-price-lg">₱{{ number_format($product->price, 2) }}</div>

                    @if($product->description)
                        <p class="product-description">{{ $product->description }}</p>
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
                                    <span class="rating-bar-label">{{ $star }} stars</span>
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
                    <h2>Customer Reviews</h2>
                </div>

                <!-- Review Form -->
                @auth
                    @php
                        $reviewerRole = auth()->user()?->role;
                        $canWriteProductReview = $reviewerRole === 'student';
                        $isOwnProduct = (int) auth()->id() === (int) $product->concessionaire_id;
                    @endphp
                    @if ($reviewerRole === 'concessionaire')
                        <div class="alert alert-info" style="margin-bottom: 32px;">
                            Concessionaires are not able to leave reviews.
                        </div>
                    @elseif (! $canWriteProductReview)
                        <div class="alert alert-info" style="margin-bottom: 32px;">
                            {{ $reviewerRole === 'admin' ? 'Admin accounts cannot leave reviews.' : 'Cashier accounts cannot leave reviews.' }}
                        </div>
                    @elseif($isOwnProduct)
                        <div class="alert alert-info" style="margin-bottom: 32px;">
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
                                    <div style="color: #dc2626; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
                                @enderror
                                <textarea name="comment" class="review-textarea" placeholder="Share your experience with this product...">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div style="color: #dc2626; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
                                @enderror
                                <button type="submit" class="btn btn-green">Submit Review</button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="alert alert-info" style="margin-bottom: 32px;">
                        <a href="{{ route('login') }}" style="color: inherit; font-weight: 600;">Log in</a> to write a review for this product.
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
                                    </div>
                                    <div class="review-stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="{{ $i <= $review->rating ? 'filled' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
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
                        <p>Be the first to review this product!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p class="footer-text">&copy; {{ date('Y') }} <a href="/">CvSU</a> — Trece Martires City Campus. All rights reserved.</p>
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
                            <div style="color: #dc2626; font-size: 14px; font-weight: 500; text-align: center; margin-top: 14px; margin-bottom: 4px; width: 100%; line-height: 1.4;">
                                {{ $message }}
                            </div>
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
                <div class="success-modal-icon" style="background: #fee2e2; box-shadow: 0 0 0 10px rgba(254, 226, 226, 0.5);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="color: #dc2626;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <h3 class="success-modal-title">Delete Review?</h3>
                <p class="success-modal-message">Are you sure you want to delete your review? This action cannot be undone.</p>
                <form method="POST" action="{{ route('products.review.delete', $product) }}" style="width: 100%;">
                    @csrf
                    @method('DELETE')
                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <button type="button" class="btn btn-outline" onclick="closeDeleteConfirmModal()" style="flex: 1;">Cancel</button>
                        <button type="submit" class="btn-modal-close" style="background: #ef4444; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2); flex: 1;">Delete</button>
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
                <h3 class="success-modal-title">Success!</h3>
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
                    star.querySelector('svg').style.color = index < rating ? '#fbbf24' : '#e2e8f0';
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
                        s.querySelector('svg').style.color = index < rating ? '#fbbf24' : '#e2e8f0';
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
