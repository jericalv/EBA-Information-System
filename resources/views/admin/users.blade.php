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

    /* Kebab actions menu */
    #users-data-table th.actions-col,
    #users-data-table td.actions-col {
        text-align: center;
        width: 56px;
    }
    .kebab-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border: 1px solid transparent;
        border-radius: 6px;
        background: transparent;
        color: #64748b;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 1px;
        line-height: 1;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }
    .kebab-btn:hover,
    .kebab-btn[aria-expanded="true"] {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #0f172a;
    }
    .kebab-btn:focus-visible {
        outline: 2px solid var(--green);
        outline-offset: 2px;
    }
    .kebab-menu {
        position: fixed;
        z-index: 1200;
        min-width: 200px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 12px 32px rgba(15,23,42,0.14);
        padding: 6px;
        display: none;
    }
    .kebab-menu.active {
        display: block;
    }
    .kebab-menu button {
        display: flex;
        align-items: center;
        gap: 9px;
        width: 100%;
        padding: 8px 10px;
        border: none;
        border-radius: 6px;
        background: transparent;
        font: inherit;
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        text-align: left;
        cursor: pointer;
        transition: background 0.12s ease;
    }
    .kebab-menu button:hover {
        background: #f1f5f9;
    }
    .kebab-menu button svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        color: #64748b;
    }
    .kebab-menu button.danger {
        color: #dc2626;
    }
    .kebab-menu button.danger svg {
        color: #dc2626;
    }
    .kebab-menu button.danger:hover {
        background: #fef2f2;
    }
    .kebab-menu .menu-divider {
        height: 1px;
        margin: 5px 4px;
        background: #e2e8f0;
    }

    /* User detail modal */
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 16px;
        margin-bottom: 4px;
    }
    .detail-item .detail-label {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 2px;
    }
    .detail-item .detail-value {
        font-size: 13px;
        color: #0f172a;
        font-weight: 600;
        overflow-wrap: anywhere;
    }
    .detail-item .detail-value .muted {
        color: #94a3b8;
        font-weight: 500;
    }
    .detail-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }
    .detail-pill.ok { background: rgba(10,92,47,0.1); color: var(--green); }
    .detail-pill.warn { background: #FEF3C7; color: #D97706; }
    .detail-pill.off { background: #F1F5F9; color: #64748b; }
    .detail-section-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: #64748b;
        margin: 18px 0 8px;
        padding-top: 14px;
        border-top: 1px solid #e2e8f0;
    }
    .activity-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 8px;
        max-height: 180px;
        overflow-y: auto;
    }
    .activity-list li {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 12.5px;
        color: #334155;
    }
    .activity-list li .activity-when {
        color: #94a3b8;
        font-size: 11.5px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .activity-empty {
        font-size: 12.5px;
        color: #94a3b8;
    }
    .detail-loading {
        text-align: center;
        padding: 24px 0;
        color: #94a3b8;
        font-size: 13px;
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
                                <button
                                    type="button"
                                    class="kebab-btn"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="Actions for {{ $user->name }}"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                    data-user-email="{{ $user->email }}"
                                    data-user-role="{{ $user->role }}"
                                    data-details-url="{{ route('admin.users.details', $user) }}"
                                    data-role-url="{{ route('admin.users.updateRole', $user) }}"
                                    data-reset-url="{{ route('admin.users.sendPasswordReset', $user) }}"
                                    data-delete-url="{{ route('admin.users.destroy', $user) }}"
                                    data-is-self="{{ $user->id === auth()->id() ? '1' : '0' }}"
                                >⋯</button>
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

    {{-- Shared row-actions menu (positioned next to the clicked ⋯ button) --}}
    <div class="kebab-menu" id="userActionsMenu" role="menu">
        <button type="button" id="menuViewDetails" role="menuitem">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View Details
        </button>
        <button type="button" id="menuChangeRole" role="menuitem">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8l2 2-4.5 4.5L14 15l.5-2.5L19 8z"/></svg>
            Change Role&hellip;
        </button>
        <button type="button" id="menuSendReset" role="menuitem">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Send Password Reset
        </button>
        <div class="menu-divider" id="menuDeleteDivider"></div>
        <button type="button" id="menuDeleteUser" class="danger" role="menuitem">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            Delete User
        </button>
    </div>

    {{-- Change-role modal --}}
    <div class="modal-backdrop" id="changeRoleModal">
        <div class="modal" style="width:min(420px,100%);">
            <h3>Change Role</h3>
            <div class="notice" id="changeRoleNotice"></div>
            <form id="changeRoleForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="field">
                    <label for="change_role_select">New Role</label>
                    <select id="change_role_select" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="concessionaire">Concessionaire</option>
                        <option value="cashier">Cashier</option>
                        <option value="faculty">Faculty</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-outline" data-close-modal="changeRoleModal">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-green">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- User detail modal --}}
    <div class="modal-backdrop" id="userDetailModal">
        <div class="modal">
            <h3 id="userDetailTitle">User Details</h3>
            <div id="userDetailBody">
                <div class="detail-loading">Loading&hellip;</div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" data-close-modal="userDetailModal">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Close
                </button>
            </div>
        </div>
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

    // === Row actions (kebab) menu ===
    const userActionsMenu = document.getElementById('userActionsMenu');
    const menuDeleteButton = document.getElementById('menuDeleteUser');
    const menuDeleteDivider = document.getElementById('menuDeleteDivider');
    const changeRoleModal = document.getElementById('changeRoleModal');
    const changeRoleForm = document.getElementById('changeRoleForm');
    const changeRoleNotice = document.getElementById('changeRoleNotice');
    const changeRoleSelect = document.getElementById('change_role_select');
    const userDetailModal = document.getElementById('userDetailModal');
    const userDetailTitle = document.getElementById('userDetailTitle');
    const userDetailBody = document.getElementById('userDetailBody');
    const pageCsrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    let activeKebab = null;
    let menuUser = null;

    function closeActionsMenu() {
        userActionsMenu.classList.remove('active');
        if (activeKebab) {
            activeKebab.setAttribute('aria-expanded', 'false');
            activeKebab = null;
        }
    }

    function openActionsMenu(button) {
        menuUser = { ...button.dataset };
        const isSelf = menuUser.isSelf === '1';
        menuDeleteButton.style.display = isSelf ? 'none' : '';
        menuDeleteDivider.style.display = isSelf ? 'none' : '';

        userActionsMenu.classList.add('active');
        activeKebab = button;
        button.setAttribute('aria-expanded', 'true');

        const rect = button.getBoundingClientRect();
        const menuRect = userActionsMenu.getBoundingClientRect();
        let left = rect.right - menuRect.width;
        let top = rect.bottom + 6;
        if (left < 8) left = 8;
        if (top + menuRect.height > window.innerHeight - 8) {
            top = rect.top - menuRect.height - 6;
        }
        userActionsMenu.style.left = `${left}px`;
        userActionsMenu.style.top = `${top}px`;
    }

    document.querySelectorAll('.kebab-btn').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            if (activeKebab === button) {
                closeActionsMenu();
            } else {
                closeActionsMenu();
                openActionsMenu(button);
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (userActionsMenu.classList.contains('active') && !userActionsMenu.contains(event.target)) {
            closeActionsMenu();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeActionsMenu();
            changeRoleModal.classList.remove('active');
            userDetailModal.classList.remove('active');
        }
    });
    window.addEventListener('scroll', closeActionsMenu, true);
    window.addEventListener('resize', closeActionsMenu);

    function submitHiddenForm(action, method) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = action;

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = pageCsrfToken;
        form.appendChild(tokenInput);

        if (method !== 'POST') {
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = method;
            form.appendChild(methodInput);
        }

        document.body.appendChild(form);
        form.submit();
    }

    // --- Change role ---
    let roleModalUser = null;

    document.getElementById('menuChangeRole').addEventListener('click', () => {
        if (!menuUser) return;
        roleModalUser = menuUser;
        changeRoleForm.action = roleModalUser.roleUrl;
        changeRoleSelect.value = roleModalUser.userRole;
        if (changeRoleSelect.value !== roleModalUser.userRole) {
            changeRoleSelect.selectedIndex = -1; // legacy/invalid role not in the list
        }
        changeRoleNotice.textContent = `Update the role for ${roleModalUser.userName} (${roleModalUser.userEmail}).`;
        closeActionsMenu();
        changeRoleModal.classList.add('active');
    });

    changeRoleForm.addEventListener('submit', (event) => {
        const name = roleModalUser ? roleModalUser.userName : 'this user';
        if (!confirm(`Change ${name}'s role to ${changeRoleSelect.value}?`)) {
            event.preventDefault();
        }
    });

    // --- Send password reset ---
    document.getElementById('menuSendReset').addEventListener('click', () => {
        if (!menuUser) return;
        const { userName, userEmail, resetUrl } = menuUser;
        closeActionsMenu();
        if (confirm(`Send a password reset link to ${userName} (${userEmail})?`)) {
            submitHiddenForm(resetUrl, 'POST');
        }
    });

    // --- Delete ---
    menuDeleteButton.addEventListener('click', () => {
        if (!menuUser) return;
        const { userName, deleteUrl } = menuUser;
        closeActionsMenu();
        if (confirm(`Delete ${userName}? This cannot be undone.`)) {
            submitHiddenForm(deleteUrl, 'DELETE');
        }
    });

    // --- View details ---
    function detailItem(label, valueNode) {
        const item = document.createElement('div');
        item.className = 'detail-item';
        const labelEl = document.createElement('div');
        labelEl.className = 'detail-label';
        labelEl.textContent = label;
        item.appendChild(labelEl);
        const valueEl = document.createElement('div');
        valueEl.className = 'detail-value';
        if (typeof valueNode === 'string' || valueNode === null || valueNode === undefined) {
            if (valueNode) {
                valueEl.textContent = valueNode;
            } else {
                const muted = document.createElement('span');
                muted.className = 'muted';
                muted.textContent = '—';
                valueEl.appendChild(muted);
            }
        } else {
            valueEl.appendChild(valueNode);
        }
        item.appendChild(valueEl);
        return item;
    }

    function detailPill(text, tone) {
        const pill = document.createElement('span');
        pill.className = `detail-pill ${tone}`;
        pill.textContent = text;
        return pill;
    }

    function renderUserDetails(data) {
        userDetailBody.textContent = '';

        const grid = document.createElement('div');
        grid.className = 'detail-grid';
        grid.appendChild(detailItem('Full Name', data.name));
        grid.appendChild(detailItem('Email', data.email));
        grid.appendChild(detailItem('Role', data.role ? data.role.charAt(0).toUpperCase() + data.role.slice(1) : null));
        grid.appendChild(detailItem('Phone', data.phone_number));
        grid.appendChild(detailItem('Joined', data.joined ? `${data.joined} (${data.joined_relative})` : null));
        grid.appendChild(detailItem(
            'Email Verified',
            data.email_verified_at ? detailPill(`Verified ${data.email_verified_at}`, 'ok') : detailPill('Unverified', 'warn')
        ));
        grid.appendChild(detailItem(
            'Two-Factor Auth',
            data.two_factor_enabled ? detailPill('Enabled', 'ok') : detailPill('Not enabled', 'off')
        ));

        if (data.role === 'concessionaire') {
            grid.appendChild(detailItem(
                'Account Status',
                data.is_active_concessionaire ? detailPill('Active', 'ok') : detailPill('Inactive', 'off')
            ));
            grid.appendChild(detailItem('Business Name', data.business_name));
            grid.appendChild(detailItem('Monthly Fee', data.monthly_fee ? `₱${data.monthly_fee}` : null));
            grid.appendChild(detailItem('Location', data.location));
        }
        userDetailBody.appendChild(grid);

        const activityTitle = document.createElement('div');
        activityTitle.className = 'detail-section-title';
        activityTitle.textContent = 'Recent Activity';
        userDetailBody.appendChild(activityTitle);

        if (Array.isArray(data.recent_activity) && data.recent_activity.length > 0) {
            const list = document.createElement('ul');
            list.className = 'activity-list';
            data.recent_activity.forEach((entry) => {
                const item = document.createElement('li');
                const description = document.createElement('span');
                description.textContent = entry.description || entry.action;
                const when = document.createElement('span');
                when.className = 'activity-when';
                when.textContent = entry.when;
                item.appendChild(description);
                item.appendChild(when);
                list.appendChild(item);
            });
            userDetailBody.appendChild(list);
        } else {
            const empty = document.createElement('div');
            empty.className = 'activity-empty';
            empty.textContent = 'No recorded activity for this account.';
            userDetailBody.appendChild(empty);
        }
    }

    document.getElementById('menuViewDetails').addEventListener('click', async () => {
        if (!menuUser) return;
        const { userName, detailsUrl } = menuUser;
        closeActionsMenu();

        userDetailTitle.textContent = userName;
        userDetailBody.innerHTML = '<div class="detail-loading">Loading&hellip;</div>';
        userDetailModal.classList.add('active');

        try {
            const response = await fetch(detailsUrl, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Request failed');
            renderUserDetails(await response.json());
        } catch (error) {
            userDetailBody.innerHTML = '<div class="detail-loading">Could not load user details. Please try again.</div>';
        }
    });

    // Generic close handling for the new modals
    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById(button.dataset.closeModal)?.classList.remove('active');
        });
    });
    [changeRoleModal, userDetailModal].forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.remove('active');
            }
        });
    });

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
