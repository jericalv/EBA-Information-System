@extends('faculty.layout')

@section('title', 'Transaction Logs')

@section('extra-css')
<style>
    /* Scoped restyle of the shared cashier Livewire component so it matches
       the faculty graphite design system. Cashier portal is unaffected. */
    .bg-\[\#1a3c2e\] { background-color: var(--pine) !important; }
    .hover\:bg-\[\#214837\]:hover { background-color: var(--pine-strong) !important; }
    .text-\[\#1a3c2e\] { color: var(--ink) !important; }
    .focus-within\:border-\[\#1a3c2e\]:focus-within { border-color: var(--pine) !important; }
    .focus-within\:ring-\[\#1a3c2e\]\/10:focus-within { --tw-ring-color: rgba(31, 41, 55, 0.10) !important; }
    .bg-emerald-100 { background-color: #E9EDF1 !important; }
    .text-emerald-800 { color: #1F2937 !important; }

    /* Dark theme: re-map the component's Tailwind slate/white utilities to the
       dark graphite tokens (scoped to this page; cashier portal unaffected). */
    html[data-theme="dark"] .bg-white { background-color: var(--card) !important; }
    html[data-theme="dark"] .bg-slate-50,
    html[data-theme="dark"] .bg-slate-50\/90 { background-color: var(--hover) !important; }
    html[data-theme="dark"] .bg-slate-100,
    html[data-theme="dark"] .bg-slate-200 { background-color: var(--hover-2) !important; }
    html[data-theme="dark"] .text-slate-900,
    html[data-theme="dark"] .text-slate-800,
    html[data-theme="dark"] .text-slate-700 { color: var(--ink) !important; }
    html[data-theme="dark"] .text-slate-600,
    html[data-theme="dark"] .text-slate-500 { color: var(--muted) !important; }
    html[data-theme="dark"] .text-slate-400 { color: var(--faint) !important; }
    html[data-theme="dark"] .border-slate-100,
    html[data-theme="dark"] .border-slate-200 { border-color: var(--line) !important; }
    html[data-theme="dark"] .border-slate-300 { border-color: var(--line-strong) !important; }
    html[data-theme="dark"] .bg-emerald-100 { background-color: rgba(169, 180, 196, 0.14) !important; }
    html[data-theme="dark"] .text-emerald-800 { color: #C5CDD9 !important; }
    html[data-theme="dark"] .bg-\[\#1a3c2e\] { color: #10151B !important; }

    /* Transaction summary statcards — mirrors the admin portal exactly so the
       two roles share one identical design (colors hardcoded, not from the
       faculty graphite tokens). */
    .tx-stat-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .tx-stat-card {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        min-width: 0;
    }
    .tx-stat-label {
        font-family: 'IBM Plex Mono', ui-monospace, 'Cascadia Mono', monospace;
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .tx-stat-value {
        font-size: 34px;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.03em;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .tx-stat-value.is-pine { color: var(--pine); }
    .tx-stat-foot {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid var(--line);
        font-size: 12px;
        color: var(--faint);
    }
    @media (max-width: 900px) {
        .tx-stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('breadcrumb')
    [Dashboard]({{ route('staff.dashboard') }}) / Transaction Logs
@endsection

@section('content')
    @livewire('cashier.transaction-logs')
@endsection
