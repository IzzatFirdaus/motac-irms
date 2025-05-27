<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use App\Models\LoanApplication;
use App\Models\EmailApplication;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;         // Correct base class
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;  // For setting page title

//#[Layout('layouts.app')] // System Design implies a main app layout
// Removed class-level #[Title('')] as it's handled by the method below
class Dashboard extends Component
{
    // Properties for User Dashboard as per System Design 6.2
    public string $displayUserName = '';

    // ICT Loan related properties
    public int $pendingLoanRequestsCount = 0;
    public int $activeLoansCount = 0;
    public EloquentCollection $userRecentLoanApplications;

    // Email/ID Application related properties
    public int $pendingEmailRequestsCount = 0;
    public EloquentCollection $userRecentEmailApplications;

    // Property to hold the dynamic part of the title
    public string $pageTitleAppName = '';

    /**
     * Initialize collections to prevent errors if mount fails or user is not authenticated early.
     */
    public function __construct()
    {
        $this->userRecentLoanApplications = new EloquentCollection();
        $this->userRecentEmailApplications = new EloquentCollection();
        $this->pageTitleAppName = config('app.name', 'MOTAC RMS'); // Initialize dynamic part of title
    }

    /**
     * Mount component and fetch initial data when the component is initialized.
     */
    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user) {
            // Per User model design (name field)
            $this->displayUserName = $user->name ?? __('Pengguna Tidak Dikenali');

            // Fetch counts specific to the authenticated user for ICT Loans
            // Statuses based on LoanApplication model constants and System Design 4.3
            $this->pendingLoanRequestsCount = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_DRAFT,
                    LoanApplication::STATUS_PENDING_SUPPORT,
                    LoanApplication::STATUS_PENDING_HOD_REVIEW,
                    LoanApplication::STATUS_PENDING_BPM_REVIEW,
                    LoanApplication::STATUS_APPROVED,
                ])->count();

            $this->activeLoansCount = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_ISSUED,
                    LoanApplication::STATUS_PARTIALLY_ISSUED,
                    LoanApplication::STATUS_OVERDUE,
                ])->count();

            $this->userRecentLoanApplications = LoanApplication::where('user_id', $user->id)
                ->with(['applicationItems:id,loan_application_id,equipment_type,quantity_requested,quantity_approved'])
                ->latest()
                ->limit(3)
                ->get();

            // Fetch counts specific to the authenticated user for Email/ID Applications
            // Statuses based on EmailApplication model constants and System Design 4.2
            $this->pendingEmailRequestsCount = EmailApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    EmailApplication::STATUS_DRAFT,
                    EmailApplication::STATUS_PENDING_SUPPORT,
                    EmailApplication::STATUS_PENDING_ADMIN,
                    EmailApplication::STATUS_APPROVED,
                ])->count();

            $this->userRecentEmailApplications = EmailApplication::where('user_id', $user->id)
                ->latest()
                ->limit(3)
                ->get();

        } else {
            $this->displayUserName = __('Pengguna Tetamu');
            Log::warning('MOTAC Dashboard: User not authenticated during mount. Dashboard data will be limited.');
            $this->pendingLoanRequestsCount = 0;
            $this->activeLoansCount = 0;
            $this->pendingEmailRequestsCount = 0;
        }
    }

    /**
     * Dynamically compute the title for the Title attribute.
     * This method is automatically called by Livewire due to the #[Title] attribute.
     */
    //#[Title]
    public function pageTitle(): string
    {
        return __('Papan Pemuka') . ' - ' . $this->pageTitleAppName;
    }

    /**
     * Render the component's view.
     */
    public function render(): View
    {
        // The page title is now handled by the #[Title] attribute and pageTitle() method.
        return view('livewire.dashboard');
    }
}
