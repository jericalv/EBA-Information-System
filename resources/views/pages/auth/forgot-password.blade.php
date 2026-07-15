@php
    $fieldKeys = ['email'];
    $generalErrors = collect($errors->getMessages())->except($fieldKeys)->flatten();
    $linkSent = (bool) session('status');
@endphp

<x-auth-shell
    title="Forgot Password"
    eyebrow="EBA Account · Recovery"
    heading="Forgot your password?"
    :sub="$linkSent ? null : 'Enter the email on your account and we\'ll send you a reset link.'"
    flow="recovery"
    :step="$linkSent ? 2 : 1"
    panel-title="Locked out? It happens."
    panel-text="We'll email you a secure link so you can set a new password and get back to the registry."
>
    @if ($linkSent)
        <div class="flash" role="status">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
            <div>{{ session('status') }}</div>
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

    <form method="POST" action="{{ route('password.email') }}">
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

        <button type="submit" class="btn btn-primary btn-block">
            {{ $linkSent ? 'Resend reset link' : 'Send reset link' }}
        </button>
    </form>

    <hr class="card-divider">
    <div class="card-footer">
        Remembered it after all? <a href="{{ route('login') }}">Back to log in</a>
    </div>

    <x-slot:under>
        <p class="under-card">Reset links expire after a short while &mdash; use the newest email</p>
    </x-slot:under>
</x-auth-shell>
