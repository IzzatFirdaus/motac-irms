{{-- resources/views/transactions/transaction-return.blade.php --}}
{{-- Transaction Return Page: Handles the equipment return process --}}
@extends('layouts.app')

@section('title', __('Proses Pemulangan Peralatan'))

@section('content')
    <div class="container py-4">
        {{-- Livewire component for BPM equipment return (admin context), pass transaction ID --}}
        @livewire('resource-management.admin.bpm.process-return', ['issueTransactionId' => $loanTransaction->id])
    </div>
@endsection
