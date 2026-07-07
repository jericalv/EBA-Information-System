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
            --line-dark: rgba(255, 255, 255, 0.14);
            --font-display: 'Inter', 'Manrope', system-ui, sans-serif;
            --font-body: 'Manrope', system-ui, -apple-system, sans-serif;
            --font-mono: 'IBM Plex Mono', 'SFMono-Regular', Consolas, monospace;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            color: var(--ink);
            line-height: 1.6;
            background: var(--paper);
            overflow-x: hidden;
        }

        img { max-width: 100%; }

        a:focus-visible, button:focus-visible, summary:focus-visible {
            outline: 2px solid var(--green);
            outline-offset: 3px;
            border-radius: 2px;
        }

        .wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ===== Shared type ===== */
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

        .sec-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(30px, 4vw, 46px);
            line-height: 1.06;
            letter-spacing: -0.8px;
            color: var(--ink);
        }

        .sec-sub {
            font-size: 16px;
            color: var(--ink-soft);
            line-height: 1.75;
            max-width: 560px;
        }

        .text-green-700 { color: var(--green); }

        /* ===== Buttons (portal language: 6px radius) ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 13px 24px;
            font-family: var(--font-body);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.1px;
            text-decoration: none;
            border-radius: 6px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background-color .2s ease, border-color .2s ease, color .2s ease, box-shadow .2s ease;
        }

        .btn svg { width: 15px; height: 15px; flex-shrink: 0; }

        .btn-primary {
            background: var(--green);
            color: #fff;
        }
        .btn-primary:hover {
            background: var(--green-bright);
            box-shadow: 0 6px 18px rgba(10, 92, 47, 0.25);
        }

        .btn-ghost {
            background: transparent;
            color: var(--ink);
            border-color: var(--line-strong);
        }
        .btn-ghost:hover {
            border-color: var(--green);
            color: var(--green);
            background: rgba(10, 92, 47, 0.04);
        }

        .btn-gold {
            background: var(--gold);
            color: var(--green-deep);
        }
        .btn-gold:hover {
            background: var(--gold-soft);
            box-shadow: 0 6px 18px rgba(201, 154, 46, 0.3);
        }

        .btn-white-outline {
            background: transparent;
            color: #fff;
            border-color: rgba(255, 255, 255, 0.35);
        }
        .btn-white-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.7);
        }

        /* ===== Navbar ===== */
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
        .nav-brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }
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

        /* ===== Flow-line artwork (decorative) ===== */
        .flow-img {
            position: absolute;
            pointer-events: none;
            user-select: none;
            z-index: 0;
        }
        .flow-hero-tr {
            top: -60px;
            right: -150px;
            width: 940px;
            opacity: 0.36;
            transform: rotate(-14deg);
        }
        .flow-hero-bl {
            bottom: -130px;
            left: -200px;
            width: 720px;
            opacity: 0.28;
            transform: rotate(10deg);
        }
        .flow-uniforms {
            top: -40px;
            right: -220px;
            width: 780px;
            opacity: 0.2;
            transform: rotate(16deg);
        }
        .flow-features {
            bottom: -120px;
            left: -180px;
            width: 720px;
            opacity: 0.18;
            transform: rotate(-9deg);
        }
        .flow-vmv {
            top: -60px;
            right: -160px;
            width: 780px;
            opacity: 0.14;
            transform: rotate(12deg);
        }
        .flow-faq {
            bottom: -100px;
            left: -200px;
            width: 700px;
            opacity: 0.17;
            transform: rotate(-12deg);
        }
        .flow-cta {
            top: -60px;
            right: -100px;
            width: 780px;
            opacity: 0.22;
            transform: rotate(-11deg);
        }
        @media (max-width: 768px) {
            .flow-hero-tr { width: 560px; right: -180px; }
            .flow-hero-bl, .flow-features, .flow-faq { display: none; }
            .flow-uniforms, .flow-vmv { width: 480px; right: -180px; }
            .flow-cta { width: 520px; }
        }

        /* ===== Hero ===== */
        .hero {
            position: relative;
            padding: 150px 0 0;
            background: var(--paper);
            overflow: hidden;
        }
        .hero > .wrap, .hero .registry-strip { position: relative; z-index: 1; }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 56px;
            align-items: center;
            padding-bottom: 64px;
        }

        .hero-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(38px, 5.4vw, 66px);
            line-height: 1.02;
            letter-spacing: -1.6px;
            color: var(--ink);
            margin: 18px 0 20px;
        }

        .hero-description {
            font-size: 17px;
            line-height: 1.75;
            color: var(--ink-soft);
            max-width: 520px;
            margin-bottom: 30px;
        }

        .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

        /* Framed specimen panel — reused for hero / about / faq imagery */
        .frame-panel {
            position: relative;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 24px 60px rgba(24, 36, 32, 0.09);
        }
        .frame-panel::before,
        .frame-panel::after {
            content: '';
            position: absolute;
            width: 26px; height: 26px;
            border-color: var(--gold);
            border-style: solid;
            pointer-events: none;
        }
        .frame-panel::before { top: -7px; left: -7px; border-width: 2px 0 0 2px; }
        .frame-panel::after { bottom: -7px; right: -7px; border-width: 0 2px 2px 0; }
        .frame-panel-media {
            border-radius: 7px;
            background-color: var(--paper-deep);
            background-image: radial-gradient(rgba(24, 36, 32, 0.12) 1px, transparent 1px);
            background-size: 16px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            min-height: 300px;
        }
        .frame-panel-media img {
            width: 100%;
            max-height: 440px;
            object-fit: contain;
        }
        .frame-panel-caption {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 4px 0;
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--ink-faint);
        }

        /* Registry strip — live counts under the hero */
        .registry-strip {
            border-top: 1px solid var(--line-strong);
            border-bottom: 1px solid var(--line-strong);
            background: var(--paper);
        }
        .registry-strip-inner {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
        .strip-item {
            padding: 22px 28px;
            border-left: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .strip-item:first-child { border-left: none; padding-left: 4px; }
        .strip-num {
            font-family: var(--font-mono);
            font-size: 26px;
            font-weight: 600;
            color: var(--ink);
            line-height: 1.1;
        }
        .strip-num .strip-unit { color: var(--gold); }
        .strip-label {
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 500;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: var(--ink-faint);
        }

        /* ===== Section shell ===== */
        .sec { padding: 96px 0; }
        .sec, .uniforms-sec {
            position: relative;
            overflow: hidden;
            overflow: clip;
        }
        .sec > .wrap, .uniforms-sec > .wrap { position: relative; z-index: 1; }
        .sec-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 32px;
            margin-bottom: 44px;
        }
        .sec-head-main { max-width: 640px; }
        .sec-head-main .sec-title { margin: 14px 0 12px; }
        .sec-link {
            font-family: var(--font-mono);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--green);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid var(--gold);
            white-space: nowrap;
            transition: gap .2s ease, color .2s ease;
        }
        .sec-link:hover { gap: 12px; color: var(--green-bright); }
        .sec-link svg { width: 13px; height: 13px; }

        /* ===== Uniform stock rail (signature) ===== */
        .uniforms-sec {
            background: var(--paper);
            padding: 96px 0 88px;
        }

        .rail-shell { position: relative; }
        .rail {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: 272px;
            gap: 18px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding: 4px 4px 22px;
            scrollbar-width: none;
        }
        .rail::-webkit-scrollbar { display: none; }

        .rail-controls {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .rail-btn {
            width: 40px; height: 40px;
            border-radius: 6px;
            border: 1px solid var(--line-strong);
            background: var(--card);
            color: var(--ink);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: border-color .2s ease, color .2s ease, background-color .2s ease;
        }
        .rail-btn:hover { border-color: var(--green); color: var(--green); }
        .rail-btn:disabled { opacity: 0.35; cursor: default; }
        .rail-btn:disabled:hover { border-color: var(--line-strong); color: var(--ink); }
        .rail-btn svg { width: 16px; height: 16px; }
        .rail-counter {
            font-family: var(--font-mono);
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 1.5px;
            color: var(--ink-faint);
            min-width: 72px;
            text-align: center;
        }
        .rail-counter b { color: var(--ink); font-weight: 600; }

        .stock-card {
            scroll-snap-align: start;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            color: var(--ink);
            display: flex;
            flex-direction: column;
            transition: transform .3s cubic-bezier(0.16, 1, 0.3, 1), border-color .3s ease, box-shadow .3s ease;
        }
        .stock-card:hover {
            transform: translateY(-5px);
            border-color: rgba(10, 92, 47, 0.4);
            box-shadow: 0 18px 40px rgba(24, 36, 32, 0.12);
        }

        .stock-media {
            position: relative;
            aspect-ratio: 5 / 4.5;
            background-color: var(--paper-deep);
            background-image: radial-gradient(rgba(24, 36, 32, 0.1) 1px, transparent 1px);
            background-size: 15px 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--line);
            overflow: hidden;
        }
        .stock-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .stock-card:hover .stock-media img { transform: scale(1.045); }
        .stock-media .stock-media-placeholder {
            width: 56px; height: 56px;
            color: var(--line-strong);
        }

        .stock-status {
            position: absolute;
            top: 12px; left: 12px;
            z-index: 2;
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--line);
            border-radius: 4px;
            padding: 5px 9px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--ink-soft);
        }
        .stock-status .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #16A34A;
        }
        .stock-status.low .dot { background: #D97706; }
        .stock-status.out .dot { background: #DC2626; }
        .stock-card.is-out .stock-media img { filter: grayscale(0.75); opacity: 0.75; }

        .stock-body { padding: 16px 16px 14px; flex: 1; }
        .stock-kicker {
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 6px;
        }
        .stock-name {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.2px;
            line-height: 1.25;
            margin-bottom: 12px;
        }

        .size-run { display: flex; flex-wrap: wrap; gap: 5px; }
        .size-chip {
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 500;
            padding: 3px 7px;
            border: 1px solid var(--line);
            border-radius: 4px;
            background: var(--paper);
            color: var(--ink-soft);
        }
        .size-chip.empty {
            color: var(--line-strong);
            text-decoration: line-through;
            background: transparent;
        }

        .stock-foot {
            border-top: 1px solid var(--line);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .stock-price {
            font-family: var(--font-mono);
            font-size: 14px;
            font-weight: 600;
            color: var(--green);
            white-space: nowrap;
        }
        .stock-price .from {
            font-size: 9.5px;
            font-weight: 500;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--ink-faint);
            margin-right: 4px;
        }
        .stock-view {
            font-size: 12px;
            font-weight: 700;
            color: var(--ink-faint);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color .2s ease;
        }
        .stock-view svg { width: 12px; height: 12px; }
        .stock-card:hover .stock-view { color: var(--green); }

        .rail-empty {
            border: 1px dashed var(--line-strong);
            border-radius: 12px;
            background: var(--card);
            padding: 56px 24px;
            text-align: center;
        }
        .rail-empty h3 {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .rail-empty p { color: var(--ink-soft); font-size: 14.5px; margin-bottom: 20px; }

        /* ===== Concessionaires marquee ===== */
        .cc-sec {
            background: var(--card-soft);
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }

        .mq {
            overflow: hidden;
            position: relative;
            margin-top: 8px;
        }
        .mq::before, .mq::after {
            content: '';
            position: absolute;
            top: 0; bottom: 0;
            width: 70px;
            z-index: 2;
            pointer-events: none;
        }
        .mq::before { left: 0; background: linear-gradient(to right, var(--card-soft), transparent); }
        .mq::after { right: 0; background: linear-gradient(to left, var(--card-soft), transparent); }

        .mq-track {
            display: flex;
            gap: 18px;
            width: max-content;
            animation: mq-drift 46s linear infinite;
        }
        .mq:hover .mq-track { animation-play-state: paused; }
        @keyframes mq-drift {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        .cc-static {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 300px));
            justify-content: center;
            gap: 18px;
            margin-top: 8px;
        }

        .cc-card {
            position: relative;
            width: 285px;
            aspect-ratio: 3 / 3.8;
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            border: 1px solid var(--line);
            background-color: var(--paper-deep);
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: flex-end;
            flex-shrink: 0;
        }
        .cc-static .cc-card { width: auto; }
        .cc-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(7, 52, 28, 0.88) 0%, rgba(7, 52, 28, 0.25) 45%, transparent 68%);
            transition: background .3s ease;
        }
        .cc-card:hover::before {
            background: linear-gradient(to top, rgba(7, 52, 28, 0.95) 0%, rgba(7, 52, 28, 0.35) 50%, rgba(7, 52, 28, 0.06) 75%);
        }
        .cc-card-info {
            position: relative;
            z-index: 1;
            padding: 18px;
            color: #fff;
            width: 100%;
        }
        .cc-tag {
            font-family: var(--font-mono);
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: var(--gold-soft);
            display: block;
            margin-bottom: 7px;
        }
        .cc-name {
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 700;
            letter-spacing: -0.2px;
            line-height: 1.2;
        }
        .cc-loc {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.82);
        }
        .cc-loc svg { width: 12px; height: 12px; flex-shrink: 0; }

        .cc-empty {
            border: 1px dashed var(--line-strong);
            border-radius: 12px;
            padding: 56px 24px;
            text-align: center;
            color: var(--ink-soft);
        }
        .cc-empty h3 {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
        }

        /* ===== Features ===== */
        .features-sec { background: var(--paper); }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }
        .feature-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 30px 26px;
            display: flex;
            flex-direction: column;
            transition: transform .3s cubic-bezier(0.16, 1, 0.3, 1), border-color .3s ease, box-shadow .3s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            border-color: rgba(10, 92, 47, 0.35);
            box-shadow: 0 16px 36px rgba(24, 36, 32, 0.1);
        }
        .feature-icon {
            width: 46px; height: 46px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(10, 92, 47, 0.08);
            border: 1px solid rgba(10, 92, 47, 0.14);
            margin-bottom: 22px;
        }
        .feature-icon svg { width: 22px; height: 22px; color: var(--green); }
        .feature-title {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.2px;
            margin-bottom: 10px;
        }
        .feature-desc {
            font-size: 14.5px;
            color: var(--ink-soft);
            line-height: 1.75;
        }

        .feature-card.spotlight {
            background: var(--green-deep);
            border-color: var(--green-deep);
        }
        .feature-card.spotlight:hover { border-color: var(--gold); }
        .feature-card.spotlight .feature-icon {
            background: rgba(255, 255, 255, 0.09);
            border-color: rgba(228, 195, 107, 0.4);
        }
        .feature-card.spotlight .feature-icon svg { color: var(--gold-soft); }
        .feature-card.spotlight .feature-title { color: #fff; }
        .feature-card.spotlight .feature-desc { color: rgba(255, 255, 255, 0.78); }

        /* ===== About ===== */
        .about-sec { background: var(--card-soft); border-top: 1px solid var(--line); }
        .about-grid {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 64px;
            align-items: center;
        }
        .about-text .sec-title { margin: 14px 0 16px; }
        .about-text > p {
            font-size: 16px;
            color: var(--ink-soft);
            line-height: 1.8;
            margin-bottom: 32px;
            max-width: 520px;
        }
        .ledger-items { display: flex; flex-direction: column; }
        .ledger-item {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding: 20px 4px;
            border-top: 1px solid var(--line);
        }
        .ledger-item:last-child { border-bottom: 1px solid var(--line); }
        .ledger-item-icon {
            width: 40px; height: 40px;
            flex-shrink: 0;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: var(--paper);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ledger-item-icon svg { width: 19px; height: 19px; color: var(--green); }
        .ledger-item strong {
            display: block;
            font-family: var(--font-display);
            font-size: 16.5px;
            font-weight: 700;
            margin-bottom: 3px;
        }
        .ledger-item span {
            font-size: 14px;
            color: var(--ink-soft);
            line-height: 1.65;
        }

        /* ===== Vision / Mission / Values ===== */
        .vmv-sec {
            background: var(--green-deep);
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .vmv-sec::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 22px 22px;
            pointer-events: none;
        }
        .vmv-inner { position: relative; z-index: 1; }
        .vmv-head { margin-bottom: 48px; max-width: 640px; }
        .vmv-head .eyebrow { color: var(--gold-soft); }
        .vmv-head .sec-title { color: #fff; margin: 14px 0 12px; }
        .vmv-head p { color: rgba(255, 255, 255, 0.72); font-size: 15.5px; }

        .vmv-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
        .vmv-col {
            padding: 6px 32px 6px 0;
            border-left: 1px solid var(--line-dark);
            padding-left: 32px;
        }
        .vmv-col:first-child { border-left: none; padding-left: 0; }
        .vmv-col h3 {
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gold-soft);
            margin-bottom: 18px;
        }
        .vmv-col > p {
            font-size: 14.5px;
            line-height: 1.85;
            color: rgba(255, 255, 255, 0.85);
        }
        .core-values-list { display: flex; flex-direction: column; gap: 14px; }
        .core-value-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .core-value-item svg {
            width: 15px; height: 15px;
            color: var(--gold);
            flex-shrink: 0;
        }
        .core-value-item strong {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
        }

        /* ===== FAQ ===== */
        .faq-sec { background: var(--paper); }
        .faq-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 64px;
            align-items: start;
        }
        .faq-intro { margin-bottom: 36px; }
        .faq-intro .sec-title { margin: 14px 0 12px; }

        .faq-list { display: flex; flex-direction: column; gap: 12px; }
        .faq-list details {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: hidden;
            transition: border-color .25s ease;
        }
        .faq-list details:hover { border-color: rgba(10, 92, 47, 0.35); }
        .faq-list details[open] { border-color: rgba(10, 92, 47, 0.45); }
        .faq-list summary {
            cursor: pointer;
            user-select: none;
            list-style: none;
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            font-size: 15px;
            font-weight: 700;
            color: var(--ink);
        }
        .faq-list summary::-webkit-details-marker { display: none; }
        .faq-marker {
            font-family: var(--font-mono);
            font-size: 16px;
            font-weight: 500;
            color: var(--green);
            flex-shrink: 0;
            width: 18px;
            text-align: center;
        }
        .faq-marker::before { content: '+'; }
        .faq-list details[open] .faq-marker::before { content: '−'; }
        .faq-answer {
            padding: 0 20px 20px;
            font-size: 14.5px;
            color: var(--ink-soft);
            line-height: 1.75;
        }
        .faq-visual { position: sticky; top: 100px; }

        /* ===== CTA ===== */
        .cta-sec {
            background: var(--green-deep);
            position: relative;
            overflow: hidden;
        }
        .cta-sec::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 22px 22px;
            pointer-events: none;
        }
        .cta-inner {
            position: relative;
            z-index: 1;
            max-width: 680px;
            margin: 0 auto;
            text-align: center;
            color: #fff;
        }
        .cta-inner .eyebrow {
            color: var(--gold-soft);
            justify-content: center;
        }
        .cta-inner .eyebrow::after {
            content: '';
            width: 22px; height: 1px;
            background: var(--gold);
            flex-shrink: 0;
        }
        .cta-inner h2 {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(30px, 4vw, 46px);
            letter-spacing: -0.8px;
            line-height: 1.08;
            margin: 16px 0 14px;
        }
        .cta-inner p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.75;
            margin-bottom: 32px;
        }
        .cta-actions { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }

        /* ===== Footer ===== */
        .footer {
            background: var(--ink);
            color: rgba(255, 255, 255, 0.55);
            padding: 64px 0 32px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 48px;
        }
        .footer-brand img {
            height: 52px;
            width: auto;
            max-width: 220px;
            object-fit: contain;
            display: block;
            margin-bottom: 16px;
        }
        .footer-desc { font-size: 14px; line-height: 1.75; max-width: 340px; }
        .footer-heading {
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 600;
            color: var(--gold-soft);
            text-transform: uppercase;
            letter-spacing: 1.8px;
            margin-bottom: 20px;
        }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 11px; }
        .footer-links a {
            color: rgba(255, 255, 255, 0.55);
            text-decoration: none;
            font-size: 14px;
            transition: color .2s ease;
        }
        .footer-links a:hover { color: var(--gold-soft); }
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            font-family: var(--font-mono);
            font-size: 11.5px;
            letter-spacing: 0.4px;
        }

        /* ===== Toast ===== */
        .toast {
            position: fixed;
            top: 88px; right: 24px;
            z-index: 2000;
            background: var(--card);
            border: 1px solid var(--line);
            border-left: 3px solid var(--green);
            border-radius: 8px;
            padding: 14px 18px;
            box-shadow: 0 14px 40px rgba(24, 36, 32, 0.16);
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 400px;
            animation: toast-in 0.4s ease, toast-out 0.4s ease 4.6s forwards;
        }
        .toast-icon {
            width: 26px; height: 26px;
            background: rgba(10, 92, 47, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .toast-icon svg { width: 13px; height: 13px; color: var(--green); }
        .toast-title { font-weight: 700; font-size: 13.5px; color: var(--ink); }
        .toast-message { font-size: 13px; color: var(--ink-soft); }
        .toast-close { background: none; border: none; cursor: pointer; padding: 4px; color: var(--ink-faint); }
        .toast-close:hover { color: var(--ink); }
        .toast-close svg { width: 14px; height: 14px; }
        @keyframes toast-in { from { transform: translateX(110%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes toast-out { from { opacity: 1; } to { opacity: 0; visibility: hidden; } }

        /* ===== Motion ===== */
        .hero-rise {
            opacity: 0;
            transform: translateY(24px);
            animation: rise 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .hero-rise.d1 { animation-delay: 0.08s; }
        .hero-rise.d2 { animation-delay: 0.18s; }
        .hero-rise.d3 { animation-delay: 0.28s; }
        .hero-rise.d4 { animation-delay: 0.4s; }
        @keyframes rise {
            to { opacity: 1; transform: translateY(0); }
        }

        .rv {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .rv.in { opacity: 1; transform: translateY(0); }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .hero-rise { animation: none; opacity: 1; transform: none; }
            .rv { opacity: 1; transform: none; transition: none; }
            .mq-track { animation: none; flex-wrap: wrap; width: auto; justify-content: center; }
            .mq-track a[aria-hidden="true"] { display: none; }
            .stock-card, .feature-card, .stock-media img { transition: none; }
        }

        /* ===== Scrollbar ===== */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--paper-deep); }
        ::-webkit-scrollbar-thumb { background: var(--line-strong); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--ink-faint); }

        /* ===== Responsive ===== */
        @media (max-width: 1024px) {
            .hero { padding-top: 128px; }
            .hero-grid { grid-template-columns: 1fr; gap: 40px; padding-bottom: 48px; }
            .hero-visual { max-width: 560px; margin: 0 auto; width: 100%; }
            .about-grid, .faq-grid { grid-template-columns: 1fr; gap: 44px; }
            .faq-visual { position: static; max-width: 520px; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .feature-card.spotlight { grid-column: span 2; }
            .vmv-grid { grid-template-columns: 1fr; gap: 36px; }
            .vmv-col { border-left: none; padding-left: 0; border-top: 1px solid var(--line-dark); padding-top: 30px; }
            .vmv-col:first-child { border-top: none; padding-top: 0; }
        }

        @media (max-width: 768px) {
            .sec { padding: 68px 0; }
            .uniforms-sec { padding: 68px 0 60px; }
            .sec-head { flex-direction: column; align-items: flex-start; gap: 20px; margin-bottom: 32px; }

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
                padding: 16px 24px 20px;
                border-bottom: 1px solid var(--line);
                box-shadow: 0 16px 40px rgba(24, 36, 32, 0.1);
                gap: 6px;
            }
            .nav-links.active .btn-login { margin-left: 0; text-align: center; justify-content: center; display: flex; }

            .registry-strip-inner { grid-template-columns: 1fr; }
            .strip-item { border-left: none; border-top: 1px solid var(--line); padding: 16px 4px; }
            .strip-item:first-child { border-top: none; }

            .features-grid { grid-template-columns: 1fr; }
            .feature-card.spotlight { grid-column: auto; }

            .rail { grid-auto-columns: 236px; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 36px; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }

        @media (max-width: 480px) {
            .footer-grid { grid-template-columns: 1fr; }
            .cc-card { width: 240px; }
        }
    </style>
</head>
<body>

    @if (session('success'))
    <div class="toast" id="toast" role="status">
        <div class="toast-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="toast-content">
            <div class="toast-title">Success!</div>
            <div class="toast-message">{{ session('success') }}</div>
        </div>
        <button class="toast-close" onclick="document.getElementById('toast').remove()" aria-label="Dismiss notification">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
                    @include('partials.public-nav-links', ['loginButtonClass' => 'btn btn-login'])
                </div>
            @endif
        </div>
    </nav>

    @php
        $activePartnerFilter = function ($q) {
            $q->where('role', 'concessionaire')
              ->where('is_active_concessionaire', true)
              ->where('is_approved', true);
        };

        $statProducts = \App\Models\Product::notDeleted()->available()
            ->whereHas('concessionaire', $activePartnerFilter)
            ->count();

        $statPartners = \App\Models\User::query()
            ->where('role', 'concessionaire')
            ->where('is_active_concessionaire', true)
            ->where('is_approved', true)
            ->count();

        $statUniforms = \App\Models\UniformStock::where('is_visible', true)->count();
    @endphp

    <!-- ===== HERO ===== -->
    <header class="hero">
        <img class="flow-img flow-hero-tr" src="{{ asset('images/flowlines1-green.png') }}" alt="" aria-hidden="true">
        <img class="flow-img flow-hero-bl" src="{{ asset('images/flowlines3-green.png') }}" alt="" aria-hidden="true">
        <div class="wrap">
            <div class="hero-grid">
                <div class="hero-text">
                    <span class="eyebrow hero-rise">External &amp; Business Affairs Office</span>
                    <h1 class="hero-title hero-rise d1">
                        {!! \App\Models\SiteSetting::get('hero_title', 'Your Campus Marketplace, All in One Place') !!}
                    </h1>
                    <p class="hero-description hero-rise d2">
                        {{ \App\Models\SiteSetting::get('hero_subtitle', 'Browse products, discover concessionaires, and stay connected with everything CvSU Trece Martires has to offer.') }}
                    </p>
                    <div class="hero-actions hero-rise d3">
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            Browse Products
                            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="{{ route('concessionaires.index') }}" class="btn btn-ghost">View Concessionaires</a>
                    </div>
                </div>

                <div class="hero-visual hero-rise d4">
                    <div class="frame-panel">
                        <div class="frame-panel-media">
                            <img src="{{ \App\Models\SiteSetting::image('hero_image', asset('images/vector-1-transparent.png')) }}" alt="EBA Campus Concessionaire System">
                        </div>
                        <div class="frame-panel-caption">
                            <span>CvSU &mdash; Trece Martires City Campus</span>
                            <span>EST. REGISTRY</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($statProducts > 0 || $statPartners > 0 || $statUniforms > 0)
        <div class="registry-strip hero-rise d4">
            <div class="wrap">
                <div class="registry-strip-inner">
                    <div class="strip-item">
                        <span class="strip-num">{{ number_format($statProducts) }}<span class="strip-unit">+</span></span>
                        <span class="strip-label">Products listed on campus</span>
                    </div>
                    <div class="strip-item">
                        <span class="strip-num">{{ number_format($statPartners) }}<span class="strip-unit">+</span></span>
                        <span class="strip-label">Active concessionaire partners</span>
                    </div>
                    <div class="strip-item">
                        <span class="strip-num">{{ number_format($statUniforms) }}<span class="strip-unit">+</span></span>
                        <span class="strip-label">Uniform &amp; item lines in stock</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </header>

    <!-- ===== UNIFORM STOCKS (live from the registry) ===== -->
    @php
        $sizeOrder = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];

        $landingUniforms = \App\Models\UniformStock::query()
            ->where('is_visible', true)
            ->where(function ($q) {
                $q->whereNull('item_type')->orWhere('item_type', '!=', 'books');
            })
            ->orderByRaw('(quantity > 0) desc')
            ->orderBy('item_name')
            ->take(10)
            ->get();
    @endphp
    <section class="uniforms-sec" id="uniforms">
        <img class="flow-img flow-uniforms" src="{{ asset('images/flowlines1-green.png') }}" alt="" aria-hidden="true" loading="lazy">
        <div class="wrap">
            <div class="sec-head rv">
                <div class="sec-head-main">
                    <span class="eyebrow">Registry / Uniform Stocks</span>
                    <h2 class="sec-title">{{ \App\Models\SiteSetting::get('uniforms_title', 'Campus uniforms, straight from the stockroom.') }}</h2>
                    <p class="sec-sub">{{ \App\Models\SiteSetting::get('uniforms_subtitle', 'The official uniform lines the office keeps on hand — sizes, prices, and availability, updated as the ledger moves. No account needed to browse.') }}</p>
                </div>
                <div class="rail-controls">
                    @if ($landingUniforms->count() > 0)
                    <button type="button" class="rail-btn" id="railPrev" aria-label="Scroll to previous items">
                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <span class="rail-counter" id="railCounter" aria-hidden="true"><b>01</b> / {{ str_pad($landingUniforms->count(), 2, '0', STR_PAD_LEFT) }}</span>
                    <button type="button" class="rail-btn" id="railNext" aria-label="Scroll to next items">
                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    @endif
                </div>
            </div>

            @if ($landingUniforms->count() > 0)
                <div class="rail-shell rv">
                    <div class="rail" id="uniformRail" tabindex="0" aria-label="Uniform stock cards">
                        @foreach ($landingUniforms as $stock)
                            @php
                                $qty = (int) $stock->quantity;
                                $isOut = $qty === 0;
                                $isLow = $qty > 0 && $qty <= 10;

                                $rawSizes = is_array($stock->sizes) ? $stock->sizes : [];
                                $sizeRun = [];
                                foreach ($sizeOrder as $sizeKey) {
                                    if (array_key_exists($sizeKey, $rawSizes)) {
                                        $sizeRun[$sizeKey] = (int) $rawSizes[$sizeKey];
                                    }
                                }
                                foreach ($rawSizes as $sizeKey => $sizeVal) {
                                    if (is_int($sizeKey) && is_string($sizeVal)) {
                                        $sizeRun[$sizeVal] = $sizeRun[$sizeVal] ?? -1; // legacy list form: size known, qty unknown
                                    } elseif (is_string($sizeKey) && ! isset($sizeRun[$sizeKey])) {
                                        $sizeRun[$sizeKey] = (int) $sizeVal;
                                    }
                                }

                                $rawPrices = is_array($stock->prices) ? $stock->prices : [];
                                $positivePrices = array_values(array_filter(array_map('floatval', $rawPrices), fn ($p) => $p > 0));
                                $minPrice = $positivePrices !== [] ? min($positivePrices) : (float) $stock->unit_price;
                                $maxPrice = $positivePrices !== [] ? max($positivePrices) : (float) $stock->unit_price;
                            @endphp
                            <a href="{{ route('stocks.show', $stock) }}" class="stock-card {{ $isOut ? 'is-out' : '' }}">
                                <div class="stock-media">
                                    <span class="stock-status {{ $isOut ? 'out' : ($isLow ? 'low' : '') }}">
                                        <span class="dot" aria-hidden="true"></span>
                                        {{ $isOut ? 'Out of stock' : ($isLow ? 'Low stock' : 'Available') }}
                                    </span>
                                    @if ($stock->image)
                                        <img src="{{ asset('storage/' . $stock->image) }}" alt="{{ $stock->item_name }}" loading="lazy">
                                    @else
                                        <svg class="stock-media-placeholder" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5 12 3l8.25 4.5L12 12 3.75 7.5Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12 12 16.5 20.25 12"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 16.5 12 21l8.25-4.5"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="stock-body">
                                    <div class="stock-kicker">Campus attire</div>
                                    <h3 class="stock-name">{{ $stock->item_name }}</h3>
                                    @if ($sizeRun !== [])
                                        <div class="size-run" aria-label="Sizes">
                                            @foreach ($sizeRun as $sizeKey => $sizeQty)
                                                <span class="size-chip {{ $sizeQty === 0 ? 'empty' : '' }}">{{ $sizeKey }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="stock-foot">
                                    @if ($minPrice > 0)
                                        <span class="stock-price">
                                            @if ($maxPrice > $minPrice)<span class="from">From</span>@endif&#8369;{{ number_format($minPrice, 2) }}
                                        </span>
                                    @else
                                        <span class="stock-price"><span class="from">Priced at the office</span></span>
                                    @endif
                                    <span class="stock-view">
                                        Details
                                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="rv" style="margin-top: 10px;">
                    <a href="{{ route('products.index') }}" class="sec-link">
                        See everything on the Products page
                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            @else
                <div class="rail-empty rv">
                    <h3>Uniform stocks will appear here soon</h3>
                    <p>Once the office posts uniform lines to the registry, you can check sizes and availability right from this page.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Browse the marketplace</a>
                </div>
            @endif
        </div>
    </section>

    <!-- ===== CONCESSIONAIRES ===== -->
    @php
        $showcaseConcessionaires = \App\Models\User::query()
            ->where('role', 'concessionaire')
            ->where('is_active_concessionaire', true)
            ->where('is_approved', true)
            ->latest()
            ->take(12)
            ->get(['id', 'name', 'business_name', 'carousel_image', 'cover_photo', 'profile_photo', 'location']);

        $ccGradients = [
            'linear-gradient(150deg, #0A5C2F, #07341C)',
            'linear-gradient(150deg, #C99A2E, #8a6510)',
            'linear-gradient(150deg, #14532d, #052e16)',
            'linear-gradient(150deg, #365314, #1a2e05)',
            'linear-gradient(150deg, #713f12, #422006)',
        ];
    @endphp
    <section class="cc-sec sec">
        <div class="wrap">
            <div class="sec-head rv">
                <div class="sec-head-main">
                    <span class="eyebrow">Registry / Campus Partners</span>
                    <h2 class="sec-title">{{ \App\Models\SiteSetting::get('showcase_title', 'Campus Concessionaires') }}</h2>
                    <p class="sec-sub">{{ \App\Models\SiteSetting::get('showcase_subtitle', 'These are our listed concessionaires that are currently partnered with the campus. They provide quality food, services, and products directly to students and staff.') }}</p>
                </div>
                <a href="{{ route('concessionaires.index') }}" class="sec-link">
                    View all partners
                    <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>

        @if ($showcaseConcessionaires->count() > 3)
            <div class="mq rv" aria-label="Campus concessionaires">
                <div class="mq-track" style="animation-duration: {{ max(28, $showcaseConcessionaires->count() * 6) }}s;">
                    @foreach ([false, true] as $isClone)
                        @foreach ($showcaseConcessionaires as $i => $c)
                            @php
                                $ccImg = $c->carousel_image ?: ($c->cover_photo ?: $c->profile_photo);
                                $ccBg = $ccImg
                                    ? "url('" . asset('storage/' . $ccImg) . "')"
                                    : $ccGradients[$i % count($ccGradients)];
                            @endphp
                            <a class="cc-card" href="{{ route('concessionaires.show', $c) }}"
                               style="background-image: {{ $ccBg }};"
                               @if ($isClone) aria-hidden="true" tabindex="-1" @endif>
                                <div class="cc-card-info">
                                    <span class="cc-tag">Concessionaire</span>
                                    <div class="cc-name">{{ $c->business_name ?: $c->name }}</div>
                                    @if ($c->location)
                                    <div class="cc-loc">
                                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $c->location }}
                                    </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @elseif ($showcaseConcessionaires->count() > 0)
            <div class="wrap">
                <div class="cc-static rv">
                    @foreach ($showcaseConcessionaires as $i => $c)
                        @php
                            $ccImg = $c->carousel_image ?: ($c->cover_photo ?: $c->profile_photo);
                            $ccBg = $ccImg
                                ? "url('" . asset('storage/' . $ccImg) . "')"
                                : $ccGradients[$i % count($ccGradients)];
                        @endphp
                        <a class="cc-card" href="{{ route('concessionaires.show', $c) }}" style="background-image: {{ $ccBg }};">
                            <div class="cc-card-info">
                                <span class="cc-tag">Concessionaire</span>
                                <div class="cc-name">{{ $c->business_name ?: $c->name }}</div>
                                @if ($c->location)
                                <div class="cc-loc">
                                    <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $c->location }}
                                </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <div class="wrap">
                <div class="cc-empty rv">
                    <h3>Concessionaires coming soon</h3>
                    <p>Approved campus partners will appear here automatically.</p>
                </div>
            </div>
        @endif
    </section>

    <!-- ===== FEATURES ===== -->
    <section class="features-sec sec" id="features">
        <img class="flow-img flow-features" src="{{ asset('images/flowlines3-green.png') }}" alt="" aria-hidden="true" loading="lazy">
        <div class="wrap">
            <div class="sec-head rv">
                <div class="sec-head-main">
                    <span class="eyebrow">The Platform</span>
                    <h2 class="sec-title">{{ \App\Models\SiteSetting::get('features_title', 'Everything Your Office Needs, In One System') }}</h2>
                    <p class="sec-sub">{{ \App\Models\SiteSetting::get('features_subtitle', 'From partnership management to stock and product tracking, streamline every aspect of the External and Business Affairs Office operations.') }}</p>
                </div>
            </div>

            <div class="features-grid">
                <div class="feature-card rv">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">{{ \App\Models\SiteSetting::get('feature_1_title', 'Partnership Applications') }}</h3>
                    <p class="feature-desc">{{ \App\Models\SiteSetting::get('feature_1_desc', 'Allow organizations and individuals to apply for partnerships directly through the platform, keeping requirements, progress, and review steps clear from day one.') }}</p>
                </div>

                <div class="feature-card spotlight rv">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">{{ \App\Models\SiteSetting::get('feature_2_title', 'Secure & Reliable') }}</h3>
                    <p class="feature-desc">{{ \App\Models\SiteSetting::get('feature_2_desc', 'Role-based access control ensures administrators, faculty, concessionaires, cashiers, and students each get the tools they need without exposing the rest.') }}</p>
                </div>

                <div class="feature-card rv">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">{{ \App\Models\SiteSetting::get('feature_3_title', 'Payments & Tracking') }}</h3>
                    <p class="feature-desc">{{ \App\Models\SiteSetting::get('feature_3_desc', 'Record payments, generate receipts, and monitor concessionaire transactions with a cleaner status view that supports both office staff and concessionaires.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ABOUT ===== -->
    <section class="about-sec sec" id="about">
        <div class="wrap">
            <div class="about-grid">
                <div class="about-visual rv">
                    <div class="frame-panel">
                        <div class="frame-panel-media">
                            <img src="{{ \App\Models\SiteSetting::image('about_image', asset('images/sample picture.jpg')) }}" alt="About EBA">
                        </div>
                        <div class="frame-panel-caption">
                            <span>The Office, On Campus</span>
                            <span>EBA / TMC</span>
                        </div>
                    </div>
                </div>
                <div class="about-text rv">
                    <span class="eyebrow">About the Office</span>
                    <h2 class="sec-title">{!! \App\Models\SiteSetting::get('about_title', 'Built to <span class="text-green-700">empower</span> our campus.') !!}</h2>
                    <p>{{ \App\Models\SiteSetting::get('about_subtitle', 'A modern and streamlined platform for the External and Business Affairs Office to handle partnerships, products, and campus services with ease.') }}</p>

                    <div class="ledger-items">
                        <div class="ledger-item">
                            <div class="ledger-item-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <strong>Faster applications</strong>
                                <span>Apply for partnerships and upload requirements directly through the online portal. No more endless paperwork.</span>
                            </div>
                        </div>
                        <div class="ledger-item">
                            <div class="ledger-item-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <strong>Centralized marketplace</strong>
                                <span>Students and staff get instant access to concessionaire products and university uniform stocks in one place.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== VISION / MISSION / VALUES ===== -->
    <section class="vmv-sec sec">
        <img class="flow-img flow-vmv" src="{{ asset('images/flowlines3-gold.png') }}" alt="" aria-hidden="true" loading="lazy">
        <div class="wrap vmv-inner">
            <div class="vmv-head rv">
                <span class="eyebrow">Cavite State University</span>
                <h2 class="sec-title">Our vision, mission &amp; core values</h2>
                <p>Guiding principles that drive excellence at CvSU Trece Martires City Campus.</p>
            </div>

            <div class="vmv-grid rv">
                <div class="vmv-col">
                    <h3>University Vision</h3>
                    <p>{{ \App\Models\SiteSetting::get('vision', 'The premier university in historic Cavite globally recognized for excellence in character development, academics, research, innovation and sustainable community engagement.') }}</p>
                </div>
                <div class="vmv-col">
                    <h3>University Mission</h3>
                    <p>{{ \App\Models\SiteSetting::get('mission', 'Cavite State University shall provide excellent, equitable and relevant educational opportunities in the arts, sciences and technology through quality instruction and responsive research and development activities. It shall produce professional, skilled and morally upright individuals for global competitiveness.') }}</p>
                </div>
                <div class="vmv-col">
                    <h3>Core Values</h3>
                    <div class="core-values-list">
                        @for ($i = 1; $i <= 5; $i++)
                            @php $cv = \App\Models\SiteSetting::get('core_value_'.$i, ''); @endphp
                            @if ($cv)
                            <div class="core-value-item">
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                </svg>
                                <strong>{{ $cv }}</strong>
                            </div>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FAQ ===== -->
    <section class="faq-sec sec" id="faq">
        <img class="flow-img flow-faq" src="{{ asset('images/flowlines1-green.png') }}" alt="" aria-hidden="true" loading="lazy">
        <div class="wrap">
            <div class="faq-grid">
                <div>
                    <div class="faq-intro rv">
                        <span class="eyebrow">Questions</span>
                        <h2 class="sec-title">Frequently asked questions</h2>
                        <p class="sec-sub">Everything you need to know about the External and Business Affairs Information System.</p>
                    </div>

                    @php
                        $faqDefaults = [
                            1 => ['How do I apply for a concessionaire partnership?', 'Simply create an account on our platform. Once registered, navigate to the application portal where you can fill out the required information and upload necessary documents like your MOA, Contract, and Business Proposal securely.'],
                            2 => ['Who can view and review products in the marketplace?', 'Anyone can browse the marketplace to check available products and concessionaires. However, only registered and approved students and faculty members can submit ratings and leave reviews to ensure feedback authenticity.'],
                            3 => ['How do concessionaires track their payment records?', 'Approved concessionaires have access to an exclusive dashboard where they can view their payment history, upcoming fixed monthly deadlines, and any overdue balances synced directly from the Cashier module.'],
                            4 => ['Is the system restricted to just concessionaires?', 'No! While concessionaires use it for managing store offerings and checking balances, the system is actively used by students to check marketplace availability and by campus staff to monitor uniform stocks safely and securely.'],
                        ];
                    @endphp
                    <div class="faq-list rv">
                        @foreach ($faqDefaults as $i => $faq)
                            @php
                                $faqQuestion = \App\Models\SiteSetting::get('faq_'.$i.'_question', $faq[0]);
                                $faqAnswer   = \App\Models\SiteSetting::get('faq_'.$i.'_answer', $faq[1]);
                            @endphp
                            @if ($faqQuestion)
                            <details>
                                <summary>
                                    {{ $faqQuestion }}
                                    <span class="faq-marker" aria-hidden="true"></span>
                                </summary>
                                <div class="faq-answer">{{ $faqAnswer }}</div>
                            </details>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="faq-visual rv">
                    <div class="frame-panel">
                        <div class="frame-panel-media">
                            <img src="{{ \App\Models\SiteSetting::image('faq_image', asset('images/vector-2.jpg')) }}" alt="FAQ">
                        </div>
                        <div class="frame-panel-caption">
                            <span>Need more help?</span>
                            <span>EBA / FAQ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="cta-sec sec">
        <img class="flow-img flow-cta" src="{{ asset('images/flowlines3-gold.png') }}" alt="" aria-hidden="true" loading="lazy">
        <div class="wrap">
            <div class="cta-inner rv">
                <span class="eyebrow">Get Started</span>
                <h2>Ready to join the campus registry?</h2>
                <p>Create an account to review products, track your application, or partner with the External and Business Affairs Office at CvSU &mdash; Trece Martires City Campus.</p>
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
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="wrap">
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
                <span>External &amp; Business Affairs Office</span>
            </div>
        </div>
    </footer>

    <script>
        // Navbar shadow on scroll
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        }, { passive: true });

        // Mobile menu toggle
        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }

        // Smooth scroll for same-page anchors
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const hash = this.getAttribute('href');
                if (hash.length < 2) return;
                const target = document.querySelector(hash);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    document.getElementById('navLinks')?.classList.remove('active');
                }
            });
        });

        // Scroll reveals
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.rv').forEach(el => revealObserver.observe(el));

        // Uniform rail controls
        (function () {
            const rail = document.getElementById('uniformRail');
            if (!rail) return;
            const prev = document.getElementById('railPrev');
            const next = document.getElementById('railNext');
            const counter = document.getElementById('railCounter');
            const cards = rail.querySelectorAll('.stock-card');
            const total = cards.length;

            function step() {
                return (cards[0]?.offsetWidth || 272) + 18;
            }

            function update() {
                const maxScroll = rail.scrollWidth - rail.clientWidth;
                prev.disabled = rail.scrollLeft <= 4;
                next.disabled = rail.scrollLeft >= maxScroll - 4;
                if (counter && total > 0) {
                    const idx = Math.min(total, Math.round(rail.scrollLeft / step()) + 1);
                    counter.innerHTML = '<b>' + String(idx).padStart(2, '0') + '</b> / ' + String(total).padStart(2, '0');
                }
            }

            prev.addEventListener('click', () => rail.scrollBy({ left: -step() * 2, behavior: 'smooth' }));
            next.addEventListener('click', () => rail.scrollBy({ left: step() * 2, behavior: 'smooth' }));
            rail.addEventListener('scroll', update, { passive: true });
            window.addEventListener('resize', update);
            update();
        })();
    </script>

    @include('partials.pending-application-banner')
</body>
</html>
