<?php

// File: app/Livewire/ResourceManagement/Approval/Dashboard.php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\Approval;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

/**
 * Livewire Component for the Approver's Dashboard.
 * Displays pending approval tasks and allows officers to record decisions.
 * System Design Ref: 6.2 (Approver Dashboard), 9.4 (Approval Workflow Module)
 */
#[Layout('layouts.app')]
class Dashboard extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // --- Filters ---
    #[Url(keep: true, history: true, as: 'status_kelulusan')]
    public string $filterStatus = Approval::STATUS_PENDING;

    #[Url(keep: true, history: true, as: 'jenis_permohonan')]
    public string $filterType = ''; // Can be 'loan' or 'email' or empty for all

    public string $searchTerm = '';

    // --- Modals and Current Task State ---
    public bool $showApprovalModal = false;

    public ?int $currentApprovalId = null; // ID of the approval task being actioned

    public ?Approval $currentApprovalTask = null; // The approval task model being actioned

    public string $approvalDecision = ''; // 'approved' or 'rejected'

    public string $approvalNotes = ''; // Notes from the approver

    public array $approvalItems = []; // For quantity adjustments in loan approvals

    public string $modalTitle = '';

    // --- Service Injection ---
    protected ApprovalService $approvalService;

    public function boot(ApprovalService $approvalService): void
    {
        $this->approvalService = $approvalService;
    }

    public function mount(): void
    {
        $this->authorize('viewAny', Approval::class); // Assuming a policy for Approvals
        Log::info('Livewire\Approval\Dashboard: Approval dashboard loaded.', [
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Computed property to retrieve pending approval tasks for the authenticated user.
     *
     * @return LengthAwarePaginator<Approval>
     */
    #[Computed]
    public function approvalTasks(): LengthAwarePaginator
    {
        $approver = Auth::user();
        throw_if(! $approver instanceof User, new \RuntimeException('Authenticated user not found.'));

        $query = Approval::where('officer_id', $approver->id)
            ->with(['approvable', 'approvable.user', 'officer']); // Eager load relationships

        // Apply filters
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType) {
            if ($this->filterType === 'loan') {
                $query->whereMorphedTo('approvable', LoanApplication::class);
            }
            // Removed email filter as per v4.0 refactoring
            // elseif ($this->filterType === 'email') {
            //     $query->whereMorphedTo('approvable', EmailApplication::class);
            // }
        }

        if ($this->searchTerm) {
            $searchTermLower = strtolower($this->searchTerm);
            $query->where(function ($q) use ($searchTermLower) {
                $q->whereHasMorph('approvable', [LoanApplication::class], function ($morphQuery) use ($searchTermLower) {
                    $morphQuery->whereHas('user', function ($userQuery) use ($searchTermLower) {
                        $userQuery->whereRaw('LOWER(name) LIKE ?', ['%'.$searchTermLower.'%'])
                            ->orWhereRaw('LOWER(email) LIKE ?', ['%'.$searchTermLower.'%']);
                    });
                });
            });
        }

        // Order by latest pending tasks first
        $query->orderByRaw("CASE
            WHEN status = '".Approval::STATUS_PENDING."' THEN 1
            WHEN status = '".Approval::STATUS_APPROVED."' THEN 2
            WHEN status = '".Approval::STATUS_REJECTED."' THEN 3
            ELSE 4
        END")
            ->orderBy('created_at', 'desc');

        $tasks = $query->paginate(10); // Paginate the results

        Log::info(sprintf('Livewire\Approval\Dashboard: Fetched %d approval tasks for user %d.', $tasks->total(), $approver->id), [
            'filterStatus' => $this->filterStatus,
            'filterType' => $this->filterType,
            'searchTerm' => $this->searchTerm,
        ]);

        return $tasks;
    }

    public function openApprovalModal(int $approvalId, string $decisionType): void
    {
        try {
            $approvalTask = Approval::with('approvable.loanApplicationItems')->findOrFail($approvalId); // Eager load loanApplicationItems
            $this->authorize('performApproval', $approvalTask); // Authorize the action

            $this->currentApprovalId = $approvalId;
            $this->currentApprovalTask = $approvalTask;
            $this->approvalDecision = $decisionType;
            $this->approvalNotes = ''; // Reset notes

            // Initialize approvalItems for LoanApplication based on current state
            $this->approvalItems = [];
            if ($approvalTask->approvable instanceof LoanApplication) {
                foreach ($approvalTask->approvable->loanApplicationItems as $item) {
                    $this->approvalItems[] = [
                        'id' => $item->id,
                        'equipment_id' => $item->equipment_id,
                        'requested_quantity' => $item->quantity,
                        'quantity_approved' => ($decisionType === Approval::STATUS_APPROVED) ? $item->quantity : 0, // Set default based on decision
                        'equipment_name' => $item->equipment->brand.' '.$item->equipment->model.' ('.$item->equipment->tag_id.')',
                    ];
                }
            }

            $this->modalTitle = ($decisionType === Approval::STATUS_APPROVED) ? 'Lulus Permohonan' : 'Tolak Permohonan'; // Corrected constants
            $this->showApprovalModal = true;

            Log::info(sprintf('Livewire\Approval\Dashboard: Opened approval modal for task %d with decision type %s.', $approvalId, $decisionType), [
                'user_id' => Auth::id(),
            ]);
        } catch (ModelNotFoundException $e) {
            $this->dispatch('toastr', type: 'error', message: __('Tugas kelulusan tidak ditemui.'));
            Log::error('Livewire\Approval\Dashboard: Approval task not found.', ['approval_id' => $approvalId, 'error' => $e->getMessage()]);
        } catch (AuthorizationException $e) {
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk membuat keputusan ke atas tugas ini.'));
            Log::warning('Livewire\Approval\Dashboard: Authorization failed for approval task.', ['approval_id' => $approvalId, 'user_id' => Auth::id(), 'error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $this->dispatch('toastr', type: 'error', message: __('Ralat: Gagal membuka modal kelulusan.'));
            Log::error('Livewire\Approval\Dashboard: Unexpected error opening approval modal.', ['approval_id' => $approvalId, 'error' => $e->getMessage()]);
        }
    }

    public function processApproval(): void
    {
        try {
            $this->validateApprovalInputs();

            if (! $this->currentApprovalId || ! $this->currentApprovalTask) {
                throw new \RuntimeException('Tiada tugas kelulusan aktif.');
            }

            $user = Auth::user();
            throw_if(! $user instanceof User, new \RuntimeException('Authenticated user not found.'));

            // Call the approval service to record the decision
            $this->approvalService->recordApprovalDecision( // This method needs to be in ApprovalService
                $this->currentApprovalTask,
                $this->approvalDecision,
                $this->approvalNotes,
                $this->approvalItems // Pass items for loan application processing
            );

            $this->dispatch('toastr', type: 'success', message: __('Keputusan kelulusan berjaya direkodkan.'));
            $this->closeApprovalModal();
            $this->resetPage(); // Refresh the list
            Log::info(sprintf('Livewire\Approval\Dashboard: Approval decision recorded for task %d.', $this->currentApprovalId), [
                'decision' => $this->approvalDecision,
                'user_id' => $user->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toastr', type: 'error', message: __('Sila semak semula borang kelulusan anda.'));
            Log::warning('Livewire\Approval\Dashboard: Validation failed for approval decision.', ['errors' => $e->errors()]);
            throw $e; // Re-throw to show validation errors in the form
        } catch (AuthorizationException $e) {
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk membuat keputusan ini.'));
            Log::warning('Livewire\Approval\Dashboard: Authorization failed during approval process.', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $this->dispatch('toastr', type: 'error', message: sprintf('Ralat semasa merekod keputusan: %s', $e->getMessage()));
            Log::error('Livewire\Approval\Dashboard: Error processing approval.', ['approval_id' => $this->currentApprovalId, 'error' => $e->getMessage()]);
        }
    }

    public function closeApprovalModal(): void
    {
        $this->showApprovalModal = false;
        $this->reset(['currentApprovalId', 'currentApprovalTask', 'approvalDecision', 'approvalNotes', 'approvalItems', 'modalTitle']);
        $this->resetValidation(); // Clear validation errors
    }

    protected function validateApprovalInputs(): array
    {
        $rules = [
            'approvalNotes' => ['nullable', 'string', 'max:1000'],
            'approvalItems' => ['array'],
        ];

        // Dynamic rules for quantity approved only if decision is 'approved' and it's a loan application
        if ($this->approvalDecision === Approval::STATUS_APPROVED && $this->currentApprovalTask?->approvable instanceof LoanApplication) { // Corrected constant
            foreach ($this->approvalItems as $index => $item) {
                $maxQty = $item['requested_quantity']; // Max quantity to approve is the requested quantity
                $rules['approvalItems.' . $index . '.quantity_approved'] = [
                    'required',
                    'integer',
                    'min:0',
                    'max:' . $maxQty,
                ];
            }
        }

        $validated = $this->validate($rules, $this->getValidationMessages());
        Log::debug('Livewire\Approval\Dashboard: Approval inputs validated.', ['validated' => $validated]);

        return $validated;
    }

    protected function getValidationMessages(): array
    {
        $messages = [
            'approvalNotes.max' => __('approvals.validation.notes_max'),
            'approvalItems.array' => __('approvals.validation.items_array'),
        ];

        if ($this->approvalDecision === Approval::STATUS_APPROVED && $this->currentApprovalTask?->approvable instanceof LoanApplication) { // Corrected constant
            foreach ($this->approvalItems as $index => $item) {
                $itemTypeDisplay = $item['equipment_name'] ?? 'Item';
                $maxQty = $item['requested_quantity'];
                $messages['approvalItems.' . $index . '.quantity_approved.required'] = __('approvals.validation.quantity_required', ['itemType' => $itemTypeDisplay]);
                $messages['approvalItems.' . $index . '.quantity_approved.integer'] = __('approvals.validation.quantity_integer', ['itemType' => $itemTypeDisplay]);
                $messages['approvalItems.' . $index . '.quantity_approved.min'] = __('approvals.validation.quantity_min', ['itemType' => $itemTypeDisplay]);
                $messages['approvalItems.' . $index . '.quantity_approved.max'] = __('approvals.validation.quantity_max', ['itemType' => $itemTypeDisplay, 'max' => $maxQty]);
            }
        }
        return $messages;
    }

    public function getViewApplicationRoute(Approval $approvalTask): ?string
    {
        $approvable = $approvalTask->approvable;
        if (!$approvable || !$approvable->id) {
            return null;
        }

        $routeName = null;
        $routeParams = [];

        if ($approvable instanceof LoanApplication) {
            $routeName = 'loan-applications.show';
            $routeParams = ['loan_application' => $approvable->id];
        }
        // Removed EmailApplication specific route

        if ($routeName && Route::has($routeName)) {
            try {
                return route($routeName, $routeParams);
            } catch (\Exception $e) {
                Log::error('Error generating getViewApplicationRoute: ' . $e->getMessage(), ['routeName' => $routeName, 'params' => $routeParams]);
                return null;
            }
        }
        return null;
    }
}
