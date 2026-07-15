@props([
    'title',
    'heading',
    'sub' => null,
    'eyebrow' => 'EBA Account',
    'flow' => 'account',
    'step' => 1,
    'wide' => false,
    'panelTitle' => null,
    'panelText' => null,
])

@php
    $flows = [
        'account' => [
            'label' => 'How your account works',
            'steps' => [
                ['Create your account', 'Students and business partners register here'],
                ['Confirm your email', 'We send a link — it stays valid for 24 hours'],
                ['Sign in', 'Students activate instantly · partners are reviewed by the EBA office'],
            ],
        ],
        'recovery' => [
            'label' => 'Account recovery',
            'steps' => [
                ['Request a reset link', 'Tell us the email on your account'],
                ['Open the email', 'Follow the secure link we send you'],
                ['Set a new password', 'Then sign in as usual'],
            ],
        ],
    ];
    $ledger = $flows[$flow] ?? $flows['account'];
    $panelTitle = $panelTitle ?? 'The campus registry of Cavite State University — Trece Martires.';
    $panelText = $panelText ?? 'One account for uniforms, canteen concessionaires, and business partnership applications.';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | EBA Information System</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|manrope:400,500,600,700,800|ibm-plex-mono:400,500,600&display=swap" rel="stylesheet" />
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
            --red: #B42318;
            --red-line: #E4B8B2;
            --red-soft: #FBF0EE;
            --font-display: 'Inter', 'Manrope', system-ui, sans-serif;
            --font-body: 'Manrope', system-ui, -apple-system, sans-serif;
            --font-mono: 'IBM Plex Mono', 'SFMono-Regular', Consolas, monospace;
        }

        body {
            font-family: var(--font-body);
            color: var(--ink);
            line-height: 1.6;
            background: var(--paper);
        }

        img { max-width: 100%; }

        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible {
            outline: 2px solid var(--green);
            outline-offset: 2px;
            border-radius: 2px;
        }

        .auth-split {
            display: flex;
            min-height: 100vh;
        }

        /* ===== Left: registry panel (approved dark dotted treatment) ===== */
        .id-panel {
            position: relative;
            width: 44%;
            max-width: 620px;
            background: var(--green-deep);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 48px;
            padding: 40px 48px 32px;
            overflow: hidden;
        }
        .id-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 22px 22px;
            pointer-events: none;
        }
        .id-panel .flow-img {
            position: absolute;
            pointer-events: none;
            user-select: none;
            z-index: 0;
            bottom: -120px;
            right: -220px;
            width: 720px;
            opacity: 0.10;
            transform: rotate(-16deg);
            filter: brightness(0) invert(1);
        }
        .id-panel > * { position: relative; z-index: 1; }

        .id-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
        }
        .id-brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            background: #fff;
            border-radius: 8px;
            padding: 3px;
        }
        .id-brand-text { display: flex; flex-direction: column; }
        .id-brand-title {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.2px;
            line-height: 1.25;
        }
        .id-brand-subtitle {
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 500;
            color: var(--gold-soft);
            letter-spacing: 1.1px;
            text-transform: uppercase;
        }

        .id-main { max-width: 440px; }
        .id-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: clamp(24px, 2.4vw, 33px);
            line-height: 1.12;
            letter-spacing: -0.7px;
            margin-bottom: 14px;
        }
        .id-text {
            font-size: 14.5px;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.72);
            margin-bottom: 34px;
        }

        /* Ledger stepper — the account flow as a registry entry */
        .ledger {
            position: relative;
            border: 1px solid var(--line-dark);
            background: rgba(255, 255, 255, 0.04);
            border-radius: 10px;
            padding: 20px 22px 8px;
        }
        .ledger .tick {
            position: absolute;
            width: 14px;
            height: 14px;
            border-color: var(--gold);
            border-style: solid;
            border-width: 0;
        }
        .ledger .tick.tl { top: -1px; left: -1px; border-top-width: 2px; border-left-width: 2px; border-top-left-radius: 10px; }
        .ledger .tick.br { bottom: -1px; right: -1px; border-bottom-width: 2px; border-right-width: 2px; border-bottom-right-radius: 10px; }

        .ledger-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: var(--gold-soft);
            padding-bottom: 14px;
            border-bottom: 1px solid var(--line-dark);
        }
        .ledger-head small {
            font-size: 10px;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.45);
        }

        .steps { list-style: none; }
        .steps li {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 13px 0;
        }
        .steps li + li { border-top: 1px dashed var(--line-dark); }
        .step-no {
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.45);
            border: 1px solid var(--line-dark);
            border-radius: 6px;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .step-body { min-width: 0; }
        .step-body b {
            display: block;
            font-size: 13.5px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.62);
            line-height: 1.4;
        }
        .step-body small {
            display: block;
            font-size: 12px;
            line-height: 1.55;
            color: rgba(255, 255, 255, 0.42);
        }
        .steps li.active .step-no {
            color: var(--green-deep);
            background: var(--gold-soft);
            border-color: var(--gold-soft);
        }
        .steps li.active .step-body b { color: #fff; }
        .steps li.active .step-body small { color: rgba(255, 255, 255, 0.6); }

        .id-foot {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.38);
        }

        /* ===== Right: form panel ===== */
        .form-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 32px 48px;
        }
        .form-inner {
            width: 100%;
            max-width: {{ $wide ? '600px' : '440px' }};
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            text-decoration: none;
            color: var(--ink-faint);
            margin-bottom: 18px;
            transition: color .2s ease;
        }
        .back-link:hover { color: var(--green); }

        .auth-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 38px 36px;
            box-shadow: 0 24px 60px rgba(24, 36, 32, 0.09);
            animation: card-rise .45s ease both;
        }
        @keyframes card-rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
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

        .card-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 27px;
            letter-spacing: -0.6px;
            line-height: 1.15;
            margin: 12px 0 6px;
        }
        .card-sub {
            font-size: 14px;
            color: var(--ink-soft);
            line-height: 1.65;
            margin-bottom: 4px;
        }
        .card-head { margin-bottom: 24px; }

        /* ===== Flash messages (single region — field errors live under their fields) ===== */
        .flash {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            border: 1px solid var(--line);
            border-left: 3px solid var(--green);
            background: var(--card-soft);
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 13.5px;
            line-height: 1.6;
            color: var(--green-deep);
            margin-bottom: 20px;
        }
        .flash svg { width: 17px; height: 17px; flex-shrink: 0; margin-top: 2px; }
        .flash-error {
            border-color: var(--red-line);
            border-left-color: var(--red);
            background: var(--red-soft);
            color: var(--red);
        }
        .flash-info {
            border-color: #EAD9AC;
            border-left-color: var(--gold);
            background: #FBF6EA;
            color: #7A5A16;
        }

        /* ===== Form ===== */
        .form-group { margin-bottom: 18px; }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
        }
        .label-optional {
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--ink-faint);
        }

        .input-wrap { position: relative; }
        input[type="email"], input[type="password"], input[type="text"], input[type="tel"], select {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--line-strong);
            border-radius: 8px;
            font-size: 14px;
            font-family: var(--font-body);
            color: var(--ink);
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        input::placeholder { color: #A9B5AC; }
        input:focus, select:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
        }
        input[readonly] {
            background: var(--card-soft);
            color: var(--ink-soft);
        }
        input.is-invalid, select.is-invalid { border-color: var(--red); }
        input.is-invalid:focus, select.is-invalid:focus { box-shadow: 0 0 0 3px rgba(180, 35, 24, 0.12); }

        select {
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            padding-right: 38px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%230A5C2F' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
        }

        .error-msg {
            display: flex;
            gap: 6px;
            align-items: baseline;
            color: var(--red);
            font-size: 12.5px;
            font-weight: 600;
            line-height: 1.5;
            margin-top: 6px;
        }
        .error-msg::before {
            content: '!';
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 600;
            color: #fff;
            background: var(--red);
            border-radius: 50%;
            width: 14px;
            height: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transform: translateY(-1px);
        }

        .input-wrap input[type="password"], .input-wrap input[type="text"] { padding-right: 44px; }
        .toggle-pw {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--ink-faint);
            padding: 6px;
            display: flex;
            align-items: center;
            border-radius: 6px;
            transition: color .2s ease;
        }
        .toggle-pw:hover { color: var(--green); }

        .field-hint {
            display: flex;
            gap: 9px;
            align-items: flex-start;
            margin-top: 8px;
            padding: 9px 12px;
            border-left: 2px solid var(--gold);
            background: var(--card-soft);
            border-radius: 0 8px 8px 0;
            font-size: 12.5px;
            line-height: 1.6;
            color: var(--ink-soft);
        }
        .field-hint strong { color: var(--ink); font-weight: 700; }
        .mono-chip {
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.6px;
            color: var(--green);
            background: rgba(10, 92, 47, 0.08);
            border-radius: 4px;
            padding: 1px 6px;
            white-space: nowrap;
        }

        /* ===== Buttons (portal language: 6px radius) ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
        .btn-block { width: 100%; }
        .btn-primary { background: var(--green); color: #fff; }
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

        /* ===== Card footer ===== */
        .card-divider {
            border: none;
            border-top: 1px solid var(--line);
            margin: 24px 0 18px;
        }
        .card-footer {
            text-align: center;
            font-size: 14px;
            color: var(--ink-soft);
        }
        .card-footer a {
            color: var(--green);
            font-weight: 700;
            text-decoration: none;
        }
        .card-footer a:hover { text-decoration: underline; }

        .under-card {
            text-align: center;
            font-family: var(--font-mono);
            font-size: 10.5px;
            letter-spacing: 0.8px;
            color: var(--ink-faint);
            margin-top: 18px;
            line-height: 1.9;
        }

        /* ===== Responsive ===== */
        @media (max-width: 979px) {
            .auth-split { flex-direction: column; }
            .id-panel {
                width: 100%;
                max-width: none;
                gap: 26px;
                padding: 24px 24px 22px;
            }
            .id-panel .flow-img { display: none; }
            .id-main { max-width: none; }
            .id-title { font-size: 21px; margin-bottom: 8px; }
            .id-text { display: none; }
            .step-body small { display: none; }
            .steps li { padding: 10px 0; }
            .id-foot { display: none; }
            .form-panel { justify-content: flex-start; padding: 28px 16px 44px; }
            .auth-card { padding: 28px 22px; }
        }
        @media (max-width: 560px) {
            .form-row { grid-template-columns: 1fr; gap: 0; }
        }
        @media (prefers-reduced-motion: reduce) {
            .auth-card { animation: none; }
            * { transition: none !important; }
        }
    </style>
