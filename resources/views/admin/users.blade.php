@extends('admin.layout')

@section('title', 'Manage Users')

@section('extra-css')
<style>
    .users-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .users-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .users-filter-actions {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    tbody tr.hidden {
        display: none;
    }

    /* === Consistent button system (icon + label) === */
    .btn svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }
    .btn-sm svg {
        width: 14px;
        height: 14px;
    }
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .btn:focus-visible {
        outline: 2px solid var(--green);
        outline-offset: 2px;
    }
    .btn-red:focus-visible {
        outline-color: #dc2626;
    }
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1100;
        padding: 16px;
    }
    .modal-backdrop.active {
        display: flex;
    }
    .modal {
        width: min(560px, 100%);
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    }
    .modal h3 {
        margin: 0 0 14px;
        font-size: 20px;
        color: #064420;
    }
    .modal .notice {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 14px;
    }
    .field {
        display: grid;
        gap: 6px;
        margin-bottom: 12px;
    }
    .field label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }
    .field input,
    .field select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font: inherit;
        background: #fff;
        color: #0f172a;
    }
    .field input[readonly] {
        background: #f8fafc;
        color: #475569;
    }
    .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 16px;
    }
    .modal-feedback {
        display: none;
        margin-top: 12px;
        padding: 12px 14px;
        border-radius: 8px;
        font-size: 13px;
    }
    .modal-feedback.error {
        display: block;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    .modal-feedback.success {
        display: block;
        background: rgba(10,92,47,0.08);
        border: 1px solid rgba(10,92,47,0.18);
        color: #0a5c2f;
    }

    /* === Compact Users Table === */
    #users-data-table thead th {
        padding: 8px 16px;
        font-size: 10.5px;
        letter-spacing: 0.6px;
        white-space: nowrap;
    }
    #users-data-table td {
        padding: 8px 16px;
        font-size: 12.5px;
    }
    #users-data-table tbody tr {
        transition: background 0.15s ease;
    }
    #users-data-table .user-cell {
        gap: 10px;
    }
    #users-data-table .user-avatar {
        width: 32px;
        height: 32px;
        font-size: 11px;
        background: linear-gradient(135deg, var(--green) 0%, var(--green-light) 100%);
        box-shadow: 0 2px 4px rgba(10,92,47,0.18);
    }
    #users-data-table .user-name {
        font-size: 12.5px;
        line-height: 1.3;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    #users-data-table .user-email {
        font-size: 11px;
        margin-top: 1px;
        color: #64748b;
    }
    .you-badge {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        background: rgba(10,92,47,0.1);
        color: var(--green);
        padding: 1px 6px;
        border-radius: 999px;
    }

    /* Role badge with dot */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
        line-height: 1;
    }
    .role-badge .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
    }

    .joined-cell {
        color: #475569;
        font-size: 12px;
        white-space: nowrap;
    }
    .joined-cell .joined-rel {
        display: block;
        font-size: 10.5px;
        color: #94a3b8;
        margin-top: 1px;
    }

    /* Role update + actions */
    .role-select-sm {
        padding: 5px 8px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 12px;
        font-family: inherit;
        background: #f8fafc;
        color: #1e293b;
        cursor: pointer;
        min-width: 128px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .role-select-sm:focus {
        outline: none;
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(10,92,47,0.12);
        background: #fff;
    }
    #users-data-table th.actions-col,
    #users-data-table td.actions-col {
        text-align: center;
    }
    .actions-cell-inner {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }
    .role-update-form {
        display: flex;
        gap: 6px;
        align-items: center;
    }
</style>
@endsection

