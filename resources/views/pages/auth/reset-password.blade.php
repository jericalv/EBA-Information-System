@php
    $fieldKeys = ['email', 'password', 'password_confirmation'];
    $generalErrors = collect($errors->getMessages())->except($fieldKeys)->flatten();
@endphp

<x-auth-shell
    title="Reset Password"
    eyebrow="EBA Account · Recovery"
    heading="Set a new password"
    sub="Almost done — choose a new password for your account."
    flow="recovery"
    :step="3"
    panel-title="Locked out? It happens."
    panel-text="You followed the secure link from your email. Pick a new password and you're back in."
>
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

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ request('email') }}"
                class="@error('email') is-invalid @enderror" readonly>
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">New password</label>
            <div class="input-wrap">
                <input type="password" id="password" name="password"
                    placeholder="Min. 8 characters" class="@error('password') is-invalid @enderror"
                    required autofocus autocomplete="new-password">
                <x-pw-toggle target="password" />
            </div>
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm new password</label>
            <div class="input-wrap">
                <input type="password" id="password_confirmation" name="password_confirmation"
                    placeholder="Re-enter your new password"
                    required autocomplete="new-password">
                <x-pw-toggle target="password_confirmation" />
            </div>
            @error('password_confirmation')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block">Reset password</button>
    </form>

    <hr class="card-divider">
    <div class="card-footer">
        <a href="{{ route('login') }}">Back to log in</a>
    </div>

    <x-slot:under>
        <p class="under-card">If this link has expired, request a fresh one from the log in page</p>
    </x-slot:under>
</x-auth-shell>
