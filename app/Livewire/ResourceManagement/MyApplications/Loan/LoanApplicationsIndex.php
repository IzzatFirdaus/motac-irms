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
use Throwable;

/**
 * LoanApplicationsIndex Livewire component
 * Displays, filters, and manages the user's own ICT loan applications.
 */
#[Layout('layouts.app')]
class LoanApplicationsIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';

    public string $filterStatus = '';

    protected string $paginationTheme = 'bootstrap';

    public int $perPage = 10;

    // Approval/cancel modal state
    public ?int $selectedApplicationId = null;

    public bool $showApprovalActionModal = false;

    public ?string $approvalActionType = null;

    public string $approvalComments = '';

    // Service for approval actions
    protected ApprovalService $approvalService;

    // Validation rules and messages for comments
    protected array $rules = [
        'approvalComments' => '',
    ];

    protected array $messages = [
        'approvalComments.required' => 'Sila masukkan sebab penolakan.',
    ];

    /**
     * Inject ApprovalService for handling approval actions.
     */
    public function boot(ApprovalService $approvalService): void
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Mount component and authorize listing.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', LoanApplication::class);
    }

    /**
     * Computed property: paginated and filtered loan applications for this user.
     */
    public function getLoanApplicationsProperty()
    {
        /** @var User $user */
        $user = Auth::user();

        $query = $user->loanApplicationsAsApplicant()
            ->with(['approvals', 'user'])
            ->orderBy('created_at', 'desc');

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('application_number', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('purpose', 'like', '%'.$this->searchTerm.'%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Status options for the filter dropdown.
     */
    public function getStatusOptionsProperty(): array
    {
        // Map status constants to readable labels (can use translation keys here)
        return [
            ''                                                            => __('Semua Status'),
            LoanApplication::STATUS_DRAFT                                 => __('Draf'),
            LoanApplication::STATUS_PENDING_SUPPORT                       => __('Menunggu Sokongan Pegawai'),
            LoanApplication::STATUS_PENDING_APPROVER_REVIEW               => __('Menunggu Kelulusan'),
            LoanApplication::STATUS_PENDING_BPM_REVIEW                    => __('Menunggu Pengesahan BPM'),
            LoanApplication::STATUS_APPROVED                              => __('Diluluskan'),
            LoanApplication::STATUS_REJECTED                              => __('Ditolak'),
            LoanApplication::STATUS_PARTIALLY_ISSUED                      => __('Dikeluarkan Sebahagian'),
            LoanApplication::STATUS_ISSUED                                => __('Dikeluarkan'),
            LoanApplication::STATUS_RETURNED                              => __('Telah Dipulangkan'),
            LoanApplication::STATUS_OVERDUE                               => __('Tertunggak'),
            LoanApplication::STATUS_CANCELLED                             => __('Dibatalkan'),
            LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION => __('Dipulangkan Sebahagian'),
            LoanApplication::STATUS_COMPLETED                             => __('Selesai'),
        ];
    }

    /**
     * Show modal for approval/cancellation action.
     */
    public function confirmApprovalAction(int $applicationId, string $actionType): void
    {
        $this->selectedApplicationId = $applicationId;
        $this->approvalActionType    = $actionType;
        $this->approvalComments      = '';
        $this->resetValidation();

        // Set required validation for rejection, optional otherwise
        if ($this->approvalActionType === 'reject') {
            $this->rules['approvalComments'] = 'required|string|min:10';
        } else {
            $this->rules['approvalComments'] = 'nullable|string';
        }

        $this->showApprovalActionModal = true;
        $this->dispatch('openModal', elementId: 'approvalActionModal');
    }

    /**
     * Perform approval/cancel action.
     */
    public function performApprovalAction(): void
    {
        $this->validate();

        try {
            $loanApplication = LoanApplication::findOrFail($this->selectedApplicationId);

            if ($this->approvalActionType === 'cancel') {
                $this->authorize('cancel', $loanApplication);
                $this->approvalService->recordApprovalDecision(
                    $loanApplication->approvals()->latest()->first(),
                    Approval::STATUS_CANCELED,
                    $this->approvalComments,
                    []
                );
                $this->dispatch('toastr', type: 'success', message: 'Permohonan pinjaman berjaya dibatalkan.');
            } else {
                $this->authorize('reject', $loanApplication);
                $this->approvalService->recordApprovalDecision(
                    $loanApplication->approvals()->latest()->first(),
                    Approval::STATUS_REJECTED,
                    $this->approvalComments,
                    []
                );
                $this->dispatch('toastr', type: 'success', message: 'Permohonan pinjaman berjaya ditolak.');
            }

            $this->closeApprovalActionModal();
            $this->resetPage();
        } catch (AuthorizationException $e) {
            Log::error('Authorization error performing approval action: '.$e->getMessage(), [
                'user_id'        => Auth::id(),
                'application_id' => $this->selectedApplicationId,
                'action_type'    => $this->approvalActionType,
            ]);
            $this->dispatch('toastr', type: 'error', message: 'Anda tidak mempunyai kebenaran untuk melakukan tindakan ini.');
        } catch (Throwable $e) {
            Log::error('Error performing approval action for loan application: '.$e->getMessage(), [
                'user_id'        => Auth::id(),
                'application_id' => $this->selectedApplicationId,
                'action_type'    => $this->approvalActionType,
                'error'          => $e->getTraceAsString(),
            ]);
            $this->dispatch('toastr', type: 'error', message: 'Gagal melakukan tindakan. Sila cuba sebentar lagi.');
        }
    }

    /**
     * Close the approval/cancel modal.
     */
    public function closeApprovalActionModal(): void
    {
        $this->selectedApplicationId = null;
        $this->approvalActionType    = null;
        $this->approvalComments      = '';
        $this->resetValidation();
        $this->showApprovalActionModal = false;
        $this->dispatch('closeModal', elementId: 'approvalActionModal');
    }

    /**
     * Delete a draft loan application.
     */
    #[On('deleteLoanApplication')]
    public function deleteLoanApplication(string $id): void
    {
        try {
            $application = LoanApplication::findOrFail($id);
            $this->authorize('delete', $application);
            $application->delete();

            $this->dispatch('toastr', type: 'success', message: 'Permohonan draf #'.$id.' telah berjaya dipadam.');
        } catch (AuthorizationException $e) {
            $this->dispatch('toastr', type: 'error', message: 'Anda tidak mempunyai kebenaran untuk memadam permohonan ini.');
        } catch (\Exception $e) {
            Log::error('Failed to delete loan application: '.$e->getMessage());
            $this->dispatch('toastr', type: 'error', message: 'Gagal memadam permohonan tersebut.');
        }
    }

    /**
     * Reset pagination when search term changes.
     */
    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when status filter changes.
     */
    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Reset all filters and pagination.
     */
    public function resetFilters(): void
    {
        $this->reset(['searchTerm', 'filterStatus']);
        $this->resetPage();
    }

    /**
     * Render the Blade view for the user's loan applications index.
     */
    public function render(): View
    {
        return view('livewire.resource-management.my-applications.loan.loan-applications-index', [
            'applications'  => $this->loanApplications,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
