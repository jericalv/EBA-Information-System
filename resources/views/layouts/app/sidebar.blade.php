<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-stone-100 dark:bg-zinc-800">
        @include('partials.pending-application-banner')

        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                @php
                    $sidebarUser = auth()->user();
                    $avatarUrl = $sidebarUser->profile_photo ? asset('storage/' . $sidebarUser->profile_photo) : null;
                @endphp
                <flux:profile
                    :initials="$sidebarUser->initials()"
                    :avatar="$avatarUrl"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        @php
                            $role = $sidebarUser->role;
                            $roleLabel = match ($role) {
                                'cashier' => 'Cashier',
                                'faculty' => 'Faculty',
                                'concessionaire' => 'Concessionaire',
                                default => 'Admin',
                            };
                            $roleClasses = match ($role) {
                                'cashier' => 'bg-blue-100 text-blue-700',
                                'faculty' => 'bg-emerald-100 text-emerald-700',
                                'concessionaire' => 'bg-amber-100 text-amber-800',
                                default => 'bg-violet-100 text-violet-700',
                            };
                            $quickLink = match ($role) {
                                'concessionaire' => ['href' => route('concessionaire.dashboard'), 'label' => 'Dashboard', 'icon' => 'home'],
                                'cashier' => ['href' => route('home'), 'label' => 'Home', 'icon' => 'home'],
                                'faculty' => ['href' => route('staff.dashboard'), 'label' => 'Dashboard', 'icon' => 'home'],
                                default => null,
                            };
                            $hasPartnershipApplication = $role === 'concessionaire' ? \App\Models\PartnershipApplication::where(function ($query) use ($sidebarUser) {
                                $query->where('user_id', $sidebarUser->id)
                                    ->orWhere(function ($q) use ($sidebarUser) {
                                        $q->whereNull('user_id')->where('email', $sidebarUser->email);
                                    });
                            })->exists() : false;
                        @endphp
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="$sidebarUser->name"
                                    :initials="$sidebarUser->initials()"
                                    :src="$avatarUrl"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <div class="flex items-center gap-2">
                                        <flux:heading class="truncate">{{ $sidebarUser->name }}</flux:heading>
                                        <span class="inline-flex w-fit items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $roleClasses }}">
                                            {{ $roleLabel }}
                                        </span>
                                    </div>
                                    <flux:text class="truncate">{{ $sidebarUser->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        @if ($quickLink)
                            <flux:menu.item :href="$quickLink['href']" :icon="$quickLink['icon']" wire:navigate>
                                {{ __($quickLink['label']) }}
                            </flux:menu.item>
                        @endif
                        @if ($role === 'concessionaire')
                            <flux:menu.item :href="route('application')" icon="document-text" wire:navigate>
                                {{ __('Application Status') }}
                            </flux:menu.item>
                        @endif
                        @if ($role === 'faculty')
                            <flux:menu.item :href="route('staff.history')" icon="clock" wire:navigate>
                                {{ __('History') }}
                            </flux:menu.item>
                        @endif
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
