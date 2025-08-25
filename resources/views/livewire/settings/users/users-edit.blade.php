{{--
    Livewire Component View: Edit User Page
    This file is intended to be used as a Livewire component view and MUST use a single root element.
    Do NOT include @extends or @section directives.
    All code/content must be wrapped in a single root <div> for Livewire compatibility.
--}}

<div>
    <div class="container-fluid px-lg-4 py-4">
        <!-- Header with page title and back button -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="fas fa-user-edit me-2"></i>{{ __('Kemaskini Pengguna') }}
                @if ($user->exists)
                    <span class="text-muted fw-normal ms-2"> - {{ $user->name }}</span>
                @endif
            </h1>
            <a href="{{ route('settings.users.index') }}" wire:navigate class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                <i class="fas fa-arrow-left me-1"></i>
                {{ __('Kembali ke Senarai Pengguna') }}
            </a>
        </div>

        {{-- Success Message --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{__('Tutup')}}"></button>
            </div>
        @endif

        {{-- Error Message --}}
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{__('Tutup')}}"></button>
            </div>
        @endif

        <!-- Edit User Form Card -->
        <div class="card shadow-sm motac-card">
            <div class="card-header bg-light py-3 motac-card-header">
                <h3 class="h5 card-title fw-semibold mb-0">{{ __('Borang Maklumat Pengguna') }}</h3>
            </div>
            <div class="card-body">
                {{-- Include the user form fields partial. Pass mode as 'edit' --}}
                @include('livewire.settings.users.users-form-fields', ['mode' => 'edit'])
            </div>
        </div>
    </div>
</div>
