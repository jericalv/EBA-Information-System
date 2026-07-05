<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.settings-eba')] #[Title('Profile Settings')] class extends Component {
    use ProfileValidationRules, WithFileUploads;

    public string $name = '';
    public $profile_photo_file = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => $this->nameRules(),
        ]);

        $user->update([
            'name' => $validated['name'],
        ]);

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Upload or replace the current user's profile photo.
     */
    public function uploadProfilePhoto(): void
    {
        $this->validate([
            'profile_photo_file' => 'required|image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);

        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $this->profile_photo_file->store('avatars/' . $user->id, 'public');

        $user->update([
            'profile_photo' => $path,
        ]);

        $this->reset('profile_photo_file');
        $this->dispatch('profile-photo-updated');
    }

    /**
     * Remove the current user's profile photo.
     */
    public function removeProfilePhoto(): void
    {
        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->update([
            'profile_photo' => null,
        ]);

        $this->reset('profile_photo_file');
        $this->dispatch('profile-photo-updated');
    }

}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your display name and profile photo')">
        @php
            $currentUser = auth()->user();
        @endphp

        <div class="section-card" style="margin-bottom:16px;">
            <div style="font-size:15px;font-weight:700;color:#065f46;margin-bottom:10px;">{{ __('Profile photo') }}</div>

            <div style="display:flex;flex-wrap:wrap;gap:14px;align-items:center;">
                <div>
                    @if ($profile_photo_file)
                        <img src="{{ $profile_photo_file->temporaryUrl() }}" alt="{{ __('Profile preview') }}" class="avatar" style="width:72px;height:72px;">
                    @elseif ($currentUser->profile_photo)
                        <img src="{{ asset('storage/' . $currentUser->profile_photo) }}" alt="{{ $currentUser->name }}" class="avatar" style="width:72px;height:72px;">
                    @else
                        <div class="avatar" style="width:72px;height:72px;">{{ $currentUser->initials() }}</div>
                    @endif
                </div>

                <div style="flex:1;min-width:260px;">
                    <label class="field-label" for="profile_photo_file">{{ __('Select photo') }}</label>
                    <input id="profile_photo_file" class="field-input" wire:model="profile_photo_file" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <div class="helper-text">{{ __('Accepted formats: JPG, JPEG, PNG, WEBP (max 2MB).') }}</div>

                    <div style="margin-top:10px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                        <button class="btn-green" wire:click="uploadProfilePhoto" type="button">{{ __('Upload Photo') }}</button>

                        @if ($currentUser->profile_photo)
                            <button class="btn-muted" wire:click="removeProfilePhoto" type="button">{{ __('Remove Photo') }}</button>
                        @endif

                        <x-action-message class="saved-msg" on="profile-photo-updated">
                            {{ __('Photo updated.') }}
                        </x-action-message>
                    </div>
                </div>
            </div>
        </div>

        <form wire:submit="updateProfileInformation" class="form-grid">
            <div>
                <label class="field-label" for="name">{{ __('Name') }}</label>
                <input id="name" wire:model="name" class="field-input" type="text" required autofocus autocomplete="name">
            </div>

            <div>
                <label class="field-label">{{ __('Email') }}</label>
                <div class="text-readonly">{{ $currentUser->email }}</div>
            </div>

            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <button type="submit" class="btn-green" data-test="update-profile-button">{{ __('Save') }}</button>

                <x-action-message class="saved-msg" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
