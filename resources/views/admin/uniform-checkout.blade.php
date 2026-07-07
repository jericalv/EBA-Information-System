@extends('admin.layout')

@section('title', 'Uniform Checkout')

@section('extra-css')
    @livewireStyles
    @include('partials.checkout-ledger-css')
@endsection

@section('content')
    <div class="page-head">
        <div>
            <span class="eyebrow">Sales</span>
            <h1 class="page-title">Uniform Checkout</h1>
        </div>
        <span class="page-date">{{ now()->format('l, F d, Y') }}</span>
    </div>

    @livewire('ledger-checkout')
@endsection

@section('scripts')
    @livewireScripts
@endsection
