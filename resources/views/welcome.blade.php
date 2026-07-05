<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EBA Information System | CvSU - Trece Martires City Campus</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cvsu-green: #0A5C2F;
            --cvsu-green-light: #0D7A3E;
            --cvsu-green-dark: #064420;
            --cvsu-gold: #D4A843;
            --cvsu-gold-light: #E8C96A;
            --white: #FFFFFF;
            --gray-50: #EBE8E2;
            --gray-100: #F0EDE8;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--gray-800);
            line-height: 1.6;
            overflow-x: hidden;
            background: #F5F5F4;
            opacity: 0;
            animation: fadeIn 0.6s ease-out 0.1s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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

        .nav-links a:hover {
            color: var(--cvsu-green);
            background: rgba(10, 92, 47, 0.06);
        }

        .nav-links a.active {
            color: var(--cvsu-green);
            background: rgba(10, 92, 47, 0.1);
            font-weight: 700;
        }

        .nav-links .btn-login {
            color: var(--cvsu-green);
            font-weight: 600;
        }

        .nav-links .btn-register {
            background: var(--cvsu-green);
            color: var(--white);
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 8px;
        }

        .nav-links .btn-register:hover {
            background: var(--cvsu-green-light);
            color: var(--white);
        }

        .nav-links .btn-logout {
            background: transparent;
            color: #dc2626;
            font-weight: 600;
            padding: 8px 20px;
            border: 2px solid #dc2626;
            border-radius: 8px;
        }
        .nav-links .btn-logout:hover {
            background: #dc2626;
            color: var(--white);
        }

        /* Mobile Menu */
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

        .nav-profile-mobile {
            display: none;
        }

        /* ===== HERO ===== */
        .hero {
            position: relative;
            min-height: 72vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-color: #F0EDE8;
            background-image: 
                linear-gradient(to bottom, transparent 0%, transparent 70%, #F0EDE8 85%, white 100%),
                radial-gradient(circle, #C5BFB5 1px, transparent 1px);
            background-size: 100% 100%, 24px 24px;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(10, 92, 47, 0.08), transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
            z-index: 0;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(212, 168, 67, 0.08), transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 1280px;
            margin: 0 auto;
            padding: 96px 24px 56px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            width: 100%;
            box-sizing: border-box;
        }

        .hero-text {
            color: var(--gray-900);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            max-width: 620px;
        }

        .hero-title {
            font-size: clamp(36px, 5vw, 62px);
            font-weight: 900;
            line-height: 1.04;
            letter-spacing: -1.8px;
            margin-bottom: 18px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .hero-line {
            display: block;
            color: var(--gray-900);
            opacity: 0;
            transform: translateY(30px);
            animation: slideInFromTop 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .hero-line:nth-child(1) { animation-delay: 0.2s; }
        .hero-line:nth-child(2) { animation-delay: 0.4s; }
        .hero-line:nth-child(3) { animation-delay: 0.6s; }

        .text-green-700 {
            color: #15803d;
        }

        .hero-description {
            font-size: 18px;
            line-height: 1.7;
            color: var(--gray-700);
            margin-bottom: 30px;
            max-width: 600px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.8s forwards;
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: flex-start;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 1s forwards;
        }

        .hero-image-wrap {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 320px;
        }

        .hero-image {
            width: min(100%, 540px);
            max-height: 480px;
            object-fit: contain;
            animation: float 6s ease-in-out infinite;
            filter: drop-shadow(0 20px 40px rgba(10, 92, 47, 0.15));
            transition: filter 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .hero-image:hover {
            filter: drop-shadow(0 30px 60px rgba(10, 92, 47, 0.25));
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--cvsu-green);
            color: var(--white);
            box-shadow: 0 8px 20px rgba(10, 92, 47, 0.22);
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            background: var(--cvsu-green-light);
            box-shadow: 0 16px 36px rgba(10, 92, 47, 0.35);
        }

        .btn-primary:active {
            transform: translateY(-1px) scale(0.98);
        }

        .btn-outline {
            background: var(--white);
            color: var(--cvsu-green);
            border: 2px solid rgba(10, 92, 47, 0.28);
        }

        .btn-outline:hover {
            background: var(--cvsu-green);
            color: var(--white);
            border-color: var(--cvsu-green);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 28px rgba(10, 92, 47, 0.25);
        }

        .btn-outline:active {
            transform: translateY(-1px) scale(0.98);
        }

        /* ===== FEATURES SECTION ===== */
        .features {
            padding: 80px 24px;
            background: white;
        }

        .section-container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .section-label {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--cvsu-green);
            background: rgba(10, 92, 47, 0.08);
            padding: 6px 16px;
            border-radius: 100px;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: -0.8px;
            margin-bottom: 16px;
            line-height: 1.15;
        }

        .section-subtitle {
            font-size: 17px;
            color: var(--gray-600);
            max-width: 640px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .feature-card {
            position: relative;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 250, 248, 0.96) 100%);
            border-radius: 28px;
            padding: 0;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            inset: auto -15% -20% auto;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(10, 92, 47, 0.06), transparent 70%);
            pointer-events: none;
            opacity: 1;
            border-radius: 50%;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .feature-card::after {
            content: '';
            position: absolute;
            inset: -10% auto auto -15%;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(212, 168, 67, 0.08), transparent 70%);
            pointer-events: none;
            opacity: 1;
            border-radius: 50%;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .feature-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 32px 70px rgba(15, 23, 42, 0.18);
            border-color: rgba(10, 92, 47, 0.2);
        }

        .feature-card:hover::before {
            transform: scale(1.4) translateY(-10px);
            opacity: 0.9;
        }

        .feature-card:hover::after {
            transform: scale(1.3) translateY(10px);
            opacity: 1;
        }

        .feature-card-inner {
            padding: 36px 32px;
            position: relative;
            z-index: 1;
        }

        .feature-card.feature-spotlight {
            background: linear-gradient(145deg, #0A5C2F 0%, #064420 100%);
            border-color: rgba(10, 92, 47, 0.2);
            box-shadow: 0 20px 50px rgba(10, 92, 47, 0.25);
        }

        .feature-card.feature-spotlight::before {
            background: radial-gradient(circle, rgba(212, 168, 67, 0.18), transparent 70%);
            opacity: 1;
        }

        .feature-card.feature-spotlight::after {
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08), transparent 70%);
            opacity: 1;
        }

        .feature-card.feature-spotlight:hover {
            box-shadow: 0 28px 60px rgba(10, 92, 47, 0.35);
        }

        .feature-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 18px;
            margin-bottom: 24px;
        }

        .feature-icon-wrap {
            position: relative;
        }

        .feature-icon {
            width: 68px;
            height: 68px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(10, 92, 47, 0.1) 0%, rgba(212, 168, 67, 0.2) 100%);
            box-shadow: inset 0 2px 0 rgba(255, 255, 255, 0.4), 0 12px 28px rgba(10, 92, 47, 0.15);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .feature-icon::before {
            content: '';
            position: absolute;
            inset: -100% 0 0 -100%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: translateX(-100%) translateY(-100%);
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(-5deg);
            box-shadow: inset 0 2px 0 rgba(255, 255, 255, 0.6), 0 16px 36px rgba(10, 92, 47, 0.25);
        }

        .feature-card:hover .feature-icon::before {
            transform: translateX(100%) translateY(100%);
        }

        .feature-spotlight .feature-icon {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(212, 168, 67, 0.25) 100%);
            box-shadow: inset 0 2px 0 rgba(255, 255, 255, 0.15), 0 12px 28px rgba(0, 0, 0, 0.2);
        }

        .feature-icon svg {
            width: 32px;
            height: 32px;
            color: var(--cvsu-green);
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .feature-spotlight .feature-icon svg {
            color: rgba(255, 255, 255, 0.95);
        }

        .feature-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 14px;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }

        .feature-spotlight .feature-title {
            color: var(--white);
        }

        .feature-desc {
            font-size: 15px;
            color: var(--gray-600);
            line-height: 1.75;
            margin-bottom: 26px;
        }

        .feature-spotlight .feature-desc {
            color: rgba(255, 255, 255, 0.85);
        }

        /* ===== CONCESSIONAIRES SHOWCASE ===== */
        .concessionaires-showcase {
            padding: 120px 24px;
            background-color: #F0EDE8;
            background-image: 
                linear-gradient(to bottom, white 0%, #F0EDE8 15%, transparent 30%, transparent 70%, #F0EDE8 85%, white 100%),
                radial-gradient(circle, #C5BFB5 1px, transparent 1px);
            background-size: 100% 100%, 24px 24px;
        }
        .showcase-grid {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 80px;
            align-items: center;
        }
        .showcase-text h2 {
            font-size: clamp(28px, 3.5vw, 40px);
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: -0.8px;
            margin-bottom: 20px;
            line-height: 1.15;
        }
        .showcase-text p {
            font-size: 16px;
            color: var(--gray-600);
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .card-container {
            position: relative;
            height: 540px;
            width: 100%;
            max-width: 540px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 28px;
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.15);
            background: var(--gray-200);
            border: 8px solid var(--white);
        }
        .grid-card {
            position: absolute;
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            z-index: 1;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            cursor: pointer;
        }
        .grid-card.top-left { top: 0; left: 0; height: calc(50% - 4px); width: calc(50% - 4px); border-radius: 0 0 12px 0; }
        .grid-card.top-right { top: 0; right: 0; height: calc(50% - 4px); width: calc(50% - 4px); border-radius: 0 0 0 12px; }
        .grid-card.bottom-left { bottom: 0; left: 0; height: calc(50% - 4px); width: calc(50% - 4px); border-radius: 0 12px 0 0; }
        .grid-card.bottom-right { bottom: 0; right: 0; height: calc(50% - 4px); width: calc(50% - 4px); border-radius: 12px 0 0 0; }

        .grid-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.4) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .grid-card:hover::before {
            opacity: 1;
        }

        .grid-card:hover {
            height: 100% !important;
            width: 100% !important;
            z-index: 10;
            border-radius: 0;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            transform: scale(1.02);
        }

        .grid-card .card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 24px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.4) 60%, transparent 100%);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .grid-card:hover .card-content {
            padding-bottom: 42px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.5) 60%, transparent 100%);
        }
        .grid-card .card-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
            transform: translateY(20px);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .grid-card:hover .card-title {
            transform: translateY(0);
            font-size: 24px;
        }
        .grid-card .card-description {
            font-size: 14px;
            opacity: 0;
            max-height: 0;
            line-height: 1.5;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            text-shadow: 0 1px 4px rgba(0,0,0,0.6);
            overflow: hidden;
            color: rgba(255,255,255,0.9);
            transform: translateY(10px);
        }
        .grid-card:hover .card-description {
            opacity: 1;
            max-height: 120px;
            margin-top: 12px;
            transform: translateY(0);
        }
        @media (max-width: 1024px) {
            .showcase-grid { grid-template-columns: 1fr; gap: 40px; }
            .showcase-text { text-align: center; }
            .showcase-text .section-label { margin: 0 auto 16px; }
        }

        /* ===== ABOUT SECTION ===== */
        .about {
            padding: 100px 24px;
            background: white;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .about-image {
            position: relative;
        }

        .about-image img {
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.12);
        }

        .about-image-accent {
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 180px;
            height: 180px;
            background: linear-gradient(135deg, var(--cvsu-green), var(--cvsu-gold));
            border-radius: 24px;
            z-index: -1;
            opacity: 0.15;
        }

        .about-text h2 {
            font-size: clamp(28px, 3.5vw, 40px);
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: -0.8px;
            margin-bottom: 20px;
            line-height: 1.15;
        }

        .about-text p {
            font-size: 16px;
            color: var(--gray-600);
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .about-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 28px;
        }

        .about-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 15px;
            color: var(--gray-700);
        }

        .about-list li .check {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(10, 92, 47, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .about-list li .check svg {
            width: 12px;
            height: 12px;
            color: var(--cvsu-green);
        }

        /* ===== CTA SECTION ===== */
        .cta {
            padding: 100px 24px;
            background: linear-gradient(135deg, var(--cvsu-green-dark) 0%, var(--cvsu-green) 50%, var(--cvsu-green-light) 100%);
            position: relative;
            overflow: hidden;
        }

        .cta-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.04;
            background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0);
            background-size: 32px 32px;
        }

        .cta-content {
            position: relative;
            z-index: 2;
            max-width: 720px;
            margin: 0 auto;
            text-align: center;
            color: var(--white);
        }

        .cta-content h2 {
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 800;
            letter-spacing: -0.8px;
            margin-bottom: 16px;
            line-height: 1.15;
        }

        .cta-content p {
            font-size: 17px;
            opacity: 0.85;
            margin-bottom: 36px;
            line-height: 1.7;
        }

        .cta-actions {
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-gold {
            background: var(--cvsu-gold);
            color: var(--gray-900);
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(212, 168, 67, 0.4);
        }

        .btn-gold:hover {
            background: var(--cvsu-gold-light);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 16px 36px rgba(212, 168, 67, 0.5);
        }

        .btn-white-outline {
            background: transparent;
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-white-outline:hover {
            background: rgba(255, 255, 255, 0.95);
            color: var(--cvsu-green);
            border-color: rgba(255, 255, 255, 0.9);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 28px rgba(255, 255, 255, 0.2);
        }

        /* ===== VISION MISSION VALUES ===== */
        .vmv-section {
            background: var(--white);
            padding: 100px 24px;
        }
        .vmv-container {
            max-width: 1280px;
            margin: 0 auto;
        }
        .vmv-header {
            text-align: center;
            margin-bottom: 60px;
        }
        .vmv-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(10, 92, 47, 0.08);
            color: var(--cvsu-green);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 8px 16px;
            border-radius: 50px;
            margin-bottom: 20px;
        }
        .vmv-header h2 {
            font-size: 36px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }
        .vmv-header p {
            color: var(--gray-600);
            font-size: 17px;
            max-width: 600px;
            margin: 0 auto;
        }
        .vmv-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }
        .vmv-card {
            background: var(--white);
            border-radius: 20px;
            padding: 36px 28px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--gray-100);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .vmv-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
            border-color: rgba(10, 92, 47, 0.15);
        }
        .vmv-card h3 {
            font-size: 22px;
            font-weight: 700;
            color: var(--cvsu-green);
            margin-bottom: 16px;
        }
        .vmv-card > p {
            font-size: 15px;
            line-height: 1.8;
            color: var(--gray-600);
        }
        .core-values-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 8px;
        }
        .core-value-item {
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 12px;
            border-radius: 12px;
            cursor: pointer;
        }

        .core-value-item:hover {
            background: rgba(10, 92, 47, 0.04);
            transform: translateX(8px);
        }
        .core-value-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--cvsu-green) 0%, var(--cvsu-green-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .core-value-item:hover .core-value-icon {
            transform: rotate(10deg) scale(1.1);
            box-shadow: 0 8px 20px rgba(10, 92, 47, 0.3);
        }

        .core-value-icon svg {
            width: 24px;
            height: 24px;
            color: var(--white);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .core-value-item:hover .core-value-icon svg {
            transform: scale(1.1);
        }
        .core-value-text {
            flex: 1;
        }
        .core-value-text strong {
            display: block;
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 2px;
        }
        .core-value-text span {
            font-size: 13px;
            color: var(--gray-600);
        }
        @media (max-width: 1024px) {
            .vmv-grid { grid-template-columns: 1fr; max-width: 600px; margin: 0 auto; }
        }
        @media (max-width: 768px) {
            .vmv-header h2 { font-size: 28px; }
            .vmv-section { padding: 60px 24px; }
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--gray-900);
            color: rgba(255, 255, 255, 0.6);
            padding: 64px 24px 32px;
        }

        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .footer-brand img {
            height: 56px;
            width: auto;
            max-width: 220px;
            object-fit: contain;
            display: block;
        }

        .footer-brand-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--white);
        }

        .footer-desc {
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .footer-heading {
            font-size: 13px;
            font-weight: 700;
            color: var(--white);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: inline-block;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--cvsu-gold-light);
            transition: width 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .footer-links a:hover {
            color: var(--cvsu-gold-light);
            transform: translateX(4px);
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gray-600); }

        /* ===== RESPONSIVE ===== */
        @media (min-width: 1600px) {
            .hero-content { max-width: 1400px; }
            .hero-title { font-size: 64px; }
            .hero-description { font-size: 19px; }
            .section-container { max-width: 1400px; }
        }

        @media (max-width: 1024px) {
            .hero-content {
                padding: 90px 24px 50px;
                flex-direction: column;
                gap: 28px;
            }
            .hero-text {
                align-items: center;
                text-align: center;
            }
            .hero-title {
                align-items: center;
            }
            .hero-description { margin: 0 auto 28px; }
            .hero-actions { justify-content: center; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .about-grid { grid-template-columns: 1fr; gap: 40px; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
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
            .features-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; gap: 8px; text-align: center; }
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(40px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        @keyframes fadeInLeft {
            from { 
                opacity: 0; 
                transform: translateX(-40px); 
            }
            to { 
                opacity: 1; 
                transform: translateX(0); 
            }
        }

        @keyframes fadeInRight {
            from { 
                opacity: 0; 
                transform: translateX(40px); 
            }
            to { 
                opacity: 1; 
                transform: translateX(0); 
            }
        }

        @keyframes scaleIn {
            from { 
                opacity: 0; 
                transform: scale(0.8); 
            }
            to { 
                opacity: 1; 
                transform: scale(1); 
            }
        }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0px); 
            }
            50% { 
                transform: translateY(-20px); 
            }
        }

        @keyframes slideInFromTop {
            from { 
                opacity: 0; 
                transform: translateY(-60px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        @keyframes rotateIn {
            from { 
                opacity: 0; 
                transform: rotate(-10deg) scale(0.9); 
            }
            to { 
                opacity: 1; 
                transform: rotate(0deg) scale(1); 
            }
        }

        @keyframes shimmer {
            0% { 
                background-position: -1000px 0; 
            }
            100% { 
                background-position: 1000px 0; 
            }
        }

        @keyframes pulse {
            0%, 100% { 
                transform: scale(1); 
                opacity: 1; 
            }
            50% { 
                transform: scale(1.05); 
                opacity: 0.8; 
            }
        }

        .animate-in {
            animation: fadeInUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-left {
            animation: fadeInLeft 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-right {
            animation: fadeInRight 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-scale {
            animation: scaleIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }
        .delay-3 { animation-delay: 0.3s; opacity: 0; }
        .delay-4 { animation-delay: 0.4s; opacity: 0; }
        .delay-5 { animation-delay: 0.5s; opacity: 0; }
        .delay-6 { animation-delay: 0.6s; opacity: 0; }

        /* ===== FAQ SECTION ===== */
        .faq-container details {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .faq-container details:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06) !important;
        }

        .faq-container details[open] {
            background: var(--gray-50) !important;
        }

        .faq-container details[open] summary ~ * {
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .faq-container summary::-webkit-details-marker {
            display: none;
        }

        .faq-container summary {
            cursor: pointer;
            user-select: none;
        }

        .faq-container summary:hover {
            color: var(--cvsu-green) !important;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            top: 90px;
            right: 24px;
            z-index: 2000;
            background: var(--white);
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 400px;
            animation: slideIn 0.4s ease, fadeOut 0.4s ease 4.6s forwards;
        }
        .toast-success {
            border-left: 4px solid var(--cvsu-green);
        }
        .toast-icon {
            width: 24px;
            height: 24px;
            background: rgba(10, 92, 47, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .toast-icon svg {
            width: 14px;
            height: 14px;
            color: var(--cvsu-green);
        }
        .toast-content {
            flex: 1;
        }
        .toast-title {
            font-weight: 600;
            font-size: 14px;
            color: var(--gray-900);
            margin-bottom: 2px;
        }
        .toast-message {
            font-size: 13px;
            color: var(--gray-600);
        }
        .toast-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: var(--gray-600);
        }
        .toast-close:hover {
            color: var(--gray-900);
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; visibility: hidden; }
        }
    </style>
</head>
<body>

    @if (session('success'))
    <div class="toast toast-success" id="toast">
        <div class="toast-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="toast-content">
            <div class="toast-title">Success!</div>
            <div class="toast-message">{{ session('success') }}</div>
        </div>
        <button class="toast-close" onclick="document.getElementById('toast').remove()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

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

    <!-- ===== HERO ===== -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text animate-in">
                <h1 class="hero-title">
                    {!! \App\Models\SiteSetting::get('hero_title', 'Your Campus Marketplace, All in One Place') !!}
                </h1>
                <p class="hero-description">
                    {{ \App\Models\SiteSetting::get('hero_subtitle', 'Browse products, discover concessionaires, and stay connected with everything CvSU Trece Martires has to offer.') }}
                </p>
                <div class="hero-actions">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        Browse Products
                    </a>
                    <a href="{{ route('concessionaires.index') }}" class="btn btn-outline">
                        View Concessionaires
                    </a>
                </div>
            </div>

            <div class="hero-image-wrap animate-in delay-1">
                <img src="{{ \App\Models\SiteSetting::image('hero_image', asset('images/vector-1-transparent.png')) }}" alt="EBA Campus Concessionaire System" class="hero-image">
            </div>
        </div>
    </section>

    <!-- ===== FEATURES ===== -->
    <section class="features" id="features">
        <div class="section-container">
            <div class="section-header">
                <span class="section-label">Platform Features</span>
                <h2 class="section-title">{{ \App\Models\SiteSetting::get('features_title', 'Everything Your Office Needs, In One System') }}</h2>
                <p class="section-subtitle">
                    From partnership management to stock and product tracking, streamline every aspect of the 
                    External and Business Affairs Office operations.
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-card-inner">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrap">
                                <div class="feature-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <h3 class="feature-title">{{ \App\Models\SiteSetting::get('feature_1_title', 'Partnership Applications') }}</h3>
                        <p class="feature-desc">{{ \App\Models\SiteSetting::get('feature_1_desc', 'Allow organizations and individuals to apply for partnerships directly through the platform, keeping requirements, progress, and review steps clear from day one.') }}</p>
                    </div>
                </div>

                <div class="feature-card feature-spotlight">
                    <div class="feature-card-inner">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrap">
                                <div class="feature-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <h3 class="feature-title">{{ \App\Models\SiteSetting::get('feature_2_title', 'Secure & Reliable') }}</h3>
                        <p class="feature-desc">{{ \App\Models\SiteSetting::get('feature_2_desc', 'Role-based access control ensures administrators, faculty, concessionaires, cashiers, and students each get the tools they need without exposing the rest.') }}</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-card-inner">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrap">
                                <div class="feature-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <h3 class="feature-title">{{ \App\Models\SiteSetting::get('feature_3_title', 'Payments & Tracking') }}</h3>
                        <p class="feature-desc">{{ \App\Models\SiteSetting::get('feature_3_desc', 'Record payments, generate receipts, and monitor concessionaire transactions with a cleaner status view that supports both office staff and concessionaires.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== VISION MISSION VALUES ===== -->
    <section class="vmv-section">
        <div class="vmv-container">
            <div class="vmv-header">
                <span class="vmv-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                    Cavite State University
                </span>
                <h2>Our Vision, Mission & Core Values</h2>
                <p>Guiding principles that drive excellence at CvSU Trece Martires City Campus</p>
            </div>

            <div class="vmv-grid">
                <!-- Vision -->
                <div class="vmv-card">
                    <h3>University Vision</h3>
                    <p>{{ \App\Models\SiteSetting::get('vision', 'The premier university in historic Cavite globally recognized for excellence in character development, academics, research, innovation and sustainable community engagement.') }}</p>
                </div>

                <!-- Mission -->
                <div class="vmv-card">
                    <h3>University Mission</h3>
                    <p>{{ \App\Models\SiteSetting::get('mission', 'Cavite State University shall provide excellent, equitable and relevant educational opportunities in the arts, sciences and technology through quality instruction and responsive research and development activities. It shall produce professional, skilled and morally upright individuals for global competitiveness.') }}</p>
                </div>

                <!-- Core Values -->
                <div class="vmv-card">
                    <h3>Core Values</h3>
                    <div class="core-values-list">
                        @for($i = 1; $i <= 5; $i++)
                        @php $cv = \App\Models\SiteSetting::get('core_value_'.$i, ''); @endphp
                        @if($cv)
                        <div class="core-value-item">
                            <div class="core-value-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                </svg>
                            </div>
                            <div class="core-value-text">
                                <strong>{{ $cv }}</strong>
                            </div>
                        </div>
                        @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CONCESSIONAIRES SHOWCASE (coverflow gallery) ===== -->
    <section class="concessionaires-showcase">
        <div class="section-container">
            @php
                $showcaseConcessionaires = \App\Models\User::query()
                    ->where('role', 'concessionaire')
                    ->where('is_active_concessionaire', true)
                    ->where('is_approved', true)
                    ->latest()
                    ->take(12)
                    ->get(['id', 'name', 'business_name', 'carousel_image', 'cover_photo', 'profile_photo', 'location']);

                $ccGradients = [
                    'linear-gradient(135deg,#0A5C2F,#0D7A3E)',
                    'linear-gradient(135deg,#D4A843,#b8860b)',
                    'linear-gradient(135deg,#1d4ed8,#3b82f6)',
                    'linear-gradient(135deg,#7e22ce,#a855f7)',
                    'linear-gradient(135deg,#be185d,#ec4899)',
                ];
            @endphp

            {{-- Coverflow gallery: heading/intro on top, curved 3D carousel below.
                 Intentionally different from the concessionaires page hero carousel. --}}
            <style>
                .cf-header { display:grid; grid-template-columns:1.1fr 1fr; gap:48px; align-items:flex-end; margin-bottom:56px; }
                .cf-title { font-size:clamp(36px,5vw,58px); font-weight:900; letter-spacing:-1.6px; line-height:1.04; color:var(--gray-900); }
                .cf-intro p { font-size:17px; color:var(--gray-600); line-height:1.7; margin-bottom:24px; }
                @media (max-width:900px){ .cf-header{ grid-template-columns:1fr; gap:18px; text-align:center; } .cf-intro .btn{ margin-left:auto; margin-right:auto; } }

                .cf-gallery { position:relative; width:100%; height:430px; perspective:1800px; }
                .cf-stage { position:relative; width:100%; height:100%; transform-style:preserve-3d; }
                .cf-card {
                    position:absolute; top:50%; left:50%; width:300px; height:380px; margin:-190px 0 0 -150px;
                    border-radius:22px; overflow:hidden; text-decoration:none; cursor:pointer;
                    background-color:#e2e8f0; background-size:cover; background-position:center; background-repeat:no-repeat;
                    box-shadow:0 30px 60px rgba(0,0,0,.28);
                    transition:transform .6s cubic-bezier(.16,1,.3,1), opacity .55s ease, box-shadow .4s ease;
                    will-change:transform, opacity;
                }
                .cf-card::after { content:''; position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,.82) 0%, rgba(0,0,0,.15) 45%, transparent 72%); }
                .cf-card.is-active { box-shadow:0 44px 80px rgba(10,92,47,.32); }
                .cf-card-info { position:absolute; left:0; right:0; bottom:0; padding:22px 20px; z-index:2; color:#fff; }
                .cf-tag { display:inline-block; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; background:rgba(255,255,255,.2); -webkit-backdrop-filter:blur(6px); backdrop-filter:blur(6px); padding:4px 11px; border-radius:50px; margin-bottom:10px; }
                .cf-name { font-size:21px; font-weight:800; letter-spacing:-.3px; line-height:1.2; text-shadow:0 2px 10px rgba(0,0,0,.5); }
                .cf-loc { display:flex; align-items:center; gap:5px; margin-top:6px; font-size:13px; color:rgba(255,255,255,.85); }
                .cf-loc svg { width:13px; height:13px; flex-shrink:0; }

                .cf-arrow { position:absolute; top:50%; transform:translateY(-50%); z-index:50; width:52px; height:52px; border-radius:50%; border:none; cursor:pointer; background:var(--white); color:var(--cvsu-green); display:flex; align-items:center; justify-content:center; box-shadow:0 10px 30px rgba(0,0,0,.15); transition:all .3s ease; }
                .cf-arrow:hover { transform:translateY(-50%) scale(1.1); box-shadow:0 14px 36px rgba(0,0,0,.22); }
                .cf-arrow svg { width:22px; height:22px; }
                .cf-prev { left:max(8px, calc(50% - 580px)); }
                .cf-next { right:max(8px, calc(50% - 580px)); }

                .cf-foot { display:flex; flex-direction:column; align-items:center; gap:14px; margin-top:34px; }
                .cf-dots { display:flex; gap:8px; }
                .cf-dot { width:8px; height:8px; border-radius:50%; border:none; cursor:pointer; background:#cbd5e1; padding:0; transition:all .3s ease; }
                .cf-dot.active { background:var(--cvsu-green); width:26px; border-radius:50px; }
                .cf-hint { font-size:13px; color:var(--gray-600); font-weight:500; letter-spacing:.3px; }

                @media (max-width:768px){
                    .cf-gallery { height:360px; }
                    .cf-card { width:228px; height:300px; margin:-150px 0 0 -114px; }
                    .cf-arrow { width:44px; height:44px; }
                    .cf-prev { left:6px; } .cf-next { right:6px; }
                }
            </style>

            <div class="cf-header">
                <h2 class="cf-title">{{ \App\Models\SiteSetting::get('showcase_title', 'Campus Concessionaires') }}</h2>
                <div class="cf-intro">
                    <p>{{ \App\Models\SiteSetting::get('showcase_subtitle', 'These are our listed concessionaires that are currently partnered with the campus. They provide quality food, services, and products directly to students and staff.') }}</p>
                    <a href="{{ route('concessionaires.index') }}" class="btn btn-primary" style="padding:14px 30px; font-size:16px;">
                        View All Concessionaires
                    </a>
                </div>
            </div>

            @if($showcaseConcessionaires->count() > 0)
                <div class="cf-gallery" id="cfGallery">
                    <div class="cf-stage" id="cfStage">
                        @foreach($showcaseConcessionaires as $i => $c)
                            @php
                                $ccImg = $c->carousel_image ?: ($c->cover_photo ?: $c->profile_photo);
                                $ccName = $c->business_name ?: $c->name;
                                $ccBg = $ccImg
                                    ? "url('" . asset('storage/' . $ccImg) . "')"
                                    : $ccGradients[$i % count($ccGradients)];
                            @endphp
                            <a class="cf-card{{ $i === 0 ? ' is-active' : '' }}" data-index="{{ $i }}"
                               href="{{ route('concessionaires.show', $c) }}"
                               style="background-image:{{ $ccBg }};">
                                <div class="cf-card-info">
                                    <span class="cf-tag">Concessionaire</span>
                                    <div class="cf-name">{{ $ccName }}</div>
                                    @if($c->location)
                                    <div class="cf-loc">
                                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $c->location }}
                                    </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if($showcaseConcessionaires->count() > 1)
                    <button type="button" class="cf-arrow cf-prev" onclick="cfMove(-1)" aria-label="Previous concessionaire">
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" class="cf-arrow cf-next" onclick="cfMove(1)" aria-label="Next concessionaire">
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    @endif
                </div>

                <div class="cf-foot">
                    @if($showcaseConcessionaires->count() > 1)
                    <div class="cf-dots" id="cfDots">
                        @foreach($showcaseConcessionaires as $i => $c)
                            <button type="button" class="cf-dot{{ $i === 0 ? ' active' : '' }}" onclick="cfGo({{ $i }})" aria-label="Go to slide {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                    @endif
                    <span class="cf-hint">Click a card or use the arrows to explore</span>
                </div>

                <script>
                    (function () {
                        var stage = document.getElementById('cfStage');
                        if (!stage) return;
                        var gallery = document.getElementById('cfGallery');
                        var cards = Array.prototype.slice.call(stage.querySelectorAll('.cf-card'));
                        var dots  = Array.prototype.slice.call(document.querySelectorAll('#cfDots .cf-dot'));
                        var total = cards.length, active = 0, timer = null;

                        function layout() {
                            var w = gallery.offsetWidth;
                            var gap = Math.max(120, Math.min(320, w * 0.27));
                            cards.forEach(function (card, i) {
                                var o = i - active;
                                var abs = Math.abs(o);
                                var tx = o * gap;
                                var rot = Math.max(-38, Math.min(38, -o * 26));
                                var tz = -abs * 110;
                                var sc = Math.max(0.7, 1 - abs * 0.08);
                                card.style.transform = 'translateX(' + tx + 'px) translateZ(' + tz + 'px) rotateY(' + rot + 'deg) scale(' + sc + ')';
                                card.style.opacity = abs > 2 ? 0 : 1;
                                card.style.zIndex = 100 - abs;
                                card.style.pointerEvents = abs > 2 ? 'none' : 'auto';
                                card.classList.toggle('is-active', i === active);
                            });
                            dots.forEach(function (d, i) { d.classList.toggle('active', i === active); });
                        }
                        function restart() {
                            if (timer) clearInterval(timer);
                            if (total > 1) timer = setInterval(function () { active = (active + 1) % total; layout(); }, 5000);
                        }
                        window.cfMove = function (dir) { active = (active + dir + total) % total; layout(); restart(); };
                        window.cfGo   = function (i) { active = i; layout(); restart(); };

                        // Clicking a side card recenters it; the active card follows its link.
                        cards.forEach(function (card, i) {
                            card.addEventListener('click', function (e) {
                                if (i !== active) { e.preventDefault(); cfGo(i); }
                            });
                        });

                        gallery.addEventListener('mouseenter', function () { if (timer) clearInterval(timer); });
                        gallery.addEventListener('mouseleave', restart);
                        window.addEventListener('resize', layout);
                        layout(); restart();
                    })();
                </script>
            @else
                <div style="display:flex; align-items:center; justify-content:center; min-height:300px; border-radius:22px; background:linear-gradient(135deg,#0A5C2F,#0D7A3E); color:var(--white); text-align:center; padding:24px;">
                    <div>
                        <div style="font-size:22px; font-weight:800;">Concessionaires Coming Soon</div>
                        <div style="margin-top:8px; opacity:.85;">Approved campus partners will appear here automatically.</div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- ===== ABOUT ===== -->
    <section class="about" id="about" style="background: var(--white); padding: 80px 24px;">
        <div class="section-container">
            <div class="about-grid" style="align-items: center; gap: 60px;">
                <div class="about-image">
                    <img src="{{ \App\Models\SiteSetting::image('about_image', asset('images/sample picture.jpg')) }}" alt="About EBA" style="border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                    <div class="about-image-accent"></div>
                </div>
                <div class="about-text">
                    <h2 style="font-size: clamp(32px, 4vw, 56px); font-weight: 900; line-height: 1.1; letter-spacing: -1.5px; color: var(--gray-900); margin-bottom: 20px;">
                        Built to <span class="text-green-700">Empower</span> <br>Our Campus.
                    </h2>
                    <p style="font-size: 18px; color: var(--gray-600); line-height: 1.6; margin-bottom: 40px; max-width: 520px;">
                        A modern and streamlined platform for the External and Business Affairs Office to handle partnerships, products, and campus services with ease.
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <!-- Feature 1 -->
                        <div style="display: flex; align-items: flex-start; gap: 16px; background: var(--gray-50); padding: 20px; border-radius: 16px; border: 1px solid var(--gray-200);">
                            <div style="width: 52px; height: 52px; background: var(--white); border-radius: 12px; display: flex; justify-content: center; align-items: center; color: var(--cvsu-green); box-shadow: 0 4px 10px rgba(0,0,0,0.05); flex-shrink: 0;">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <span style="display: block; font-weight: 800; font-size: 18px; color: var(--gray-900); margin-bottom: 4px;">Faster Applications</span>
                                <span style="font-size: 15px; color: var(--gray-600); line-height: 1.5;">Apply for partnerships and upload requirements directly through our online portal. No more endless paperwork.</span>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div style="display: flex; align-items: flex-start; gap: 16px; background: var(--gray-50); padding: 20px; border-radius: 16px; border: 1px solid var(--gray-200);">
                            <div style="width: 52px; height: 52px; background: var(--white); border-radius: 12px; display: flex; justify-content: center; align-items: center; color: var(--cvsu-green); box-shadow: 0 4px 10px rgba(0,0,0,0.05); flex-shrink: 0;">
                               <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <span style="display: block; font-weight: 800; font-size: 18px; color: var(--gray-900); margin-bottom: 4px;">Centralized Marketplace</span>
                                <span style="font-size: 15px; color: var(--gray-600); line-height: 1.5;">Give students and staff instant access to browse concessionaire products and university uniform stocks.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FAQ ===== -->
    <section class="faq" id="faq" style="background: white; padding: 100px 24px;">
        <div class="section-container" style="max-width: 1200px; margin: 0 auto;">
            <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 60px; align-items: center;">
                
                <!-- FAQ Left Content -->
                <div>
                    <div style="margin-bottom: 40px;">
                        <span class="section-label">Got Questions?</span>
                        <h2 style="font-size: clamp(32px, 4vw, 48px); font-weight: 900; letter-spacing: -1px; color: var(--gray-900); margin-bottom: 16px;">
                            Frequently Asked Questions
                        </h2>
                        <p style="font-size: 17px; color: var(--gray-600); line-height: 1.6;">
                            Everything you need to know about the External and Business Affairs Information System.
                        </p>
                    </div>

                    @php
                        $faqDefaults = [
                            1 => ['How do I apply for a concessionaire partnership?', 'Simply create an account on our platform. Once registered, navigate to the application portal where you can fill out the required information and upload necessary documents like your MOA, Contract, and Business Proposal securely.'],
                            2 => ['Who can view and review products in the marketplace?', 'Anyone can browse the marketplace to check available products and concessionaires. However, only registered and approved students and faculty members can submit ratings and leave reviews to ensure feedback authenticity.'],
                            3 => ['How do concessionaires track their payment records?', 'Approved concessionaires have access to an exclusive dashboard where they can view their payment history, upcoming fixed monthly deadlines, and any overdue balances synced directly from the Cashier module.'],
                            4 => ['Is the system restricted to just concessionaires?', 'No! While concessionaires use it for managing store offerings and checking balances, the system is actively used by students to check marketplace availability and by campus staff to monitor uniform stocks safely and securely.'],
                        ];
                    @endphp
                    <div class="faq-container" style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($faqDefaults as $i => $faq)
                            @php
                                $faqQuestion = \App\Models\SiteSetting::get('faq_'.$i.'_question', $faq[0]);
                                $faqAnswer   = \App\Models\SiteSetting::get('faq_'.$i.'_answer', $faq[1]);
                            @endphp
                            @if($faqQuestion)
                            <!-- FAQ {{ $i }} -->
                            <details style="background: var(--white); border-radius: 12px; border: 1px solid var(--gray-200); box-shadow: 0 2px 8px rgba(0,0,0,0.02); overflow: hidden; cursor: pointer;">
                                <summary style="padding: 20px; font-size: 16px; font-weight: 700; color: var(--gray-900); display: flex; justify-content: space-between; align-items: center; list-style: none;">
                                    {{ $faqQuestion }}
                                    <span style="color: var(--cvsu-green); font-size: 20px; font-weight: 400;">+</span>
                                </summary>
                                <div style="padding: 0 20px 20px; font-size: 15px; color: var(--gray-600); line-height: 1.6;">
                                    {{ $faqAnswer }}
                                </div>
                            </details>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- FAQ Right Visual -->
                <div style="display: flex; justify-content: center; align-items: center; position: relative; padding: 20px;">
                    <!-- Decorative Background Blob -->
                    <div style="position: absolute; width: 100%; max-width: 580px; height: 100%; background: linear-gradient(135deg, var(--cvsu-green) 0%, var(--cvsu-gold) 100%); opacity: 0.15; border-radius: 32px; transform: rotate(3deg); z-index: 0;"></div>
                    
                    <!-- Image Styling -->
                    <img src="{{ \App\Models\SiteSetting::image('faq_image', asset('images/vector-2.jpg')) }}" alt="FAQ" style="position: relative; z-index: 1; width: 100%; max-width: 600px; border-radius: 24px; box-shadow: 0 24px 48px rgba(0, 0, 0, 0.12); border: 8px solid var(--white); object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                </div>
                
            </div>
            <style>
                @media (max-width: 992px) {
                    .faq .section-container > div {
                        grid-template-columns: 1fr !important;
                    }
                    .faq .section-container img {
                        max-width: 400px;
                        margin-top: 40px;
                    }
                }
            </style>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="cta">
        <div class="cta-pattern"></div>
        <div class="cta-content">
            <h2>Ready to Get Started?</h2>
            <p>
                Join the platform today and experience a streamlined, efficient, and transparent 
                way to manage external and business affairs at CvSU &mdash; Trece Martires City Campus.
            </p>
            <div class="cta-actions">
                @guest
                    <a href="{{ route('register') }}" class="btn btn-gold">Create an Account</a>
                    <a href="{{ route('login') }}" class="btn btn-white-outline">Log In</a>
                @else
                    <a href="{{ route('products.index') }}" class="btn btn-gold">Browse Products</a>
                    <a href="{{ route('concessionaires.index') }}" class="btn btn-white-outline">View Concessionaires</a>
                @endguest
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand">
                        <img src="{{ asset('images/eba-logo.png') }}" alt="EBA Logo">
                    </div>
                    <p class="footer-desc">
                        A web-based platform for the External and Business Affairs Office of 
                        Cavite State University &mdash; Trece Martires City Campus. Streamlining partnerships, 
                        products, and business operations.
                    </p>
                </div>

                <div>
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="/#about">About</a></li>
                        <li><a href="{{ route('products.index') }}">Products</a></li>
                        <li><a href="{{ route('concessionaires.index') }}">Concessionaires</a></li>
                        <li><a href="{{ route('login') }}">Log In</a></li>
                        <li><a href="{{ route('partnership.apply') }}">Partner With Us</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-heading">Services</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('partnership.apply') }}">Partnership Applications</a></li>
                        <li><a href="{{ route('products.index') }}">Browse Products</a></li>
                        <li><a href="{{ route('products.index') }}">Uniform Stocks</a></li>
                        <li><a href="{{ route('concessionaires.index') }}">Concessionaire Directory</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-heading">Contact</h4>
                    <ul class="footer-links">
                        <li><a href="#">CvSU-TMC Campus</a></li>
                        <li><a href="#">Trece Martires City</a></li>
                        <li><a href="#">Cavite, Philippines</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} CvSU &mdash; Trece Martires City Campus. All rights reserved.</span>
                <span>External & Business Affairs Office</span>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        // Mobile menu toggle
        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    document.getElementById('navLinks')?.classList.remove('active');
                }
            });
        });

        // Enhanced Intersection Observer with staggered animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0) scale(1) rotate(0deg)';
                    }, index * 100);
                    
                    // Unobserve after animation to improve performance
                    animationObserver.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all animated elements
        document.querySelectorAll('.feature-card, .about-text, .about-image, .vmv-card, .vmv-header, .showcase-text, .showcase-visual').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(40px)';
            el.style.transition = 'all 0.8s cubic-bezier(0.16, 1, 0.3, 1)';
            animationObserver.observe(el);
        });

        // Add interactive hover effect to feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-12px) scale(1.03)`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
            });
        });

        // Stagger fade-in for hero elements
        const heroElements = document.querySelectorAll('.hero-text, .hero-image-wrap');
        heroElements.forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            setTimeout(() => {
                el.style.transition = 'all 1s cubic-bezier(0.16, 1, 0.3, 1)';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, 200 + (i * 200));
        });

        // Add shimmer effect on buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.overflow = 'hidden';
            });
        });

        // Counter animation (if needed for stats)
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            const timer = setInterval(() => {
                start += increment;
                if (start >= target) {
                    element.textContent = target;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(start);
                }
            }, 16);
        }

        // Add ripple effect to buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255, 255, 255, 0.6)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s ease-out';
                ripple.style.pointerEvents = 'none';
                
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>

</body>
    @include('partials.pending-application-banner')

</html>
