@extends('faculty.layout')

@section('breadcrumb')
    [Dashboard]({{ route('staff.dashboard') }}) / Transaction Logs
@endsection

@section('content')
    @livewire('cashier.transaction-logs')
@endsection
