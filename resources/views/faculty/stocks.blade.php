@extends('faculty.layout')

@section('title', 'Stocks')

@section('extra-css')
<style>
    [x-cloak] { display: none !important; }

    /* ---------- Stat cards ---------- */
    .stk-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    @media (max-width: 1200px) { .stk-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 640px) { .stk-stat-grid { grid-template-columns: 1fr; } }
    .stk-stat-card {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        box-shadow: var(--shadow-card);
        min-width: 0;
    }
    .stk-stat-label {
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .stk-stat-value {
        font-family: var(--font-ui);
        font-size: 30px;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.03em;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .stk-stat-value.is-amber { color: var(--amber, #d97706); }
    .stk-stat-value.is-danger { color: var(--danger, #b91c1c); }

    /* ---------- Table card ---------- */
    .stocks-table-card { position: relative; overflow: visible !important; }
    .stocks-table-card .card-body,
    .stocks-table-card table,
    .stocks-table-card tbody,
    .stocks-table-card tbody tr,
    .stocks-table-card tbody td { overflow: visible !important; }

    .stocks-table-card table {
        width: 100%;
        border-collapse: collapse;
        min-width: 860px;
        table-layout: fixed;
    }
    .stocks-table-card th,
    .stocks-table-card td {
        padding: 13px 14px;
        border-bottom: 1px solid var(--line);
        text-align: left;
        vertical-align: middle;
    }
    .stocks-table-card th {
        background: var(--hover);
        color: var(--muted);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .stocks-table-card th.sortable { cursor: pointer; user-select: none; }
    .stocks-table-card th.sortable:hover { color: var(--ink); }
    .sort-arrow { display: inline-block; margin-left: 4px; font-size: 10px; opacity: 0; transition: opacity 0.1s ease; }
    th[aria-sort="ascending"] .sort-arrow,
    th[aria-sort="descending"] .sort-arrow { opacity: 1; }

    tbody[data-row="stock"] > tr.stock-row { cursor: pointer; transition: background-color 0.1s ease; }
    tbody[data-row="stock"] > tr.stock-row:hover { background: var(--hover); }
    tbody[data-row="stock"].is-open > tr.stock-row { background: var(--hover); }
    tbody[data-row="stock"].hidden { display: none; }

    .row-caret {
        width: 13px;
        height: 13px;
        margin-right: 7px;
        color: var(--faint);
        flex-shrink: 0;
        transition: transform 0.15s ease;
    }
    tbody.is-open .row-caret { transform: rotate(90deg); color: var(--pine); }

    .stocks-thumb {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        overflow: hidden;
        background: var(--hover);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--line);
    }
    .stocks-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .stocks-thumb svg { width: 20px; height: 20px; color: var(--faint); }

    /* ---------- Detail row ---------- */
    tr.stock-detail-row td {
        background: var(--hover);
        padding: 16px 18px 18px;
    }
    .detail-sizes { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
    .size-cell {
        display: inline-flex;
        align-items: baseline;
        gap: 7px;
        padding: 6px 11px;
        border-radius: 8px;
        border: 1px solid var(--line-strong);
        background: var(--card);
        font-size: 12.5px;
    }
    .size-cell b {
        font-family: var(--font-mono);
        font-size: 11.5px;
        font-weight: 700;
        color: var(--muted);
    }
    .size-cell .qty { font-weight: 800; color: var(--ink); font-variant-numeric: tabular-nums; }
    .size-cell .prc { font-size: 11.5px; color: var(--muted); }
    .size-cell.is-low { border-color: rgba(217, 119, 6, 0.55); }
    .size-cell.is-low .qty { color: #d97706; }
    .size-cell.is-out { border-color: rgba(220, 38, 38, 0.5); }
    .size-cell.is-out .qty { color: #dc2626; }
    html[data-theme="dark"] .size-cell.is-low .qty { color: #E9C288; }
    html[data-theme="dark"] .size-cell.is-out .qty { color: #F0A0A0; }
    .detail-meta { font-size: 12.5px; color: var(--muted); }
    .detail-meta strong { color: var(--ink); }

    /* ---------- Tabs ---------- */
    .stk-tabs {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        padding: 12px 16px;
        border-bottom: 1px solid var(--line);
    }
    .stk-tab {
        border: 1px solid transparent;
        background: transparent;
        border-radius: 999px;
        padding: 6px 14px;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--muted);
        cursor: pointer;
        transition: background-color 0.12s ease, color 0.12s ease;
    }
    .stk-tab:hover { background: var(--hover-2); color: var(--ink); }
    .stk-tab.is-active { background: var(--pine-soft); color: var(--pine); border-color: var(--line-strong); }
    .stk-tab .cnt {
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        margin-left: 4px;
        opacity: 0.8;
    }

    /* ---------- Search ---------- */
    .stocks-search {
        height: 38px;
        padding: 0 14px 0 34px;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        font-size: 13px;
        color: var(--ink);
        background: var(--field, var(--card));
        width: 220px;
        font-family: inherit;
        transition: border-color 0.12s ease, box-shadow 0.12s ease;
    }
    .stocks-search:focus {
        outline: none;
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.10);
    }

    /* ---------- Badges ---------- */
    .type-chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 6px;
        background: var(--hover-2);
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }
    .health-badge {
        font-weight: 700;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .health-badge .dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
    .health-badge.is-healthy { color: #15803d; }
    .health-badge.is-healthy .dot { background: #16a34a; }
    .health-badge.is-low { color: #d97706; }
    .health-badge.is-low .dot { background: #d97706; }
    .health-badge.is-out { color: #dc2626; }
    .health-badge.is-out .dot { background: #dc2626; }
    html[data-theme="dark"] .health-badge.is-healthy { color: #8CD6AF; }
    html[data-theme="dark"] .health-badge.is-healthy .dot { background: #8CD6AF; }
    html[data-theme="dark"] .health-badge.is-low { color: #E9C288; }
    html[data-theme="dark"] .health-badge.is-low .dot { background: #E9C288; }
    html[data-theme="dark"] .health-badge.is-out { color: #F0A0A0; }
    html[data-theme="dark"] .health-badge.is-out .dot { background: #F0A0A0; }
    .health-sizes {
        display: block;
        margin-top: 2px;
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .health-sizes.is-out-sizes { color: #dc2626; }
    .health-sizes.is-low-sizes { color: #d97706; }
    html[data-theme="dark"] .health-sizes.is-out-sizes { color: #F0A0A0; }
    html[data-theme="dark"] .health-sizes.is-low-sizes { color: #E9C288; }
    .health-badge + .health-sizes { margin-top: 3px; }

    .stock-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        line-height: 1rem;
        font-weight: 600;
        border: 1px solid transparent;
        white-space: nowrap;
    }
    .stock-status-badge-dot { width: 0.375rem; height: 0.375rem; border-radius: 9999px; margin-right: 0.375rem; flex-shrink: 0; }
    .stock-status-badge-active { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
    .stock-status-badge-active .stock-status-badge-dot { background: #16a34a; }
    .stock-status-badge-archived { background: #ffe4e6; color: #9f1239; border-color: #fda4af; }
    .stock-status-badge-archived .stock-status-badge-dot { background: #f43f5e; }
    html[data-theme="dark"] .stock-status-badge-active { background: rgba(30, 149, 96, 0.16); color: #8CD6AF; border-color: rgba(30, 149, 96, 0.4); }
    html[data-theme="dark"] .stock-status-badge-active .stock-status-badge-dot { background: #8CD6AF; }
    html[data-theme="dark"] .stock-status-badge-archived { background: rgba(244, 63, 94, 0.14); color: #F5A3B1; border-color: rgba(244, 63, 94, 0.4); }
    html[data-theme="dark"] .stock-status-badge-archived .stock-status-badge-dot { background: #F5A3B1; }

    /* ---------- Row actions ---------- */
    .actions-dropdown-container { position: relative; display: inline-block; text-align: left; }
    .row-menu-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--muted);
        cursor: pointer;
        transition: background-color 0.12s ease, color 0.12s ease, border-color 0.12s ease;
    }
    .row-menu-btn svg { width: 17px; height: 17px; }
    .row-menu-btn:hover,
    .row-menu-btn.is-open { background: var(--pine-soft); color: var(--pine); border-color: var(--line-strong); }
    .row-menu-btn:focus-visible { outline: 2px solid rgba(31, 41, 55, 0.40); outline-offset: 2px; }
    .actions-dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 4px);
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 10px;
        box-shadow: var(--shadow-pop);
        z-index: 60;
        width: 186px;
        overflow: hidden;
        display: none;
    }
    .actions-dropdown-menu.active { display: block; }
    .actions-dropdown-item {
        width: 100%;
        padding: 8px 12px;
        font-size: 13px;
        color: var(--ink);
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
        text-decoration: none;
    }
    .actions-dropdown-item:hover { background: var(--pine-soft); color: var(--ink); }
    .actions-dropdown-item svg { width: 14px; height: 14px; flex-shrink: 0; }
    .actions-dropdown-item.btn-delete-item { color: #dc2626; font-weight: 500; }
    .actions-dropdown-item.btn-delete-item:hover { background: #fef2f2; color: #b91c1c; }
    html[data-theme="dark"] .actions-dropdown-item.btn-delete-item { color: #F0A0A0; }
    html[data-theme="dark"] .actions-dropdown-item.btn-delete-item:hover { background: rgba(227, 106, 106, 0.12); color: #F4B6B6; }

    /* ---------- Modals (adjust / archive / delete) ---------- */
    .stk-modal {
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
    .stk-modal.active { display: flex; }
    .stk-modal-content {
        background: var(--card);
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 440px;
        animation: modalSlideIn 0.2s ease-out;
        overflow: hidden;
    }
    @keyframes modalSlideIn {
        from { opacity: 0; transform: scale(0.96) translateY(-8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .stk-modal-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .stk-modal-header h3 { margin: 0; font-size: 16px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; }
    .stk-modal-header h3.is-danger { color: #dc2626; }
    html[data-theme="dark"] .stk-modal-header h3.is-danger { color: #F0A0A0; }
    .stk-modal-header h3 svg { width: 20px; height: 20px; flex-shrink: 0; }
    .stk-modal-close {
        border: 0;
        background: var(--hover-2);
        border-radius: 6px;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--muted);
    }
    .stk-modal-close:hover { color: var(--ink); }
    .stk-modal-close svg { width: 18px; height: 18px; }
    .stk-modal-body { padding: 22px; }
    .stk-modal-body p { margin: 0 0 16px 0; color: var(--ink); font-size: 14px; line-height: 1.5; }
    .stk-modal-footer {
        padding: 14px 22px;
        border-top: 1px solid var(--line);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: var(--hover);
    }
    .stk-modal-field { margin-bottom: 14px; }
    .stk-modal-field label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 6px;
    }
    .stk-modal-field input,
    .stk-modal-field select {
        width: 100%;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        font-family: inherit;
        font-size: 14px;
        color: var(--ink);
        background: var(--field, var(--card));
        height: 40px;
        padding: 0 12px;
        box-sizing: border-box;
    }
    .stk-modal-field select { cursor: pointer; }
    .stk-modal-field input:focus,
    .stk-modal-field select:focus {
        outline: none;
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.10);
    }
    html[data-theme="dark"] .stocks-search:focus,
    html[data-theme="dark"] .stk-modal-field input:focus,
    html[data-theme="dark"] .stk-modal-field select:focus {
        box-shadow: 0 0 0 3px rgba(169, 180, 196, 0.18);
    }
    .stk-modal-help { margin-top: 5px; font-size: 12px; color: var(--muted); line-height: 1.4; }
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
    html[data-theme="dark"] .delete-modal-warning { background: rgba(244, 63, 94, 0.12); border-color: rgba(244, 63, 94, 0.35); color: #F5A3B1; }
    .archive-modal-warning {
        padding: 12px 14px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        border-radius: 8px;
        font-weight: 500;
        font-size: 13px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
        box-sizing: border-box;
    }
    html[data-theme="dark"] .archive-modal-warning { background: rgba(227, 164, 72, 0.12); border-color: rgba(227, 164, 72, 0.35); color: #E9C288; }
    .archive-modal-warning svg { flex-shrink: 0; width: 16px; height: 16px; margin-top: 1px; }

    /* ---------- Recent activity ---------- */
    .mv-list { display: flex; flex-direction: column; }
    .mv-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid var(--line);
        font-size: 13px;
    }
    .mv-row:last-child { border-bottom: none; }
    .mv-badge {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        padding: 2px 9px;
        border-radius: 999px;
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        border: 1px solid transparent;
    }
    .mv-badge.type-initial,
    .mv-badge.type-restock { background: var(--pine-soft); color: var(--pine); }
    .mv-badge.type-sale,
    .mv-badge.type-edit { background: var(--hover-2); color: var(--muted); }
    .mv-badge.type-correction { background: #FEF3E2; color: #92400E; }
    html[data-theme="dark"] .mv-badge.type-correction { background: rgba(227, 164, 72, 0.14); color: #E9C288; }
    .mv-main { flex: 1; min-width: 0; color: var(--ink); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .mv-meta { font-size: 12px; color: var(--muted); white-space: nowrap; }
    .mv-delta { font-family: var(--font-mono); font-weight: 700; font-variant-numeric: tabular-nums; }
    .mv-delta.is-plus { color: var(--pine); }
    .mv-delta.is-minus { color: var(--danger, #b91c1c); }
</style>
@endsection

@php
    $activeStocks = $stocks->where('is_visible', true);
    $tabCounts = [
        'all' => $activeStocks->count(),
        'uniforms' => $activeStocks->where('item_type', 'uniforms')->count(),
        'books' => $activeStocks->where('item_type', 'books')->count(),
        'low' => $stats['low'] + $stats['out'],
        'archived' => $stats['archived'],
    ];
@endphp

@section('content')
<section class="stk-stat-grid" aria-label="Inventory statistics">
    <div class="stk-stat-card">
        <span class="stk-stat-label">Active Items</span>
        <span class="stk-stat-value">{{ number_format($stats['total']) }}</span>
    </div>
    <div class="stk-stat-card">
        <span class="stk-stat-label">Low Stock</span>
        <span class="stk-stat-value {{ $stats['low'] > 0 ? 'is-amber' : '' }}">{{ number_format($stats['low']) }}</span>
    </div>
    <div class="stk-stat-card">
        <span class="stk-stat-label">Out of Stock</span>
        <span class="stk-stat-value {{ $stats['out'] > 0 ? 'is-danger' : '' }}">{{ number_format($stats['out']) }}</span>
    </div>
    <div class="stk-stat-card">
        <span class="stk-stat-label">Inventory Value</span>
        <span class="stk-stat-value">₱{{ number_format($stats['value'], 2) }}</span>
    </div>
</section>

<section class="card stocks-table-card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
            <strong style="font-size:16px;color: var(--ink);">Stock Items</strong>
            <div style="font-size:13px;color: var(--muted);margin-top:4px;">Manage your inventory items — click a row for the per-size breakdown</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;">
            <a href="{{ route('staff.stocks.create') }}" class="btn btn-green">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                </svg>
                Add Item
            </a>
            <div style="position:relative;display:inline-flex;align-items:center;">
                <svg style="position:absolute;left:10px;width:15px;height:15px;color: var(--faint);pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                </svg>
                <input id="stocks-instant-search" class="stocks-search" type="text" placeholder="Search items..." oninput="filterRows()">
            </div>
        </div>
    </div>

    <div class="stk-tabs" role="tablist" aria-label="Stock filters">
        <button type="button" class="stk-tab is-active" data-tab="all" onclick="setTab(this)">All <span class="cnt">{{ $tabCounts['all'] }}</span></button>
        <button type="button" class="stk-tab" data-tab="uniforms" onclick="setTab(this)">Uniforms <span class="cnt">{{ $tabCounts['uniforms'] }}</span></button>
        <button type="button" class="stk-tab" data-tab="books" onclick="setTab(this)">Books <span class="cnt">{{ $tabCounts['books'] }}</span></button>
        <button type="button" class="stk-tab" data-tab="low" onclick="setTab(this)">Needs Attention <span class="cnt">{{ $tabCounts['low'] }}</span></button>
        <button type="button" class="stk-tab" data-tab="archived" onclick="setTab(this)">Archived <span class="cnt">{{ $tabCounts['archived'] }}</span></button>
    </div>

    <div class="card-body">
        <table id="stocks-table">
            <thead>
                <tr>
                    <th style="width:56px;">Image</th>
                    <th class="sortable" style="width:200px;" data-sort="name" onclick="sortRows(this)">Item Name<span class="sort-arrow">▲</span></th>
                    <th class="sortable" style="width:100px;" data-sort="type" onclick="sortRows(this)">Type<span class="sort-arrow">▲</span></th>
                    <th class="sortable" style="width:120px;" data-sort="price" onclick="sortRows(this)">Price<span class="sort-arrow">▲</span></th>
                    <th class="sortable" style="width:96px;" data-sort="qty" onclick="sortRows(this)">Qty<span class="sort-arrow">▲</span></th>
                    <th style="width:150px;">Stock Level</th>
                    <th style="width:104px;">Visibility</th>
                    <th style="width:88px;text-align:right;">Actions</th>
                </tr>
            </thead>
            @forelse ($stocks as $stock)
                @php
                    $health = $stock->stockHealth();
                    $range = $stock->priceRange();
                    $carried = $stock->carriedSizes();
                    $priceMap = is_array($stock->prices) ? $stock->prices : [];
                    $threshold = max(0, (int) ($stock->low_stock_threshold ?? 10));
                @endphp
                <tbody
                    data-row="stock"
                    data-name="{{ strtolower($stock->item_name) }}"
                    data-type="{{ $stock->item_type ?? '' }}"
                    data-visible="{{ $stock->is_visible ? '1' : '0' }}"
                    data-health="{{ $health['status'] }}"
                    data-qty="{{ (int) $stock->quantity }}"
                    data-price="{{ $range ? $range[0] : 0 }}"
                    data-stock-id="{{ $stock->id }}"
                >
                    <tr class="stock-row" onclick="toggleDetail(this, event)">
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
                        <td style="font-weight:700;color: var(--ink);">
                            <span style="display:inline-flex;align-items:center;min-width:0;">
                                <svg class="row-caret" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                                </svg>
                                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $stock->item_name }}</span>
                            </span>
                        </td>
                        <td>
                            @if($stock->item_type)
                                <span class="type-chip">{{ $stock->item_type }}</span>
                            @else
                                <span style="color: var(--faint);font-size:13px;">—</span>
                            @endif
                        </td>
                        <td style="font-variant-numeric:tabular-nums;color: var(--ink);">
                            @if($range === null)
                                <span style="color: var(--faint);">—</span>
                            @elseif($range[0] === $range[1])
                                ₱{{ number_format($range[0], 2) }}
                            @else
                                ₱{{ number_format($range[0], 0) }}–{{ number_format($range[1], 0) }}
                            @endif
                        </td>
                        <td style="font-weight:700;color: var(--ink);font-variant-numeric:tabular-nums;">{{ number_format($stock->quantity) }}</td>
                        <td>
                            @if($health['status'] === 'out')
                                <span class="health-badge is-out"><span class="dot"></span>Out of Stock</span>
                            @elseif($health['status'] === 'low')
                                {{-- Any size at 0 is genuinely out, so lead red and label each group. --}}
                                <span class="health-badge {{ $health['out_sizes'] !== [] ? 'is-out' : 'is-low' }}">
                                    <span class="dot"></span>{{ $health['out_sizes'] !== [] ? 'Out of Stock' : 'Low Stock' }}
                                </span>
                                @if($health['out_sizes'] !== [])
                                    <span class="health-sizes is-out-sizes" title="Out of stock: {{ implode(', ', $health['out_sizes']) }}">
                                        Out: {{ implode(' ', array_slice($health['out_sizes'], 0, 4)) }}{{ count($health['out_sizes']) > 4 ? ' +' . (count($health['out_sizes']) - 4) : '' }}
                                    </span>
                                @endif
                                @if($health['low_sizes'] !== [])
                                    <span class="health-sizes is-low-sizes" title="Low stock: {{ implode(', ', $health['low_sizes']) }}">
                                        Low: {{ implode(' ', array_slice($health['low_sizes'], 0, 4)) }}{{ count($health['low_sizes']) > 4 ? ' +' . (count($health['low_sizes']) - 4) : '' }}
                                    </span>
                                @endif
                            @else
                                <span class="health-badge is-healthy"><span class="dot"></span>Healthy</span>
                            @endif
                        </td>
                        <td>
                            @if($stock->is_visible)
                                <span class="stock-status-badge stock-status-badge-active"><span class="stock-status-badge-dot"></span>Active</span>
                            @else
                                <span class="stock-status-badge stock-status-badge-archived"><span class="stock-status-badge-dot"></span>Archived</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div class="actions-dropdown-container">
                                <button type="button" class="row-menu-btn" aria-label="More actions" aria-haspopup="menu" onclick="toggleActionsMenu(this)">
                                    <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/>
                                    </svg>
                                </button>
                                <div class="actions-dropdown-menu">
                                    <a class="actions-dropdown-item" href="{{ route('staff.stocks.edit', $stock) }}">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        Edit Details
                                    </a>
                                    <button
                                        type="button"
                                        class="actions-dropdown-item"
                                        data-adjust-url="{{ route('staff.stocks.adjust', $stock) }}"
                                        data-item-name="{{ $stock->item_name }}"
                                        data-sizes='@json($carried)'
                                        onclick="openAdjustModalFromButton(this)"
                                    >
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                            <circle cx="12" cy="12" r="9.25" stroke-width="1.5"/>
                                        </svg>
                                        Adjust Stock
                                    </button>
                                    <button
                                        type="button"
                                        class="actions-dropdown-item"
                                        data-archive-url="{{ route('staff.stocks.archive', $stock->id) }}"
                                        data-item-name="{{ $stock->item_name }}"
                                        data-visible="{{ $stock->is_visible ? '1' : '0' }}"
                                        onclick="openArchiveModalFromButton(this)"
                                    >
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                        </svg>
                                        {{ $stock->is_visible ? 'Archive Item' : 'Restore Item' }}
                                    </button>
                                    <button
                                        type="button"
                                        class="actions-dropdown-item btn-delete-item"
                                        data-delete-url="{{ route('staff.stocks.destroy', $stock->id) }}"
                                        data-item-name="{{ $stock->item_name }}"
                                        onclick="openDeleteModalFromButton(this)"
                                    >
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 9m-4.78 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Delete Item
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="stock-detail-row" hidden>
                        <td colspan="8">
                            @if($stock->isUniform() && $carried !== [])
                                <div class="detail-sizes">
                                    @foreach($carried as $sizeKey => $sizeQty)
                                        @php
                                            $sizeClass = $sizeQty === 0 ? 'is-out' : ($sizeQty <= $threshold ? 'is-low' : '');
                                            $sizePrice = (float) ($priceMap[$sizeKey] ?? 0);
                                        @endphp
                                        <span class="size-cell {{ $sizeClass }}">
                                            <b>{{ $sizeKey }}</b>
                                            <span class="qty">{{ number_format($sizeQty) }}</span>
                                            @if($sizePrice > 0)<span class="prc">₱{{ number_format($sizePrice, 2) }}</span>@endif
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="detail-meta">
                                Low stock alert at <strong>{{ $threshold }}</strong>
                                @if($stock->isUniform()) per size @endif
                                · Value on hand <strong>₱{{ number_format($stock->inventoryValue(), 2) }}</strong>
                                · Added <strong>{{ $stock->created_at?->format('M j, Y') ?? '—' }}</strong>
                                · Last updated <strong>{{ $stock->updated_at?->format('M j, Y') ?? '—' }}</strong>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @empty
                <tbody>
                    <tr><td colspan="8" style="text-align:center;padding:32px;color: var(--faint);">No stock items found.</td></tr>
                </tbody>
            @endforelse
            <tbody id="stocks-no-results-body" style="display:none;">
                <tr><td colspan="8" style="text-align:center;padding:32px;color: var(--faint);">No items match this view.</td></tr>
            </tbody>
        </table>
    </div>
</section>

@if ($recentMovements->isNotEmpty())
    <section class="card" style="margin-top:20px;">
        <div class="card-header">
            <strong style="font-size:15px;color: var(--ink);">Recent Stock Activity</strong>
            <div style="font-size:12.5px;color: var(--muted);margin-top:3px;">Every restock, correction, edit, and sale is recorded here</div>
        </div>
        <div class="card-body" style="padding:6px 22px 14px;">
            <div class="mv-list">
                @foreach ($recentMovements as $movement)
                    <div class="mv-row">
                        <span class="mv-badge type-{{ $movement->type }}">{{ $movement->type }}</span>
                        <span class="mv-main">
                            <strong>{{ $movement->stock?->item_name ?? 'Deleted item' }}</strong>
                            @if ($movement->size) <span style="font-family: var(--font-mono);font-size:12px;">({{ $movement->size }})</span> @endif
                            <span class="mv-delta {{ $movement->quantity_change >= 0 ? 'is-plus' : 'is-minus' }}">
                                {{ $movement->quantity_change >= 0 ? '+' : '' }}{{ number_format($movement->quantity_change) }}
                            </span>
                            → {{ number_format($movement->quantity_after) }} left
                            @if ($movement->note) <span style="color: var(--muted);">· {{ $movement->note }}</span> @endif
                        </span>
                        <span class="mv-meta">{{ $movement->user?->name ?? 'System' }} · {{ $movement->created_at->format('M j, g:i A') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ---------- Adjust Stock Modal ---------- --}}
<div id="adjustStockModal" class="stk-modal">
    <div class="stk-modal-content">
        <div class="stk-modal-header">
            <h3>Adjust Stock — <span id="adjustModalItemName"></span></h3>
            <button type="button" class="stk-modal-close" onclick="closeAdjustModal()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="adjustStockForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="stk-modal-body">
                <div class="stk-modal-field">
                    <label for="adjust_action">Action</label>
                    <select id="adjust_action" name="action">
                        <option value="restock">Restock — add stock</option>
                        <option value="deduct">Deduct — remove stock (correction, damage)</option>
                    </select>
                </div>
                <div class="stk-modal-field" id="adjustSizeField">
                    <label for="adjust_size">Size</label>
                    <select id="adjust_size" name="size"></select>
                </div>
                <div class="stk-modal-field">
                    <label for="adjust_amount">Amount</label>
                    <input type="number" id="adjust_amount" name="amount" min="1" max="100000" required>
                </div>
                <div class="stk-modal-field">
                    <label for="adjust_note">Note <span style="font-weight:500;color: var(--muted);">(optional)</span></label>
                    <input type="text" id="adjust_note" name="note" maxlength="255" placeholder="e.g. Supplier delivery, damaged items">
                </div>
                <p class="stk-modal-help" style="margin:0;">Recorded in the stock activity log with your name. For bigger changes, use Edit Details.</p>
            </div>
            <div class="stk-modal-footer">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeAdjustModal()">Cancel</button>
                <button type="submit" class="btn btn-green btn-sm">Apply Adjustment</button>
            </div>
        </form>
    </div>
</div>

{{-- ---------- Archive / Restore Modal ---------- --}}
<div id="archiveStockModal" class="stk-modal">
    <div class="stk-modal-content">
        <div class="stk-modal-header">
            <h3>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
                <span id="archiveModalTitle">Archive Stock Item</span>
            </h3>
            <button type="button" class="stk-modal-close" onclick="closeArchiveModal()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="archiveStockForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="stk-modal-body">
                <p><span id="archiveModalPromptText">Are you sure you want to archive the stock item</span> <strong id="archiveModalItemName"></strong>?</p>
                <div class="archive-modal-warning">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
                    </svg>
                    <div><strong>Note:</strong> This action only updates item availability while preserving historical sales records.</div>
                </div>
            </div>
            <div class="stk-modal-footer">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeArchiveModal()">Cancel</button>
                <button id="archiveModalSubmit" type="submit" class="btn btn-green btn-sm">Archive Item</button>
            </div>
        </form>
    </div>
</div>

{{-- ---------- Delete Modal (permanent) ---------- --}}
<div id="deleteStockModal" class="stk-modal">
    <div class="stk-modal-content">
        <div class="stk-modal-header">
            <h3 class="is-danger">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                Delete Stock Item
            </h3>
            <button type="button" class="stk-modal-close" onclick="closeDeleteModal()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="deleteStockForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="stk-modal-body">
                <p>Are you sure you want to permanently delete <strong id="deleteModalItemName"></strong>?</p>
                <div class="delete-modal-warning">
                    This action cannot be undone. The stock record, its movement history, and attached image will be permanently deleted.
                </div>
            </div>
            <div class="stk-modal-footer">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="btn btn-danger btn-sm">Confirm Delete</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentTab = 'all';

    function setTab(button) {
        currentTab = button.dataset.tab;
        document.querySelectorAll('.stk-tab').forEach((tab) => tab.classList.toggle('is-active', tab === button));
        filterRows();
    }

    function matchesTab(body) {
        const visible = body.dataset.visible === '1';
        switch (currentTab) {
            case 'archived': return !visible;
            case 'uniforms': return visible && body.dataset.type === 'uniforms';
            case 'books': return visible && body.dataset.type === 'books';
            case 'low': return visible && (body.dataset.health === 'low' || body.dataset.health === 'out');
            default: return visible;
        }
    }

    function filterRows() {
        const input = document.getElementById('stocks-instant-search');
        const bodies = document.querySelectorAll('tbody[data-row="stock"]');
        const noResults = document.getElementById('stocks-no-results-body');
        const query = (input?.value || '').trim().toLowerCase();
        let shown = 0;

        bodies.forEach((body) => {
            const matches = matchesTab(body) && (!query || (body.dataset.name || '').includes(query));
            body.classList.toggle('hidden', !matches);
            if (!matches) { collapseDetail(body); } else { shown += 1; }
        });

        if (noResults) {
            noResults.style.display = (bodies.length > 0 && shown === 0) ? '' : 'none';
        }
    }

    /* ---------- Sorting ---------- */
    function sortRows(th) {
        const key = th.dataset.sort;
        const table = document.getElementById('stocks-table');
        const currentSort = th.getAttribute('aria-sort');
        const direction = currentSort === 'ascending' ? -1 : 1;

        table.querySelectorAll('th.sortable').forEach((header) => {
            header.removeAttribute('aria-sort');
            const arrow = header.querySelector('.sort-arrow');
            if (arrow) arrow.textContent = '▲';
        });
        th.setAttribute('aria-sort', direction === 1 ? 'ascending' : 'descending');
        const arrow = th.querySelector('.sort-arrow');
        if (arrow) arrow.textContent = direction === 1 ? '▲' : '▼';

        const numeric = key === 'qty' || key === 'price';
        const bodies = Array.from(document.querySelectorAll('tbody[data-row="stock"]'));

        bodies.sort((a, b) => {
            if (numeric) {
                return direction * ((parseFloat(a.dataset[key]) || 0) - (parseFloat(b.dataset[key]) || 0));
            }
            return direction * (a.dataset[key] || '').localeCompare(b.dataset[key] || '');
        });

        const anchor = document.getElementById('stocks-no-results-body');
        bodies.forEach((body) => table.insertBefore(body, anchor));
    }

    /* ---------- Expandable detail rows ---------- */
    function toggleDetail(row, event) {
        if (event.target.closest('.actions-dropdown-container') || event.target.closest('a') || event.target.closest('button')) {
            return;
        }
        const body = row.closest('tbody');
        const detail = body?.querySelector('.stock-detail-row');
        if (!detail) return;
        const opening = detail.hidden;
        detail.hidden = !opening;
        body.classList.toggle('is-open', opening);
    }

    function collapseDetail(body) {
        const detail = body.querySelector('.stock-detail-row');
        if (detail) detail.hidden = true;
        body.classList.remove('is-open');
    }

    /* ---------- Row action menus ---------- */
    function toggleActionsMenu(button) {
        const menu = button.closest('.actions-dropdown-container')?.querySelector('.actions-dropdown-menu');
        if (!menu) return;

        document.querySelectorAll('.actions-dropdown-menu.active').forEach((openMenu) => {
            if (openMenu !== menu) {
                openMenu.classList.remove('active');
                openMenu.closest('.actions-dropdown-container')?.querySelector('.row-menu-btn')?.classList.remove('is-open');
            }
        });

        const isActive = menu.classList.toggle('active');
        button.classList.toggle('is-open', isActive);
    }

    function closeAllMenus() {
        document.querySelectorAll('.actions-dropdown-menu.active').forEach((menu) => {
            menu.classList.remove('active');
            menu.closest('.actions-dropdown-container')?.querySelector('.row-menu-btn')?.classList.remove('is-open');
        });
    }

    /* ---------- Adjust modal ---------- */
    function openAdjustModalFromButton(button) {
        closeAllMenus();

        const modal = document.getElementById('adjustStockModal');
        const form = document.getElementById('adjustStockForm');
        const nameEl = document.getElementById('adjustModalItemName');
        const sizeField = document.getElementById('adjustSizeField');
        const sizeSelect = document.getElementById('adjust_size');
        if (!modal || !form) return;

        let sizes = {};
        try { sizes = JSON.parse(button.dataset.sizes || '{}'); } catch (e) { sizes = {}; }

        form.action = button.dataset.adjustUrl || '';
        form.reset();
        if (nameEl) nameEl.textContent = button.dataset.itemName || '';

        const sizeKeys = Object.keys(sizes);
        if (sizeKeys.length > 0) {
            sizeField.style.display = '';
            sizeSelect.innerHTML = '';
            sizeSelect.required = true;
            sizeKeys.forEach((size) => {
                const option = document.createElement('option');
                option.value = size;
                option.textContent = size + ' — ' + Number(sizes[size]).toLocaleString() + ' in stock';
                sizeSelect.appendChild(option);
            });
        } else {
            sizeField.style.display = 'none';
            sizeSelect.innerHTML = '';
            sizeSelect.required = false;
        }

        modal.classList.add('active');
        refreshBodyScrollLock();
    }

    function closeAdjustModal() {
        document.getElementById('adjustStockModal')?.classList.remove('active');
        refreshBodyScrollLock();
    }

    /* ---------- Archive modal ---------- */
    function openArchiveModalFromButton(button) {
        closeAllMenus();
        const modal = document.getElementById('archiveStockModal');
        const form = document.getElementById('archiveStockForm');
        const nameEl = document.getElementById('archiveModalItemName');
        const titleEl = document.getElementById('archiveModalTitle');
        const promptTextEl = document.getElementById('archiveModalPromptText');
        const submitEl = document.getElementById('archiveModalSubmit');
        if (!modal || !form) return;

        const isArchiveAction = button.dataset.visible === '1';

        form.action = button.dataset.archiveUrl || '';
        if (nameEl) nameEl.textContent = button.dataset.itemName || '';
        if (titleEl) titleEl.textContent = isArchiveAction ? 'Archive Stock Item' : 'Restore Stock Item';
        if (promptTextEl) {
            promptTextEl.textContent = isArchiveAction
                ? 'Are you sure you want to archive the stock item'
                : 'Are you sure you want to restore the stock item';
        }
        if (submitEl) submitEl.textContent = isArchiveAction ? 'Archive Item' : 'Restore Item';

        modal.classList.add('active');
        refreshBodyScrollLock();
    }

    function closeArchiveModal() {
        document.getElementById('archiveStockModal')?.classList.remove('active');
        refreshBodyScrollLock();
    }

    /* ---------- Delete modal ---------- */
    function openDeleteModalFromButton(button) {
        closeAllMenus();
        const modal = document.getElementById('deleteStockModal');
        const form = document.getElementById('deleteStockForm');
        const nameEl = document.getElementById('deleteModalItemName');
        if (!modal || !form) return;

        form.action = button.dataset.deleteUrl || '';
        if (nameEl) nameEl.textContent = button.dataset.itemName || '';

        modal.classList.add('active');
        refreshBodyScrollLock();
    }

    function closeDeleteModal() {
        document.getElementById('deleteStockModal')?.classList.remove('active');
        refreshBodyScrollLock();
    }

    function refreshBodyScrollLock() {
        const anyOpen = document.querySelector('.stk-modal.active') !== null;
        document.body.style.overflow = anyOpen ? 'hidden' : '';
    }

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.actions-dropdown-container')) {
            closeAllMenus();
        }
    });

    document.querySelectorAll('.stk-modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.remove('active');
                refreshBodyScrollLock();
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.stk-modal.active').forEach((modal) => modal.classList.remove('active'));
            refreshBodyScrollLock();
            closeAllMenus();
        }
    });

    filterRows();
</script>
@endsection
