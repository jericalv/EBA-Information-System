<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Apply for Partnership | EBA Information System</title>
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
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 4px 30px rgba(0,0,0,0.06);
        }
        .nav-container {
            max-width: 1280px; margin: 0 auto; padding: 0 24px;
            display: flex; align-items: center; justify-content: space-between; height: 72px;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 14px;
            text-decoration: none; color: #111;
        }
        .nav-brand img { width: 44px; height: 44px; border-radius: 10px; object-fit: contain; }
        .nav-brand-text { display: flex; flex-direction: column; }
        .nav-brand-title { font-size: 16px; font-weight: 800; color: var(--green); line-height: 1.2; }
        .nav-brand-sub { font-size: 11px; font-weight: 500; color: #666; letter-spacing: 0.3px; }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-links a {
            text-decoration: none; font-size: 14px; font-weight: 500; color: #666;
            padding: 8px 16px; border-radius: 8px; transition: all 0.2s;
        }
        .nav-links a:hover { color: var(--green); background: rgba(10,92,47,0.06); }

        /* ===== MAIN ===== */
        .page-wrapper {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: 100px 24px 50px; position: relative;
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
            padding: 40px; width: 100%; max-width: 600px;
            animation: slideUp 0.5s ease;
        }
        .card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, var(--green), var(--gold));
            border-radius: 20px 20px 0 0;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== HEADER ===== */
        .card-header { text-align: center; margin-bottom: 28px; }
        .card-header h2 { font-size: 24px; font-weight: 800; letter-spacing: -0.3px; margin-bottom: 6px; color: var(--green); }
        .card-header p { color: #718096; font-size: 14px; line-height: 1.5; }

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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }
        .form-group { margin-bottom: 18px; }
        .form-group.full { grid-column: 1 / -1; }
        .form-group label {
            display: block; font-weight: 600; font-size: 12px; color: #2d3748; margin-bottom: 6px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .form-group label .required { color: #dc2626; }
        .form-group label .optional { color: #718096; font-weight: 400; text-transform: none; }
        
        input[type="text"], input[type="email"], input[type="tel"], textarea {
            width: 100%; padding: 12px 14px; border: 2px solid #e2e8f0; border-radius: 10px;
            font-size: 14px; font-family: 'Inter', sans-serif; background: #f8fafc; transition: all 0.2s;
        }
        input:focus, textarea:focus {
            outline: none; border-color: var(--green); background: #fff;
            box-shadow: 0 0 0 3px rgba(10,92,47,0.1);
        }
        input.is-invalid, textarea.is-invalid { border-color: #ef4444; }
        input.is-invalid:focus, textarea.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
        .error-msg { color: #ef4444; font-size: 12px; margin-top: 4px; }
        .help-text { color: #718096; font-size: 11px; margin-top: 4px; }

        textarea { resize: vertical; min-height: 100px; }

        /* File input */
        .file-input-wrapper {
            position: relative;
            border: 2px dashed #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: #f8fafc;
            transition: all 0.2s;
            cursor: pointer;
        }
        .file-input-wrapper:hover {
            border-color: var(--green);
            background: rgba(10,92,47,0.02);
        }
        .file-input-wrapper input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }
        .file-input-wrapper .file-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
            color: var(--green);
        }
        .file-input-wrapper .file-text {
            font-size: 13px;
            color: #4a5568;
        }
        .file-input-wrapper .file-text strong {
            color: var(--green);
        }
        .file-name {
            margin-top: 10px;
            font-size: 13px;
            color: var(--green);
            font-weight: 600;
        }

        /* ===== BUTTONS ===== */
        .btn-row {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-cancel {
            background: #fff;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }
        .btn-cancel:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }
        .btn-submit {
            background: var(--green);
            color: #fff;
            border: none;
        }
        .btn-submit:hover {
            background: var(--green-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10,92,47,0.25);
        }

        /* ===== PAGE FOOTER ===== */
        .site-footer {
            text-align: center; padding: 24px; font-size: 13px; color: #718096;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 640px) {
            .nav-links { display: none; }
            .card { padding: 28px 20px; margin: 0 8px; }
            .page-wrapper { padding: 86px 16px 40px; }
            .form-row, .form-row-3 { grid-template-columns: 1fr; }
            .btn-row { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="nav-brand">
                <img src="{{ asset('images/eba-logo.png') }}" alt="EBA Logo">
                <div class="nav-brand-text">
                    <span class="nav-brand-title">EBA Information System</span>
                    <span class="nav-brand-sub">CvSU &mdash; Trece Martires City Campus</span>
                </div>
            </a>
            <div class="nav-links">
                <a href="{{ route('home') }}">Home</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;font-family:inherit;font-size:14px;font-weight:500;color:#dc2626;padding:8px 16px;">Log out</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="bg-pattern"></div>
        <div class="card">
            <div class="card-header">
                <h2>Apply for Partnership</h2>
                <p>Submit your business partnership application to collaborate with CvSU Trece Martires City Campus. Our team will review your proposal and get back to you.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('partnership.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-row-3">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                            placeholder="e.g. Juan" class="@error('first_name') is-invalid @enderror" required>
                        @error('first_name')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="middle_name">Middle Name <span class="optional">(Optional)</span></label>
                        <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                            placeholder="Leave blank if none" class="@error('middle_name') is-invalid @enderror">
                        @error('middle_name')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                            placeholder="e.g. Dela Cruz" class="@error('last_name') is-invalid @enderror" required>
                        @error('last_name')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            placeholder="you@email.com" class="@error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone <span class="optional">(Optional, PH Mobile)</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                            placeholder="09XXXXXXXXX or +639XXXXXXXXX" class="@error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                        <div class="help-text">Format: 09XXXXXXXXX or +639XXXXXXXXX</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="business_name">Business Name <span class="required">*</span></label>
                    <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}"
                        placeholder="Your business or organization name" class="@error('business_name') is-invalid @enderror" required>
                    @error('business_name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="proposal">Brief Proposal <span class="required">*</span></label>
                    <textarea id="proposal" name="proposal" rows="4"
                        placeholder="Tell us about your business and what partnership you're proposing (max 1000 characters)"
                        class="@error('proposal') is-invalid @enderror" required maxlength="1000">{{ old('proposal') }}</textarea>
                    @error('proposal')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                    <div class="help-text">Maximum 1000 characters</div>
                </div>

                <div class="form-group">
                    <label for="letter_of_intent">Letter of Intent <span class="optional">(Optional)</span></label>
                    <div class="file-input-wrapper">
                        <svg class="file-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <div class="file-text">
                            <strong>Click to upload</strong> or drag and drop<br>
                            PDF, DOC, DOCX, JPG, or PNG (max 5MB)
                        </div>
                        <input type="file" id="letter_of_intent" name="letter_of_intent" 
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="@error('letter_of_intent') is-invalid @enderror"
                            onchange="updateFileName(this)">
                        <div class="file-name" id="fileName"></div>
                    </div>
                    @error('letter_of_intent')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                    <div class="help-text">Letter of Intent is optional. You may upload it later from your Application Status page.</div>
                </div>

                <div class="form-group" style="margin-top: 4px;">
                    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 14px; font-size: 13px; color: #92400e; line-height: 1.55;">
                        <strong>Signed Documents Reminder</strong><br>
                        After submission, please upload the following signed documents via your Application Status page:<br>
                        - Signed Memorandum of Agreement (MOA)<br>
                        - Signed Contract<br>
                        Letter of Intent is optional.
                    </div>
                </div>

                <div class="btn-row">
                    <a href="{{ route('home') }}" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-submit">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="site-footer">
        &copy; {{ date('Y') }} CvSU &mdash; Trece Martires City Campus. External & Business Affairs Office.
    </footer>

    <script>
        function updateFileName(input) {
            const fileName = document.getElementById('fileName');
            if (input.files && input.files[0]) {
                fileName.textContent = input.files[0].name;
            } else {
                fileName.textContent = '';
            }
        }
    </script>
</body>
</html>
