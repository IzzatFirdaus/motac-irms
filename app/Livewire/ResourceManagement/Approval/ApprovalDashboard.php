<?php

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
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

/**
 * ApprovalDashboard Livewire Component
 * Displays pending approval tasks and allows officers to record decisions.
 * Used for /approvals route as per routes/web.php
 */
#[Layout('layouts.app')]
class ApprovalDashboard extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // --- Filters ---
    #[Url(keep: true, history: true, as: 'status_kelulusan')]
    public string $filterStatus = Approval::STATUS_PENDING;

    #[Url(keep: true, history: true, as: 'jenis_permohonan')]
    public string $filterType = ''; // Can be 'loan_application', 'helpdesk_ticket', or '' for all

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

    /**
     * Dependency injection for ApprovalService.
     */
    public function boot(ApprovalService $approvalService): void
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Component mount: authorize view and log access.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', Approval::class);
        Log::info('Livewire\Approval\ApprovalDashboard: Approval dashboard loaded.', [
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Retrieve approval tasks for the authenticated officer, with filters.
     */
    #[Computed]
    public function approvalTasks(): LengthAwarePaginator
    {
        $approver = Auth::user();
        throw_if(! $approver instanceof User, new \RuntimeException('Authenticated user not found.'));

        $query = Approval::where('officer_id', $approver->id)
            ->with(['approvable', 'approvable.user', 'officer']);

        // Apply status filter
        if ($this->filterStatus && $this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // Apply type filter (matches Blade: 'loan_application', 'helpdesk_ticket')
        if ($this->filterType && $this->filterType !== 'all') {
            if ($this->filterType === 'loan_application') {
                $query->whereMorphedTo('approvable', LoanApplication::class);
            }
            // If you support helpdesk_ticket, add logic for that model
            // elseif ($this->filterType === 'helpdesk_ticket') { ... }
        }

        // Search filter (searches applicant name/email for loan applications)
        if ($this->searchTerm) {
            $searchTermLower = strtolower($this->searchTerm);
            $query->where(function ($q) use ($searchTermLower) {
                $q->whereHasMorph('approvable', [LoanApplication::class], function ($morphQuery) use ($searchTermLower) {
                    $morphQuery->whereHas('user', function ($userQuery) use ($searchTermLower) {
                        $userQuery->whereRaw('LOWER(name) LIKE ?', ['%'.$searchTermLower.'%'])
                            ->orWhereRaw('LOWER(email) LIKE ?', ['%'.$searchTermLower.'%']);
                    });
                });
                // Extend this block if you add search for helpdesk_ticket
            });
        }

        // Order: latest pending first
        $query->orderByRaw("CASE
            WHEN status = '".Approval::STATUS_PENDING."' THEN 1
            WHEN status = '".Approval::STATUS_APPROVED."' THEN 2
            WHEN status = '".Approval::STATUS_REJECTED."' THEN 3
            ELSE 4
        END")
            ->orderBy('created_at', 'desc');

        $tasks = $query->paginate(10);

        Log::info(sprintf('Livewire\Approval\ApprovalDashboard: Fetched %d approval tasks for user %d.', $tasks->total(), $approver->id), [
            'filterStatus' => $this->filterStatus,
            'filterType' => $this->filterType,
            'searchTerm' => $this->searchTerm,
        ]);

        return $tasks;
    }

    /**
     * Open the modal for a specific approval task.
     */
    public function openApprovalModal(int $approvalId, string $decisionType = ''): void
    {
        try {
            $approvalTask = Approval::with('approvable.loanApplicationItems')->findOrFail($approvalId);
            $this->authorize('performApproval', $approvalTask);

            $this->currentApprovalId = $approvalId;
            $this->currentApprovalTask = $approvalTask;
            $this->approvalDecision = $decisionType ?? '';
            $this->approvalNotes = '';

            $this->approvalItems = [];
            if ($approvalTask->approvable instanceof LoanApplication) {
                foreach ($approvalTask->approvable->loanApplicationItems as $item) {
                    $this->approvalItems[] = [
                        'id' => $item->id,
                        'equipment_id' => $item->equipment_id,
                        'requested_quantity' => $item->quantity,
                        'quantity_approved' => ($decisionType === Approval::STATUS_APPROVED) ? $item->quantity : 0,
                        'equipment_name' => $item->equipment->brand.' '.$item->equipment->model.' ('.$item->equipment->tag_id.')',
                    ];
                }
            }

            $this->modalTitle = ($decisionType === Approval::STATUS_APPROVED) ? 'Lulus Permohonan' : 'Tolak Permohonan';
            $this->showApprovalModal = true;

            Log::info(sprintf('Livewire\Approval\ApprovalDashboard: Opened approval modal for task %d with decision type %s.', $approvalId, $decisionType), [
                'user_id' => Auth::id(),
            ]);
        } catch (ModelNotFoundException $e) {
            $this->dispatch('toastr', type: 'error', message: __('Tugas kelulusan tidak ditemui.'));
            Log::error('Livewire\Approval\ApprovalDashboard: Approval task not found.', ['approval_id' => $approvalId, 'error' => $e->getMessage()]);
        } catch (AuthorizationException $e) {
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk membuat keputusan ke atas tugas ini.'));
            Log::warning('Livewire\Approval\ApprovalDashboard: Authorization failed for approval task.', ['approval_id' => $approvalId, 'user_id' => Auth::id(), 'error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $this->dispatch('toastr', type: 'error', message: __('Ralat: Gagal membuka modal kelulusan.'));
            Log::error('Livewire\Approval\ApprovalDashboard: Unexpected error opening approval modal.', ['approval_id' => $approvalId, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Process the approval action from the modal.
     */
    public function processApproval(): void
    {
        try {
            $this->validateApprovalInputs();

            if (! $this->currentApprovalId || ! $this->currentApprovalTask) {
                throw new \RuntimeException('Tiada tugas kelulusan aktif.');
            }

            $user = Auth::user();
            throw_if(! $user instanceof User, new \RuntimeException('Authenticated user not found.'));

            $this->approvalService->recordApprovalDecision(
                $this->currentApprovalTask,
                $this->approvalDecision,
                $this->approvalNotes,
                $this->approvalItems
            );

            $this->dispatch('toastr', type: 'success', message: __('Keputusan kelulusan berjaya direkodkan.'));
            $this->closeApprovalModal();
            $this->resetPage();
            Log::info(sprintf('Livewire\Approval\ApprovalDashboard: Approval decision recorded for task %d.', $this->currentApprovalId), [
                'decision' => $this->approvalDecision,
                'user_id' => $user->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toastr', type: 'error', message: __('Sila semak semula borang kelulusan anda.'));
            Log::warning('Livewire\Approval\ApprovalDashboard: Validation failed for approval decision.', ['errors' => $e->errors()]);
            throw $e;
        } catch (AuthorizationException $e) {
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk membuat keputusan ini.'));
            Log::warning('Livewire\Approval\ApprovalDashboard: Authorization failed during approval process.', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $this->dispatch('toastr', type: 'error', message: sprintf('Ralat semasa merekod keputusan: %s', $e->getMessage()));
            Log::error('Livewire\Approval\ApprovalDashboard: Error processing approval.', ['approval_id' => $this->currentApprovalId, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Close the approval modal and reset related state.
     */
    public function closeApprovalModal(): void
    {
        $this->showApprovalModal = false;
        $this->reset(['currentApprovalId', 'currentApprovalTask', 'approvalDecision', 'approvalNotes', 'approvalItems', 'modalTitle']);
        $this->resetValidation();
    }

    /**
     * Validate approval form inputs.
     */
    protected function validateApprovalInputs(): array
    {
        $rules = [
            'approvalNotes' => ['nullable', 'string', 'max:1000'],
            'approvalItems' => ['array'],
        ];

        if ($this->approvalDecision === Approval::STATUS_APPROVED && $this->currentApprovalTask?->approvable instanceof LoanApplication) {
            foreach ($this->approvalItems as $index => $item) {
                $maxQty = $item['requested_quantity'];
                $rules['approvalItems.' . $index . '.quantity_approved'] = [
                    'required',
                    'integer',
                    'min:0',
                    'max:' . $maxQty,
                ];
            }
        }

        $validated = $this->validate($rules, $this->getValidationMessages());
        Log::debug('Livewire\Approval\ApprovalDashboard: Approval inputs validated.', ['validated' => $validated]);
        return $validated;
    }

    /**
     * Custom messages for approval validation.
     */
    protected function getValidationMessages(): array
    {
        $messages = [
            'approvalNotes.max' => __('approvals.validation.notes_max'),
            'approvalItems.array' => __('approvals.validation.items_array'),
        ];

        if ($this->approvalDecision === Approval::STATUS_APPROVED && $this->currentApprovalTask?->approvable instanceof LoanApplication) {
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

    /**
     * Get the route to view the related application.
     */
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
            $routeParams = ['loanApplication' => $approvable->id];
        }

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

    /**
     * Render the approval dashboard Blade view.
     */
    public function render(): View
    {
        // Render the correct Blade file, as mapped in web.php
        return view('livewire.resource-management.approval.approval-dashboard');
    }
}
