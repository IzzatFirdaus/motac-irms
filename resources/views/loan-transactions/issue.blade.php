{{-- resources/views/loan-transactions/issue.blade.php --}}
{{-- Page to process equipment issuance for a loan application --}}
@extends('layouts.app')

@section('title', __('Keluarkan Peralatan untuk Pinjaman #:app_id', ['app_id' => $loanApplication->id]))

@section('content')
    @include('loan-transactions.loan-transaction-issue')
@endsection
