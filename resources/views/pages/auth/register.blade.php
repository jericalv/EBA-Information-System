@php
    $fieldKeys = ['account_type', 'first_name', 'middle_name', 'last_name', 'suffix', 'email', 'phone_number', 'password', 'password_confirmation', 'certify'];
    $generalErrors = collect($errors->getMessages())->except($fieldKeys)->flatten();
    $emailSent = (bool) session('success');
@endphp

<x-auth-shell
    title="Register"
    eyebrow="EBA Account · Registration"
    :heading="$emailSent ? 'Check your inbox' : 'Create your account'"
    :sub="$emailSent ? null : 'One form for students and business partners.'"
    :step="$emailSent ? 2 : 1"
    :wide="! $emailSent"
>
    @if ($emailSent)
        {{-- Registration recorded: the account only exists once the emailed link is opened. --}}
        <div style="text-align:center;padding:6px 0 2px;">
            <div style="width:64px;height:64px;margin:0 auto 18px;border-radius:50%;background:var(--card-soft);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;color:var(--green);">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>
            </div>
            <p style="font-size:14.5px;line-height:1.7;color:var(--ink-soft);margin-bottom:16px;" role="status">
                {{ session('success') }}
            </p>
            <p style="margin-bottom:22px;"><span class="mono-chip">Link valid for 24 hours</span></p>
            <p style="font-size:13px;line-height:1.7;color:var(--ink-faint);margin-bottom:26px;">
                Nothing arriving? Check your spam folder. If the link expires,
                just fill in the registration form again to get a fresh one.
            </p>
            <a href="{{ route('login') }}" class="btn btn-ghost btn-block">Back to log in</a>
        </div>
    @else
        @if (session('error'))
            <div class="flash flash-error" role="alert">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if ($generalErrors->isNotEmpty())
            <div class="flash flash-error" role="alert">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                <div>
                    @foreach ($generalErrors as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('register.pending') }}">
            @csrf

            <div class="form-group">
                <label for="account_type">I am registering as</label>
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
                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                        placeholder="Juan" class="@error('first_name') is-invalid @enderror"
                        required autofocus autocomplete="given-name">
                    @error('first_name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="middle_name">Middle name <span class="label-optional">optional</span></label>
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
                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                        placeholder="Dela Cruz" class="@error('last_name') is-invalid @enderror"
                        required autocomplete="family-name">
                    @error('last_name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="suffix">Suffix <span class="label-optional">optional</span></label>
                    <input type="text" id="suffix" name="suffix" value="{{ old('suffix') }}"
                        placeholder="Jr., Sr., III" class="@error('suffix') is-invalid @enderror"
                        autocomplete="honorific-suffix">
                    @error('suffix')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    placeholder="you@example.com" class="@error('email') is-invalid @enderror"
                    required autocomplete="email">
                @error('email')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone_number">Phone number <span class="label-optional">optional</span></label>
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
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password"
                            placeholder="Min. 8 characters" class="@error('password') is-invalid @enderror"
                            required minlength="8" autocomplete="new-password">
                        <x-pw-toggle target="password" />
                    </div>
                    <div class="password-strength" aria-live="polite">
                        <span id="passwordStrengthLabel" class="password-strength-label">Weak</span>
                        <div class="password-strength-bar" role="progressbar" aria-label="Password strength" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                            <div id="passwordStrengthFill" class="password-strength-fill"></div>
                        </div>
                    </div>
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm password</label>
                    <div class="input-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Re-enter password"
                            required autocomplete="new-password">
                        <x-pw-toggle target="password_confirmation" />
                    </div>
                    @error('password_confirmation')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="certify-wrap">
                <input type="checkbox" id="certify" name="certify" required>
                <label for="certify">
                    I certify that the information above is true and correct, and I agree to abide by the University's rules and regulations.
                </label>
            </div>
            @error('certify')
                <div class="error-msg" style="margin-top:-10px;margin-bottom:14px;">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary btn-block">Create account</button>
        </form>

        <hr class="card-divider">
        <div class="card-footer">
            Already registered? <a href="{{ route('login') }}">Log in</a>
        </div>
    @endif

    <x-slot:scripts>
        <style>
            .password-strength { margin-top: 8px; }
            .password-strength-label {
                display: block;
                font-family: var(--font-mono);
                font-size: 10.5px;
                font-weight: 600;
                letter-spacing: 0.8px;
                text-transform: uppercase;
                color: var(--ink-faint);
                margin-bottom: 5px;
            }
            .password-strength-bar {
                width: 100%;
                height: 5px;
                border-radius: 999px;
                background: var(--paper-deep);
                overflow: hidden;
            }
            .password-strength-fill {
                width: 0;
                height: 100%;
                border-radius: inherit;
                background: var(--red);
                transition: width .25s ease, background-color .25s ease;
            }
            .certify-wrap {
                display: flex;
                align-items: flex-start;
                gap: 11px;
                margin-bottom: 18px;
                padding: 13px 14px;
                background: var(--card-soft);
                border: 1px solid var(--line);
                border-radius: 8px;
            }
            .certify-wrap input[type="checkbox"] {
                width: 17px;
                height: 17px;
                flex-shrink: 0;
                accent-color: var(--green);
                margin-top: 2px;
                cursor: pointer;
            }
            .certify-wrap label {
                font-size: 12.5px;
                color: var(--ink-soft);
                line-height: 1.6;
                cursor: pointer;
                font-weight: 500;
                margin: 0;
            }
        </style>
        <script>
            var accountType = document.getElementById('account_type');
            var accountTypeHint = document.getElementById('accountTypeHint');
            var emailInput = document.getElementById('email');

            if (accountType && accountTypeHint) {
                var hints = {
                    student: {
                        text: '<div><strong>Students &amp; CvSU members:</strong> use your official CvSU email ending in <strong>@cvsu.edu.ph</strong>. Your account activates as soon as you confirm the email.</div>',
                        placeholder: 'juandelacruz@cvsu.edu.ph'
                    },
                    concessionaire: {
                        text: '<div><strong>Concessionaires / partners:</strong> use any valid email address. After confirming, your application goes to the EBA office for review before activation.</div>',
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

            function evaluatePasswordStrength(password) {
                var lengthScore = 0;
                if (password.length >= 8) lengthScore += 1;
                if (password.length >= 12) lengthScore += 1;
                if (password.length >= 16) lengthScore += 1;

                var varietyScore = 0;
                if (/[a-z]/.test(password)) varietyScore += 1;
                if (/[A-Z]/.test(password)) varietyScore += 1;
                if (/\d/.test(password)) varietyScore += 1;
                if (/[^A-Za-z0-9]/.test(password)) varietyScore += 1;

                var totalScore = Math.min(lengthScore + varietyScore, 7);
                var percent = Math.round((totalScore / 7) * 100);

                if (password.length === 0) {
                    return { label: 'Weak', percent: 0, color: '#B42318' };
                }
                if (totalScore <= 2) {
                    return { label: 'Weak', percent: Math.max(percent, 20), color: '#B42318' };
                }
                if (totalScore <= 4) {
                    return { label: 'Fair', percent: Math.max(percent, 45), color: '#C99A2E' };
                }
                if (totalScore <= 6) {
                    return { label: 'Strong', percent: Math.max(percent, 70), color: '#0D7A3E' };
                }
                return { label: 'Very strong', percent: 100, color: '#0A5C2F' };
            }

            var passwordInput = document.getElementById('password');
            var strengthLabel = document.getElementById('passwordStrengthLabel');
            var strengthFill = document.getElementById('passwordStrengthFill');
            var strengthBar = document.querySelector('.password-strength-bar');

            if (passwordInput && strengthLabel && strengthFill && strengthBar) {
                var syncStrengthMeter = function () {
                    var value = passwordInput.value || '';
                    var result = evaluatePasswordStrength(value);
                    strengthLabel.textContent = value ? result.label : 'Password strength';
                    strengthLabel.style.color = value ? result.color : '#68786D';
                    strengthFill.style.width = result.percent + '%';
                    strengthFill.style.backgroundColor = result.color;
                    strengthBar.setAttribute('aria-valuenow', String(result.percent));
                };

                passwordInput.addEventListener('input', syncStrengthMeter);
                syncStrengthMeter();
            }
        </script>
    </x-slot:scripts>
</x-auth-shell>