@section('content')
    <!-- Search & Filter -->
    <div class="card" style="margin-bottom: 20px;">
        <div style="padding: 16px 24px;">
            <div class="users-toolbar" style="margin-bottom:14px;">
                <div>
                    <h3 style="margin:0;font-size:18px;color:#064420;">Users</h3>
                    <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Manage all user accounts from one place.</p>
                </div>
                <div class="users-toolbar-actions">
                    <button type="button" class="btn btn-green" id="openStaffAccountModalButton">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        Create Staff Account
                    </button>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.users') }}" class="toolbar">
                @php
                    $activeSort = in_array(request('sort'), ['asc', 'desc'], true) ? request('sort') : 'desc';
                @endphp
                <div class="search-box">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input id="users-search-input" type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" oninput="filterRows()">
                </div>
                <select name="sort" class="filter-select" onchange="this.form.submit()">
                    <option value="asc" {{ $activeSort === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ $activeSort === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
                <select name="role" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="concessionaire" {{ request('role') === 'concessionaire' ? 'selected' : '' }}>Concessionaire</option>
                    <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                    <option value="faculty" {{ request('role') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                </select>

            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h3>Users ({{ $users->total() }})</h3>
        </div>
        <div class="card-body">
            <table id="users-data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr
                            data-row="user"
                            data-name="{{ strtolower($user->name) }}"
                            data-email="{{ strtolower($user->email) }}"
                            data-role="{{ strtolower($user->role) }}"
                        >
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">{{ $user->initials() }}</div>
                                    <div>
                                        <div class="user-name">
                                            {{ $user->name }}
                                            @if ($user->id === auth()->id())
                                                <span class="you-badge">You</span>
                                            @endif
                                        </div>
                                        <div class="user-email">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleBadgeStyle = match ($user->role) {
                                        'admin'          => 'background:#FEE2E2;color:#DC2626;',
                                        'cashier'        => 'background:#DBEAFE;color:#1D4ED8;',
                                        'faculty'        => 'background:#EDE9FE;color:#7C3AED;',
                                        'concessionaire' => 'background:#D1FAE5;color:#059669;',
                                        'student'        => 'background:#FEF3C7;color:#D97706;',
                                        default          => 'background:#F1F5F9;color:#475569;',
                                    };
                                @endphp
                                <span class="role-badge" style="{{ $roleBadgeStyle }}">
                                    <span class="dot"></span>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="joined-cell">
                                    {{ $user->created_at->format('M d, Y') }}
                                    <span class="joined-rel">{{ $user->created_at->diffForHumans() }}</span>
                                </span>
                            </td>
                            <td class="actions-col">
                                <div class="actions-cell-inner">
                                    {{-- Manual role override --}}
                                    <form method="POST" action="{{ route('admin.users.updateRole', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        @php
                                            $validRoles = ['admin', 'cashier', 'concessionaire', 'student', 'faculty'];
                                            $hasInvalidRole = ! in_array($user->role, $validRoles, true);
                                        @endphp
                                        <div class="role-update-form">
                                            <select name="role" class="role-select-sm" required>
                                                @if ($hasInvalidRole)
                                                    <option value="" selected>⚠ Invalid Role</option>
                                                @endif
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="concessionaire" {{ $user->role === 'concessionaire' ? 'selected' : '' }}>Concessionaire</option>
                                                <option value="cashier" {{ $user->role === 'cashier' ? 'selected' : '' }}>Cashier</option>
                                                <option value="faculty" {{ $user->role === 'faculty' ? 'selected' : '' }}>Faculty</option>
                                                <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                            </select>
                                            <button type="submit" class="btn btn-green btn-sm"
                                                onclick="return confirm('Change {{ $user->name }}\'s role to ' + this.form.role.value + '?')">
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                                Update Role
                                            </button>
                                        </div>
                                    </form>

                                    {{-- Delete --}}
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-red btn-sm"
                                                onclick="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:32px; color:#94a3b8;">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                    <tr id="users-no-results-row" style="display:none;">
                        <td colspan="4" style="text-align:center; padding:32px; color:#94a3b8;">
                            No results found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="pagination-wrap">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <div class="modal-backdrop" id="staffAccountModal">
        <div class="modal">
            <h3>Create Staff Account</h3>
            <div class="notice">Create an internal cashier or faculty account without using public registration.</div>

            <form id="staffAccountForm" method="POST" action="{{ route('admin.staff.create') }}">
                @csrf

                <div class="field">
                    <label for="staff_name">Full Name</label>
                    <input id="staff_name" name="name" type="text" maxlength="255" required>
                </div>

                <div class="field">
                    <label for="staff_email">Email Address</label>
                    <input id="staff_email" name="email" type="email" required>
                </div>

                <div class="field">
                    <label for="staff_role">Role</label>
                    <select id="staff_role" name="role" required>
                        <option value="cashier">Cashier</option>
                        <option value="faculty">Faculty</option>
                    </select>
                </div>

                <div class="field">
                    <label for="staff_password">Password</label>
                    <input id="staff_password" name="password" type="password" minlength="8" required>
                </div>

                <div class="field">
                    <label for="staff_password_confirmation">Confirm Password</label>
                    <input id="staff_password_confirmation" name="password_confirmation" type="password" minlength="8" required>
                </div>

                <div id="staffAccountFeedback" class="modal-feedback"></div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-outline" id="closeStaffAccountModalButton">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Close
                    </button>
                    <button type="submit" class="btn btn-green" id="submitStaffAccountButton">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        <span id="submitStaffAccountButtonLabel">Create Account</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function filterRows() {
        const input = document.getElementById('users-search-input');
        const rows = document.querySelectorAll('tr[data-row="user"]');
        const noResultsRow = document.getElementById('users-no-results-row');

        if (!input || rows.length === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
            return;
        }

        const query = input.value.trim().toLowerCase();
        let visibleRows = 0;

        rows.forEach((row) => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const role = row.dataset.role || '';
            const matches = !query || `${name} ${email} ${role}`.includes(query);

            row.classList.toggle('hidden', !matches);
            if (matches) {
                visibleRows += 1;
            }
        });

        if (noResultsRow) {
            noResultsRow.style.display = visibleRows === 0 ? '' : 'none';
        }
    }

    const staffAccountModal = document.getElementById('staffAccountModal');
    const staffAccountForm = document.getElementById('staffAccountForm');
    const openStaffAccountModalButton = document.getElementById('openStaffAccountModalButton');
    const closeStaffAccountModalButton = document.getElementById('closeStaffAccountModalButton');
    const submitStaffAccountButton = document.getElementById('submitStaffAccountButton');
    const submitStaffAccountButtonLabel = document.getElementById('submitStaffAccountButtonLabel');
    const staffAccountFeedback = document.getElementById('staffAccountFeedback');
    const staffPasswordInput = document.getElementById('staff_password');
    const staffPasswordConfirmationInput = document.getElementById('staff_password_confirmation');

    function setStaffAccountFeedback(message, type) {
        staffAccountFeedback.classList.remove('error', 'success');

        if (!message) {
            staffAccountFeedback.textContent = '';
            staffAccountFeedback.style.display = 'none';
            return;
        }

        staffAccountFeedback.textContent = message;
        staffAccountFeedback.classList.add(type === 'success' ? 'success' : 'error');
        staffAccountFeedback.style.display = 'block';
    }

    function openStaffAccountModal() {
        setStaffAccountFeedback('', 'error');
        staffAccountForm.reset();
        staffAccountModal.classList.add('active');
    }

    function closeStaffAccountModal() {
        setStaffAccountFeedback('', 'error');
        staffAccountForm.reset();
        staffAccountModal.classList.remove('active');
    }

    openStaffAccountModalButton?.addEventListener('click', openStaffAccountModal);
    closeStaffAccountModalButton?.addEventListener('click', closeStaffAccountModal);

    staffAccountModal?.addEventListener('click', function (event) {
        if (event.target === staffAccountModal) {
            closeStaffAccountModal();
        }
    });

    staffAccountForm?.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (staffPasswordInput.value !== staffPasswordConfirmationInput.value) {
            setStaffAccountFeedback('Passwords do not match.', 'error');
            return;
        }

        const originalButtonText = submitStaffAccountButtonLabel.textContent;
        submitStaffAccountButton.disabled = true;
        submitStaffAccountButtonLabel.textContent = 'Creating...';
        setStaffAccountFeedback('', 'error');

        const formData = new FormData(staffAccountForm);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        formData.append('_token', csrfToken);

        try {
            const response = await fetch(staffAccountForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success) {
                setStaffAccountFeedback(data.message || 'Account created successfully.', 'success');
                setTimeout(() => {
                    closeStaffAccountModal();
                    window.location.reload();
                }, 2000);
                return;
            }

            if (response.status === 422 && data.errors) {
                const firstError = Object.values(data.errors).flat().shift();
                setStaffAccountFeedback(firstError || 'Validation failed.', 'error');
                return;
            }

            setStaffAccountFeedback(data.message || 'Something went wrong. Please try again.', 'error');
        } catch (error) {
            setStaffAccountFeedback('Something went wrong. Please try again.', 'error');
        } finally {
            submitStaffAccountButton.disabled = false;
            submitStaffAccountButtonLabel.textContent = originalButtonText;
        }
    });

    filterRows();
</script>
@endsection
