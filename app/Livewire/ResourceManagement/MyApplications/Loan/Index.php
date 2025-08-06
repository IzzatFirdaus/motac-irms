<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\MyApplications\Loan;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable; // Add this line to import the Throwable interface

#[Layout('layouts.app')]
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';

    public string $filterStatus = '';

    protected string $paginationTheme = 'bootstrap';

    public int $perPage = 10;

    public ?int $selectedApplicationId = null;

    public bool $showApprovalActionModal = false;

    public ?string $approvalActionType = null;

    public string $approvalComments = '';

    protected ApprovalService $approvalService;

    protected array $rules = [
        'approvalComments' => '',
    ];

    protected array $messages = [
        'approvalComments.required' => 'Sila masukkan sebab penolakan.',
    ];

    public function boot(ApprovalService $approvalService): void
    {
        $this->approvalService = $approvalService;
    }

    public function mount(): void
    {
        // Ensure authorization is specifically for LoanApplication
        $this->authorize('viewAny', LoanApplication::class); // Authorize viewing of loan applications
    }

    public function getLoanApplicationsProperty()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = $user->loanApplicationsAsApplicant()
            ->with(['approvals', 'user']) // Eager load approvals and user
            ->orderBy('created_at', 'desc');

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('application_number', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('purpose', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate($this->perPage);
    }

    public function getStatusOptionsProperty(): array
    {
        // Define status options for filtering
        return [
            '' => 'Semua Status',
            LoanApplication::STATUS_DRAFT => LoanApplication::STATUS_DRAFT,
            LoanApplication::STATUS_PENDING_SUPPORT => LoanApplication::STATUS_PENDING_SUPPORT,
            LoanApplication::STATUS_PENDING_APPROVER_REVIEW => LoanApplication::STATUS_PENDING_APPROVER_REVIEW,
            LoanApplication::STATUS_PENDING_BPM_REVIEW => LoanApplication::STATUS_PENDING_BPM_REVIEW,
            LoanApplication::STATUS_APPROVED => LoanApplication::STATUS_APPROVED,
            LoanApplication::STATUS_REJECTED => LoanApplication::STATUS_REJECTED,
            LoanApplication::STATUS_PARTIALLY_ISSUED => LoanApplication::STATUS_PARTIALLY_ISSUED,
            LoanApplication::STATUS_ISSUED => LoanApplication::STATUS_ISSUED,
            LoanApplication::STATUS_RETURNED => LoanApplication::STATUS_RETURNED,
            LoanApplication::STATUS_OVERDUE => LoanApplication::STATUS_OVERDUE,
            LoanApplication::STATUS_CANCELLED => LoanApplication::STATUS_CANCELLED,
            LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION => LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION,
            LoanApplication::STATUS_COMPLETED => LoanApplication::STATUS_COMPLETED,
        ];
    }

    public function confirmApprovalAction(int $applicationId, string $actionType): void
    {
        $this->selectedApplicationId = $applicationId;
        $this->approvalActionType = $actionType;
        $this->approvalComments = ''; // Reset comments
        $this->resetValidation(); // Clear any previous validation errors

        // Dynamically set validation rules for comments based on action type
        if ($this->approvalActionType === 'reject') {
            $this->rules['approvalComments'] = 'required|string|min:10';
        } else {
            // For 'cancel' or other actions, comments might be optional or have different rules
            $this->rules['approvalComments'] = 'nullable|string';
        }

        $this->showApprovalActionModal = true;
        $this->dispatch('openModal', elementId: 'approvalActionModal');
    }


    public function performApprovalAction(): void
    {
        $this->validate(); // Validate comments based on dynamically set rules

        try {
            $loanApplication = LoanApplication::findOrFail($this->selectedApplicationId);

            // Authorization check for the specific action type
            if ($this->approvalActionType === 'cancel') {
                $this->authorize('cancel', $loanApplication);
                // Call a service method to handle cancellation
                $this->approvalService->recordApprovalDecision(
                    $loanApplication->approvals()->latest()->first(), // Get the latest approval record
                    Approval::STATUS_CANCELED,
                    $this->approvalComments,
                    [] // No approval items for cancellation
                );
                $this->dispatch('toastr', type: 'success', message: 'Permohonan pinjaman berjaya dibatalkan.');
            } else {
                // This 'else' block likely handles the 'reject' action if this modal is reused.
                // If it's only for 'cancel' action, this else block might not be needed or needs refinement.
                // Assuming 'reject' is also handled here for a user to reject their own draft.
                $this->authorize('reject', $loanApplication); // Or a specific 'cancel' policy
                $this->approvalService->recordApprovalDecision(
                    $loanApplication->approvals()->latest()->first(), // Get the latest approval record
                    Approval::STATUS_REJECTED, // Assuming this is for rejecting
                    $this->approvalComments,
                    [] // No approval items for rejection from user side
                );
                $this->dispatch('toastr', type: 'success', message: 'Permohonan pinjaman berjaya ditolak.');
            }

            $this->closeApprovalActionModal();
            $this->resetPage(); // Refresh the list
        } catch (AuthorizationException $e) {
            Log::error('Authorization error performing approval action: ' . $e->getMessage(), ['user_id' => Auth::id(), 'application_id' => $this->selectedApplicationId, 'action_type' => $this->approvalActionType]);
            $this->dispatch('toastr', type: 'error', message: 'Anda tidak mempunyai kebenaran untuk melakukan tindakan ini.');
        } catch (Throwable $e) { // Now 'Throwable' is correctly recognized
            Log::error('Error performing approval action for loan application: ' . $e->getMessage(), ['user_id' => Auth::id(), 'application_id' => $this->selectedApplicationId, 'action_type' => $this->approvalActionType, 'error' => $e->getTraceAsString()]);
            $this->dispatch('toastr', type: 'error', message: 'Gagal melakukan tindakan. Sila cuba sebentar lagi.');
        }
    }


    public function closeApprovalActionModal(): void
    {
        $this->selectedApplicationId = null;
        $this->approvalActionType = null;
        $this->approvalComments = '';
        $this->resetValidation();
        $this->dispatch('closeModal', elementId: 'approvalActionModal');
    }

    #[On('deleteLoanApplication')]
    public function deleteLoanApplication(string $id): void
    {
        try {
            $application = LoanApplication::findOrFail($id);

            $this->authorize('delete', $application);

            $application->delete();

            $this->dispatch('toastr', type: 'success', message: 'Permohonan draf #' . $id . ' telah berjaya dipadam.');
        } catch (AuthorizationException $e) {
            $this->dispatch('toastr', type: 'error', message: 'Anda tidak mempunyai kebenaran untuk memadam permohonan ini.');
        } catch (\Exception $e) {
            Log::error('Failed to delete loan application: ' . $e->getMessage());
            $this->dispatch('toastr', type: 'error', message: 'Gagal memadam permohonan tersebut.');
        }
    }

    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['searchTerm', 'filterStatus']);
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.resource-management.my-applications.loan.index', [
            'applications' => $this->loanApplications,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