</head>
<body>
    <div class="auth-split">
        <aside class="id-panel">
            <img class="flow-img" src="{{ asset('images/flowlines1-green.png') }}" alt="" aria-hidden="true">

            <a href="{{ route('home') }}" class="id-brand">
                <img src="{{ asset('images/eba-logo.png') }}" alt="EBA logo">
                <span class="id-brand-text">
                    <span class="id-brand-title">EBA Information System</span>
                    <span class="id-brand-subtitle">CvSU &mdash; Trece Martires</span>
                </span>
            </a>

            <div class="id-main">
                <h1 class="id-title">{{ $panelTitle }}</h1>
                <p class="id-text">{{ $panelText }}</p>

                <div class="ledger" aria-label="{{ $ledger['label'] }}">
                    <span class="tick tl" aria-hidden="true"></span>
                    <span class="tick br" aria-hidden="true"></span>
                    <div class="ledger-head">
                        <span>{{ $ledger['label'] }}</span>
                        <small>{{ now()->format('Y') }}</small>
                    </div>
                    <ol class="steps">
                        @foreach ($ledger['steps'] as $i => [$stepTitle, $stepNote])
                            <li @class(['active' => $step === $i + 1]) @if($step === $i + 1) aria-current="step" @endif>
                                <span class="step-no">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="step-body">
                                    <b>{{ $stepTitle }}</b>
                                    <small>{{ $stepNote }}</small>
                                </span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

            <div class="id-foot">
                <span>Enterprise Business Affairs</span>
                <span>Campus Registry</span>
            </div>
        </aside>

        <main class="form-panel">
            <div class="form-inner">
                <a href="{{ route('home') }}" class="back-link">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
                    Back to campus site
                </a>

                <div class="auth-card">
                    <div class="card-head">
                        <span class="eyebrow">{{ $eyebrow }}</span>
                        <h2 class="card-title">{{ $heading }}</h2>
                        @if ($sub)
                            <p class="card-sub">{{ $sub }}</p>
                        @endif
                    </div>

                    {{ $slot }}
                </div>

                {{ $under ?? '' }}
            </div>
        </main>
    </div>

    <script>
        function togglePassword(id, btn) {
            var input = document.getElementById(id);
            var open = btn.querySelector('.ico-eye');
            var off = btn.querySelector('.ico-eye-off');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            open.style.display = show ? 'none' : 'block';
            off.style.display = show ? 'block' : 'none';
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        }
    </script>
    {{ $scripts ?? '' }}
</body>
</html>
