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
        background: #fff;
        border: 1px solid #e2e8f0;
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
        color: #64748b;
    }
    .tx-stat-value {
        font-size: 34px;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.03em;
        color: #0f172a;
        font-variant-numeric: tabular-nums;
    }
    .tx-stat-value.is-pine { color: #0A5C2F; }
    .tx-stat-foot {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid #e2e8f0;
        font-size: 12px;
        color: #94a3b8;
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
