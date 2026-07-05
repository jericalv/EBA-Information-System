<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | EBA Information System</title>
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
            padding: 48px 40px; width: 100%; max-width: 440px;
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
        input[type="email"], input[type="password"], input[type="text"] {
            width: 100%; padding: 13px 16px; border: 2px solid #e2e8f0; border-radius: 10px;
            font-size: 15px; font-family: 'Inter', sans-serif; background: #f8fafc; transition: all 0.2s;
        }
        input:focus {
            outline: none; border-color: var(--green); background: #fff;
            box-shadow: 0 0 0 3px rgba(10,92,47,0.1);
        }
        input.is-invalid { border-color: #ef4444; }
        input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
        .error-msg { color: #ef4444; font-size: 13px; margin-top: 5px; }

        /* ===== BUTTON ===== */
        .btn-submit {
            width: 100%; padding: 14px; background: var(--green); color: #fff; border: none;
            border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer;
            font-family: 'Inter', sans-serif; transition: all 0.3s;
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

    <div style="display:flex;min-height:calc(100vh - 72px);">

        <div class="auth-image-panel" style="width:50%;position:relative;overflow:hidden;">
            <img src="{{ asset('images/vector-2.JPG') }}" alt="" style="width:100%;height:100%;object-fit:cover;display:block;min-height:100vh;">
        </div>

        <div class="auth-form-panel" style="width:50%;display:flex;align-items:center;justify-content:center;padding:48px 40px;background:#fff;">
            <div style="width:100%;max-width:380px;">
                <div class="card-header">
                    <img src="{{ asset('images/eba-logo.png') }}" style="height:48px;margin-bottom:12px;" alt="EBA">
                    <h2>Forgot Password</h2>
                    <p>We'll send a reset link to your email</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="you@example.com" class="@error('email') is-invalid @enderror"
                            required autofocus autocomplete="email">
                        @error('email')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">Send Reset Link</button>
                </form>

                <div class="card-footer">
                    <a href="{{ route('login') }}">&larr; Back to Log In</a>
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
    </script>
</body>
</html>
