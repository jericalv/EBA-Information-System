@extends('admin.layout')

@section('title', 'Stocks')

@section('extra-css')
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
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .stocks-card-blue {
        border-top-color: #3b82f6;
    }
    .stocks-card-indigo {
        border-top-color: #6366f1;
    }
    .stocks-card-green {
        border-top-color: #10b981;
    }
    .stocks-card-gray {
        border-top-color: #64748b;
    }
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
    .stocks-card-icon-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    .stocks-card-icon-indigo {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    }
    .stocks-card-icon-green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .stocks-card-icon-gray {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
    }
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
    grid-template-columns: 1fr;
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

    /* Edit Modal */
    #editStockModal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(2px);
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn 0.15s ease-out;
    }
    #editStockModal.active {
        display: flex;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.96) translateY(-8px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    .edit-modal-content {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 768px;
        max-height: 80vh;
        overflow: hidden;
        animation: modalSlideIn 0.2s ease-out;
    }
    .edit-modal-header {
        padding: 24px 28px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 10;
        border-radius: 16px 16px 0 0;
    }
    .edit-modal-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .edit-modal-close {
        width: 32px;
        height: 32px;
        border: 0;
        background: #f1f5f9;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
        color: #64748b;
    }
    .edit-modal-close:hover {
        background: #e2e8f0;
        color: #334155;
    }
    .edit-modal-close svg {
        width: 18px;
        height: 18px;
    }
    .edit-modal-body {
        padding: 24px 28px;
        max-height: calc(80vh - 150px);
        overflow-y: auto;
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
    .edit-modal-field input[type="number"],
    .edit-modal-field input[type="file"] {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        color: #0f172a;
        background: #f8fafc;
    }
    .edit-modal-field input[type="number"] {
        height: 44px;
        padding: 0 14px;
    }
    .edit-modal-field input[type="file"] {
        padding: 11px;
    }
    .edit-modal-field input:focus {
        outline: none;
        border-color: #0a5c2f;
        box-shadow: 0 0 0 3px rgba(10,92,47,0.08);
        background: #fff;
    }
    .edit-modal-preview {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .edit-modal-preview-thumb {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .edit-modal-preview-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .edit-modal-preview-thumb svg {
        width: 24px;
        height: 24px;
        color: #94a3b8;
    }
    .edit-modal-preview-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .edit-modal-footer {
        padding: 20px 28px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        position: sticky;
        bottom: 0;
        background: #fff;
        border-radius: 0 0 16px 16px;
    }

    .table-search-inline {
        width: auto;
        max-width: 360px;
    }

    /* Allow dropdown menus to float beyond table/card edges without clipping */
    .stocks-table-card {
        position: relative;
        overflow: visible !important;
    }
    .stocks-table-card .card-body,
        .stocks-table-card table{
        width: 100%;
        border-collapse: collapse;
        min-width: 700px;
        table-layout: fixed;
    }
    .stocks-table-card tbody,
    .stocks-table-card tbody tr,
    .stocks-table-card tbody td {
        overflow: visible !important;
    }
    .actions-cell {
        position: relative;
        overflow: visible !important;
    }
    
    tbody tr.hidden {
        display: none;
    }

    @media (max-width: 1200px) {
        .stocks-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Standardized Actions Dropdown Styling */
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
        transition: transform 0.2s;
        color: #64748b;
    }
    .btn-actions-trigger.active svg {
        transform: rotate(180deg);
        color: #0f172a;
    }
    .actions-dropdown-menu {
        position: absolute;
        right: 0;
        /* top / bottom set dynamically by Alpine :style */
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        z-index: 50;
        width: 130px;
        overflow: hidden;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
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
    /* Alpine transition helpers for the dropdown */
    .add-transition { transition: opacity 0.15s ease, transform 0.15s ease; }
    .dd-hidden  { opacity: 0; transform: scale(0.95); }
    .dd-visible { opacity: 1; transform: scale(1); }
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

    /* Custom Delete Confirmation Modal */
    #deleteStockModal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(2px);
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn 0.15s ease-out;
    }
    #deleteStockModal.active {
        display: flex;
    }
    .delete-modal-content {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 440px;
        animation: modalSlideIn 0.2s ease-out;
        overflow: hidden;
    }
    .delete-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .delete-modal-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: #dc2626;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .delete-modal-close {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }
    .delete-modal-close:hover {
        background: #f1f5f9;
        color: #475569;
    }
    .delete-modal-close svg {
        width: 20px;
        height: 20px;
    }
    .delete-modal-body {
        padding: 24px;
        font-size: 14px;
        color: #475569;
        line-height: 1.5;
    }
    .delete-modal-body p {
        margin: 0 0 16px 0;
    }
    .delete-modal-body strong {
        color: #0f172a;
    }
    .delete-modal-warning {
        padding: 12px 14px;
        background: #fff1f2;
        border: 1px solid #ffe4e6;
        color: #9f1239;
        border-radius: 8px;
        font-weight: 500;
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 13px;
        box-sizing: border-box;
    }
    .delete-modal-warning svg {
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        margin-top: 1px;
    }
    .delete-modal-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        border-radius: 0 0 16px 16px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
</style>
@endsection

@section('content')
@php
    $totalItems = $stocks->count();
    $visibleItems = $stocks->where('is_visible', true)->count();
    $hiddenItems = $totalItems - $visibleItems;
    $totalQuantity = $stocks->sum('quantity');
    $lowStockCount = $stocks->where('quantity', '<', 10)->count();
@endphp
<div class="stocks-grid">
    
    <section class="card stocks-table-card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
            <div>
                <strong style="font-size:16px;color:#111827;">Stock Items</strong>
                <div style="font-size:13px;color:#64748b;margin-top:4px;">Manage your inventory items</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;">
    <button type="button" class="btn btn-green" onclick="openAddModal()">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width:14px;height:14px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
        </svg>
        Add Item
    </button>
    <div style="position:relative;display:inline-flex;align-items:center;">
        <svg style="position:absolute;left:10px;width:15px;height:15px;color:#94a3b8;pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
        </svg>
        <input
            id="stocks-instant-search"
            type="text"
            placeholder="Search items..."
            oninput="filterRows()"
            style="height:38px;padding:0 14px 0 34px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;width:220px;font-family:inherit;"
            onfocus="this.style.borderColor='#0a5c2f';this.style.boxShadow='0 0 0 3px rgba(10,92,47,0.08)'"
            onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'"
                >
            </div>
        </div>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px;">Image</th>
                        <th style="width:200px;">Item Name</th>
                        <th style="width:100px;">Quantity</th>
                        <th style="width:130px;">STOCK LEVEL</th>  {{-- ADD --}}
                        <th style="width:110px;">STATUS</th>
                        <th style="width:100px;text-align:right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stocks as $stock)
                        <tr data-row="stock" data-name="{{ strtolower($stock->item_name) }}" data-stock-id="{{ $stock->id }}">
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
                                @if($stock->quantity === 0)
                                    <span style="color:#dc2626;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:5px;">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#dc2626;display:inline-block;flex-shrink:0;"></span>
                                        No Stock
                                    </span>
                                @elseif($stock->quantity <= 10)
                                    <span style="color:#d97706;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:5px;">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#d97706;display:inline-block;flex-shrink:0;"></span>
                                        Low Stock
                                    </span>
                                @else
                                    <span style="color:#059669;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:5px;">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#059669;display:inline-block;flex-shrink:0;"></span>
                                        Healthy
                                    </span>
                                @endif
                            </td>
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
                            <td class="actions-cell relative" style="text-align:right;">
                                <div class="actions-dropdown-container" x-data="{ open: false, flipped: false }" :style="open ? 'z-index: 60;' : 'z-index: 2;'">
                                    <button
                                        @click="
                                            flipped = ($el.getBoundingClientRect().bottom + 120 > window.innerHeight);
                                            open = !open;
                                        "
                                        :class="{ 'active': open }"
                                        class="btn-actions-trigger"
                                        type="button">
                                        Actions
                                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="open"
                                         @click.outside="open = false"
                                         x-transition:enter="add-transition"
                                         x-transition:enter-start="dd-hidden"
                                         x-transition:enter-end="dd-visible"
                                         x-transition:leave="add-transition"
                                         x-transition:leave-start="dd-visible"
                                         x-transition:leave-end="dd-hidden"
                                         :style="flipped
                                             ? 'bottom: calc(100% + 4px); top: auto; transform-origin: bottom right;'
                                             : 'top: calc(100% + 4px); bottom: auto; transform-origin: top right;'"
                                         class="actions-dropdown-menu absolute right-0 z-50 mt-1 w-36 rounded-md bg-white shadow-xl border border-slate-100 pointer-events-auto"
                                         style="display:none;">
                                        <button
                                            type="button"
                                            @click="open = false; openEditModal({{ $stock->id }}, @js($stock->item_name), {{ (int) $stock->quantity }}, @js($stock->image ? asset('storage/' . $stock->image) : ''), @js(route('admin.stocks.update', $stock)), @js($stock->item_type ?? ''), @js($stock->prices ?? []), @js($stock->sizes ?? []), @js((float) ($stock->unit_price ?? 0)))"
                                            class="actions-dropdown-item"
                                        >
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;flex-shrink:0;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Edit Details
                                        </button>

                                        <button
                                            type="button"
                                            @click="open = false; openDeleteModal({{ $stock->id }}, @js($stock->item_name), @js(route('admin.stocks.delete', $stock->id)), {{ $stock->is_visible ? 'true' : 'false' }})"
                                            class="actions-dropdown-item btn-delete-item"
                                        >
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;flex-shrink:0;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 9m-4.78 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            {{ $stock->is_visible ? 'Archive Item' : 'Restore Item' }}
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:32px;color:#94a3b8;">No stock items found.</td>
                        </tr>
                    @endforelse
                    <tr id="stocks-no-results-row" style="display:none;">
                        <td colspan="6" style="text-align:center;padding:32px;color:#94a3b8;">No results found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Edit Stock Modal -->
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
        <form id="editStockForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="edit-modal-body">
                <input type="hidden" id="modalItemType" name="item_type" value="">
                <div class="edit-modal-field">
                    <label>Item Name</label>
                    <div style="padding: 12px 14px; background: #f1f5f9; border-radius: 8px; font-weight: 600; color: #334155; font-size: 14px;" id="modalItemName"></div>
                </div>

                <div class="edit-modal-field" id="edit_prices_section" style="display:none;">
                    <label>Prices by Size</label>
                    <div style="display:grid;grid-template-columns:80px minmax(0,1fr) minmax(0,1fr);gap:10px;align-items:center;margin-bottom:6px;">
                        <span></span>
                        <span style="font-size:12px;font-weight:700;color:#334155;">Prices by Size</span>
                        <span style="font-size:12px;font-weight:700;color:#334155;">Stocks by Size</span>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(1,minmax(0,1fr));gap:10px;">
                        @foreach(['XS','S','M','L','XL','2XL','3XL','4XL','5XL'] as $size)
                        <div style="display:grid;grid-template-columns:80px minmax(0,1fr) minmax(0,1fr);gap:10px;align-items:end;">
                            <label style="display:block;font-size:12px;font-weight:700;color:#334155;margin-bottom:0;">{{ $size }}</label>
                            <input
                                type="number"
                                id="edit_price_{{ strtolower($size) }}"
                                name="price_{{ strtolower($size) }}"
                                min="0"
                                step="0.01"
                                value="0"
                                style="width:100%;height:38px;padding:0 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;background:#f8fafc;font-family:inherit;"
                            >
                            <input
                                type="number"
                                id="edit_qty_{{ strtolower($size) }}"
                                name="qty_{{ strtolower($size) }}"
                                min="0"
                                value="0"
                                style="width:100%;height:38px;padding:0 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;background:#f8fafc;font-family:inherit;"
                            >
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="edit-modal-field" id="edit_book_price_section" style="display:none;">
                    <label for="modalBookPrice">Price (₱)</label>
                    <input type="number" id="modalBookPrice" name="book_price" min="0" step="0.01" value="0"
                        style="width:100%;height:44px;padding:0 14px;border:1px solid #e2e8f0;border-radius:8px;font-family:inherit;font-size:14px;color:#0f172a;background:#f8fafc;">
                </div>

                <div class="edit-modal-field">
                    <label for="modalQuantity">Quantity</label>
                    <input type="number" id="modalQuantity" name="quantity" min="0" max="3000" pattern="\d*" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                </div>

                <div class="edit-modal-field">
                    <label>Current Image</label>
                    <div class="edit-modal-preview">
                        <div class="edit-modal-preview-thumb" id="modalCurrentImageThumb">
                            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5 12 3l8.25 4.5L12 12 3.75 7.5Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12 12 16.5 20.25 12"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 16.5 12 21l8.25-4.5"/>
                            </svg>
                        </div>
                        <span class="edit-modal-preview-label">This is the current image</span>
                    </div>
                </div>
                
                <div class="edit-modal-field">
                    <label for="modalImage">Upload New Image (Optional)</label>
                    <input type="file" id="modalImage" name="image" accept=".jpg,.jpeg,.png,.webp">
                    <p class="stocks-help">Leave empty to keep the current image</p>
                </div>
            </div>
            <div class="edit-modal-footer">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-green btn-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Delete Stock Modal -->
<div id="deleteStockModal">
    <div class="delete-modal-content">
        <div class="delete-modal-header">
            <h3>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <span id="deleteModalTitle">Archive Stock Item</span>
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
                <p><span id="deleteModalPromptText">Are you sure you want to archive the stock item</span> <strong id="deleteModalItemName"></strong>?</p>
                
                <div class="delete-modal-warning">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
                    </svg>
                    <div>
                        <strong>Note:</strong> This action updates item availability while preserving historical sales records.
                    </div>
                </div>
            </div>
            <div class="delete-modal-footer">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeDeleteModal()">Cancel</button>
                <button id="deleteModalSubmit" type="submit" class="btn btn-sm" style="background:#dc2626;color:#fff;border:1px solid #dc2626;cursor:pointer;font-weight:600;min-height:36px;border-radius:6px;padding:0 16px;">Archive Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
<!-- Add Stock Modal -->
<div id="addStockModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,0.7);backdrop-filter:blur(2px);align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);width:100%;max-width:768px;max-height:80vh;overflow-y:auto;animation:modalSlideIn 0.2s ease-out;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:#fff;z-index:10;border-radius:16px 16px 0 0;">
            <h3 style="margin:0;font-size:16px;font-weight:700;">Add New Item</h3>
            <button type="button" class="edit-modal-close" onclick="closeAddModal()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="addStockForm" method="POST" action="{{ route('admin.stocks.store') }}" enctype="multipart/form-data">
            @csrf
            <div style="padding:24px;">

                {{-- Item Type --}}
                <div class="edit-modal-field">
                    <label for="add_item_type">Item Type</label>
                    <select id="add_item_type" name="item_type" onchange="toggleAddPrices(this.value)" style="width:100%;height:42px;padding:0 14px;border:1px solid #e2e8f0;border-radius:8px;font-family:inherit;font-size:14px;color:#0f172a;background:#f8fafc;" required>
                        <option value="">-- Select Type --</option>
                        <option value="books">Books</option>
                        <option value="uniforms">Uniforms</option>
                    </select>
                </div>

                {{-- Item Name --}}
                <div class="edit-modal-field">
                    <label for="add_item_name">Item Name</label>
                    <input type="text" id="add_item_name" name="item_name" maxlength="100" style="width:100%;height:42px;padding:0 14px;border:1px solid #e2e8f0;border-radius:8px;font-family:inherit;font-size:14px;color:#0f172a;background:#f8fafc;" required>
                </div>

                {{-- Prices by Size (only for uniforms) --}}
                <div id="add_prices_section" style="display:none;margin-bottom:18px;">
                    <label style="display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:10px;">Prices by Size</label>
                    <div style="display:grid;grid-template-columns:80px minmax(0,1fr) minmax(0,1fr);gap:10px;align-items:center;margin-bottom:6px;">
                        <span></span>
                        <span style="font-size:12px;font-weight:700;color:#334155;">Prices by Size</span>
                        <span style="font-size:12px;font-weight:700;color:#334155;">Stocks by Size</span>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(1,minmax(0,1fr));gap:10px;">
                        @foreach(['XS','S','M','L','XL','2XL','3XL','4XL','5XL'] as $size)
                        <div style="display:grid;grid-template-columns:80px minmax(0,1fr) minmax(0,1fr);gap:10px;align-items:end;">
                            <label style="display:block;font-size:12px;font-weight:700;color:#334155;margin-bottom:0;">{{ $size }}</label>
                            <input type="number" name="price_{{ strtolower($size) }}" min="0" step="0.01" value="0"
                                style="width:100%;height:38px;padding:0 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;background:#f8fafc;font-family:inherit;">
                            <input type="number" name="qty_{{ strtolower($size) }}" min="0" value="0"
                                style="width:100%;height:38px;padding:0 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;background:#f8fafc;font-family:inherit;">
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Price (only for books) --}}
                <div id="add_book_price_section" class="edit-modal-field" style="display:none;">
                    <label for="add_book_price">Price (₱)</label>
                    <input type="number" id="add_book_price" name="book_price" min="0" step="0.01" value="0"
                        style="width:100%;height:42px;padding:0 14px;border:1px solid #e2e8f0;border-radius:8px;font-family:inherit;font-size:14px;color:#0f172a;background:#f8fafc;">
                    <p class="stocks-help">Selling price per book. Auto-fills at checkout and can be adjusted per sale.</p>
                </div>

                {{-- Quantity --}}
                <div class="edit-modal-field">
                    <label for="add_quantity">Quantity</label>
                    <input type="number" id="add_quantity" name="quantity" min="0" max="3000" value="0"
                        style="width:100%;height:42px;padding:0 14px;border:1px solid #e2e8f0;border-radius:8px;font-family:inherit;font-size:14px;color:#0f172a;background:#f8fafc;" required>
                </div>

                {{-- Image --}}
                <div class="edit-modal-field">
                    <label for="add_image">Choose Image</label>
                    <input type="file" id="add_image" name="image" accept=".jpg,.jpeg,.png,.webp"
                        style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px;font-size:14px;background:#f8fafc;">
                    <p class="stocks-help">Optional. If not provided, a placeholder is shown on the public page.</p>
                </div>

                {{-- Visibility --}}
                <label class="stocks-visible-row">
                    <input type="checkbox" name="is_visible" value="1" checked>
                    Show on public products page
                </label>

            </div>
            <div style="padding:16px 24px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:10px;background:#f8fafc;border-radius:0 0 16px 16px;position:sticky;bottom:0;">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn btn-green btn-sm">Add Item</button>
            </div>
        </form>
    </div>
</div>
@section('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function openAddModal() {
    const modal = document.getElementById('addStockModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    }

    function closeAddModal() {
        const modal = document.getElementById('addStockModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        document.getElementById('addStockForm').reset();
        document.getElementById('add_prices_section').style.display = 'none';
    }

    function toggleAddPrices(type) {
        const section = document.getElementById('add_prices_section');
        const bookPriceSection = document.getElementById('add_book_price_section');
        const quantityInput = document.getElementById('add_quantity');
        section.style.display = type === 'uniforms' ? 'block' : 'none';
        if (bookPriceSection) {
            bookPriceSection.style.display = type === 'books' ? 'block' : 'none';
        }

        if (quantityInput) {
            const isUniform = type === 'uniforms';
            quantityInput.readOnly = isUniform;
            quantityInput.style.background = isUniform ? '#f1f5f9' : '#f8fafc';

            if (isUniform) {
                recalculateAddQuantityFromSizes();
            }
        }
    }

    function recalculateAddQuantityFromSizes() {
        const quantityInput = document.getElementById('add_quantity');
        if (!quantityInput) return;

        let total = 0;
        document.querySelectorAll('#add_prices_section input[name^="qty_"]').forEach((input) => {
            const value = Number.parseInt(input.value || '0', 10);
            total += Number.isFinite(value) && value > 0 ? value : 0;
        });

        quantityInput.value = total;
    }

    document.getElementById('addStockModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'addStockModal') closeAddModal();
    });
    function openAddModal() {
    const modal = document.getElementById('addStockModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    }

    function closeAddModal() {
        const modal = document.getElementById('addStockModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        document.getElementById('addStockForm')?.reset();
        document.getElementById('add_prices_section').style.display = 'none';
        const addBookPriceSection = document.getElementById('add_book_price_section');
        if (addBookPriceSection) {
            addBookPriceSection.style.display = 'none';
        }
        const quantityInput = document.getElementById('add_quantity');
        if (quantityInput) {
            quantityInput.readOnly = false;
            quantityInput.style.background = '#f8fafc';
        }
    }

    document.getElementById('addStockModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'addStockModal') closeAddModal();
    });

    document.querySelectorAll('#add_prices_section input[name^="qty_"]').forEach((input) => {
        input.addEventListener('input', recalculateAddQuantityFromSizes);
    });
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

    function openEditModal(stockId, stockName, stockQuantity, stockImage, updateUrl, stockItemType = '', stockPrices = {}, stockSizes = {}, stockUnitPrice = 0) {
        const modal = document.getElementById('editStockModal');
        const form = document.getElementById('editStockForm');
        const nameEl = document.getElementById('modalItemName');
        const quantityEl = document.getElementById('modalQuantity');
        const thumbEl = document.getElementById('modalCurrentImageThumb');
        const itemTypeEl = document.getElementById('modalItemType');
        const pricesSectionEl = document.getElementById('edit_prices_section');
        const bookPriceSectionEl = document.getElementById('edit_book_price_section');
        const bookPriceEl = document.getElementById('modalBookPrice');

        if (!modal || !form) return;

        const sizeKeys = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];
        const normalizedType = (stockItemType || '').toLowerCase();

        // Set form action
        form.action = updateUrl;

        // Set item name (display only)
        if (nameEl) {
            nameEl.textContent = stockName;
        }

        // Set quantity
        if (quantityEl) {
            quantityEl.value = stockQuantity;
        }

        if (itemTypeEl) {
            itemTypeEl.value = normalizedType;
        }

        if (pricesSectionEl) {
            pricesSectionEl.style.display = normalizedType === 'uniforms' ? 'block' : 'none';
        }

        if (bookPriceSectionEl) {
            bookPriceSectionEl.style.display = normalizedType === 'books' ? 'block' : 'none';
        }

        if (bookPriceEl) {
            bookPriceEl.value = Number(stockUnitPrice) >= 0 ? Number(stockUnitPrice) : 0;
        }

        sizeKeys.forEach((sizeKey) => {
            const key = sizeKey.toLowerCase();
            const priceInput = document.getElementById(`edit_price_${key}`);
            const qtyInput = document.getElementById(`edit_qty_${key}`);

            if (priceInput) {
                const priceValue = stockPrices?.[sizeKey] ?? 0;
                priceInput.value = Number(priceValue) >= 0 ? Number(priceValue) : 0;
            }

            if (qtyInput) {
                const qtyValue = stockSizes?.[sizeKey] ?? 0;
                qtyInput.value = Number(qtyValue) >= 0 ? Number(qtyValue) : 0;
            }
        });

        // Set current image preview
        if (thumbEl) {
            if (stockImage && stockImage !== '') {
                thumbEl.innerHTML = `<img src="${stockImage}" alt="${stockName}">`;
            } else {
                thumbEl.innerHTML = `
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5 12 3l8.25 4.5L12 12 3.75 7.5Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12 12 16.5 20.25 12"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 16.5 12 21l8.25-4.5"/>
                    </svg>
                `;
            }
        }

        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        const modal = document.getElementById('editStockModal');
        if (!modal) return;

        modal.classList.remove('active');
        document.body.style.overflow = '';

        // Reset form
        const form = document.getElementById('editStockForm');
        const imageInput = document.getElementById('modalImage');
        const pricesSectionEl = document.getElementById('edit_prices_section');
        if (pricesSectionEl) {
            pricesSectionEl.style.display = 'none';
        }
        const bookPriceSectionEl = document.getElementById('edit_book_price_section');
        if (bookPriceSectionEl) {
            bookPriceSectionEl.style.display = 'none';
        }

        ['xs', 's', 'm', 'l', 'xl', '2xl', '3xl', '4xl', '5xl'].forEach((sizeKey) => {
            const priceInput = document.getElementById(`edit_price_${sizeKey}`);
            const qtyInput = document.getElementById(`edit_qty_${sizeKey}`);

            if (priceInput) {
                priceInput.value = 0;
            }

            if (qtyInput) {
                qtyInput.value = 0;
            }
        });

        if (imageInput) {
            imageInput.value = '';
        }
    }

    // Close modal on backdrop click
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

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeEditModal();
            closeDeleteModal();
        }
    });

    function openDeleteModal(stockId, stockName, deleteUrl, isCurrentlyVisible = true) {
        const modal = document.getElementById('deleteStockModal');
        const form = document.getElementById('deleteStockForm');
        const nameEl = document.getElementById('deleteModalItemName');
        const titleEl = document.getElementById('deleteModalTitle');
        const promptTextEl = document.getElementById('deleteModalPromptText');
        const submitEl = document.getElementById('deleteModalSubmit');

        if (!modal || !form) return;

        const isArchiveAction = Boolean(isCurrentlyVisible);

        // Set action URL
        form.action = deleteUrl;

        // Set display name
        if (nameEl) {
            nameEl.textContent = stockName;
        }

        if (titleEl) {
            titleEl.textContent = isArchiveAction ? 'Archive Stock Item' : 'Restore Stock Item';
        }

        if (promptTextEl) {
            promptTextEl.textContent = isArchiveAction
                ? 'Are you sure you want to archive the stock item'
                : 'Are you sure you want to restore the stock item';
        }

        if (submitEl) {
            submitEl.textContent = isArchiveAction ? 'Archive Item' : 'Restore Item';
        }

        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteStockModal');
        if (!modal) return;

        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    filterRows();
</script>
@endsection
