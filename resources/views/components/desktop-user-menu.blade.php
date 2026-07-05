<flux:dropdown position="bottom" align="start">
    @php
        $menuUser = auth()->user();
        $role = $menuUser->role;
        $roleLabel = match ($role) {
            'admin' => 'Admin',
            'cashier' => 'Cashier',
            'faculty' => 'Faculty',
            'concessionaire' => 'Concessionaire',
            'student' => 'Student',
            default => 'Invalid Role',
        };
        $roleClasses = match ($role) {
            'admin' => 'bg-violet-100 text-violet-700',
            'cashier' => 'bg-blue-100 text-blue-700',
            'faculty' => 'bg-emerald-100 text-emerald-700',
            'concessionaire' => 'bg-amber-100 text-amber-800',
            'student' => 'bg-green-100 text-green-700',
            default => 'bg-red-100 text-red-700',
        };
        $avatarUrl = $menuUser->profile_photo ? asset('storage/' . $menuUser->profile_photo) : null;
        $quickLink = match ($role) {
            'concessionaire' => ['href' => route('concessionaire.dashboard'), 'label' => 'Dashboard', 'icon' => 'home'],
            'cashier' => ['href' => route('home'), 'label' => 'Home', 'icon' => 'home'],
            'faculty' => ['href' => route('staff.dashboard'), 'label' => 'Dashboard', 'icon' => 'home'],
            default => null,
        };
        $hasPartnershipApplication = $role === 'concessionaire' ? \App\Models\PartnershipApplication::where(function ($query) use ($menuUser) {
            $query->where('user_id', $menuUser->id)
                ->orWhere(function ($q) use ($menuUser) {
                    $q->whereNull('user_id')->where('email', $menuUser->email);
                });
        })->exists() : false;
    @endphp

    <flux:sidebar.profile
        :name="$menuUser->name"
        :initials="$menuUser->initials()"
        :avatar="$avatarUrl"
        icon:trailing="chevrons-up-down"
        data-test="sidebar-menu-button"
    />

    <flux:menu>
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <flux:avatar
                :name="$menuUser->name"
                :initials="$menuUser->initials()"
                :src="$avatarUrl"
            />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <div class="flex items-center gap-2">
                    <flux:heading class="truncate">{{ $menuUser->name }}</flux:heading>
                    <span class="inline-flex w-fit items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $roleClasses }}">
                        {{ $roleLabel }}
                    </span>
                </div>
                <flux:text class="truncate">{{ $menuUser->email }}</flux:text>
            </div>
        </div>
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
                @if (! $menuUser->is_approved)
                    <flux:menu.item :href="route('application')" icon="clipboard-document-list" wire:navigate>
                        {{ __('My Application') }}
                    </flux:menu.item>
                @endif
            @endif
            @if ($role === 'cashier')
                <flux:menu.item :href="route('cashier.payments')" icon="credit-card" wire:navigate>
                    {{ __('Payments') }}
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
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>
