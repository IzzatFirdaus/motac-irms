@extends('layouts.app') {{-- Uses the MOTAC main layout with Noto Sans and MOTAC theme colors --}}

{{-- Page title for browser/tab and header --}}
@section('title', __('Rekod Pulangan Peralatan'))

@section('content')
    <div class="container-fluid">
        {{--
            The 'process-return' Livewire component will display the ICT equipment return process UI.
            It receives the loan application ID as a parameter.
            Make sure the Livewire component expects 'loanApplicationId' as its public property or mount argument.
        --}}
        @livewire('resource-management.admin.bpm.process-return', ['loanApplicationId' => $loanApplicationId])
    </div>
@endsection
