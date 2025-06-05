{{-- resources/views/transactions/return_form_page.blade.php --}}
@extends('layouts.app') {{-- Adjust this to your main application layout (e.g., adminlte::page, layouts.admin) --}}

@section('title', __('Proses Pemulangan Peralatan'))

{{-- Optional: If your layout uses a specific section for page titles/headers --}}
@hasSection('content_header')
    @section('content_header')
        <h1 class="m-0 text-dark">{{ __('Proses Pemulangan Peralatan') }}</h1>
    @endsection
@else
    {{-- Fallback or alternative title placement if content_header is not used by your layout --}}
@endif

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                {{--
                    Pass the issueTransactionId and loanApplicationId to the Livewire component.
                    The Livewire component's mount method ('App\Livewire\ResourceManagement\Admin\BPM\ProcessReturn')
                    will expect these parameters.
                --}}
                @livewire('resource-management.admin.bpm.process-return', [
                    'issueTransactionId' => $issueTransactionId,
                    'loanApplicationId' => $loanApplicationId
                ])
            </div>
        </div>
    </div>
@endsection
