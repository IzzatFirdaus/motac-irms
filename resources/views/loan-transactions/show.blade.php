{{-- resources/views/loan-transactions/show.blade.php --}}
{{-- Show details for a single loan transaction --}}

@extends('layouts.app')

@section('title', __('transaction.show_title') . ' #' . $loanTransaction->id)

@section('content')
    @include('loan-transactions.loan-transaction-show')
@endsection
