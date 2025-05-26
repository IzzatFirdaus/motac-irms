<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')] // Ensure layouts.app is Bootstrap-compatible
class OutstandingLoans extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $searchTerm = '';
    protected string $paginationTheme = 'bootstrap'; // Converted to Bootstrap

    public function mount(): void
    {
        $this->authorize('viewAny', LoanApplication::class); // General BPM permission
    }

    public function getOutstandingApplicationsProperty() // Changed to computed property
    {
        /** @var User $user */
        $user = Auth::user();
        // BPM staff should see applications that are approved and awaiting issuance by them,
        // or partially issued and awaiting further issuance.
        $query = LoanApplication::query()
            ->with([
                'user:id,name,department_id',
                'user.department:id,name',
                'applicationItems', // No need for detailed equipment details at this high-level list for outstanding loans; can be fetched on the "issue" form/view.
                'approvals' // To see who approved
            ])
            ->whereIn('status', [
                LoanApplication::STATUS_APPROVED, // Approved by HOD, ready for BPM action
                LoanApplication::STATUS_PENDING_BPM_REVIEW, // If BPM has a specific review stage before issuance
                // LoanApplication::STATUS_PARTIALLY_ISSUED, // If BPM can continue issuing for partially issued ones (currently commented out, depends on workflow)
            ]);
            // Optionally, filter by applications where current_approval_stage is specifically for BPM
            // ->where('current_approval_stage', Approval::STAGE_LOAN_BPM_REVIEW) // Example, if you have such a field

        if (!empty($this->searchTerm)) {
            $searchTerm = '%' . $this->searchTerm . '%';
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('id', 'like', $searchTerm)
                    ->orWhere('purpose', 'like', $searchTerm)
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('name', 'like', $searchTerm);
                    });
            });
        }

        return $query->orderBy('updated_at', 'desc')->paginate(10); // Show most recently updated applications first
    }

    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.resource-management.admin.bpm.outstanding-loans', [
            'applications' => $this->outstandingApplications, // Access computed property
        ])->title(__('Permohonan Pinjaman Tertunggak (Tindakan BPM)'));
    }
}
