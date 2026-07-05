<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.settings-eba')] #[Title('Notification settings')] class extends Component {
    public bool $email_partnership_updates = true;
    public bool $email_contract_expiry = true;
    public bool $in_app_notifications = true;

    public bool $isConcessionaire = false;

    /**
     * Default notification preferences for users.
     *
     * @return array<string, bool>
     */
    private function defaultPreferences(): array
    {
        return [
            'email_partnership_updates' => true,
            'email_contract_expiry' => true,
            'in_app_notifications' => true,
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();
        $preferences = array_merge($this->defaultPreferences(), $user->notification_preferences ?? []);

        $this->email_partnership_updates = (bool) $preferences['email_partnership_updates'];
        $this->email_contract_expiry = (bool) $preferences['email_contract_expiry'];
        $this->in_app_notifications = (bool) $preferences['in_app_notifications'];
        $this->isConcessionaire = $user->role === 'concessionaire';
    }

    public function save(): void
    {
        $user = Auth::user();

        $payload = [
            'email_partnership_updates' => (bool) $this->email_partnership_updates,
            'email_contract_expiry' => $user->role === 'concessionaire'
                ? (bool) $this->email_contract_expiry
                : true,
            'in_app_notifications' => (bool) $this->in_app_notifications,
        ];

        $user->update([
            'notification_preferences' => $payload,
        ]);

        $this->dispatch('preferences-saved');
        session()->flash('status', 'notification-preferences-saved');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout
        :heading="__('Notification Preferences')"
        :subheading="__('Control which email and in-app updates you receive.')"
    >
        <form wire:submit="save" class="my-6 w-full space-y-5">
            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <flux:heading size="sm">{{ __('Partnership Updates') }}</flux:heading>
                            <flux:text class="text-sm text-zinc-500">{{ __('Application status and partnership workflow emails.') }}</flux:text>
                        </div>
                        <flux:switch wire:model="email_partnership_updates" />
                    </div>

                    @if ($isConcessionaire)
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <flux:heading size="sm">{{ __('Contract Expiry Warnings') }}</flux:heading>
                                <flux:text class="text-sm text-zinc-500">{{ __('Expiry warning and expiry notice emails for concessionaires.') }}</flux:text>
                            </div>
                            <flux:switch wire:model="email_contract_expiry" />
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <flux:heading size="sm">{{ __('In-app Notifications') }}</flux:heading>
                            <flux:text class="text-sm text-zinc-500">{{ __('Database notifications shown in the application.') }}</flux:text>
                        </div>
                        <flux:switch wire:model="in_app_notifications" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>

                <x-action-message on="preferences-saved">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>

            @if (session('status') === 'notification-preferences-saved')
                <flux:text class="font-medium !text-green-600 !dark:text-green-400">{{ __('Notification preferences saved successfully.') }}</flux:text>
            @endif
        </form>
    </x-pages::settings.layout>
</section>
