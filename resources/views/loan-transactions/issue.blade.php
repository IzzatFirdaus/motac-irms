@extends('layouts.app')

@section('title', __('Keluarkan Peralatan untuk Pinjaman #:app_id', ['app_id' => $loanApplication->id]))

@section('content')
    <div class="container py-4">
        {{--
            This single line now loads the entire issuance form and its logic from the ProcessIssuance component.
            We pass the loan application ID, and the component will handle the rest.
        --}}
        @livewire('resource-management.admin.bpm.process-issuance', ['loanApplicationId' => $loanApplication->id])
    </div>
@endsection
