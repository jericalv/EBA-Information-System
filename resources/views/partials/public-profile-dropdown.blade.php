@php
    $compactTrigger = $compactTrigger ?? false;
    $publicUser = auth()->user();
    $role = $publicUser->role;
    $roleLabel = match ($role) {
        'admin' => 'Admin',
        'cashier' => 'Cashier',
        'faculty' => 'Faculty',
        'concessionaire' => 'Concessionaire',
        'student' => 'Student',
        default => 'Invalid Role',
    };

    $roleStyle = match ($role) {
        'admin' => 'background:#ede9fe;color:#5b21b6;',
        'cashier' => 'background:#dbeafe;color:#1d4ed8;',
        'faculty' => 'background:#dcfce7;color:#166534;',
        'concessionaire' => 'background:#fef3c7;color:#92400e;',
        'student' => 'background:#dcfce7;color:#15803d;',
        default => 'background:#fee2e2;color:#b91c1c;',
    };

@endphp

@once
<style>
    .public-profile-dropdown {
        display: inline-block;
    }

    .public-profile-dropdown-panel {
        right: 0;
    }

    @media (max-width: 768px) {
        .nav-mobile-actions .nav-profile-mobile {
            display: none;
        }

        .nav-links.active .public-profile-dropdown {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .nav-links.active .public-profile-dropdown-panel {
            left: 50%;
            right: auto;
            transform: translateX(-50%);
            margin-left: auto;
            margin-right: auto;
        }
    }
</style>
@endonce

<details class="public-profile-dropdown" style="position:relative;">
    <summary style="list-style:none;cursor:pointer;display:flex;align-items:center;gap:8px;border:1px solid #d1d5db;background:#fff;padding:6px {{ $compactTrigger ? '8px' : '10px' }};border-radius:999px;">
        @if ($publicUser->profile_photo)
            <img src="{{ asset('storage/' . $publicUser->profile_photo) }}" alt="{{ $publicUser->name }}" style="width:30px;height:30px;border-radius:999px;object-fit:cover;">
        @else
            <span style="width:30px;height:30px;border-radius:999px;background:#e2e8f0;color:#334155;font-weight:700;font-size:12px;display:inline-flex;align-items:center;justify-content:center;">{{ $publicUser->initials() }}</span>
        @endif
        @if (! $compactTrigger)
            <span style="font-size:13px;font-weight:700;color:#334155;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $publicUser->name }}</span>
        @endif
    </summary>

    <div class="public-profile-dropdown-panel" style="position:absolute;top:calc(100% + 8px);width:min(280px,calc(100vw - 24px));max-width:280px;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 14px 34px rgba(0,0,0,0.14);padding:12px;z-index:2000;">
        <div style="display:flex;gap:10px;align-items:center;padding-bottom:10px;border-bottom:1px solid #eef2f7;">
            @if ($publicUser->profile_photo)
                <img src="{{ asset('storage/' . $publicUser->profile_photo) }}" alt="{{ $publicUser->name }}" style="width:40px;height:40px;border-radius:999px;object-fit:cover;">
            @else
                <span style="width:40px;height:40px;border-radius:999px;background:#e2e8f0;color:#334155;font-weight:700;display:inline-flex;align-items:center;justify-content:center;">{{ $publicUser->initials() }}</span>
            @endif
            <div style="min-width:0;">
                <div style="font-size:14px;font-weight:700;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $publicUser->name }}</div>
                <div style="font-size:12px;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $publicUser->email }}</div>
                <span style="margin-top:4px;display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;{{ $roleStyle }}">{{ $roleLabel }}</span>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:6px;padding-top:10px;">
            @if ($role === 'concessionaire')
                @if ($publicUser->is_approved)
                    <a href="{{ route('concessionaire.dashboard') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Dashboard</a>
                    <a href="{{ route('concessionaire.products') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">My Products</a>
                @endif
                <a href="{{ route('settings.application') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">My Application</a>
                <a href="{{ route('profile.edit') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Settings</a>
            @elseif ($role === 'admin')
                <a href="{{ route('dashboard') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Dashboard</a>
            @elseif ($role === 'cashier')
                <a href="{{ route('home') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Home</a>
                <a href="{{ route('cashier.payments') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Payments</a>
                <a href="{{ route('profile.edit') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Settings</a>
            @elseif ($role === 'faculty')
                <a href="{{ route('staff.partnerships.index') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Partnerships</a>
                <a href="{{ route('staff.concessionaires.index') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Concessionaires</a>
                <a href="{{ route('staff.history') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">History</a>
                <a href="{{ route('profile.edit') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Settings</a>
            @elseif ($role === 'student')
                <a href="{{ route('home') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Home</a>
                <a href="{{ route('profile.edit') }}" style="text-decoration:none;color:#334155;font-weight:600;padding:8px 10px;border-radius:8px;">Settings</a>
            @else
                <span style="color:#b91c1c;font-weight:700;font-size:12px;padding:8px 10px;">Unknown role. Contact admin.</span>
            @endif

            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" style="width:100%;text-align:left;border:none;background:#fee2e2;color:#b91c1c;font-weight:700;padding:8px 10px;border-radius:8px;cursor:pointer;">Log out</button>
            </form>
        </div>
    </div>
</details>
