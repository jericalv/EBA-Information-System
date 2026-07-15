<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Sign-In | EBA Information System</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:600,700|manrope:400,500,600,700|ibm-plex-mono:400,500,600&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink: #16231C;
            --ink-soft: #43534A;
            --ink-faint: #68786D;
            --paper: #EDF2E8;
            --card: #FFFFFF;
            --green: #0A5C2F;
            --green-bright: #0D7A3E;
            --line: #D7E0CF;
            --line-strong: #BECBB3;
            --red: #B42318;
            --red-line: #E4B8B2;
            --red-soft: #FBF0EE;
            --font-display: 'Inter', system-ui, sans-serif;
            --font-body: 'Manrope', system-ui, -apple-system, sans-serif;
            --font-mono: 'IBM Plex Mono', 'SFMono-Regular', Consolas, monospace;
        }

        body {
            font-family: var(--font-body);
            color: var(--ink);
            background: var(--paper);
            background-image: radial-gradient(rgba(22, 35, 28, 0.06) 1px, transparent 1px);
            background-size: 22px 22px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            line-height: 1.55;
        }

        a:focus-visible, button:focus-visible, input:focus-visible {
            outline: 2px solid var(--green);
            outline-offset: 2px;
            border-radius: 2px;
        }

        .login-wrap {
            width: 100%;
            max-width: 400px;
        }

        .login-card {
            background: var(--card);
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(22, 35, 28, 0.05), 0 12px 32px rgba(22, 35, 28, 0.08);
        }

        /* Card header: wordmark row with ADMIN stamp */
        .card-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 28px;
            border-bottom: 1px solid var(--line);
        }
        .card-head img {
            height: 32px;
            width: 32px;
            object-fit: contain;
            border-radius: 4px;
        }
        .card-head .wordmark {
            flex: 1;
            min-width: 0;
        }
        .card-head .wordmark strong {
            display: block;
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }
        .card-head .wordmark span {
            display: block;
            font-size: 12px;
            color: var(--ink-faint);
            white-space: nowrap;
        }
        .admin-tag {
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.12em;
            color: var(--ink-soft);
            border: 1px solid var(--line-strong);
            border-radius: 3px;
            padding: 3px 8px;
        }

        .card-body {
            padding: 26px 28px 28px;
        }

        .card-body h1 {
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin-bottom: 4px;
        }
        .card-body .lede {
            font-size: 13.5px;
            color: var(--ink-faint);
            margin-bottom: 22px;
        }

        .flash-error {
            background: var(--red-soft);
            border: 1px solid var(--red-line);
            color: var(--red);
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 18px;
        }

        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-soft);
            margin-bottom: 6px;
        }

        .input-wrap { position: relative; }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            font-size: 14.5px;
            font-family: inherit;
            color: var(--ink);
            background: var(--card);
            transition: border-color 0.15s;
        }
        .input-wrap input { padding-right: 44px; }
        input::placeholder { color: var(--ink-faint); opacity: 0.7; }
        input:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
        }
        input.is-invalid { border-color: var(--red); }

        .field-error {
            font-size: 12.5px;
            color: var(--red);
            margin-top: 5px;
        }

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
        }
        .toggle-pw:hover { color: var(--ink); }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 4px 0 20px;
        }
        .remember-row input {
            width: 16px;
            height: 16px;
            accent-color: var(--green);
        }
        .remember-row label {
            font-size: 13.5px;
            color: var(--ink-soft);
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 11px;
            background: var(--green);
            color: #fff;
            border: 1px solid var(--green);
            border-radius: 6px;
            font-size: 14.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-login:hover { background: var(--green-bright); }

        .card-foot {
            border-top: 1px solid var(--line);
            padding: 12px 28px;
            font-family: var(--font-mono);
            font-size: 10px;
            letter-spacing: 0.06em;
            white-space: nowrap;
            color: var(--ink-faint);
            text-align: center;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 13.5px;
            color: var(--ink-faint);
            text-decoration: none;
        }
        .back-link:hover { color: var(--green); text-decoration: underline; }

        @media (max-width: 440px) {
            .card-head, .card-body { padding-left: 20px; padding-right: 20px; }
            .card-foot { padding-left: 20px; padding-right: 20px; }
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="card-head">
                <img src="{{ asset('images/eba-logo.png') }}" alt="EBA logo">
                <div class="wordmark">
                    <strong>EBA Information System</strong>
                    <span>CvSU — Trece Martires City Campus</span>
                </div>
                <span class="admin-tag">ADMIN</span>
            </div>

            <div class="card-body">
                <h1>Sign in</h1>
                <p class="lede">Use your administrator account to continue.</p>

                @if (session('error'))
                    <div class="flash-error">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="you@cvsu.edu.ph" required autofocus
                            class="@error('email') is-invalid @enderror">
                        @error('email')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <input type="password" id="password" name="password"
                                placeholder="Your password" required
                                class="@error('password') is-invalid @enderror">
                            <x-pw-toggle target="password" />
                        </div>
                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="remember-row">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Keep me signed in</label>
                    </div>

                    <button type="submit" class="btn-login">Sign in</button>
                </form>
            </div>

            <div class="card-foot">
                AUTHORIZED PERSONNEL ONLY · ACCESS IS LOGGED
            </div>
        </div>

        <a href="{{ route('home') }}" class="back-link">← Back to main site</a>
    </div>

    <script>
        function togglePassword(target, btn) {
            var input = document.getElementById(target);
            var eye = btn.querySelector('.ico-eye');
            var eyeOff = btn.querySelector('.ico-eye-off');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            eye.style.display = show ? 'none' : 'block';
            eyeOff.style.display = show ? 'block' : 'none';
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        }
    </script>
</body>
</html>
