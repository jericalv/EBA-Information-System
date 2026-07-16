{{-- Shared styles for the ledger-checkout Livewire component. Every color
     comes from the host layout's design tokens, so the component picks up
     each portal's palette (admin pine, faculty graphite) automatically. --}}
<style>
    [x-cloak] { display: none !important; }

    .co-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .co-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .co-cash-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 11px;
        border-radius: 6px;
        border: 1px solid #CDE3D4;
        background: #F0F7F2;
        color: #14532D;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .co-cash-badge svg { width: 14px; height: 14px; }

    /* ---------- Cart table (self-contained; no layout table CSS needed) ---------- */
    .co-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 960px;
    }
    .co-table thead th {
        text-align: left;
        padding: 11px 20px;
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--muted);
        background: #FAFCFA;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }
    .co-table tbody td {
        padding: 13px 20px;
        font-size: 13.5px;
        border-bottom: 1px solid var(--line);
        vertical-align: middle;
    }
    .co-table tbody tr { transition: background-color 0.12s ease; }
    .co-table tbody tr:hover { background: #FAFCFA; }
    .co-table tbody tr:last-child td { border-bottom: none; }
    .co-table .table-num {
        font-family: var(--font-mono);
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        color: var(--ink);
        white-space: nowrap;
    }
    .co-table .table-num.is-pine { color: var(--pine); }
    .co-table .table-dim { color: var(--faint); }
    .co-subtotal { font-size: 14px; }

    .co-col-item { min-width: 200px; }
    .co-col-size { min-width: 210px; }
    .co-col-qty { min-width: 110px; }
    .co-col-price { min-width: 130px; }

    .co-control { width: 100%; }
    input.co-control {
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        background: #fff;
        color: var(--ink);
        font-family: var(--font-ui);
        font-size: 13.5px;
        padding: 9px 12px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    input.co-control:focus {
        outline: none;
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--pine) 12%, transparent);
    }
    input.co-control.is-invalid {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(185, 28, 28, 0.12);
    }

    /* ---------- Footer total bar ---------- */
    .co-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding: 14px 20px;
        border-top: 1px solid var(--line);
        background: #FAFCFA;
    }
    .co-total-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--ink);
    }
    .co-total-sub {
        font-size: 12px;
        color: var(--muted);
        margin-top: 1px;
    }
    .co-footer-right {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .co-total {
        font-family: var(--font-mono);
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--pine);
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    /* ---------- Success modal (ledger style) ---------- */
    .co-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(23, 37, 28, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2600;
        padding: 16px;
    }
    .co-modal {
        width: min(440px, 100%);
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
        box-shadow: var(--shadow-pop);
        overflow: hidden;
    }
    .co-modal-head {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 14px 18px;
        background: #F0F7F2;
        border-bottom: 1px solid #CDE3D4;
        color: #14532D;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: -0.01em;
    }
    .co-modal-head svg { width: 16px; height: 16px; flex-shrink: 0; }
    .co-modal-body {
        padding: 18px;
        color: var(--muted);
        font-size: 13.5px;
        line-height: 1.55;
    }
    .co-modal-body p { margin: 0; }
    .co-modal-total {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
        padding: 12px 14px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #FAFCFA;
    }
    .co-modal-total span {
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .co-modal-total strong {
        font-family: var(--font-mono);
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--pine);
        font-variant-numeric: tabular-nums;
    }
    .co-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 0 18px 18px;
    }
    .co-modal-receipt { margin-top: 14px; }
    .co-modal-receipt label {
        display: block;
        margin-bottom: 6px;
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .co-modal-receipt input.co-control { width: 100%; }

    /* ---------- Dark theme (portals that support it set data-theme="dark") ----------
       Accent falls back to admin green; faculty overrides --accent-* to graphite. */
    html[data-theme="dark"] .co-cash-badge {
        background: var(--accent-soft, rgba(30, 149, 96, 0.14));
        border-color: var(--accent-line, rgba(30, 149, 96, 0.35));
        color: var(--accent-text, #8CD6AF);
    }
    html[data-theme="dark"] .co-table thead th { background: var(--hover, #1C2721); }
    html[data-theme="dark"] .co-table tbody tr:hover { background: var(--hover, #1C2721); }
    html[data-theme="dark"] input.co-control { background: var(--field, #1B2620); }
    html[data-theme="dark"] input.co-control::placeholder { color: var(--faint); }
    html[data-theme="dark"] .co-footer { background: var(--hover, #1C2721); }
    html[data-theme="dark"] .co-modal-backdrop { background: rgba(0, 0, 0, 0.55); }
    html[data-theme="dark"] .co-modal { background: var(--card); }
    html[data-theme="dark"] .co-modal-head {
        background: var(--accent-soft, rgba(30, 149, 96, 0.14));
        border-bottom-color: var(--accent-line, rgba(30, 149, 96, 0.35));
        color: var(--accent-text, #8CD6AF);
    }
    html[data-theme="dark"] .co-modal-total { background: var(--hover, #1C2721); }
</style>
