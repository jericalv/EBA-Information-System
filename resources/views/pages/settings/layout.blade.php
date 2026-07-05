@php
    $settingsUser = auth()->user();
    $settingsRoleLabel = match ($settingsUser->role) {
        'admin' => 'Admin',
        'cashier' => 'Cashier',
        'concessionaire' => 'Concessionaire',
        'student' => 'Student',
        default => 'Invalid Role',
    };
@endphp

<div class="w-full">
    <div class="section-card" style="margin-bottom:14px;">
        <div class="user-summary">
            @if ($settingsUser->profile_photo)
                <img src="{{ asset('storage/' . $settingsUser->profile_photo) }}" alt="{{ $settingsUser->name }}" class="avatar">
            @else
                <div class="avatar">{{ $settingsUser->initials() }}</div>
            @endif

            <div style="min-width:0;">
                <div style="font-size:15px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $settingsUser->name }}</div>
                <span class="badge-role">{{ $settingsRoleLabel }}</span>
            </div>
        </div>
    </div>

    <h2 style="margin:0 0 4px;color:#065f46;font-size:20px;font-weight:800;">{{ $heading ?? '' }}</h2>
    <p style="margin:0 0 14px;color:#64748b;font-size:14px;">{{ $subheading ?? '' }}</p>

    <div class="section-card">
        {{ $slot }}
    </div>
</div>
