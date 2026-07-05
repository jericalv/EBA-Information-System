@extends('faculty.layout')

@section('title', 'Uniform Checkout')

@section('extra-css')
<style>
    /* Scoped restyle of the shared checkout Livewire component so it matches
       the faculty graphite design system. Cashier portal is unaffected. */
    .bg-green-100 { background-color: #E9EDF1 !important; }
    .text-green-600 { color: #1F2937 !important; }
</style>
@endsection

@section('breadcrumb')
    [Dashboard]({{ route('staff.dashboard') }}) / Uniform Checkout
@endsection

@section('content')
    @livewire('cashier-checkout')
@endsection
