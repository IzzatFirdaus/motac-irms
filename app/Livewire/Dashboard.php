<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use App\Models\LoanApplication;
use App\Models\EmailApplication;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')] // Bootstrap main layout
class Dashboard extends Component
{
    // MOTAC Dashboard Properties
    public string $displayUserName = '';
    public int $pendingLoanRequestsCount = 0;  // User's pending loan applications
    public int $activeLoansCount = 0;          // User's active (issued) loans
    public int $pendingEmailRequestsCount = 0; // User's pending email applications
    public EloquentCollection $userRecentLoanApplications;
    public EloquentCollection $userRecentEmailApplications;

    public function __construct()
    {
        // Initialize collections to prevent errors if mount fails or user is not authenticated early
        $this->userRecentLoanApplications = new EloquentCollection();
        $this->userRecentEmailApplications = new EloquentCollection();
    }

    /**
     * Computed property for configuration data used by the layout.
     * Ensures data is suitable for a Bootstrap layout.
     */
    #[Computed]
    public function configData(): array
    {
        if (class_exists(Helpers::class) && method_exists(Helpers::class, 'appClasses')) {
            return Helpers::appClasses();
        }
        // Minimal fallback for layout compatibility
        return ['textDirection' => 'ltr', 'templateName' => config('app.name', 'MOTAC RMS')];
    }

    /**
     * Mount component and fetch initial data.
     */
    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user) {
            $this->displayUserName = $user->name ?? __('Pengguna');

            // Fetch counts specific to the authenticated user
            $this->pendingLoanRequestsCount = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_PENDING_SUPPORT,
                    LoanApplication::STATUS_PENDING_HOD_REVIEW,
                    LoanApplication::STATUS_PENDING_BPM_REVIEW,
                    LoanApplication::STATUS_APPROVED, // Considered pending until issued
                ])->count();

            $this->activeLoansCount = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    LoanApplication::STATUS_ISSUED,
                    LoanApplication::STATUS_PARTIALLY_ISSUED,
                    LoanApplication::STATUS_OVERDUE,
                ])->count();

            $this->pendingEmailRequestsCount = EmailApplication::where('user_id', $user->id)
                ->whereIn('status', [
                    EmailApplication::STATUS_PENDING_SUPPORT,
                    EmailApplication::STATUS_PENDING_ADMIN,
                    // EmailApplication::STATUS_APPROVED, // Might be considered pending until fully processed by IT
                ])->count();

            // Fetch recent applications for the user
            $this->userRecentLoanApplications = LoanApplication::where('user_id', $user->id)
                ->with(['user', 'applicationItems']) // 'user' might be redundant if already $user
                ->latest() // Order by created_at descending
                ->limit(3)
                ->get();

            $this->userRecentEmailApplications = EmailApplication::where('user_id', $user->id)
                ->with(['user']) // 'user' might be redundant
                ->latest()
                ->limit(3)
                ->get();
        } else {
            $this->displayUserName = __('Pengguna Tetamu');
            Log::warning('MOTAC Dashboard: User not authenticated during mount. Dashboard data will be limited.');
            // Initialize counts to 0 if user is not authenticated
            $this->pendingLoanRequestsCount = 0;
            $this->activeLoansCount = 0;
            $this->pendingEmailRequestsCount = 0;
        }
    }

    /**
     * Render the component's view.
     */
    public function render(): View
    {
        return view('livewire.dashboard')->title(__('Papan Pemuka Pengguna'));
    }
}
