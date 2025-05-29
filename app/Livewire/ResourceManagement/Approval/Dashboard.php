<?php

// File: app/Livewire/ResourceManagement/Approval/Dashboard.php

namespace App\Livewire\ResourceManagement\Approval;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User; // Ensure User model is imported
use App\Services\ApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class Dashboard extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $filterStatus = Approval::STATUS_PENDING;
    public string $filterType = 'all'; // e.g., 'all', 'email_application', 'loan_application'
    public string $search = '';

    public bool $showApprovalModal = false;
    public ?int $currentApprovalId = null;
    public $approvalDecision; // 'approved' or 'rejected'
    public $approvalComments;

    protected string $paginationTheme = 'bootstrap';

    // RENAMED THE COMPUTED PROPERTY METHOD HERE
    #[Computed(persist: false)]
    public function pendingApprovalTasks(): LengthAwarePaginator // <<< NAME CHANGED HERE
    {
        $user = Auth::user();
        if (!$user) {
            Log::warning('Livewire.Approval.Dashboard: User not authenticated.');
            return new LengthAwarePaginator([], 0, 10, 1);
        }

        $query = Approval::query()
            ->where('officer_id', $user->id) // Only show approvals assigned to the current user
            ->with([
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name,personal_email'], // Use 'name' as per User model [cite: 1]
                        LoanApplication::class => [
                            'user:id,name,personal_email', // Use 'name' as per User model [cite: 1]
                            'applicationItems', // Assuming 'items' was meant to be 'applicationItems' as per LoanApplication model context [cite: 2]
                        ],
                    ]);
                },
            ]);

        if (
            $this->filterStatus !== 'all' &&
            in_array($this->filterStatus, Approval::getStatuses()) // Assuming Approval model has getStatuses()
        ) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType !== 'all') {
            // Relation::morphMap() might be useful if using aliases for morph types
            $morphMap = \Illuminate\Database\Eloquent\Relations\Relation::morphMap();
            $modelClass = $morphMap[$this->filterType] ?? $this->filterType;

            if (
                class_exists($modelClass) &&
                is_subclass_of($modelClass, \Illuminate\Database\Eloquent\Model::class)
            ) {
                $query->where('approvable_type', $modelClass);
            } else {
                Log::warning(
                    "Livewire.Approval.Dashboard: Invalid filterType '{$this->filterType}'. Resolved class '{$modelClass}' is not a valid model."
                );
            }
        }

        if (trim($this->search) !== '') {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', $searchTerm) // Search by approval ID
                    ->orWhere('stage', 'like', $searchTerm) // Search by approval stage
                    ->orWhereHasMorph(
                        'approvable',
                        [EmailApplication::class, LoanApplication::class],
                        function ($morphQ, $type) use ($searchTerm) {
                            // Search within the approvable model (EmailApplication or LoanApplication)
                            $morphQ->where('id', 'like', $searchTerm); // Search by application ID
                            if ($type === EmailApplication::class) {
                                $morphQ->orWhere('proposed_email', 'like', $searchTerm);
                            } elseif ($type === LoanApplication::class) {
                                $morphQ->orWhere('purpose', 'like', $searchTerm); // Common field for loan apps
                            }
                            // Search by applicant's name or email
                            $morphQ->orWhereHas('user', function ($userQ) use ($searchTerm) {
                                $userQ
                                    ->where('name', 'like', $searchTerm) // Use 'name' as per User model [cite: 1]
                                    ->orWhere('personal_email', 'like', $searchTerm);
                            });
                        }
                    );
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    #[Computed]
    public function currentApprovalDetails(): ?Approval
    {
        if (!$this->currentApprovalId) {
            return null;
        }
        try {
            $approval = Approval::with([
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => [
                            'user:id,name,service_status', // Use 'name' [cite: 1]
                            'user.department:id,name',
                            'user.position:id,name',
                            'user.grade:id,name',
                        ],
                        LoanApplication::class => [
                            'user:id,name', // Use 'name' [cite: 1]
                            'user.department:id,name',
                            'user.position:id,name',
                            'user.grade:id,name',
                            'applicationItems', // Consistent with above: 'applicationItems' [cite: 2]
                        ],
                    ]);
                },
                'officer:id,name', // Officer who needs to act (or acted)
            ])->find($this->currentApprovalId);

            if (!$approval) {
                Log::warning(
                    'Livewire.Approval.Dashboard: currentApprovalId set but approval not found: ' .
                    $this->currentApprovalId
                );
                $this->dispatch('closeApprovalModalEvent');
                session()->flash(
                    'error',
                    __('Approval task not found or has been removed.')
                );
            }
            return $approval;
        } catch (ModelNotFoundException $e) {
            Log::error(
                'Livewire.Approval.Dashboard: Model not found fetching current approval: ' .
                $this->currentApprovalId,
                ['exception' => $e]
            );
            $this->dispatch('closeApprovalModalEvent');
            session()->flash(
                'error',
                __('Error loading approval details. The record may not exist.')
            );
            return null;
        } catch (Throwable $e) {
            Log::error(
                'Livewire.Approval.Dashboard: Error fetching current approval details: ' .
                $e->getMessage(),
                ['exception' => $e]
            );
            $this->dispatch('closeApprovalModalEvent');
            session()->flash(
                'error',
                __('An unexpected error occurred while loading approval details.')
            );
            return null;
        }
    }

    #[Computed]
    public function currentApprovable()
    {
        return $this->currentApprovalDetails?->approvable;
    }

    public function updatedFilterStatus($value): void
    {
        $this->resetPage();
    }

    public function updatedFilterType($value): void
    {
        $this->resetPage();
    }

    public function updatedSearch($value): void
    {
        $this->resetPage();
    }

    public function openApprovalModal(int $approvalId): void
    {
        $this->currentApprovalId = $approvalId;
        $this->reset(['approvalDecision', 'approvalComments']);
        $this->resetValidation(); // Clear previous validation errors
        $this->showApprovalModal = true;
        $this->dispatch('showApprovalModalEvent'); // Event for JS to show the modal // MODIFIED FROM showApprovalModalJs TO showApprovalModalEvent
        Log::debug(
            "Livewire.Approval.Dashboard: Opening modal for Approval ID {$approvalId}."
        );
    }

    public function recordDecision(ApprovalService $approvalService): void
    {
        $currentApproval = $this->currentApprovalDetails; // Uses the computed property
        if (!$currentApproval) {
            session()->flash(
                'error',
                __('Approval task not found. Please try again.')
            );
            $this->dispatch('closeApprovalModalEvent'); // MODIFIED FROM hideApprovalModalJs TO closeApprovalModalEvent
            return;
        }

        $this->validate(); // Uses rules() method

        try {
            $this->authorize('update', $currentApproval); // Policy check
            $user = Auth::user();
            if (!$user) {
                // Should not happen if middleware protects the route, but good for robustness
                throw new \RuntimeException(__('Authenticated user not found.'));
            }

            $approvalService->processApprovalDecision(
                $currentApproval,
                $this->approvalDecision,
                $user,
                $this->approvalComments
            );

            session()->flash(
                'success',
                __('Decision recorded successfully for task #') . $currentApproval->id
            );
            Log::info(
                "Livewire.Approval.Dashboard: Decision '{$this->approvalDecision}' recorded for Approval ID {$currentApproval->id} by User ID {$user->id} via ApprovalService."
            );
            $this->dispatch('closeApprovalModalEvent'); // MODIFIED FROM hideApprovalModalJs TO closeApprovalModalEvent // Event for JS to hide the modal
            $this->reset(['showApprovalModal', 'currentApprovalId', 'approvalDecision', 'approvalComments']);
        } catch (AuthorizationException $e) {
            session()->flash(
                'error',
                __('You are not authorized to perform this action.')
            );
            Log::warning(
                "Livewire.Approval.Dashboard: AuthorizationException for Approval ID {$currentApproval->id}. User ID: " . ($user->id ?? 'N/A') . ". Error: {$e->getMessage()}"
            );
            $this->dispatch('closeApprovalModalEvent'); // MODIFIED FROM hideApprovalModalJs TO closeApprovalModalEvent
        } catch (Throwable $e) {
            session()->flash(
                'error',
                __('An error occurred while recording the decision: ') .
                $e->getMessage()
            );
            Log::error(
                "Livewire.Approval.Dashboard: Throwable error recording decision for Approval ID {$currentApproval->id}. Error: {$e->getMessage()}",
                ['exception' => $e]
            );
            // Don't close modal on unexpected error, user might want to retry or see current state
        }
    }

    #[On('closeApprovalModalEvent')] // Listens for event dispatched by JS or self
    public function closeApprovalModal(): void
    {
        $this->showApprovalModal = false;
        $this->currentApprovalId = null;
        $this->reset(['approvalDecision', 'approvalComments']);
        $this->resetValidation();
        Log::debug(
            'Livewire.Approval.Dashboard: Approval modal closed and state reset by event.'
        );
    }

    public function render(): View
    {
        // The pendingApprovals data is accessed directly in the view
        // via the computed property $this->pendingApprovalTasks (corrected comment)
        return view('livewire.resource-management.approval.dashboard');
    }

    protected function rules(): array
    {
        return [
            'approvalDecision' => [
                'required',
                'in:' . Approval::STATUS_APPROVED . ',' . Approval::STATUS_REJECTED,
            ],
            'approvalComments' => $this->approvalDecision === Approval::STATUS_REJECTED
                ? ['required', 'string', 'min:10', 'max:2000']
                : ['nullable', 'string', 'max:2000'],
        ];
    }
}
