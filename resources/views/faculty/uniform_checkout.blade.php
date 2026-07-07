@extends('faculty.layout')

@section('title', 'Uniform Checkout')

@section('extra-css')
    @include('partials.checkout-ledger-css')
    <style>
        /* Page-head pattern shared with the other portals; the faculty layout
           defines .eyebrow/.page-title but not the head wrapper itself. */
        .page-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .page-date {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            padding-bottom: 3px;
        }
    </style>
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
