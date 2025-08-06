<?php

namespace App\Livewire;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Services\ApprovalService; // For processing decisions
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule; // Make sure Rule is imported
use Illuminate\View\View; // Make sure this is imported
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On; // For listening to events
use Livewire\Attributes\Title; // Import Title attribute
use Livewire\Component;
use Livewire\WithPagination;
use Throwable; // Import Throwable
use Illuminate\Database\Eloquent\Builder; // Added for type hinting in computed property
use Illuminate\Support\Facades\DB; // Added for DB::transaction


#[Layout('layouts.app')] // Bootstrap main layout
#[Title('Papan Pemuka Kelulusan')] // Set the page title using the Livewire attribute
class ApprovalDashboard extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // Only LoanApplication::class remains as a filter option
    public string $filterType = 'all'; // 'all', LoanApplication::class

    public string $searchTerm = '';

    public string $filterStatus = Approval::STATUS_PENDING; // Default to pending

    // Modal properties for taking action
    public bool $showApprovalActionModal = false;

    public ?Approval $selectedApproval = null;

    public string $decision = ''; // 'approved' or 'rejected'

    public string $comments = '';

    protected string $paginationTheme = 'bootstrap';

    protected ApprovalService $approvalService;

    // Validation rules for the decision form
    protected function rules(): array
    {
        return [
            'decision' => ['required', 'string', Rule::in([Approval::STATUS_APPROVED, Approval::STATUS_REJECTED])],
            'comments' => [
                Rule::when($this->decision === Approval::STATUS_REJECTED, ['required', 'string', 'min:10']),
                'nullable', 'string', 'max:1000',
            ],
        ];
    }

    // Custom validation messages
    protected array $messages = [
        'decision.required' => 'Sila pilih keputusan (Lulus/Tolak).',
        'decision.in' => 'Keputusan yang dipilih tidak sah.',
        'comments.required' => 'Sila masukkan komen untuk keputusan Penolakan.',
        'comments.min' => 'Komen mestilah sekurang-kurangnya 10 aksara.',
        'comments.max' => 'Komen tidak boleh melebihi 1000 aksara.',
    ];


    /**
     * Mount the component and inject the ApprovalService.
     * This is an example of constructor injection in Livewire v3.
     */
    public function boot(ApprovalService $approvalService): void
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Computed property to get approval tasks for the current user.
     * This method will be called automatically by Livewire when accessed as a property, e.g., $this->approvalTasks.
     * Results are cached for subsequent access within the same request.
     */
    #[Computed]
    public function getApprovalTasksProperty(): LengthAwarePaginator
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure user is authenticated and has permission to view approvals
        if (! $user || ! $user->can('view_approvals')) {
            // Log::warning('ApprovalDashboard: Unauthorized access attempt to approval tasks.', ['user_id' => $user->id ?? 'guest']);
            return new LengthAwarePaginator([], 0, 10); // Return empty paginator if unauthorized
        }

        $query = Approval::where('officer_id', $user->id)
            ->with([
                'approvable' => function ($morphTo): void {
                    $morphTo->morphWith([
                        LoanApplication::class => ['user:id,name,department_id', 'user.department:id,name', 'loanApplicationItems'],
                        // EmailApplication::class => ['user:id,name,department_id', 'user.department:id,name'], // Removed
                    ]);
                },
            ]);

        // Apply filters
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType !== 'all') {
            // Ensure approvable_type matches the full class namespace
            $query->where('approvable_type', $this->filterType);
        }

        if ($this->searchTerm !== '') {
            $searchTerm = '%'.trim($this->searchTerm).'%';
            $query->where(function (Builder $q) use ($searchTerm): void {
                $q->whereHasMorph('approvable', [LoanApplication::class/*, EmailApplication::class*/], function (Builder $morphQuery) use ($searchTerm): void { // Removed EmailApplication
                    $morphQuery->where('application_no', 'like', $searchTerm)
                        ->orWhereHas('user', function (Builder $userQuery) use ($searchTerm): void {
                            $userQuery->where('name', 'like', $searchTerm);
                        });
                });
                // No need to query directly on Approval fields like 'comments' as per requirements
            });
        }

        // Order by creation date descending by default, or by a specific field if required
        $query->orderBy('created_at', 'desc');

        return $query->paginate(10);
    }

    /**
     * Computed property for status filter options.
     */
    #[Computed]
    public function getStatusOptionsProperty(): array
    {
        return [
            'all' => 'Semua Status',
            Approval::STATUS_PENDING => Approval::$STATUSES_LABELS[Approval::STATUS_PENDING],
            Approval::STATUS_APPROVED => Approval::$STATUSES_LABELS[Approval::STATUS_APPROVED],
            Approval::STATUS_REJECTED => Approval::$STATUSES_LABELS[Approval::STATUS_REJECTED],
            // Approval::STATUS_CANCELED => Approval::$STATUSES_LABELS[Approval::STATUS_CANCELED], // Not typically shown on approver dashboard unless relevant
        ];
    }

    /**
     * Computed property for application type filter options.
     */
    #[Computed]
    public function getTypeOptionsProperty(): array
    {
        return [
            'all' => 'Semua Jenis Permohonan',
            LoanApplication::class => 'Permohonan Pinjaman Peralatan ICT',
            // EmailApplication::class => 'Permohonan Akaun Emel Rasmi', // Removed
        ];
    }

    /**
     * Displays the approval action modal for a selected approval.
     */
    public function showActionModal(int $approvalId, string $actionType): void
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $approval = Approval::where('officer_id', $user->id)
                ->where('status', Approval::STATUS_PENDING)
                ->findOrFail($approvalId);

            $this->authorize('approveOrReject', $approval); // Authorize this specific approval

            $this->selectedApproval = $approval;
            $this->decision = $actionType;
            $this->comments = ''; // Reset comments for new action
            $this->resetErrorBag(); // Clear any previous validation errors

            $this->showApprovalActionModal = true;
            $this->dispatch('showApprovalActionModalEvent'); // For JS to hook into Bootstrap modal

        } catch (AuthorizationException $e) {
            Log::warning('ApprovalDashboard: Authorization failed for showActionModal.', ['approval_id' => $approvalId, 'user_id' => Auth::id(), 'error' => $e->getMessage()]);
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk melihat butiran kelulusan ini.'));
            $this->closeModal(); // Close modal if unauthorized
        } catch (\Exception $e) {
            Log::error('ApprovalDashboard: Error showing action modal.', ['approval_id' => $approvalId, 'error' => $e->getMessage()]);
            $this->dispatch('toastr', type: 'error', message: __('Gagal memaparkan modal keputusan.'));
            $this->closeModal();
        }
    }


    /**
     * Submits the approval decision (approve/reject).
     */
    public function submitDecision(): void
    {
        $this->validate();

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if (! $this->selectedApproval || ! $user) {
                throw new \Exception('No approval selected or user not authenticated.');
            }

            $this->authorize('approveOrReject', $this->selectedApproval);

            // Refactor to use the specific service methods based on decision
            if ($this->decision === Approval::STATUS_APPROVED) {
                $this->approvalService->handleApprovedDecision(
                    $this->selectedApproval,
                    $this->selectedApproval->approvable, // Assuming approvable is LoanApplication
                    $this->comments // Pass comments if needed for approval (e.g., conditions)
                );
                $this->dispatch('toastr', type: 'success', message: __('Keputusan Lulus berjaya direkodkan.'));
            } elseif ($this->decision === Approval::STATUS_REJECTED) {
                $this->approvalService->handleRejectedDecision(
                    $this->selectedApproval,
                    $this->selectedApproval->approvable, // Assuming approvable is LoanApplication
                    $this->comments
                );
                $this->dispatch('toastr', type: 'success', message: __('Keputusan Tolak berjaya direkodkan.'));
            } else {
                // This case should ideally be caught by validation, but as a fallback
                throw new \Exception('Invalid decision type provided.');
            }

            $this->closeModal();
            $this->refreshDataEvent(); // Corrected to call the existing method
        } catch (AuthorizationException $e) {
            Log::error('ApprovalDashboard: Authorization error on submitDecision.', ['message' => $e->getMessage(), 'user_id' => $user->id]);
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk tindakan ini.'));
        } catch (Throwable $e) {
            Log::error('ApprovalDashboard: Error submitting decision.', ['exception' => $e, 'user_id' => $user->id]);
            $this->dispatch('toastr', type: 'error', message: __('Gagal merekodkan keputusan: ').$e->getMessage());
        }
    }

    #[On('close-modal')]
    public function closeModal(): void
    {
        $this->showApprovalActionModal = false;
        $this->selectedApproval = null;
        $this->decision = '';
        $this->comments = '';
        $this->resetErrorBag();
        $this->dispatch('closeApprovalActionModalEvent'); // For JS to hook into Bootstrap modal
    }

    #[On('refresh-approval-list')]
    public function refreshDataEvent(): void // Renamed to avoid conflict if a 'refreshData' property exists
    {
        unset($this->approvalTasks);
    }

    public function render(): View
    {
        return view('livewire.approval-dashboard', [
            // 'approvalTasks' property will be automatically available due to #[Computed]
        ]);
    }
}
