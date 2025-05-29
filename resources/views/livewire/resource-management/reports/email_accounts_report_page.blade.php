@extends('layouts.app')

@section('title', __('Laporan Akaun E-mel & ID Pengguna'))

@section('content')
<div class="container py-4">
    @livewire('resource-management.admin.reports.email-accounts-report')
</div>
@endsection
