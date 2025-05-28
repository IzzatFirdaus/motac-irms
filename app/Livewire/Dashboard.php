<?php

declare(strict_types=1);

namespace App\Livewire; // Ensure this matches your actual namespace

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component; // For setting page title (Livewire 3.x)

class Dashboard extends Component
{
    // Properties for User Dashboard as per System Design 6.2
    public string $displayUserName = '';

    // ICT Loan related properties
    public int $pendingUserLoanApplicationsCount = 0;
    public int $activeUserLoansCount = 0;
    public EloquentCollection $userRecentLoanApplications;

    // Email/ID Application related properties
    public int $pendingUserEmailApplicationsCount = 0;
    public EloquentCollection $userRecentEmailApplications;

    /**
     * Initialize collections to prevent errors.
     */
    public function __construct()
    {
        $this->userRecentLoanApplications = new EloquentCollection();
        $this->userRecentEmailApplications = new EloquentCollection();
    }

    /**
     * Mount component and fetch initial data for the authenticated user.
     * System Design: 6.2 User Dashboard (application statuses, notifications, quick access).
     */
    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user) {
            $this->displayUserName = $user->name ?? __('Pengguna Tidak Dikenali');

            // ICT Loan Data for User - System Design: 4.3 LoanApplication Statuses
            $this->pendingUserLoanApplicationsCount = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_DRAFT,
                    LoanApplication::STATUS_PENDING_SUPPORT,
                    LoanApplication::STATUS_PENDING_HOD_REVIEW,
                    LoanApplication::STATUS_PENDING_BPM_REVIEW,
                    LoanApplication::STATUS_APPROVED, // Approved but not yet issued
                ])->count();

            $this->activeUserLoansCount = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_ISSUED,
                    LoanApplication::STATUS_PARTIALLY_ISSUED,
                    LoanApplication::STATUS_OVERDUE,
                ])->count();

            $this->userRecentLoanApplications = LoanApplication::where('user_id', $user->id)
                ->with([
                    'user:id,name', // Eager load applicant for display
                    'applicationItems:id,loan_application_id,equipment_type,quantity_requested,quantity_approved' // Essential items info
                ])
                ->latest('submitted_at') // Order by submission date; consider 'updated_at' or 'created_at' for drafts
                ->limit(5) // Configurable limit for "recent" items
                ->get();

            // Email/ID Application Data for User - System Design: 4.2 EmailApplication Statuses
            $this->pendingUserEmailApplicationsCount = EmailApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    EmailApplication::STATUS_DRAFT,
                    EmailApplication::STATUS_PENDING_SUPPORT,
                    EmailApplication::STATUS_PENDING_ADMIN,
                    EmailApplication::STATUS_APPROVED, // Approved by support, pending IT action
                    EmailApplication::STATUS_PROCESSING, // Being processed by IT
                ])->count();

            $this->userRecentEmailApplications = EmailApplication::where('user_id', $user->id)
                ->with('user:id,name') // Eager load applicant
                ->latest('created_at')
                ->limit(5)
                ->get();

        } else {
            $this->displayUserName = __('Pengguna Tetamu');
            Log::warning('MOTAC Dashboard: User not authenticated during mount. Dashboard data will be limited.');
            // Initialize counts to 0 if user is not authenticated
            $this->pendingUserLoanApplicationsCount = 0;
            $this->activeUserLoansCount = 0;
            $this->pendingUserEmailApplicationsCount = 0;
            // Collections are already initialized as empty
        }
    }

    /**
     * Method to provide the page title dynamically and allow translation.
     * This uses Livewire 3's #[Title] attribute feature by returning the title string.
     */
    //#[Title]
    public function pageTitle(): string
    {
        // Ensure config('variables.templateName') is available and translated if it's a key
        $appName = __(config('variables.templateName', 'Sistem MOTAC'));
        return __('Papan Pemuka Utama') . ' - ' . $appName;
    }

    /**
     * Render the component's view.
     * Assumes Blade file is at resources/views/livewire/dashboard/dashboard.blade.php
     */
    public function render(): View
    {
        return view('livewire.dashboard.dashboard');
    }

    // Placeholder for dashboard-specific actions if any were to be added.
    // Example:
    // public function someAction()
    // {
    //     // ... logic ...
    //     session()->flash('message', ['type' => 'success', 'content' => __('Tindakan berjaya dilaksanakan.')]);
    //     // Or using Toastr:
    //     // $this->dispatch('toastr', ['type' => 'success', 'message' => __('Action performed successfully!')]);
    // }
}
