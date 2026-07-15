@php
    // Field errors render inline under their inputs; anything else surfaces up top.
    $fieldKeys = ['email', 'password'];
    $generalErrors = collect($errors->getMessages())->except($fieldKeys)->flatten();

    $successFlash = null;
    if (session('status') === 'account-created') {
        $successFlash = session('message', 'Your account has been created successfully. You can now log in.');
    } elseif (session('status')) {
        $successFlash = session('status');
    }
@endphp

<x-auth-shell
    title="Log In"
    eyebrow="EBA Account · Sign In"
    heading="Welcome back"
    sub="Sign in to your campus registry account."
    :step="3"
>
    @if (str_contains(session('url.intended', ''), 'partnership') || str_contains(session('url.intended', ''), 'application'))
        <div class="flash flash-info" role="status">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01"/></svg>
            <div><strong>Applying for partnership?</strong> Log in first to submit your business partnership application.</div>
        </div>
    @endif

    @if ($successFlash)
        <div class="flash" role="status">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
            <div>{{ $successFlash }}</div>
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

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                placeholder="you@example.com" class="@error('email') is-invalid @enderror"
                required autofocus autocomplete="email">
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrap">
                <input type="password" id="password" name="password"
                    placeholder="Enter your password" class="@error('password') is-invalid @enderror"
                    required autocomplete="current-password">
                <x-pw-toggle target="password" />
            </div>
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
            <label style="display:flex;align-items:center;gap:8px;margin:0;font-size:13.5px;font-weight:600;color:var(--ink-soft);cursor:pointer;">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                    style="width:16px;height:16px;accent-color:var(--green);cursor:pointer;">
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="color:var(--green);font-size:13.5px;font-weight:700;text-decoration:none;">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary btn-block">Log in</button>
    </form>

    @if (Route::has('register'))
        <hr class="card-divider">
        <div class="card-footer">
            New to the registry? <a href="{{ route('register') }}">Create an account</a>
        </div>
    @endif

    <x-slot:under>
        <p class="under-card">
            Students sign in with their <span class="mono-chip">@cvsu.edu.ph</span> email<br>
            Concessionaires use the email they registered with
        </p>
    </x-slot:under>
</x-auth-shell>
