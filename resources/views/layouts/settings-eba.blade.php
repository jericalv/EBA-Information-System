<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <style>
        :root {
            --green: #0A5C2F;
            --green-light: #0D7A3E;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
            --bg: #f5f7fa;
            --card: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, sans-serif;
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .nav-container {
            max-width: 1200px;
            height: 72px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .nav-brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 10px;
        }

        .nav-brand-title {
            color: var(--green);
            font-size: 17px;
            font-weight: 800;
            line-height: 1.15;
            margin: 0;
        }

        .nav-brand-sub {
            color: #6b7280;
            font-size: 11px;
            font-weight: 500;
            margin: 0;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a {
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            padding: 8px 14px;
            border-radius: 8px;
            transition: 0.2s ease;
        }

        .nav-links a:hover {
            color: var(--green);
            background: rgba(10, 92, 47, 0.08);
        }

        .nav-links a.active {
            color: var(--green);
            background: rgba(10, 92, 47, 0.12);
            font-weight: 700;
        }

        .nav-links .btn-nav {
            border: 1px solid rgba(10, 92, 47, 0.2);
            color: var(--green);
            font-weight: 700;
            padding: 8px 18px;
        }

        .nav-links .btn-nav:hover {
            border-color: rgba(10, 92, 47, 0.35);
            background: rgba(10, 92, 47, 0.06);
        }

        .nav-mobile-actions {
            display: none;
            align-items: center;
            gap: 10px;
        }

        .nav-profile-mobile {
            display: none;
        }

        .mobile-toggle {
            display: none;
            border: 1px solid rgba(148, 163, 184, 0.6);
            background: #fff;
            padding: 6px 8px;
            border-radius: 8px;
            cursor: pointer;
        }

        .mobile-toggle span {
            display: block;
            width: 22px;
            height: 2px;
            background: #334155;
            margin: 4px 0;
            border-radius: 999px;
        }

        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 26px 20px 36px;
        }

        .content-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            padding: 24px;
        }

        .settings-shell {
            display: grid;
            grid-template-columns: 230px minmax(0, 1fr);
            gap: 24px;
        }

        .settings-nav {
            background: #fff;
            border-right: 1px solid var(--border);
            padding-right: 18px;
        }

        .settings-nav .side-link {
            display: block;
            text-decoration: none;
            color: #334155;
            font-weight: 600;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 6px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .settings-nav .side-link:hover {
            background: #f0fdf4;
            color: var(--green);
        }

        .settings-nav .side-link.active {
            background: var(--green);
            color: #fff;
        }

        .settings-panel {
            min-width: 0;
        }

        .settings-panel .max-w-lg {
            max-width: 100%;
        }

        .settings-title {
            font-size: 28px;
            line-height: 1.2;
            margin: 0 0 6px;
            color: #064e3b;
            font-weight: 800;
        }

        .settings-subtitle {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .section-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .user-summary {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            object-fit: cover;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #334155;
        }

        .badge-role {
            margin-top: 6px;
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: 700;
            background: #dcfce7;
            color: #166534;
        }

        .form-grid {
            display: grid;
            gap: 14px;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .field-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            color: #111827;
            background: #fff;
        }

        .field-input:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(13, 122, 62, 0.16);
        }

        .text-readonly {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #f8fafc;
            padding: 10px 12px;
            font-size: 14px;
            color: #475569;
        }

        .btn-green {
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            background: #0A5C2F;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-green:hover {
            background: #064420;
        }

        .btn-muted {
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 14px;
            font-weight: 700;
            color: #b91c1c;
            background: #fef2f2;
            cursor: pointer;
        }

        .helper-text {
            margin-top: 6px;
            color: #64748b;
            font-size: 12px;
        }

        .saved-msg {
            color: #166534;
            font-size: 13px;
            font-weight: 600;
        }

        @media (max-width: 960px) {
            .settings-shell {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .settings-nav {
                border-right: 0;
                border-bottom: 1px solid var(--border);
                padding-right: 0;
                padding-bottom: 12px;
            }
        }

        @media (max-width: 860px) {
            .nav-container {
                height: auto;
                padding: 12px 16px;
                flex-wrap: wrap;
            }

            .nav-links {
                display: none;
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
                padding: 12px 0 4px;
                border-top: 1px solid var(--border);
            }

            .nav-links.active {
                display: flex;
            }

            .nav-mobile-actions {
                display: flex;
                margin-left: auto;
            }

            .nav-profile-mobile {
                display: inline-flex;
            }

            .mobile-toggle {
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    @include('partials.pending-application-banner')

    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="nav-brand">
                <img src="{{ asset('images/eba-logo.png') }}" alt="EBA Logo">
                <div>
                    <p class="nav-brand-title">EBA Information System</p>
                    <p class="nav-brand-sub">CvSU - Trece Martires City Campus</p>
                </div>
            </a>

            <div class="nav-mobile-actions">
                @auth
                    <div class="nav-profile-mobile">
                        @include('partials.public-profile-dropdown', ['compactTrigger' => true])
                    </div>
                @endauth
                <button id="navToggle" class="mobile-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            @if (Route::has('login'))
                <div class="nav-links" id="navLinks">
                    @include('partials.public-nav-links', ['loginButtonClass' => 'btn-nav'])
                </div>
            @endif
        </div>
    </nav>

    <main class="main">
        <div class="content-card">
            <div class="settings-shell">
                <aside class="settings-nav">
                    <a href="{{ route('profile.edit') }}" class="side-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">Profile</a>
                    <a href="{{ route('user-password.edit') }}" class="side-link {{ request()->routeIs('user-password.edit') ? 'active' : '' }}">Password</a>
                </aside>

                <section class="settings-panel">
                    <div class="max-w-xl mx-auto px-4 sm:px-6">
                        {{ $slot }}
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script>
        const navToggle = document.getElementById('navToggle');
        const navLinks = document.getElementById('navLinks');

        if (navToggle && navLinks) {
            navToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
                navToggle.setAttribute('aria-expanded', navLinks.classList.contains('active'));
            });

            navLinks.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    navLinks.classList.remove('active');
                    navToggle.setAttribute('aria-expanded', 'false');
                });
            });
        }
    </script>

    @fluxScripts
</body>
</html>
