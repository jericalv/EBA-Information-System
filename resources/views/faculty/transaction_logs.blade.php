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
</style>
@endsection

@section('breadcrumb')
    [Dashboard]({{ route('staff.dashboard') }}) / Transaction Logs
@endsection

@section('content')
    @livewire('cashier.transaction-logs')
@endsection
