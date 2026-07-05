@extends('faculty.layout')

@section('breadcrumb')
    [Dashboard]({{ route('staff.dashboard') }}) / Uniform Checkout
@endsection

@section('content')
    @livewire('cashier-checkout')
@endsection
