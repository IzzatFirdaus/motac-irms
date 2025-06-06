@extends('layouts.app')

@section('title', __('Proses Pemulangan Peralatan'))

@section('content')
    <div class="container py-4">
        {{-- Pass the original "issue" transaction ID to the Livewire component --}}
        @livewire('resource-management.admin.bpm.process-return', ['issueTransactionId' => $loanTransaction->id])
    </div>
@endsection
