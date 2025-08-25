{{-- resources/views/loan-transactions/loan-transaction-return-form-page.blade.php --}}
{{-- Page for BPM/admin to process equipment return for a specific transaction/application via Livewire --}}

@extends('layouts.app')

@section('title', __('Proses Pemulangan Peralatan'))

@hasSection('content_header')
    @section('content_header')
        <h1 class="m-0 text-dark">{{ __('Proses Pemulangan Peralatan') }}</h1>
    @endsection
@else
    {{-- Fallback: If no content_header section exists --}}
@endif

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                {{-- Livewire component expects issueTransactionId and loanApplicationId --}}
                @livewire('resource-management.admin.bpm.process-return', [
                    'issueTransactionId' => $issueTransactionId,
                    'loanApplicationId' => $loanApplicationId
                ])
            </div>
        </div>
    </div>
@endsection
