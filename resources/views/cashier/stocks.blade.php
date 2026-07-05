@extends('faculty.layout')

@section('title', 'Stocks')

@section('content')
<style>
    .stocks-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .stocks-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        border-top-width: 3px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .stocks-card-blue { border-top-color: #3b82f6; }
    .stocks-card-indigo { border-top-color: #6366f1; }
    .stocks-card-green { border-top-color: #10b981; }
    .stocks-card-gray { border-top-color: #64748b; }
    .stocks-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .stocks-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stocks-card-icon svg {
        width: 20px;
        height: 20px;
        color: #fff;
    }
    .stocks-card-icon-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .stocks-card-icon-indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
    .stocks-card-icon-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stocks-card-icon-gray { background: linear-gradient(135deg, #64748b 0%, #475569 100%); }
    .stocks-card-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }
    .stocks-card-value {
        font-size: 30px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .stocks-card-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        margin-top: auto;
        width: fit-content;
    }
    .stocks-card-status-green {
        background: #dcfce7;
        color: #166534;
    }
    .stocks-card-status-amber {
        background: #fef3c7;
        color: #92400e;
    }
    .stocks-card-status-neutral {
        background: #f1f5f9;
        color: #475569;
    }

    .stocks-grid {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 20px;
        align-items: start;
    }

    .stocks-form-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        padding: 24px;
    }
    .stocks-form-card h3 {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
    }
    .stocks-field {
        margin-bottom: 14px;
    }
    .stocks-field label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    .stocks-field input[type="text"],
    .stocks-field input[type="number"],
    .stocks-field input[type="file"] {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        color: #0f172a;
        background: #f8fafc;
    }
    .stocks-field input[type="text"],
    .stocks-field input[type="number"] {
        height: 42px;
        padding: 0 14px;
    }
    .stocks-field input[type="file"] {
        padding: 10px;
    }
    .stocks-field input:focus {
        outline: none;
        border-color: #0a5c2f;
        box-shadow: 0 0 0 3px rgba(10,92,47,0.08);
        background: #fff;
    }
    .stocks-help {
        margin-top: 5px;
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
    }
    .stocks-visible-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        margin: 16px 0 20px;
        color: #334155;
        font-weight: 500;
    }
    .stocks-visible-row input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .stocks-thumb {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        overflow: hidden;
        background: #f8fafc;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
    }
    .stocks-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .stocks-thumb svg {
        width: 22px;
        height: 22px;
        color: #94a3b8;
    }

    .search-box {
        position: relative;
        display: inline-flex;
        align-items: center;
        width: 100%;
    }
    .search-box svg {
        position: absolute;
        left: 12px;
        width: 16px;
        height: 16px;
        color: #94a3b8;
        pointer-events: none;
    }
    .search-box input {
        width: 100%;
        height: 40px;
        padding: 0 12px 0 36px;
        border: 1px solid #dbe2ea;
        border-radius: 10px;
        font-size: 14px;
        color: #0f172a;
        background: #fff;
    }
    .search-box input:focus {
        outline: none;
        border-color: #0a5c2f;
        box-shadow: 0 0 0 3px rgba(10,92,47,0.08);
    }

    .stocks-table-card table {
        width: 100%;
        border-collapse: collapse;
        min-width: 720px;
    }
    .stocks-table-card th,
    .stocks-table-card td {
        padding: 14px 16px;
        border-bottom: 1px solid #eef2f7;
        text-align: left;
        vertical-align: middle;
    }
    .stocks-table-card th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .stocks-table-card tbody tr:last-child td {
        border-bottom: none;
    }

    .stocks-table-card {
        position: relative;
        overflow: visible !important;
    }
    .stocks-table-card .card-body,
    .stocks-table-card table,
    .stocks-table-card tbody,
    .stocks-table-card tbody tr,
    .stocks-table-card tbody td {
        overflow: visible !important;
    }

    .actions-dropdown-container {
        position: relative;
        display: inline-block;
        text-align: left;
    }
    .btn-actions-trigger {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #cbd5e1;
        padding: 5px 8px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
        height: 28px;
        line-height: 1;
        box-sizing: border-box;
    }
    .btn-actions-trigger:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #0f172a;
    }
    .btn-actions-trigger svg {
        width: 12px;
        height: 12px;
    }
    .actions-dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 4px);
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        z-index: 60;
        width: 170px;
        overflow: hidden;
        display: none;
    }
    .actions-dropdown-menu.active {
        display: block;
    }
    .actions-dropdown-item {
        width: 100%;
        padding: 8px 12px;
        font-size: 13px;
        color: #334155;
        text-align: left;
        background: none;
        border: none;
        cursor: pointer;
        font-family: inherit;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.15s;
        box-sizing: border-box;
    }
    .actions-dropdown-item:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .actions-dropdown-item.btn-delete-item {
        color: #dc2626;
        font-weight: 500;
    }
    .actions-dropdown-item.btn-delete-item:hover {
        background: #fef2f2;
        color: #b91c1c;
    }

    .stock-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        line-height: 1rem;
        font-weight: 600;
        border: 1px solid transparent;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        white-space: nowrap;
    }
    .stock-status-badge-dot {
        width: 0.375rem;
        height: 0.375rem;
        border-radius: 9999px;
        margin-right: 0.375rem;
        flex-shrink: 0;
    }
    .stock-status-badge-active {
        background: #d1fae5;
        color: #065f46;
        border-color: #a7f3d0;
    }
    .stock-status-badge-active .stock-status-badge-dot {
        background: #10b981;
    }
    .stock-status-badge-archived {
        background: #ffe4e6;
        color: #9f1239;
        border-color: #fda4af;
    }
    .stock-status-badge-archived .stock-status-badge-dot {
        background: #f43f5e;
    }

    #editStockModal,
    #deleteStockModal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(2px);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    #editStockModal.active,
    #deleteStockModal.active {
        display: flex;
    }
    .edit-modal-content,
    .delete-modal-content {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        width: 100%;
        animation: modalSlideIn 0.2s ease-out;
        overflow: hidden;
    }
    .edit-modal-content { max-width: 520px; }
    .delete-modal-content { max-width: 440px; }
    @keyframes modalSlideIn {
        from { opacity: 0; transform: scale(0.96) translateY(-8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .edit-modal-header,
    .delete-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .edit-modal-header h3,
    .delete-modal-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }
    .delete-modal-header h3 {
        color: #dc2626;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .edit-modal-close,
    .delete-modal-close {
        border: 0;
        background: #f1f5f9;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #64748b;
    }
    .edit-modal-close:hover,
    .delete-modal-close:hover {
        background: #e2e8f0;
        color: #334155;
    }
    .edit-modal-body,
    .delete-modal-body {
        padding: 24px;
    }
    .edit-modal-field {
        margin-bottom: 18px;
    }
    .edit-modal-field label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }
    .edit-modal-field input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        color: #0f172a;
        background: #f8fafc;
        height: 44px;
        padding: 0 14px;
    }
    .edit-modal-field input:focus {
        outline: none;
        border-color: #0a5c2f;
        box-shadow: 0 0 0 3px rgba(10,92,47,0.08);
        background: #fff;
    }
    .edit-modal-footer,
    .delete-modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
    }
    .delete-modal-warning {
        padding: 12px 14px;
        background: #fff1f2;
        border: 1px solid #ffe4e6;
        color: #9f1239;
        border-radius: 8px;
        font-weight: 500;
        font-size: 13px;
        box-sizing: border-box;
    }

    .table-search-inline {
        width: auto;
        max-width: 360px;
    }

    tbody tr.hidden {
        display: none;
    }

    @media (max-width: 1200px) {
        .stocks-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@php
    $totalItems = $stocks->count();
    $visibleItems = $stocks->where('is_visible', true)->count();
    $hiddenItems = $totalItems - $visibleItems;
    $totalQuantity = $stocks->sum('quantity');
    $lowStockCount = $stocks->where('quantity', '<=', 10)->count();
@endphp

<div class="stocks-overview">
    <div class="stocks-card stocks-card-blue">
        <div class="stocks-card-header">
            <div class="stocks-card-label">Total Items</div>
            <div class="stocks-card-icon stocks-card-icon-blue">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                </svg>
            </div>
        </div>
        <div class="stocks-card-value">{{ number_format($totalItems) }}</div>
        <div class="stocks-card-status stocks-card-status-neutral">{{ $stocks->count() }} item types managed</div>
    </div>

    <div class="stocks-card stocks-card-indigo">
        <div class="stocks-card-header">
            <div class="stocks-card-label">Total Quantity</div>
            <div class="stocks-card-icon stocks-card-icon-indigo">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3"/>
                </svg>
            </div>
        </div>
        <div class="stocks-card-value">{{ number_format($totalQuantity) }}</div>
        @if($lowStockCount > 0)
            <div class="stocks-card-status stocks-card-status-amber">{{ $lowStockCount }} item(s) low stock (10 or below)</div>
        @else
            <div class="stocks-card-status stocks-card-status-green">All stock levels healthy</div>
        @endif
    </div>

    <div class="stocks-card stocks-card-green">
        <div class="stocks-card-header">
            <div class="stocks-card-label">Visible Items</div>
            <div class="stocks-card-icon stocks-card-icon-green">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <div class="stocks-card-value">{{ number_format($visibleItems) }}</div>
        <div class="stocks-card-status stocks-card-status-neutral">Showing on public products page</div>
    </div>

    <div class="stocks-card stocks-card-gray">
        <div class="stocks-card-header">
            <div class="stocks-card-label">Hidden Items</div>
            <div class="stocks-card-icon stocks-card-icon-gray">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                </svg>
            </div>
        </div>
        <div class="stocks-card-value">{{ number_format($hiddenItems) }}</div>
        @if($hiddenItems === 0)
            <div class="stocks-card-status stocks-card-status-green">None hidden</div>
        @else
            <div class="stocks-card-status stocks-card-status-amber">{{ $hiddenItems }} hidden from public</div>
        @endif
    </div>
</div>

<div class="stocks-grid">
    <section class="stocks-form-card">
        <h3>Add New Item</h3>
        <form method="POST" action="{{ route('staff.stocks.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="stocks-field">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" value="{{ old('item_name') }}" maxlength="100" required>
            </div>
            <div class="stocks-field">
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="0" max="3000" value="{{ old('quantity', 0) }}" pattern="\d*" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
            </div>
            <div class="stocks-field">
                <label for="image">Choose Image</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                <p class="stocks-help">Optional. If not provided, a placeholder is shown on the public page.</p>
            </div>
            <label class="stocks-visible-row">
                <input type="checkbox" name="is_visible" value="1" {{ old('is_visible', true) ? 'checked' : '' }}>
                Show on public products page
            </label>
            <button type="submit" class="btn btn-green">Add Item</button>
        </form>
    </section>

    <section class="card stocks-table-card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
            <div>
                <strong style="font-size:16px;color:#111827;">Stock Items</strong>
                <div style="font-size:13px;color:#64748b;margin-top:4px;">Manage your inventory items</div>
            </div>
            <div class="search-box table-search-inline">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input id="stocks-instant-search" type="text" placeholder="Search items..." oninput="filterRows()">
            </div>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>STATUS</th>
                        <th style="text-align:right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stocks as $stock)
                        <tr data-row="stock" data-name="{{ strtolower($stock->item_name) }}">
                            <td>
                                <span class="stocks-thumb">
                                    @if($stock->image)
                                        <img src="{{ asset('storage/' . $stock->image) }}" alt="{{ $stock->item_name }}">
                                    @else
                                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5 12 3l8.25 4.5L12 12 3.75 7.5Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12 12 16.5 20.25 12"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 16.5 12 21l8.25-4.5"/>
                                        </svg>
                                    @endif
                                </span>
                            </td>
                            <td style="font-weight:700;color:#0f172a;">{{ $stock->item_name }}</td>
                            <td style="font-weight:700;color:#0f172a;">{{ number_format($stock->quantity) }}</td>
                            <td>
                                @if($stock->is_visible)
                                    <span class="stock-status-badge stock-status-badge-active">
                                        <span class="stock-status-badge-dot"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="stock-status-badge stock-status-badge-archived">
                                        <span class="stock-status-badge-dot"></span>
                                        Archived
                                    </span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div class="actions-dropdown-container">
                                    <button type="button" class="btn-actions-trigger" onclick="toggleActionsMenu(this)">
                                        Actions
                                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div class="actions-dropdown-menu">
                                        <button
                                            type="button"
                                            class="actions-dropdown-item"
                                            data-update-url="{{ route('staff.stocks.update', $stock->id) }}"
                                            data-item-name="{{ $stock->item_name }}"
                                            data-quantity="{{ (int) $stock->quantity }}"
                                            onclick="openEditModalFromButton(this)"
                                        >
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;flex-shrink:0;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('staff.stocks.visibility', $stock->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="actions-dropdown-item">
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;flex-shrink:0;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $stock->is_visible ? 'Hide Item' : 'Show Item' }}
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            class="actions-dropdown-item btn-delete-item"
                                            data-delete-url="{{ route('staff.stocks.destroy', $stock->id) }}"
                                            data-item-name="{{ $stock->item_name }}"
                                            onclick="openDeleteModalFromButton(this)"
                                        >
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;flex-shrink:0;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 9m-4.78 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:32px;color:#94a3b8;">No stock items found.</td>
                        </tr>
                    @endforelse
                    <tr id="stocks-no-results-row" style="display:none;">
                        <td colspan="5" style="text-align:center;padding:32px;color:#94a3b8;">No results found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div id="editStockModal">
    <div class="edit-modal-content">
        <div class="edit-modal-header">
            <h3>Edit Stock Item</h3>
            <button type="button" class="edit-modal-close" onclick="closeEditModal()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="editStockForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="edit-modal-body">
                <div class="edit-modal-field">
                    <label for="modalItemName">Item Name</label>
                    <input type="text" id="modalItemName" name="item_name" maxlength="100" required>
                </div>
                <div class="edit-modal-field">
                    <label for="modalQuantity">Quantity</label>
                    <input type="number" id="modalQuantity" name="quantity" min="0" max="3000" pattern="\d*" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                </div>
            </div>
            <div class="edit-modal-footer">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-green btn-sm">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteStockModal">
    <div class="delete-modal-content">
        <div class="delete-modal-header">
            <h3>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                Delete Stock Item
            </h3>
            <button type="button" class="delete-modal-close" onclick="closeDeleteModal()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="deleteStockForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="delete-modal-body">
                <p>Are you sure you want to permanently delete <strong id="deleteModalItemName"></strong>?</p>
                <div class="delete-modal-warning">
                    This action cannot be undone. The stock record and attached image will be deleted.
                </div>
            </div>
            <div class="delete-modal-footer">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="btn btn-sm" style="background:#dc2626;color:#fff;border:1px solid #dc2626;cursor:pointer;font-weight:600;min-height:36px;border-radius:6px;padding:0 16px;">Confirm Delete</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterRows() {
        const input = document.getElementById('stocks-instant-search');
        const rows = document.querySelectorAll('tr[data-row="stock"]');
        const noResultsRow = document.getElementById('stocks-no-results-row');

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
            const matches = !query || name.includes(query);

            row.classList.toggle('hidden', !matches);
            if (matches) {
                visibleRows += 1;
            }
        });

        if (noResultsRow) {
            noResultsRow.style.display = visibleRows === 0 ? '' : 'none';
        }
    }

    function toggleActionsMenu(button) {
        const menu = button.closest('.actions-dropdown-container')?.querySelector('.actions-dropdown-menu');
        if (!menu) return;

        document.querySelectorAll('.actions-dropdown-menu.active').forEach((openMenu) => {
            if (openMenu !== menu) {
                openMenu.classList.remove('active');
            }
        });

        menu.classList.toggle('active');
    }

    function openEditModalFromButton(button) {
        closeAllMenus();
        openEditModal(
            button.dataset.updateUrl || '',
            button.dataset.itemName || '',
            button.dataset.quantity || '0'
        );
    }

    function openDeleteModalFromButton(button) {
        closeAllMenus();
        openDeleteModal(
            button.dataset.deleteUrl || '',
            button.dataset.itemName || ''
        );
    }

    function openEditModal(updateUrl, stockName, stockQuantity) {
        const modal = document.getElementById('editStockModal');
        const form = document.getElementById('editStockForm');
        const nameEl = document.getElementById('modalItemName');
        const quantityEl = document.getElementById('modalQuantity');

        if (!modal || !form) return;

        form.action = updateUrl;
        if (nameEl) nameEl.value = stockName;
        if (quantityEl) quantityEl.value = stockQuantity;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        const modal = document.getElementById('editStockModal');
        if (!modal) return;

        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function openDeleteModal(deleteUrl, stockName) {
        const modal = document.getElementById('deleteStockModal');
        const form = document.getElementById('deleteStockForm');
        const nameEl = document.getElementById('deleteModalItemName');

        if (!modal || !form) return;

        form.action = deleteUrl;
        if (nameEl) nameEl.textContent = stockName;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteStockModal');
        if (!modal) return;

        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function closeAllMenus() {
        document.querySelectorAll('.actions-dropdown-menu.active').forEach((menu) => {
            menu.classList.remove('active');
        });
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.actions-dropdown-container')) {
            closeAllMenus();
        }
    });

    document.getElementById('editStockModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'editStockModal') {
            closeEditModal();
        }
    });

    document.getElementById('deleteStockModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'deleteStockModal') {
            closeDeleteModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeEditModal();
            closeDeleteModal();
            closeAllMenus();
        }
    });

    filterRows();
</script>
@endsection
