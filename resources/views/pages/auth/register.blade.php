<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | EBA Information System</title>
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
            background: #f5f7fa;
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

        /* ===== MAIN ===== */
        .page-wrapper {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: 110px 24px 50px; position: relative;
        }
        .bg-pattern {
            position: absolute; inset: 0; opacity: 0.025; z-index: 0;
            background-image: radial-gradient(circle at 2px 2px, var(--green) 1px, transparent 0);
            background-size: 40px 40px;
        }

        /* ===== CARD ===== */
        .card {
            position: relative; z-index: 1;
            background: #fff; border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.08);
            padding: 48px 40px; width: 100%; max-width: 480px;
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== HEADER ===== */
        .card-header { text-align: center; margin-bottom: 32px; }
        .card-logo { height: 64px; width: auto; border-radius: 14px; margin-bottom: 18px; }
        .card-header h2 { font-size: 26px; font-weight: 800; letter-spacing: -0.3px; margin-bottom: 6px; }
        .card-header p { color: #718096; font-size: 15px; }

        /* ===== ALERTS ===== */
        .alert {
            padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px;
        }
        .alert-success {
            background: rgba(10,92,47,0.07); border: 1px solid rgba(10,92,47,0.18); color: var(--green);
        }
        .alert-error {
            background: rgba(239,68,68,0.07); border: 1px solid rgba(239,68,68,0.18); color: #dc2626;
        }

        /* ===== FORM ===== */
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; font-weight: 600; font-size: 14px; color: #2d3748; margin-bottom: 7px;
        }
        .input-wrap { position: relative; }
        input[type="email"], input[type="password"], input[type="text"], input[type="tel"], select {
            width: 100%; padding: 11px 14px; border: 2px solid #e2e8f0; border-radius: 10px;
            font-size: 13px; font-family: 'Inter', sans-serif; background: #f8fafc; transition: all 0.2s;
        }
        select {
            appearance: none; -webkit-appearance: none; cursor: pointer; color: #1a202c;
            padding-right: 38px;
            background-image: linear-gradient(45deg, transparent 50%, var(--green) 50%), linear-gradient(135deg, var(--green) 50%, transparent 50%);
            background-position: calc(100% - 18px) 18px, calc(100% - 13px) 18px;
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
        }
        .input-wrap input[type="password"] { padding-right: 40px; }
        input:focus, select:focus {
            outline: none; border-color: var(--green); background-color: #fff;
            box-shadow: 0 0 0 3px rgba(10,92,47,0.1);
        }
        .field-hint {
            margin-top: 9px; padding: 10px 14px; border-radius: 9px; font-size: 12.5px; line-height: 1.5;
            background: rgba(10,92,47,0.06); border: 1px solid rgba(10,92,47,0.14); color: var(--green-dark);
            text-align: center;
        }
        .field-hint strong { font-weight: 700; }
        .label-optional { font-weight: 500; color: #94a3b8; font-size: 12px; }
        .label-required { color: #ef4444; margin-left: 2px; }

        /* ===== CERTIFY CHECKBOX ===== */
        .certify-wrap {
            display: flex; align-items: flex-start; gap: 10px;
            margin-bottom: 18px; padding: 14px 14px;
            background: rgba(10,92,47,0.04); border: 1.5px solid rgba(10,92,47,0.14);
            border-radius: 10px;
        }
        .certify-wrap input[type="checkbox"] {
            width: 18px; height: 18px; flex-shrink: 0; accent-color: var(--green);
            margin-top: 2px; cursor: pointer; border-radius: 4px;
        }
        .certify-wrap label {
            font-size: 12.5px; color: var(--gray-700); line-height: 1.55;
            cursor: pointer; font-weight: 500; margin: 0;
        }
        input.is-invalid { border-color: #ef4444; }
        input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
        .error-msg { color: #ef4444; font-size: 13px; margin-top: 5px; }

        .toggle-pw {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: #718096; padding: 2px;
            display: flex; align-items: center;
        }
        .toggle-pw:hover { color: var(--green); }

        .password-strength {
            margin-top: 10px;
        }

        .password-strength-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 12px;
            color: var(--gray-600);
            font-weight: 600;
        }

        .password-strength-label {
            color: var(--gray-700);
            transition: color 0.2s ease;
        }

        .password-strength-bar {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .password-strength-fill {
            width: 0;
            height: 100%;
            border-radius: inherit;
            background: #ef4444;
            transition: width 0.25s ease, background-color 0.25s ease;
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        /* ===== BUTTON ===== */
        .btn-submit {
            width: 100%; padding: 14px; background: var(--green); color: #fff; border: none;
            border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer;
            font-family: 'Inter', sans-serif; transition: all 0.3s; margin-top: 4px;
        }
        .btn-submit:hover {
            background: var(--green-light); transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(10,92,47,0.25);
        }
        .btn-submit:active { transform: translateY(0); }

        /* ===== FOOTER LINK ===== */
        .card-footer {
            text-align: center; margin-top: 28px; font-size: 15px; color: #4a5568;
        }
        .card-footer a { color: var(--green); font-weight: 700; text-decoration: none; }
        .card-footer a:hover { text-decoration: underline; }

        /* ===== PAGE FOOTER ===== */
        .site-footer {
            text-align: center; padding: 24px; font-size: 13px; color: #718096;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 640px) {
            .nav-links { display: none; }
            .card { padding: 36px 24px; margin: 0 8px; }
            .page-wrapper { padding: 96px 16px 40px; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
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
            .auth-image-panel { display: none !important; }
            .auth-form-panel { width: 100% !important; padding: 32px 24px !important; }
        }
    </style>
</head>
<body>
    <!-- ===== NAVBAR ===== -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="/" class="nav-brand">
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

    <div style="display:flex;min-height:calc(100vh - 72px);">

        <div class="auth-image-panel" style="width:50%;position:relative;overflow:hidden;">
            <img src="{{ asset('images/4084557.jpg') }}" alt="" style="width:100%;height:100%;object-fit:cover;display:block;min-height:100vh;">
        </div>

        <div class="auth-form-panel" style="width:50%;display:flex;align-items:center;justify-content:center;padding:48px 40px;background:#fff;">
            <div style="width:100%;max-width:380px;">
                <div class="card-header">
                    <h2>Create Account</h2>
                    <p>Join the EBA Information System</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.pending') }}">
                    @csrf

                    <div class="form-group">
                        <label for="account_type">I am registering as <span class="label-required">*</span></label>
                        <select id="account_type" name="account_type" class="@error('account_type') is-invalid @enderror" required>
                            <option value="student" @selected(old('account_type', 'student') === 'student')>Student / CvSU member</option>
                            <option value="concessionaire" @selected(old('account_type') === 'concessionaire')>Concessionaire / Business partner</option>
                        </select>
                        <div class="field-hint" id="accountTypeHint"></div>
                        @error('account_type')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name <span class="label-required">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                                placeholder="Juan" class="@error('first_name') is-invalid @enderror"
                                required autofocus autocomplete="given-name">
                            @error('first_name')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="middle_name">Middle Name <span class="label-optional">(optional)</span></label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                                placeholder="Santos" class="@error('middle_name') is-invalid @enderror"
                                autocomplete="additional-name">
                            @error('middle_name')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="last_name">Last Name <span class="label-required">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                placeholder="Dela Cruz" class="@error('last_name') is-invalid @enderror"
                                required autocomplete="family-name">
                            @error('last_name')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="suffix">Suffix <span class="label-optional">(optional)</span></label>
                            <input type="text" id="suffix" name="suffix" value="{{ old('suffix') }}"
                                placeholder="Jr., Sr., III" class="@error('suffix') is-invalid @enderror"
                                autocomplete="honorific-suffix">
                            @error('suffix')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address <span class="label-required">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="you@example.com" class="@error('email') is-invalid @enderror"
                            required autocomplete="email">
                        @error('email')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Phone Number <span class="label-optional">(optional)</span></label>
                        <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                            placeholder="09XXXXXXXXX" class="@error('phone_number') is-invalid @enderror"
                            autocomplete="tel" inputmode="numeric" pattern="[0-9+\s]*"
                            oninput="this.value=this.value.replace(/[^0-9+\s]/g,'')">
                        @error('phone_number')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password <span class="label-required">*</span></label>
                            <div class="input-wrap">
                                <input type="password" id="password" name="password"
                                    placeholder="Min 8 characters" class="@error('password') is-invalid @enderror"
                                    required minlength="8" autocomplete="new-password">
                                <button type="button" class="toggle-pw" onclick="togglePassword('password', this)">
                                    <svg class="ico-eye" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="ico-eye-off" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                            <div class="password-strength" aria-live="polite">
                                <div class="password-strength-meta">
                                    <span id="passwordStrengthLabel" class="password-strength-label">Weak</span>
                                </div>
                                <div class="password-strength-bar" role="progressbar" aria-label="Password strength" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                    <div id="passwordStrengthFill" class="password-strength-fill"></div>
                                </div>
                            </div>
                            @error('password')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password <span class="label-required">*</span></label>
                            <div class="input-wrap">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Re-enter password"
                                    required autocomplete="new-password">
                                <button type="button" class="toggle-pw" onclick="togglePassword('password_confirmation', this)">
                                    <svg class="ico-eye" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="ico-eye-off" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="certify-wrap">
                        <input type="checkbox" id="certify" name="certify" required>
                        <label for="certify">
                            I hereby certify that the above information is true and correct and that I agree to abide by the University's rules and regulations.
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">Create Account</button>
                </form>

                <div class="card-footer">
                    Already have an account? <a href="{{ route('login') }}">Log in</a>
                </div>
            </div>
        </div>

    </div>

    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }

        function togglePassword(id, btn) {
            var input = document.getElementById(id);
            var open = btn.querySelector('.ico-eye');
            var off = btn.querySelector('.ico-eye-off');
            if (input.type === 'password') {
                input.type = 'text';
                open.style.display = 'none';
                off.style.display = 'block';
            } else {
                input.type = 'password';
                open.style.display = 'block';
                off.style.display = 'none';
            }
        }

        function evaluatePasswordStrength(password) {
            var lengthScore = 0;

            if (password.length >= 16) {
                lengthScore += 1;
            }
            if (password.length >= 12) {
                lengthScore += 1;
            }
            if (password.length >= 12) {
                lengthScore += 1;
            }

            var varietyScore = 0;
            if (/[a-z]/.test(password)) {
                varietyScore += 1;
            }
            if (/[A-Z]/.test(password)) {
                varietyScore += 1;
            }
            if (/\d/.test(password)) {
                varietyScore += 1;
            }
            if (/[^A-Za-z0-9]/.test(password)) {
                varietyScore += 1;
            }

            var totalScore = Math.min(lengthScore + varietyScore, 7);
            var percent = Math.round((totalScore / 7) * 100);

            if (password.length === 0) {
                return { label: 'Weak', percent: 0, color: '#ef4444' };
            }
            if (totalScore <= 2) {
                return { label: 'Weak', percent: Math.max(percent, 20), color: '#ef4444' };
            }
            if (totalScore <= 4) {
                return { label: 'Fair', percent: Math.max(percent, 45), color: '#f59e0b' };
            }
            if (totalScore <= 6) {
                return { label: 'Strong', percent: Math.max(percent, 70), color: '#22c55e' };
            }

            return { label: 'Very Strong', percent: 100, color: '#15803d' };
        }

        var accountType = document.getElementById('account_type');
        var accountTypeHint = document.getElementById('accountTypeHint');
        var emailInput = document.getElementById('email');

        if (accountType && accountTypeHint) {
            var hints = {
                student: {
                    text: '<strong>Students &amp; CvSU members:</strong> use your official CvSU email ending in <strong>@cvsu.edu.ph</strong>. Your account is activated as soon as you confirm the email.',
                    placeholder: 'juandelacruz@cvsu.edu.ph'
                },
                concessionaire: {
                    text: '<strong>Concessionaires / partners:</strong> use any valid email address. After confirming, your application goes to the EBA office for review before activation.',
                    placeholder: 'business@gmail.com'
                }
            };

            var syncAccountTypeHint = function () {
                var info = hints[accountType.value] || hints.student;
                accountTypeHint.innerHTML = info.text;
                if (emailInput && !emailInput.value) {
                    emailInput.setAttribute('placeholder', info.placeholder);
                }
            };

            accountType.addEventListener('change', syncAccountTypeHint);
            syncAccountTypeHint();
        }

        var passwordInput = document.getElementById('password');
        var strengthLabel = document.getElementById('passwordStrengthLabel');
        var strengthFill = document.getElementById('passwordStrengthFill');
        var strengthBar = document.querySelector('.password-strength-bar');

        if (passwordInput && strengthLabel && strengthFill && strengthBar) {
            var syncStrengthMeter = function () {
                var result = evaluatePasswordStrength(passwordInput.value || '');
                strengthLabel.textContent = result.label;
                strengthLabel.style.color = result.color;
                strengthFill.style.width = result.percent + '%';
                strengthFill.style.backgroundColor = result.color;
                strengthBar.setAttribute('aria-valuenow', String(result.percent));
            };

            passwordInput.addEventListener('input', syncStrengthMeter);
            syncStrengthMeter();
        }
    </script>
</body>
</html>
