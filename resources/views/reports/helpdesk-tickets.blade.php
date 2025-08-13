{{--
    resources/views/reports/helpdesk-tickets.blade.php

    Stub view for Helpdesk Tickets Report.
    This page will display the Helpdesk Tickets report in the future.
    For now, it serves as a placeholder to prevent view not found errors.
--}}

@extends('layouts.app')

@section('title', __('Laporan Tiket Helpdesk'))

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 fw-bold text-dark">
                <i class="bi bi-headset me-2"></i>
                {{ __('Laporan Tiket Helpdesk') }}
            </h1>
            <p class="text-muted">
                {{ __('Halaman ini akan memaparkan laporan tiket helpdesk. Ciri ini akan ditambah pada masa akan datang.') }}
            </p>
        </div>
    </div>
    <div class="alert alert-info d-flex align-items-center">
        <i class="bi bi-info-circle-fill me-2"></i>
        <div>
            {{ __('Fungsi laporan tiket helpdesk sedang dibangunkan.') }}
        </div>
    </div>
</div>
@endsection
