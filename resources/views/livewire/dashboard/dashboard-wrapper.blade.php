{{-- resources/views/livewire/dashboard/dashboard-wrapper.blade.php --}}
@extends('layouts.app')

{{-- The Title attribute is already set in the Livewire component --}}

@section('content')
    <div>
        {{-- This renders the main user dashboard Livewire component --}}
        @livewire('dashboard')
    </div>
@endsection
