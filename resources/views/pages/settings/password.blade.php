<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.settings-eba')] #[Title('Password Settings')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form method="POST" wire:submit="updatePassword" class="form-grid">
            <div>
                <label class="field-label" for="current_password">{{ __('Current password') }}</label>
                <input id="current_password" wire:model="current_password" class="field-input" type="password" required autocomplete="current-password">
            </div>

            <div>
                <label class="field-label" for="password">{{ __('New password') }}</label>
                <input id="password" wire:model="password" class="field-input" type="password" required autocomplete="new-password">
            </div>

            <div>
                <label class="field-label" for="password_confirmation">{{ __('Confirm password') }}</label>
                <input id="password_confirmation" wire:model="password_confirmation" class="field-input" type="password" required autocomplete="new-password">
            </div>

            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <button type="submit" class="btn-green" data-test="update-password-button">{{ __('Save') }}</button>

                <x-action-message class="saved-msg" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
