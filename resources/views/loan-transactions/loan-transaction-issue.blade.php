{{-- resources/views/loan-transactions/loan-transaction-issue.blade.php --}}
{{-- Page to process equipment issuance for a loan application --}}
@extends('layouts.app')

@section('title', __('Keluarkan Peralatan untuk Pinjaman #:app_id', ['app_id' => $loanApplication->id]))

@section('content')
    {{-- No container to allow for a full-width layout controlled by the Livewire component --}}
    <div class="py-4">
        {{-- Load ProcessIssuance Livewire component for issuing equipment --}}
        @livewire('resource-management.admin.bpm.process-issuance', ['loanApplicationId' => $loanApplication->id])
    </div>
@endsection
